<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuggestedOrderRequest;
use App\Models\SuggestedOrder;
use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use DB;
use App\Models\SuggestedOrderItem;

class SuggestedOrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuggestedOrderRequest $request)
    {
        $supplier = WaSupplier::where([
            'supplier_code' => $request->supplier_code,
            'email' => $request->supplier_email,
        ])->first();
        if (!$supplier) {
            return response()->json([
                'result' => -1,
                'message' => 'Supplier Not found',
                'error' => ['supplier_code' => [
                    'Supplier Not found'
                ]]
            ]);
        }
        $order = new SuggestedOrder();
        $order->wa_supplier_id  = $supplier->id;
        $order->order_number    = $request->order_number;
        $order->order_date      = $request->order_date;
        $order->status          = $request->status;
        $order->save();
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
        $inventories = WaInventoryItem::select([
            '*',
            DB::RAW("($qooQuery) as qty_on_order")
        ])->whereIn('stock_id_code',$request->code)->get();
        $items = [];
        foreach ($request->code as $key => $code) {
            $item = $inventories->where('stock_id_code',$code)->first();
            if($item){
                $items[] = [
                    'suggested_order_id'=>$order->id,
                    'wa_inventory_item_id'=>$item->id,
                    'quantity'=>$request->quantity[$key],
                    'qoo'=>$item->qty_on_order,
                    'max_stock'=>$item->max_stock,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ];
            }
        }
        SuggestedOrderItem::insert($items);
        return response()->json([
            'result' => 1,
            'message' => 'Order Stored Successfully']);
    }   
}
