<?php

namespace App\Http\Controllers;

use App\ItemSupplierDemand;
use App\WaDemand;
use App\WaDemandItem;
use Illuminate\Http\Request;
use Carbon\Carbon;


class HandleExistingDemandsController extends Controller
{
    public function handleDemands()
    {
        try {
        $today = Carbon::now()->toDateString();
         
        $demands = ItemSupplierDemand::whereDate('created_at', '<',  $today)->get();

        $groupedDemands = $demands->groupBy(function ($demand) {
            return Carbon::parse($demand->created_at)->format('Y-m-d') . '-' . $demand->wa_supplier_id;
        });

        foreach ($groupedDemands as $key => $demands) {
            list($date, $supplierId) = explode('-', $key);

            $totalDemandAmount = $demands->sum(function ($demand) {
                return ($demand->demand_quantity * $demand->current_cost) - ($demand->demand_quantity * $demand->new_cost);
            });

            $demandCode = getCodeWithNumberSeries('DELTA');
            $demand = WaDemand::create([
                'wa_supplier_id' => $supplierId,
                'demand_no' => $demandCode,
                'demand_amount' => $totalDemandAmount,
                'created_by' => 1,
                'created_at' => Carbon::parse($date),
            ]);
            updateUniqueNumberSeries('DELTA',$demandCode);


            foreach ($demands as $itemSupplierDemand) {
                WaDemandItem::create([
                    'wa_demand_id' => $demand->id,
                    'wa_inventory_item_id' => $itemSupplierDemand->wa_inventory_item_id,
                    'current_cost' => $itemSupplierDemand->current_cost,
                    'new_cost' => $itemSupplierDemand->new_cost,
                    'current_price' => $itemSupplierDemand->current_price,
                    'demand_quantity' => $itemSupplierDemand->demand_quantity,
                    'created_at' => Carbon::parse($itemSupplierDemand->created_at),
                ]);
            }
        }

        return response()->json(['message' => 'Demands handled successfully']);
    } catch (\Throwable $e) {
        return response()->json(['status' => false,'message' => $e->getMessage(), 'data' => $e->getTrace()], 500);
    }
    }
    
}
