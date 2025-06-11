<?php

namespace App\Jobs;

use App\Model\WaLocationAndStore;
use App\SalesmanShift;
use App\SalesmanShiftStoreDispatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrepareStoreParkingList implements ShouldQueue
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
            $storeId = $this->shift->salesman->wa_location_and_store_id;
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
                $inventoryLocationUom = DB::table('wa_inventory_location_uom')->where('inventory_id', $shiftItem->item_id)->where('location_id', $storeId)->first();
                if ($inventoryLocationUom?->uom_id) {
                    $dispatch = SalesmanShiftStoreDispatch::latest()->with('items')->where('shift_id', $this->shift->id)
                        ->where('store_id', $storeId)
                        ->where('bin_location_id', $inventoryLocationUom->uom_id)
                        ->first();

                    if (!$dispatch) {
                        $dispatch = SalesmanShiftStoreDispatch::create([
                            'shift_id' => $this->shift->id,
                            'store_id' => $storeId,
                            'bin_location_id' => $inventoryLocationUom->uom_id,
                        ]);
                    }

                    $dispatch->items()->create([
                        'wa_inventory_item_id' => $shiftItem->item_id,
                        'total_quantity' => $shiftItem->total_quantity
                    ]);
                } else {
                    $dispatch = SalesmanShiftStoreDispatch::latest()->with('items')->where('shift_id', $this->shift->id)
                        ->where('store_id', $storeId)
                        ->where('bin_location_id', 15)
                        ->first();

                    if (!$dispatch) {
                        $dispatch = SalesmanShiftStoreDispatch::create([
                            'shift_id' => $this->shift->id,
                            'store_id' => $storeId,
                            'bin_location_id' => 15,
                        ]);
                    }

                    $dispatch->items()->create([
                        'wa_inventory_item_id' => $shiftItem->item_id,
                        'total_quantity' => $shiftItem->total_quantity
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::info("Loading Sheet failed: " . $e->getMessage());
            Log::error($e->getMessage(), $e->getTrace());
        }
    }
}
