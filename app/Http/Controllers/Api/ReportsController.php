<?php

namespace App\Http\Controllers\Api;

use App\Enums\PackSizeEnum;
use App\Model\WaInternalRequisitionItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PackSize;
use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\WaInventoryLocationTransferItemReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{

    public function inventory_location_stock(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->first();
            if (!$supplier) {
                throw new \Exception("Error Processing Request: Supplier not found.");
            }

            $locations = WaLocationAndStore::where('is_physical_store', '1')->orderBy('location_name', 'desc')->get();
            $selects = [
                'ii.stock_id_code',
                'ii.title',
                'ii.selling_price',
            ];

            if ($request->data_type == 'value') {
                foreach ($locations as $location) {
                    $selects[] = DB::raw("COALESCE(SUM(CASE WHEN sm.wa_location_and_store_id = {$location->id} THEN qauntity * ii.selling_price ELSE 0 END), 0) AS `location_$location->id`");
                }
            } else {
                foreach ($locations as $location) {
                    $selects[] = DB::raw("COALESCE(SUM(CASE WHEN sm.wa_location_and_store_id = {$location->id} THEN qauntity ELSE 0 END), 0) AS `location_$location->id`");
                }
            }

            $items = WaInventoryItem::select($selects)
                ->from('wa_inventory_items as ii')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->leftJoin('wa_stock_moves as sm', 'sm.wa_inventory_item_id', '=', 'ii.id')
                ->where('ii.status', 1)
                ->groupBy('ii.id')
                ->get();

            return response()->json([
                'result' => 1,
                'data' => [
                    'items' =>  $items,
                    'locations' => $locations
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function slow_moving_items_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $salesSub = WaStockMove::query()
                ->select([
                    DB::raw('ABS(SUM(qauntity))')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qohSub = WaStockMove::query()
                ->select([
                    DB::raw('SUM(qauntity)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $lastGrnDateSub = WaStockMove::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-') as last_grn_date")
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where('document_no', 'like', 'GRN-%')
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');


            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'category_description as category',
                ])
                ->selectSub($salesSub, 'total_sales')
                ->selectSub($qohSub, 'qty_on_hand')
                ->selectSub($lastGrnDateSub, 'last_grn_date')
                ->leftJoin('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->whereIn('wa_inventory_items.id', $items)
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('IFNULL(qty_on_hand, 0) >  IFNULL(max_stock, 0)');

            return response()->json([
                'result' => 1,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'data' => []
            ]);
        }
    }

    public function over_stock_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $salesSub = WaStockMove::query()
                ->select([
                    DB::raw('ABS(SUM(qauntity))')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qohSub = WaStockMove::query()
                ->select([
                    DB::raw('SUM(qauntity)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qooSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(SUM(quantity),0)")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where('status', 'APPROVED')
                        ->where('is_hide', '<>', 'Yes')
                        ->doesntHave('grns');

                    $query->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $purchaseOrders = DB::raw("IFNULL((SELECT 
                GROUP_CONCAT(purchase_no) 
                FROM wa_purchase_orders                 
            WHERE EXISTS (SELECT id FROM wa_purchase_order_items where wa_purchase_orders.id = wa_purchase_order_items.wa_purchase_order_id AND wa_inventory_items.id = wa_purchase_order_items.wa_inventory_item_id)
            AND NOT EXISTS (SELECT id FROM wa_grns where wa_grns.wa_purchase_order_id = wa_purchase_orders.id)
            AND wa_purchase_orders.status = 'APPROVED' 
            AND wa_purchase_orders.is_hide <> 'Yes' 
            AND wa_purchase_orders.wa_location_and_store_id = " . request()->store_location . "), '-') As purchase_order_numbers");


            $lastLpoDateSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-')")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 'APPROVED')
                            ->orWhere('status', 'COMPLETED');
                    })
                        ->where('is_hide', '<>', 'Yes')
                        ->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $lastGrnDateSub = WaStockMove::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-') as last_grn_date")
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where('document_no', 'like', 'GRN-%')
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');


            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'category_description as category',
                    DB::raw("IFNULL(stock_status.max_stock, 0) as max_stock"),
                    DB::raw("IFNULL(stock_status.re_order_level, 0) as re_order_level"),
                    $purchaseOrders
                ])
                ->selectSub($salesSub, 'total_sales')
                ->selectSub($qohSub, 'qty_on_hand')
                ->selectSub($qooSub, 'qty_on_order')
                ->selectSub($lastLpoDateSub, 'last_lpo_date')
                ->selectSub($lastGrnDateSub, 'last_grn_date')
                ->leftJoin('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->leftJoin('wa_inventory_location_stock_status as stock_status', function ($query) {
                    $query->on('stock_status.wa_inventory_item_id', 'wa_inventory_items.id')
                        ->where('wa_location_and_stores_id', request()->store_location);
                })
                ->whereIn('wa_inventory_items.id', $items)
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('IFNULL(qty_on_hand, 0) >  IFNULL(max_stock, 0)');

            return response()->json([
                'result' => 1,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'data' => []
            ]);
        }
    }

    public function dead_stock_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $salesSub = WaStockMove::query()
                ->select([
                    DB::raw('IFNULL(ABS(SUM(qauntity)),0)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qohSub = WaStockMove::query()
                ->select([
                    DB::raw('IFNULL(SUM(qauntity),0)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $lastSaleDateSub = WaStockMove::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-') as last_sale_date")
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where('document_no', 'like', 'INV-%')
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');


            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'category_description as category',
                ])
                ->selectSub($salesSub, 'total_sales')
                ->selectSub($qohSub, 'qty_on_hand')
                ->selectSub($lastSaleDateSub, 'last_sale_date')
                ->leftJoin('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->whereIn('wa_inventory_items.id', $items)
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('IFNULL(qty_on_hand, 0) > 0')
                ->havingRaw('IFNULL(total_sales, 0) = 0');

            return response()->json([
                'result' => 1,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'data' => $th->getMessage()
            ]);
        }
    }

    public function missing_items_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $salesSub = WaStockMove::query()
                ->select([
                    DB::raw('ABS(SUM(qauntity))')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qohSub = WaStockMove::query()
                ->select([
                    DB::raw('SUM(qauntity)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qooSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(SUM(quantity),0)")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where('status', 'APPROVED')
                        ->where('is_hide', '<>', 'Yes')
                        ->doesntHave('grns');

                    $query->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $purchaseOrders = DB::raw("IFNULL((SELECT 
                    GROUP_CONCAT(purchase_no) 
                    FROM wa_purchase_orders                 
                WHERE EXISTS (SELECT id FROM wa_purchase_order_items where wa_purchase_orders.id = wa_purchase_order_items.wa_purchase_order_id AND wa_inventory_items.id = wa_purchase_order_items.wa_inventory_item_id)
                AND NOT EXISTS (SELECT id FROM wa_grns where wa_grns.wa_purchase_order_id = wa_purchase_orders.id)
                AND wa_purchase_orders.status = 'APPROVED' 
                AND wa_purchase_orders.is_hide <> 'Yes' 
                AND wa_purchase_orders.wa_location_and_store_id = " . request()->store_location . "), '-') As purchase_order_numbers");


            $lastLpoDateSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-')")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 'APPROVED')
                            ->orWhere('status', 'COMPLETED');
                    })
                        ->where('is_hide', '<>', 'Yes')
                        ->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $lastGrnDateSub = WaStockMove::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-') as last_grn_date")
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where('document_no', 'like', 'GRN-%')
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');


            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'category_description as category',
                    DB::raw("IFNULL(stock_status.max_stock, 0) as max_stock"),
                    DB::raw("IFNULL(stock_status.re_order_level, 0) as re_order_level"),
                    $purchaseOrders
                ])
                ->selectSub($salesSub, 'total_sales')
                ->selectSub($qohSub, 'qty_on_hand')
                ->selectSub($qooSub, 'qty_on_order')
                ->selectSub($lastLpoDateSub, 'last_lpo_date')
                ->selectSub($lastGrnDateSub, 'last_grn_date')
                ->leftJoin('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->leftJoin('wa_inventory_location_stock_status as stock_status', function ($query) {
                    $query->on('stock_status.wa_inventory_item_id', 'wa_inventory_items.id')
                        ->where('wa_location_and_stores_id', request()->store_location);
                })
                ->whereIn('wa_inventory_items.id', $items)
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('IFNULL(qty_on_hand, 0) = 0')
                ->havingRaw('total_sales > 0');

            return response()->json([
                'result' => 1,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }
    public function reorder_items_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->firstOrFail();
            $inventoryItems = WaInventoryItemSupplier::where('wa_supplier_id', $supplier->id)->get();
            $items = $inventoryItems->pluck('wa_inventory_item_id')->toArray();

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $salesSub = WaStockMove::query()
                ->select([
                    DB::raw('IFNULL(ABS(SUM(qauntity)), 0)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qohSub = WaStockMove::query()
                ->select([
                    DB::raw('SUM(qauntity)')
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

            $qooSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(SUM(quantity),0)")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where('status', 'APPROVED')
                        ->where('is_hide', '<>', 'Yes')
                        ->doesntHave('grns');

                    $query->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $purchaseOrders = DB::raw("IFNULL((SELECT 
                    GROUP_CONCAT(purchase_no) 
                    FROM wa_purchase_orders                 
                WHERE EXISTS (SELECT id FROM wa_purchase_order_items where wa_purchase_orders.id = wa_purchase_order_items.wa_purchase_order_id AND wa_inventory_items.id = wa_purchase_order_items.wa_inventory_item_id)
                AND NOT EXISTS (SELECT id FROM wa_grns where wa_grns.wa_purchase_order_id = wa_purchase_orders.id)
                AND wa_purchase_orders.status = 'APPROVED' 
                AND wa_purchase_orders.is_hide <> 'Yes' 
                AND wa_purchase_orders.wa_location_and_store_id = " . request()->store_location . "), '-') As purchase_order_numbers");


            $lastLpoDateSub = WaPurchaseOrderItem::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-')")
                ])
                ->whereHas('getPurchaseOrder', function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 'APPROVED')
                            ->orWhere('status', 'COMPLETED');
                    })
                        ->where('is_hide', '<>', 'Yes')
                        ->where('wa_location_and_store_id', request()->store_location);
                })
                ->whereColumn('wa_purchase_order_items.wa_inventory_item_id', 'wa_inventory_items.id');

            $lastGrnDateSub = WaStockMove::query()
                ->select([
                    DB::raw("IFNULL(MAX(created_at), '-') as last_grn_date")
                ])
                ->where('wa_location_and_store_id', request()->store_location)
                ->where('document_no', 'like', 'GRN-%')
                ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');


            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'category_description as category',
                    DB::raw("IFNULL(stock_status.max_stock, 0) as max_stock"),
                    DB::raw("IFNULL(stock_status.re_order_level, 0) as re_order_level"),
                    $purchaseOrders
                ])
                ->selectSub($salesSub, 'total_sales')
                ->selectSub($qohSub, 'qty_on_hand')
                ->selectSub($qooSub, 'qty_on_order')
                ->selectSub($lastLpoDateSub, 'last_lpo_date')
                ->selectSub($lastGrnDateSub, 'last_grn_date')
                ->leftJoin('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->leftJoin('wa_inventory_location_stock_status as stock_status', function ($query) {
                    $query->on('stock_status.wa_inventory_item_id', 'wa_inventory_items.id')
                        ->where('wa_location_and_stores_id', request()->store_location);
                })
                ->whereIn('wa_inventory_items.id', $items)
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('IFNULL(qty_on_hand, 0) > 0')
                ->havingRaw('IFNULL(qty_on_hand, 0) <= re_order_level');

            return response()->json([
                'result' => 1,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    public function inventory_item_sales(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->first();
            if (!$supplier) {
                throw new \Exception("Error Processing Request: Supplier not found.");
            }

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $locations = WaLocationAndStore::where('is_physical_store', '1')->orderBy('location_name', 'desc')->get();
            $inventories = WaInventoryItem::select([
                'id',
                'stock_id_code',
                'title',
            ])->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('wa_supplier_id', $supplier->id);
            })->whereHas('packSize', function ($query) {
                $query->where('can_order', 1);
            })->where('status', 1)
                ->get();

            if ($request->data_type == 'value') {
                foreach ($locations as $location) {

                    $fullSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.wa_location_and_store_id = {$location->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN total_cost_with_vat ELSE 0 END),0),2) AS `location_full_$location->id`");

                    $packSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.wa_location_and_store_id = {$location->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN total_cost_with_vat ELSE 0 END),0),2) AS `location_pack_$location->id`");

                    $fullReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.to_store_location_id = {$location->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity * items.standard_cost ELSE 0 END),0),2) AS `location_full_return_$location->id`");

                    $smallReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.to_store_location_id = {$location->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity * items.standard_cost ELSE 0 END),0),2) AS `location_pack_return_$location->id`");
                }
            } else {
                foreach ($locations as $location) {

                    $fullSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.wa_location_and_store_id = {$location->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN quantity ELSE 0 END),0),2) AS `location_full_$location->id`");

                    $packSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.wa_location_and_store_id = {$location->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN quantity/conversion_factor ELSE 0 END),0),2) AS `location_pack_$location->id`");

                    $fullReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.to_store_location_id = {$location->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity ELSE 0 END),0),2) AS `location_full_return_$location->id`");

                    $smallReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.to_store_location_id = {$location->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity/conversion_factor ELSE 0 END),0),2) AS `location_pack_return_$location->id`");
                }
            }

            $fullSales = WaInternalRequisitionItem::query()
                ->select(array_merge([
                    'wirt.wa_inventory_item_id'
                ], $fullSelects))
                ->from('wa_internal_requisition_items as wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', '=', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'wirt.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('wirt.wa_inventory_item_id')
                ->get();

            $packSales = WaInternalRequisitionItem::query()
                ->select(array_merge([
                    'items.wa_inventory_item_id'
                ], $packSelects))
                ->from('wa_internal_requisition_items as wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wirt.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'wirt.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('wirt.wa_inventory_item_id')
                ->get();

            $fullReturns = WaInventoryLocationTransferItemReturn::query()
                ->select(array_merge([
                    'items.wa_inventory_item_id'
                ], $fullReturnSelects))
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'items.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('items.wa_inventory_item_id')
                ->get();

            $packReturns = WaInventoryLocationTransferItemReturn::query()
                ->select(array_merge([
                    'wait.wa_inventory_item_id'
                ], $smallReturnSelects))
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_assigned_items as wait', 'wait.destination_item_id', '=', 'items.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'items.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('items.wa_inventory_item_id')
                ->get();

            $items = [];
            foreach ($inventories as $inventory) {
                $item['title'] = $inventory->title;
                $item['stock_id_code'] = $inventory->stock_id_code;

                foreach ($locations as $location) {
                    $location_full_attr = "location_full_$location->id";
                    $fullSale = $fullSales->where('wa_inventory_item_id', $inventory->id)->first();
                    $fullSaleAmount = $fullSale->$location_full_attr ?? 0;

                    $location_full_return_attr = "location_full_return_$location->id";
                    $fullReturn = $fullReturns->where('wa_inventory_item_id', $inventory->id)->first();
                    $fullReturnAmount = $fullReturn->$location_full_return_attr ?? 0;

                    $location_pack_attr = "location_pack_$location->id";
                    $packSale = $packSales->where('wa_inventory_item_id', $inventory->id)->first();
                    $packSaleAmount = $packSale->$location_pack_attr ?? 0;

                    $location_pack_return_attr = "location_pack_return_$location->id";
                    $packReturn = $packReturns->where('wa_inventory_item_id', $inventory->id)->first();
                    $packReturnAmount = $packReturn->$location_pack_return_attr ?? 0;

                    $item['location_' . $location->id] = ($fullSaleAmount - $fullReturnAmount) + ($packSaleAmount - $packReturnAmount);
                }

                $items[] = $item;
            }

            return response()->json([
                'result' => 1,
                'data' => [
                    'items' =>  $items,
                    'locations' => $locations
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function get_branches()
    {
        try {
            return Restaurant::select('id', 'name')->get();
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function route_performance_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->first();
            if (!$supplier) {
                throw new \Exception("Error Processing Request: Supplier not found.");
            }

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $customers = WaCustomer::select([
                'wa_customers.id',
                'customer_name',
            ])->whereHas('route', function ($query) use ($request) {
                $query->where('restaurant_id', $request->branch);
            })->get();

            $inventories = WaInventoryItem::select([
                'id',
                'stock_id_code',
                'title',
            ])->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('wa_supplier_id', $supplier->id);
            })->whereHas('packSize', function ($query) {
                $query->where('can_order', 1);
            })->where('status', 1)
                ->get();

            foreach ($customers as $customer) {

                $fullSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.customer_id = {$customer->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN quantity ELSE 0 END),0),2) AS `customer_full_$customer->id`");

                $packSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wir.customer_id = {$customer->id} AND wir.created_at BETWEEN '{$from}' AND '{$to}' THEN quantity/conversion_factor ELSE 0 END),0),2) AS `customer_pack_$customer->id`");

                $fullReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.customer_id = {$customer->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity ELSE 0 END),0),2) AS `customer_full_return_$customer->id`");

                $smallReturnSelects[] = DB::raw("ROUND(COALESCE(SUM(CASE WHEN wilt.customer_id = {$customer->id} AND wilt.created_at BETWEEN '{$from}' AND '{$to}' AND returns.return_status = 1 AND returns.status = 'received' THEN returns.received_quantity/conversion_factor ELSE 0 END),0),2) AS `customer_pack_return_$customer->id`");
            }

            $fullSales = WaInternalRequisitionItem::query()
                ->select(array_merge([
                    'wirt.wa_inventory_item_id'
                ], $fullSelects))
                ->from('wa_internal_requisition_items as wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', '=', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'wirt.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('wirt.wa_inventory_item_id')
                ->get();

            $packSales = WaInternalRequisitionItem::query()
                ->select(array_merge([
                    'items.wa_inventory_item_id'
                ], $packSelects))
                ->from('wa_internal_requisition_items as wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wirt.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'wirt.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('wirt.wa_inventory_item_id')
                ->get();

            $fullReturns = WaInventoryLocationTransferItemReturn::query()
                ->select(array_merge([
                    'items.wa_inventory_item_id'
                ], $fullReturnSelects))
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'items.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('items.wa_inventory_item_id')
                ->get();

            $packReturns = WaInventoryLocationTransferItemReturn::query()
                ->select(array_merge([
                    'wait.wa_inventory_item_id'
                ], $smallReturnSelects))
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_assigned_items as wait', 'wait.destination_item_id', '=', 'items.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'items.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->groupBy('items.wa_inventory_item_id')
                ->get();

            $items = [];
            foreach ($inventories as $inventory) {
                $item['title'] = $inventory->title;
                $item['stock_id_code'] = $inventory->stock_id_code;

                foreach ($customers as $customer) {
                    $customer_full_attr = "customer_full_$customer->id";
                    $fullSale = $fullSales->where('wa_inventory_item_id', $inventory->id)->first();
                    $fullSaleAmount = $fullSale->$customer_full_attr ?? 0;

                    $customer_full_return_attr = "customer_full_return_$customer->id";
                    $fullReturn = $fullReturns->where('wa_inventory_item_id', $inventory->id)->first();
                    $fullReturnAmount = $fullReturn->$customer_full_return_attr ?? 0;

                    $customer_pack_attr = "customer_pack_$customer->id";
                    $packSale = $packSales->where('wa_inventory_item_id', $inventory->id)->first();
                    $packSaleAmount = $packSale->$customer_pack_attr ?? 0;

                    $customer_pack_return_attr = "customer_pack_return_$customer->id";
                    $packReturn = $packReturns->where('wa_inventory_item_id', $inventory->id)->first();
                    $packReturnAmount = $packReturn->$customer_pack_return_attr ?? 0;

                    $item['customer_' . $customer->id] = ($fullSaleAmount - $fullReturnAmount) + ($packSaleAmount - $packReturnAmount);
                }

                $items[] = $item;
            }

            return response()->json([
                'result' => 1,
                'data' => [
                    'items' =>  $items,
                    'customers' => $customers
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function salesman_performance_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->first();
            if (!$supplier) {
                throw new \Exception("Error Processing Request: Supplier not found.");
            }

            $from = $request->from . ' 00:00:00';
            $to = $request->to . ' 23:59:59';

            $fullSales = WaInternalRequisitionItem::query()
                ->select([
                    'u.id as user_id',
                    'u.phone_number',
                    'u.name',
                    'wirt.wa_inventory_item_id',
                    DB::raw("SUM(wirt.quantity) as quantity")
                ])
                ->from('wa_internal_requisition_items AS wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', '=', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'wirt.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'ii.id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->join('salesman_shifts as ss', function ($join) {
                    $join->on('ss.id', 'wir.wa_shift_id');
                })
                ->join('users as u', function ($join) {
                    $join->on('u.id', 'ss.salesman_id');
                })
                ->where('wir.restaurant_id', $request->branch)
                ->whereBetween('wir.created_at', [$from, $to])
                ->groupBy('wirt.wa_inventory_item_id', 'u.id')
                ->get();

            $packSales = WaInternalRequisitionItem::query()
                ->select([
                    'u.id as user_id',
                    'u.phone_number',
                    'items.wa_inventory_item_id',
                    DB::raw("ROUND(SUM(wirt.quantity) / conversion_factor,2) as quantity")
                ])
                ->from('wa_internal_requisition_items as wirt')
                ->join('wa_internal_requisitions as wir', 'wir.id', 'wirt.wa_internal_requisition_id')
                ->join('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wirt.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'wirt.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->join('salesman_shifts as ss', function ($join) {
                    $join->on('ss.id', 'wir.wa_shift_id');
                })
                ->join('users as u', function ($join) {
                    $join->on('u.id', 'ss.salesman_id');
                })
                ->where('wir.restaurant_id', $request->branch)
                ->whereBetween('wir.created_at', [$from, $to])
                ->groupBy('wirt.wa_inventory_item_id', 'u.id')
                ->get();

            $fullReturns = WaInventoryLocationTransferItemReturn::query()
                ->select([
                    'u.id as user_id',
                    'items.wa_inventory_item_id',
                    DB::raw("ROUND(SUM(returns.received_quantity),2) as quantity")
                ])
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_items as ii', function ($join) {
                    $join->on('ii.id', 'items.wa_inventory_item_id')->where('ii.status', 1);
                })
                ->join('pack_sizes as ps', function ($join) {
                    $join->on('ps.id', 'ii.pack_size_id')->where('ps.can_order', 1);
                })
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'items.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->join('salesman_shifts as ss', function ($join) {
                    $join->on('ss.id', 'wilt.shift_id');
                })
                ->join('users as u', function ($join) {
                    $join->on('u.id', 'ss.salesman_id');
                })
                ->where('wilt.restaurant_id', $request->branch)
                ->where([
                    'returns.status' => 'received',
                    'returns.return_status' => 1,
                ])
                ->whereBetween('wilt.created_at', [$from, $to])
                ->groupBy('items.wa_inventory_item_id', 'u.id')
                ->get();

            $packReturns = WaInventoryLocationTransferItemReturn::query()
                ->select([
                    'u.id as user_id',
                    'wait.wa_inventory_item_id',
                    DB::raw("ROUND(SUM(returns.received_quantity) / conversion_factor,2) as quantity")
                ])
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->join('wa_inventory_location_transfers as wilt', 'wilt.id', 'items.wa_inventory_location_transfer_id')
                ->join('wa_inventory_assigned_items as wait', 'wait.destination_item_id', '=', 'items.wa_inventory_item_id')
                ->join('wa_inventory_item_suppliers as iis', function ($join) use ($supplier) {
                    $join->on('iis.wa_inventory_item_id', '=', 'items.wa_inventory_item_id')
                        ->where('wa_supplier_id', $supplier->id);
                })
                ->join('salesman_shifts as ss', function ($join) {
                    $join->on('ss.id', 'wilt.shift_id');
                })
                ->join('users as u', function ($join) {
                    $join->on('u.id', 'ss.salesman_id');
                })
                ->where('wilt.restaurant_id', $request->branch)
                ->where([
                    'returns.status' => 'received',
                    'returns.return_status' => 1,
                ])
                ->whereBetween('wilt.created_at', [$from, $to])
                ->groupBy('items.wa_inventory_item_id', 'u.id')
                ->get();

            $inventories = WaInventoryItem::select([
                'id',
                'stock_id_code',
                'title',
            ])->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('wa_supplier_id', $supplier->id);
            })->whereHas('packSize', function ($query) {
                $query->where('can_order', 1);
            })->where('status', 1)
                ->get();

            $salesmen = $fullSales->map(function ($sale) {
                return (object)[
                    'user_id' => $sale->user_id,
                    'phone_number' => $sale->phone_number,
                    'name' => $sale->name,
                ];
            })->unique();

            foreach ($salesmen as $salesman) {
                $fullsalesItems = $fullSales->where('user_id', $salesman->user_id);
                $fullReturnItems = $fullReturns->where('user_id', $salesman->user_id);
                $packSalesItems = $packSales->where('user_id', $salesman->user_id);
                $packReturntems = $packReturns->where('user_id', $salesman->user_id);

                $items = [];

                foreach ($inventories as $inventory) {
                    $fullSaleAmount = $fullsalesItems->where('wa_inventory_item_id', $inventory->id)->first()->quantity ?? 0;
                    $fullReturnAmount = $fullReturnItems->where('wa_inventory_item_id', $inventory->id)->first()->quantity ?? 0;
                    $packAmount = $packSalesItems->where('wa_inventory_item_id', $inventory->id)->first()->quantity ?? 0;
                    $packReturnAmount = $packReturntems->where('wa_inventory_item_id', $inventory->id)->first()->quantity ?? 0;

                    $netQuantity = $fullSaleAmount - $fullReturnAmount + $packAmount - $packReturnAmount;

                    $items[] = (object) [
                        'stock_id_code' => $inventory->stock_id_code,
                        'title' => $inventory->title,
                        'quantity' => $netQuantity,
                    ];
                }

                $salesman->items = $items;
            }

            return response()->json([
                'result' => 1,
                'data' => [
                    'items' =>  $inventories,
                    'salesmen' => $salesmen
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function inventory_stock_report(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier)->first();
            if (!$supplier) {
                throw new \Exception("Error Processing Request");
            }
            $query = WaInventoryItem::select('wa_inventory_items.title', 'wa_inventory_items.id', 'wa_inventory_items.stock_id_code', 'wa_inventory_items.selling_price');

            $query->selectRaw(
                "(SELECT SUM(qauntity) 
                FROM wa_stock_moves 
                WHERE (wa_stock_moves.wa_inventory_item_id = child.id 
                    OR wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id) 
                    AND DATE(created_at) BETWEEN ? AND ? 
                ) AS qty_inhand",
                [$request->start_date, $request->end_date]
            );
            $query->selectRaw(
                "(SELECT SUM(
                    CASE 
                        WHEN wa_stock_moves.document_no LIKE 'INV%' AND qauntity < 0 THEN -qauntity 
                        WHEN wa_stock_moves.document_no LIKE 'RTN%' AND qauntity > 0 THEN qauntity 
                        ELSE 0 
                    END
                ) 
                FROM wa_stock_moves 
                WHERE (wa_stock_moves.wa_inventory_item_id = child.id OR wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id) 
                AND DATE(created_at) BETWEEN ? AND ? 
                AND (wa_stock_moves.document_no LIKE 'RTN%' OR wa_stock_moves.document_no LIKE 'INV%')
                ) AS qty_sold",
                [$request->start_date, $request->end_date]
            );

            // Join and Filter Conditions
            $items = $query->leftJoin('wa_inventory_assigned_items as asi', 'asi.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoin('wa_inventory_items as child', 'asi.destination_item_id', '=', 'child.id')
                ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13, 11])
                ->whereHas('inventory_item_suppliers', function ($query) use ($supplier) {
                    $query->where('wa_supplier_id', $supplier->id);
                })
                ->where('wa_inventory_items.status', 1)
                ->groupBy('wa_inventory_items.id')
                ->orderBy('child.id')
                ->get();
            $locations = WaLocationAndStore::where('is_physical_store', '1')->get();
            return response()->json([
                'result' => 1,
                'data' => $items,
                'message' => 'Ok',
                'locations' => $locations
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
                'data' => [],
                'locations' => []
            ]);
        }
    }

    public function approaching_restock_level(Request $request)
    {


        $supplier = WaSupplier::with('products')->where('supplier_code', $request->supplier_code)->first();

        /*get supplier items*/
        if (!$supplier) {
            throw new \Exception("Error Processing Request");
        }
        $items = DB::table('wa_inventory_items as ii')
            ->leftJoin('wa_inventory_item_suppliers as is', 'ii.id', '=', 'is.wa_inventory_item_id')
            ->leftJoin('wa_inventory_location_stock_status as sst', 'sst.wa_inventory_item_id', '=', 'ii.id')
            ->leftJoin('wa_location_and_stores as ss', 'sst.wa_location_and_stores_id', '=', 'ss.id')
            ->leftJoin(DB::raw('(SELECT stock_id_code, wa_location_and_store_id, MAX(created_at) as latest_move_date, new_qoh 
                FROM wa_stock_moves 
                GROUP BY stock_id_code, wa_location_and_store_id) as sm'), function ($join) {
                $join->on('sm.stock_id_code', '=', 'ii.stock_id_code')
                    ->on('sm.wa_location_and_store_id', '=', 'sst.wa_location_and_stores_id');
            })
            ->select(
                'ii.id',
                'ii.title',
                'ii.stock_id_code',
                'sst.re_order_level',
                DB::raw('IFNULL(sm.new_qoh, 0) as new_qoh'),
                'sst.wa_location_and_stores_id',
                'ss.location_name'
            )
            ->where('ii.status', 1)
            ->where('is.wa_supplier_id', $supplier->id)
            ->get();


        return response()->json([
            'result' => 1,
            'data' => $items,
            'message' => 'Ok',
        ]);
    }
}
