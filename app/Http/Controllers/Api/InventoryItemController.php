<?php

namespace App\Http\Controllers\Api;

use App\DiscountBand;
use App\Http\Controllers\Controller;
use App\ItemPromotion;
use App\Model\PackSize;
use App\Model\WaInventoryCategory;
use App\Model\WaStockMove;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaLocationAndStore;
use App\Model\WaSupplier;
use App\Model\WaUnitOfMeasure;
use App\Models\TradeAgreementDiscount;
use App\WaItemSubCategory;
use Tymon\JWTAuth\Facades\JWTAuth;


class InventoryItemController extends Controller
{
    public function list_by_supplier(Request $request)
    {
        try {
            if (!$request->supplier) {
                throw new \Exception("Supplier is missing");
            }
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            //            $supplier = WaSupplier::where('supplier_code', 'SUP-00738')->firstOrFail();
            $selects = [
                'wa_inventory_items.*'
            ];
            if ($request->max_stock_qoo) {
                $qooQuery = "SELECT 
                                SUM(quantity)
                            FROM
                                `wa_purchase_order_items`
                                    JOIN
                                `wa_purchase_orders` ON `wa_purchase_order_items`.`wa_purchase_order_id` = `wa_purchase_orders`.`id`
                                    LEFT JOIN
                                `wa_grns` ON `wa_purchase_orders`.`id` = `wa_grns`.`wa_purchase_order_id`
                            WHERE
                                `wa_purchase_order_items`.`wa_inventory_item_id` = `wa_inventory_items`.`id`
                                    AND `status` = 'APPROVED'
                                    AND `is_hide` <> 'YES'
                                    AND `wa_grns`.id IS NULL";
                $selects[] = DB::raw("($qooQuery) as qty_on_order");
            }

            $items = WaInventoryItem::select(
                $selects
            )->with(['supplier_data' => function ($e) use ($supplier) {
                $e->where('wa_supplier_id', $supplier->id);
            }])
                //                ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
                // ->join('pack_sizes', 'child.pack_size_id', '=', 'pack_sizes.id')
                ->whereHas('inventory_item_suppliers', function ($e) use ($supplier) {
                    $e->where('wa_supplier_id', $supplier->id);
                })
                ->where('wa_inventory_items.status', 1)
                ->groupBy('wa_inventory_items.id')
                ->orderBy('wa_inventory_items.id');
            $items = $items->get();

            $uom = PackSize::select('id', 'title')->get();
            $sub_categories = WaItemSubCategory::select('id', 'title')->get();
            $trade_agreement_discounts = TradeAgreementDiscount::typeList();

            return response()->json([
                'result' => 1,
                'message' => 'Data Retrived Successfully',
                'uom' => $uom,
                'sub_categories' => $sub_categories,
                'trade_agreement_discounts' => $trade_agreement_discounts,
                'data' => $items->map(function ($item) {
                    $item->selling_price_formated = manageAmountFormat($item->selling_price);
                    return $item;
                })
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function getInventoryWithPromotion(Request $request)
    {
        $appUrl = env('APP_URL');
        $today = Carbon::today();
        $search = $request->search ?? '';
        $user = JWTAuth::toUser($request->token);
        $appUrl = env('APP_URL');

        $items = DB::table('item_promotions')
            ->leftJoin('wa_inventory_items', 'item_promotions.inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('wa_inventory_items as promotion_items', 'item_promotions.promotion_item_id', '=', 'promotion_items.id')
            ->select(
                'item_promotions.*',
                'wa_inventory_items.title',
                'wa_inventory_items.image',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.selling_price',
                'promotion_items.id as promotion_item_id',
                'promotion_items.stock_id_code as promotion_item_stock_id_code',
                'promotion_items.title as promotion_item_title',
                'promotion_items.image as promotion_item_image',
                DB::raw("(SELECT SUM(wa_stock_moves.qauntity)
                    FROM wa_stock_moves
                    WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id
                    AND wa_stock_moves.wa_location_and_store_id = '$user->wa_location_and_store_id'
                ) as qoh")
            )
            ->where('item_promotions.status', 'active')
            ->where(function ($query) use ($today) {
                $query->where('item_promotions.from_date', '<=', $today)
                    ->where('item_promotions.to_date', '>=', $today);
            })
            ->where('wa_inventory_items.title', 'like', "%{$search}%");
        if ($request->item_id) {
            $items = $items->where('wa_inventory_items.id', $request->item_id);
        }
        $items =  $items->having('qoh', '>', 0)
            ->orderBy('item_promotions.id', 'desc')
            ->cursorPaginate(5);

        $items->transform(function ($sale) use ($appUrl) {
            $sale->image = "$appUrl/uploads/inventory_items/" . $sale->image;
            $sale->inventory_item = (object) [
                'id' => $sale->inventory_item_id,
                'title' => $sale->title,
                'stock_id_code' => $sale->stock_id_code,
                'selling_price' => $sale->selling_price,
            ];
            if ($sale->promotion_item_id) {
                $promotionItemImage =  "$appUrl/uploads/inventory_items/" . $sale->promotion_item_image;
                $sale->promotion_item = (object) [
                    'id' => $sale->promotion_item_id,
                    'title' => $sale->promotion_item_title,
                    'stock_id_code' => $sale->promotion_item_stock_id_code,
                    'image'  => $promotionItemImage,

                ];
            } else {
                $sale->promotion_item = null;
            }
            unset($sale->inventory_item_id, $sale->title, $sale->stock_id_code, $sale->selling_price, $sale->promotion_item_title, $sale->promotion_item_image, $sale->promotion_item_id, $sale->promotion_item_stock_id_code);

            return $sale;
        });
        return response()->json($items);
    }
    public function getInventoryWithDiscounts(Request $request)
    {
        $user = JWTAuth::toUser($request->token);
        $appUrl = env('APP_URL');
        $search = $request->search ?? '';

        $items = DB::table('discount_bands')
            ->join('wa_inventory_items', 'discount_bands.inventory_item_id', '=', 'wa_inventory_items.id')
            ->select(
                'discount_bands.*',
                'wa_inventory_items.title',
                'wa_inventory_items.image',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.selling_price',
                DB::raw("(SELECT SUM(wa_stock_moves.qauntity) 
                    FROM wa_stock_moves WHERE
                        wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id
                        AND  wa_stock_moves.wa_location_and_store_id = '$user->wa_location_and_store_id'
                ) as qoh")
            )
            ->where('discount_bands.status', 'APPROVED');
        if ($request->item_id) {
            $items = $items->where('wa_inventory_items.id', $request->item_id);
        }
        $items = $items->where('wa_inventory_items.title', 'like', "%{$search}%")
            ->having('qoh', '>', 0)
            ->orderBy('discount_bands.id')
            ->cursorPaginate(20);

        $appUrl = env('APP_URL');

        $items->transform(function ($sale) use ($appUrl) {
            $sale->image = "$appUrl/uploads/inventory_items/" . $sale->image;
            $sale->inventory_item = (object) [
                'id' => $sale->inventory_item_id,
                'title' => $sale->title,
                'stock_id_code' => $sale->stock_id_code,
                'selling_price' => $sale->selling_price,
            ];
            unset($sale->inventory_item_id, $sale->title, $sale->stock_id_code, $sale->selling_price);

            return $sale;
        });


        return response()->json($items);
    }

    public function getSupplierInventoryCount(Request $request)
    {
        try {

            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $items = WaInventoryItem::query()
                ->join('pack_sizes as ps', 'ps.id', 'wa_inventory_items.pack_size_id')
                ->where('ps.can_order', 1)
                ->where('wa_inventory_items.status', 1)
                ->whereHas('inventory_item_suppliers', function ($e) use ($supplier) {
                    $e->where('wa_supplier_id', $supplier->id);
                });

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => [
                    'items_count' => $items->count()
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function getSupplierInventory(Request $request)
    {
        try {

            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $qohSub = "SELECT 
                        IFNULL(SUM(qauntity),0)
                    FROM
                        `wa_stock_moves`
                    WHERE
                        `wa_inventory_item_id` = `wa_inventory_items`.`id`";

            $stocks = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.title',
                    'stock_id_code',
                    DB::raw("($qohSub) AS qoh")
                ])
                ->join('pack_sizes as ps', 'ps.id', 'wa_inventory_items.pack_size_id')
                ->where('ps.can_order', 1)
                ->where('wa_inventory_items.status', 1)
                ->whereHas('inventory_item_suppliers', function ($e) use ($supplier) {
                    $e->where('wa_supplier_id', $supplier->id);
                });

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => [
                    'stocks' => $stocks->get()
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function getSupplierBranchSales(Request $request)
    {
        try {
            $from = $request->filled('from') ? $request->from . " 00:00:00" : now()->subDays(30)->startOfDay()->toDateTimeString();
            $to = $request->filled('from') ? $request->to . " 23:59:59" : now()->endOfDay()->toDateTimeString();

            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $querySub = WaStockMove::query()
                ->select([
                    'wa_location_and_store_id',
                    DB::raw('SUM(total_cost) as total_sales')
                ])
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'CIV-%');
                })
                ->whereIn('wa_stock_moves.wa_inventory_item_id', $items)
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->groupBy('wa_location_and_store_id');

            $locations = WaLocationAndStore::query()
                ->select([
                    'wa_location_and_stores.location_name as name',
                    DB::raw('IFNULL(total_sales, 0) as total_sales'),
                ])
                ->leftJoinSub($querySub, 'sales', 'sales.wa_location_and_store_id', 'wa_location_and_stores.id')
                ->get();

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => [
                    'locations' => $locations
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function getSupplierCategorySales(Request $request)
    {
        try {
            $from = $request->filled('from') ? $request->from . " 00:00:00" : now()->subDays(30)->startOfDay()->toDateTimeString();
            $to = $request->filled('from') ? $request->to . " 23:59:59" : now()->endOfDay()->toDateTimeString();

            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)
                ->select([
                    'wa_inventory_category_id',
                    'wa_inventory_item_id',
                ])
                ->leftJoin('wa_inventory_items as item', 'item.id', 'wa_inventory_item_suppliers.wa_inventory_item_id')
                ->get();

            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();
            $assignedCategories = $inventoryItems->pluck('wa_inventory_category_id')->unique()->toArray();

            $querySub = WaStockMove::query()
                ->select([
                    'items.wa_inventory_category_id as category_id',
                    DB::raw('SUM(total_cost) as total_sales')
                ])
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'CIV-%');
                })
                ->join('wa_inventory_items as items', 'items.id', 'wa_stock_moves.wa_inventory_item_id')
                ->whereIn('wa_stock_moves.wa_inventory_item_id', $items)
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->groupBy('items.wa_inventory_category_id');

            $categories = WaInventoryCategory::query()
                ->select([
                    'category_description as name',
                    DB::raw('IFNULL(total_sales, 0) as total_sales'),
                ])
                ->leftJoinSub($querySub, 'sales', 'sales.category_id', 'wa_inventory_categories.id')
                ->whereIn('id', $assignedCategories)
                ->get();

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => [
                    'categories' => $categories
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }
}
