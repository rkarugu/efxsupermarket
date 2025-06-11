<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosCashSaleController extends Controller
{
    /**
     * Search for inventory items
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchInventory(Request $request)
    {
        try {
            $data = WaInventoryItem::select([
                'wa_inventory_items.*',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($request->store_location_id ?? 'wa_inventory_items.store_location_id') . ') as quantity'),
            ])
                ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
                ->where('status', 1)
                ->where(function ($q) use ($request) {
                    if ($request->search) {
                        $q->where('wa_inventory_items.title', 'LIKE', "%$request->search%");
                        $q->orWhere('stock_id_code', 'LIKE', "%$request->search%");
                    }
                })->where(function ($e) use ($request) {
                    if ($request->store_c) {
                        $e->where('store_c_deleted', 0);
                    }
                })->limit(30)->get();

            $view = '<table class="table table-bordered table-hover" id="stock_inventory" style="
            display: block;
            right: auto !important;
            position: absolute;
            min-width: 400px;
            left: 0 !important;
            max-height: 350px;
            margin-top: 4px!important;
            overflow: auto;
            padding: 0;
            background:#fff;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* Internet Explorer 10+ */
            ">';
            $view .= "<thead>";
            $view .= '<tr>';
            $view .= '<th style="width:20%">Code</th>';
            $view .= '<th style="width:70%">Description</th>';
            $view .= '<th style="width:10%">QOH</th>';
            $view .= '<th style="width:10%">PRICE</th>';
            $view .= '</tr>';
            $view .= '</thead>';
            $view .= "<tbody>";
            foreach ($data as $key => $value) {
                $qoh = WaStockMove::where('wa_inventory_item_id', $value->id)
                    ->where('wa_location_and_store_id', $request->store_location_id)
                    ->sum('qauntity');
                $view .= '<tr onclick="fetchInventoryDetails(this)" ' . ($key == 0 ? 'class="SelectedLi"' : NULL) . ' data-id="' . $value->id . '" data-title="' . $value->title . '(' . $value->stock_id_code . ')">';
                $view .= '<td style="width:20%">' . $value->stock_id_code . '</td>';
                $view .= '<td style="width:70%">' . $value->title . '</td>';
                $view .= '<td style="width:10%">' . ($qoh ?? 0) . '</td>';
                $view .= '<td style="width:10%">' . (number_format($value->selling_price, 2)) . '</td>';

                $view .= '</tr>';
            }
            $view .= '</tbody>';
            $view .= '</table>';
            
            return response()->json([
                'view' => $view,
                'results' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in searchInventory: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while searching inventory: ' . $e->getMessage()
            ], 500);
        }
    }
}
