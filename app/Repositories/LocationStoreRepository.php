<?php

namespace App\Repositories;


use App\Interfaces\LocationStoreInterface;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryCategory;
use App\Model\WaSupplier;
use Illuminate\Support\Facades\DB;
use PDF;

class LocationStoreRepository implements LocationStoreInterface
{
    public function getStockBalance()
    {
        $b = "";
        $select = [
            'wa_inventory_items.*',
        ];
        $having = [];
        $locations = WaLocationAndStore::where('is_physical_store', '1')
            ->where('location_name', '<>', 'THIKA')->get();
        foreach ($locations as $loc) {
            $select[] = DB::RAW('(select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id ' . $b . ' AND wa_stock_moves.wa_location_and_store_id = ' . $loc->id . ') as qty_inhand_' . $loc->id);
        }
        $type = request()->input('type');
        $items = WaInventoryItem::select(
            $select
        )
        ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
        ->where('wa_inventory_items.status','1')
        ->whereHas('inventory_item_suppliers', function ($e) {
            if (request()->filled('supplier')) {
                $e->where('wa_supplier_id', request()->supplier);
            }
        })
        // ->when(request()->filled('supplier'), function ($query,$supplier) {
        //     $query->whereHas('inventory_item_suppliers', function ($query) use ($supplier) {
        //         $query->where('wa_supplier_id', $supplier);
        //     });
        // })
        ->when(request()->filled('category'), function($query, $category){
            $query->where('wa_inventory_category_id', $category);
        })
        ->orderBy('wa_inventory_items.id');

        if (request()->filled('can_order')) {
            $items->where('pack_sizes.can_order', request()->can_order);
        }

        if (request()->has('print')) {
            return $items;
        }
        
        if (request()->filled('datatable')) {
            $columns = [
                'stock_id_code',
                'title',
                'uom',
                'standard_cost',
                'qauntity',
                'qty_on_order'
            ];

            $totalData = $items->count();
            $limit = request()->input('length');
            $start = request()->input('start');
            // $order = $columns[request()->input('order.0.column')];
            $dir = request()->input('order.0.dir');

            $data_query_count = $items;
            $totalFiltered = $data_query_count->count();

            $totals=[];
            $g_t_total = 0;
            $t_total = 0;
            $fixed_stock_value_balance=0;
            foreach ($locations as $loc){
                $totals[$loc->slug] = 0;
                $t_total = 0;
                $total = 0;
                foreach ($items->get() as $key => $row) {
                    $t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                    $g_t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                    $fixed_stock_value_balance += $row['qty_inhand_' . $loc->id] * $row->selling_price;
                }
                $totals[$loc->slug] = manageAmountFormat($t_total);
            }
            $totals['Total'] = manageAmountFormat($g_t_total);
            
            $items = $items//->offset($start)
                //->limit($limit)
                // ->orderBy($order, $dir)
                ->get();

            $data = array();
            $t_t_total = 0;
            foreach ($items as $key => $row) {
                $nestedData['id'] = $row->id;
                $nestedData['stock_id_code'] = $row->stock_id_code;
                $nestedData['title'] = $row->title;
                $t_total = 0;
                foreach ($locations as $loc){
                    $t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                    $t_t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                }
                $nestedData['t_total'] = manageAmountFormat($t_total);
                foreach ($locations as $loc){
                    $nestedData[$loc->slug] = manageAmountFormat($type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id]);
                }
                $data[] = $nestedData;
            }
            
            $totals['stock_id_code'] ='';
            $totals['title'] ='Grand Total:';
            $totals['t_total'] =manageAmountFormat($g_t_total);
            $totals['fixed_stock_value_balance'] =manageAmountFormat($fixed_stock_value_balance);
            
            return [
                'totalData'=>$totalData,
                'totalFiltered' => $totalFiltered,
                'data' => $data,
                'totals' => $totals,
            ];
        }

        return $items->paginate(50);
    }



    public function getStockBalanceAsAt()
    {
        $b = "";
        $select = [
            'wa_inventory_items.*',
        ];
        $having = [];
       $start_date = request()->start_date ?  request()->start_date.' 23:59:59' : date('Y-m-d 23:59:59');

        $locations = WaLocationAndStore::where('is_physical_store', '1')
            ->where('location_name', '<>', 'THIKA')->get();
        foreach ($locations as $loc) {          
        
            $select[] = DB::RAW('(SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id ' . $b . ' AND wa_stock_moves.wa_location_and_store_id = ' . $loc->id . ' AND wa_stock_moves.created_at <  \''.$start_date.'\') AS qty_inhand_' . $loc->id);
}
        $type = request()->input('type');
        $items = WaInventoryItem::select(
            $select
        )

        ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
        ->where('wa_inventory_items.status','1')
        ->whereHas('inventory_item_suppliers', function ($e) {
            if (request()->filled('supplier')) {
                $e->where('wa_supplier_id', request()->supplier);
            }
        })
        // ->when(request()->filled('supplier'), function ($query,$supplier) {
        //     $query->whereHas('inventory_item_suppliers', function ($query) use ($supplier) {
        //         $query->where('wa_supplier_id', $supplier);
        //     });
        // })
        ->when(request()->filled('category'), function($query, $category){
            $query->where('wa_inventory_category_id', $category);
        })
        ->orderBy('wa_inventory_items.id');
     

        if (request()->filled('can_order')) {
            $items->where('pack_sizes.can_order', request()->can_order);
        }


        if (request()->has('print')) {
            return $items;
        }
        
        if (request()->filled('datatable')) {
            $columns = [
                'stock_id_code',
                'title',
                'uom',
                'standard_cost',
                'qauntity',
                'qty_on_order'
            ];

            $totalData = $items->count();
            $limit = request()->input('length');
            $start = request()->input('start');
            $order = $columns[request()->input('order.0.column')];
            $dir = request()->input('order.0.dir');

            $data_query_count = $items;
            $totalFiltered = $data_query_count->count();
            $items = $items->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $data = array();
            $t_t_total = 0;
            foreach ($items as $key => $row) {
                $nestedData['stock_id_code'] = $row->stock_id_code;
                $nestedData['title'] = $row->title;
                $t_total = 0;
                foreach ($locations as $loc){
                    $t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                    $t_t_total += $type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id];
                }
                $nestedData['t_total'] = manageAmountFormat($t_total);
                foreach ($locations as $loc){
                    $nestedData[$loc->slug] = manageAmountFormat($type == 'values' ? $row['qty_inhand_' . $loc->id] * $row->selling_price : $row['qty_inhand_' . $loc->id]);
                }
                $data[] = $nestedData;
            }
            $footer=[];
            foreach ($locations as $loc){
                $l_total = 0;
                foreach ($items as $item){
                    $l_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                }
                $footer[$loc->slug]=manageAmountFormat($l_total);
            }
            $footer['stock_id_code'] ='';
            $footer['title'] ='Grand Total:';
            $footer['t_total'] =manageAmountFormat($t_t_total);
            $data[]=$footer;
            
            return [
                'totalData'=>$totalData,
                'totalFiltered' => $totalFiltered,
                'data' => $data,
                'start_date' => $start_date
            ];
        }

        return $items->paginate(50);
    }


}
