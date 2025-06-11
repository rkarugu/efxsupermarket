<?php

namespace App\Jobs;

use App\DeliverySchedule;
use App\Model\WaInternalRequisition;
use App\SalesmanShift;
use App\SalesmanShiftCustomer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDeliverySchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public SalesmanShift $shift)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $customersWithOrders = SalesmanShiftCustomer::where('salesman_shift_id', $this->shift->id)
                ->where('order_taken', true)
                ->get();

            $schedule = DeliverySchedule::create([
                'shift_id' => $this->shift->id,
                'route_id' => $this->shift->route_id,
                'expected_delivery_date' => Carbon::tomorrow(),
                'status' => 'consolidating'
            ]);

            foreach ($customersWithOrders as $customer) {
                $customerOrderIds = WaInternalRequisition::where('wa_shift_id', $this->shift->id)
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
            ->join('wa_internal_requisitions', function (JoinClause $join) {
                $join->on('wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
                    ->where('wa_internal_requisitions.wa_shift_id', $this->shift->id);
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
        } catch (\Throwable $e) {

        }
    }
}