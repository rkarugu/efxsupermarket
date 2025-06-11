<?php


namespace App\Services;

use App\DeliverySchedule;
use App\Model\WaInternalRequisition;
use App\SalesmanShift;
use App\SalesmanShiftCustomer;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    static public function createDeliverySchedule($shiftId, $routeId): void
    {
        $customersWithOrders = SalesmanShiftCustomer::where('salesman_shift_id', $shiftId)->where('order_taken', true)->get();
        $schedule = DeliverySchedule::create([
            'shift_id' => $shiftId,
            'route_id' => $routeId,
            'expected_delivery_date' => Carbon::tomorrow(),
            'status' => 'consolidating'
        ]);

        foreach ($customersWithOrders as $customer) {
            $customerOrderIds = WaInternalRequisition::where('wa_shift_id', $shiftId)
                ->where('wa_route_customer_id', $customer->route_customer_id)
                ->pluck('id')
                ->toArray();

            $schedule->customers()->create([
                'customer_id' => $customer->route_customer_id,
                'delivery_code' => random_int(100000, 999999),
                'order_id' => implode(',', $customerOrderIds)
            ]);
        }

        $shiftItems = DB::table('wa_internal_requisition_items')
            ->join('wa_internal_requisitions', function (JoinClause $join) use ($shiftId) {
                $join->on('wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
                    ->where('wa_internal_requisitions.wa_shift_id', $shiftId);
            })
            ->leftJoin('wa_inventory_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->groupBy('wa_internal_requisition_items.wa_inventory_item_id')
            ->select('wa_inventory_items.id as item_id', DB::raw('SUM(`quantity`) as total_quantity'))
            ->get();

        foreach ($shiftItems as $shiftItem) {
            $schedule->items()->create([
                'wa_inventory_item_id' => $shiftItem->item_id,
                'total_quantity' => $shiftItem->total_quantity
            ]);
        }
    }
}