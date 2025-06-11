<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaCategory;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\WaItemSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class InventorySubCategoryController extends Controller
{
    public function getInventorySubCategories(Request $request): JsonResponse
    {
        try {
            $appUrl = env('APP_URL');
            $subCategories = WaItemSubCategory::select('id', 'title as name', 'image');

            if ($request->category_id) {
                $categorySubcategoryIds = DB::table('wa_inventory_category_sub_category_relation')->where('category_id', $request->category_id)
                    ->pluck('sub_category_id')->toArray();
                $subCategories = $subCategories->whereIn('id', $categorySubcategoryIds);
            }

            if ($request->search_query) {
                $matchingItems = DB::table('wa_inventory_items')->where('title', 'like', "%$request->search_query%")
                    ->pluck('item_sub_category_id')->toArray();
                $subCategories = $subCategories->where(function ($query) use ($matchingItems, $request) {
                    $query->where('title', 'LIKE', "%$request->search_query%")->orWhereIn('id', $matchingItems);
                });

//                $subCategories = $subCategories->where('title', 'LIKE', "%$request->search_query%")->orWhereIn('id', $matchingItems);
            }
            if ($request->origin && $request->origin == 'pos') {
                $subCategories = $subCategories->orderBy('name')->get();
            }else{
                $subCategories = $subCategories->orderBy('name')
                ->cursorPaginate(20)
                ->through(function (WaItemSubCategory $subCategory) use ($appUrl) {
                    return $subCategory;
                });
            }


            return $this->jsonify($subCategories, 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getInventorySubCategoryItems(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            $appUrl = env('APP_URL');
            $items = WaInventoryItem::with(['getstockmoves', 'categoryPrices' => function ($query) {
                $query->whereNotNull('price');
            }])  ->where('status', 1)
                ->select(
                '*',
                DB::raw("(SELECT `category_description` from `wa_inventory_categories` where `id` = `wa_inventory_items`.`wa_inventory_category_id`) as category_detail"),
            );

            if ($request->category_id) {
                $items = $items->where('wa_inventory_category_id', $request->category_id);
            }

            if ($request->sub_category_id) {
                $items = $items->where('item_sub_category_id', $request->sub_category_id);
            }

            if ($request->search_query) {
                $items = $items->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', "%$request->search_query%")
                        ->orWhere('description', 'LIKE', "%$request->search_query%")
                        ->orWhere('stock_id_code', 'LIKE', "%$request->search_query%");
                });
            }

            $orderType = 'ASC';
            if ($request->filter_id == 2) {
                $orderType = 'DESC';
            }

            $items = $items->orderByRaw("'CHARINDEX($request->search_query, title, 1) DESC, title ASC'")->orderBy('selling_price', $orderType)
                ->cursorPaginate(20)
                ->through(function (WaInventoryItem $item) use ($request, $appUrl, $user) {
                    $item->image = "$appUrl/uploads/inventory_items/" . $item->image;
                    $item->totalQty = $item->getstockmoves()->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity') ?? 0;
                    //check return to supplier
                    $returnToSupplier = DB::table('wa_store_return_items')
                        ->select('wa_store_return_items.quantity as quantity')
                        ->leftJoin('wa_store_returns', 'wa_store_returns.id', '=', 'wa_store_return_items.wa_store_return_id')
                        ->where('wa_store_returns.approved', 0)
                        ->where('wa_store_returns.rejected', 0)
                        ->where('wa_store_return_items.wa_inventory_item_id', $item->id)
                        ->where('wa_store_returns.location_id', $user->wa_location_and_store_id)
                        ->get();
                    if($returnToSupplier){
                        foreach($returnToSupplier as $storeReturn){
                            $item->totalQty = $item->totalQty - $storeReturn->quantity;
                        }
                    }
                    $item->has_price_categories = count($item->categoryPrices) > 0;

                    $item->price_categories = $item->categoryPrices->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'category_name' => (WaCategory::find($category->category_id))?->title,
                            'category_price' => format_amount_with_currency((float)$category->price),
                            'raw_category_price' => (float)$category->price,
                        ];
                    });

                    $item->display_quantity = "$item->totalQty available";
                    $item->mother_has_quantity = false;

                    if ($request->origin != 'pos') {
                        if ($itemAsChild = WaInventoryAssignedItems::where('destination_item_id', $item->id)->first()) {
                            $mother = WaInventoryItem::with('getstockmoves')->find($itemAsChild->wa_inventory_item_id);
                            $conversionFactor = (float)$itemAsChild->conversion_factor;
                            $motherQty = (int)$mother->getstockmoves()->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity') ?? 0;
                            //check return to supplier
                            $motherReturnToSupplier = DB::table('wa_store_return_items')
                                ->select('wa_store_return_items.quantity as quantity')
                                ->leftJoin('wa_store_returns', 'wa_store_returns.id', '=', 'wa_store_return_items.wa_store_return_id')
                                ->where('wa_store_returns.approved', 0)
                                ->where('wa_store_returns.rejected', 0)
                                ->where('wa_store_return_items.wa_inventory_item_id', $item->id)
                                ->where('wa_store_returns.location_id', $user->wa_location_and_store_id)
                                ->get();
                            if($motherReturnToSupplier){
                                foreach($motherReturnToSupplier as $storeReturn){
                                    $motherQty = $motherQty - $storeReturn->quantity;
                                }
                            }

                            if ($motherQty > 0) {
                                $item->mother_has_quantity = true;
                                $splittableQty = $motherQty * $conversionFactor;
                                $item->display_quantity = "$item->totalQty available ($splittableQty on split)";
                            }
                        }

                    }

                    unset($item->categoryPrices);
                    unset($item->getstockmoves);
                    return $item;
                });
                // ->sortByDesc('totalQty');

            return $this->jsonify($items, 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['status' => false, 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function removeEmptySubCategories(): JsonResponse
    {
        try {
            $idsToDestroy = [];
            $catIdsToDestroy = [];
            foreach (DB::table('wa_item_sub_categories')->get() as $subCategory) {
                $itemCount = DB::table('wa_inventory_items')->where('item_sub_category_id', $subCategory->id)->count();
                if ($itemCount == 0) {
                    $idsToDestroy[] = $subCategory->id;

                    $relation = DB::table('wa_inventory_category_sub_category_relation')->where('sub_category_id', $subCategory->id)->first();
                    WaInventoryCategory::find($relation?->category_id)?->delete();
                    DB::raw("DELETE FROM wa_inventory_category_sub_category_relation WHERE sub_category_id = $subCategory->id");
                }
            }

            foreach (DB::table('wa_inventory_categories')->get() as $category) {
                $itemCount = DB::table('wa_inventory_items')->where('wa_inventory_category_id', $category->id)->count();
                if ($itemCount == 0) {
                    $catIdsToDestroy[] = $category->id;
                }
            }

            WaInventoryCategory::destroy($catIdsToDestroy);
            WaItemSubCategory::destroy($idsToDestroy);
            return $this->jsonify(['message' => 'Sub Categories removed: ' . count($idsToDestroy), 'cats' => count($catIdsToDestroy)], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['status' => false, 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function getItemFilters(Request $request): JsonResponse
    {
        try {
            $filters = [
                [
                    'id' => 1,
                    'label' => 'Price:lowest to highest'
                ],
                [
                    'id' => 2,
                    'label' => 'Price:highest to lowest'
                ]
            ];

            return $this->jsonify(['data' => $filters], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getItemCodes(Request $request): JsonResponse
    {
        try {
            $items = DB::table('wa_inventory_items')->select('id', 'title as name')->get();
            return $this->jsonify($items, 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['status' => false, 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
}
