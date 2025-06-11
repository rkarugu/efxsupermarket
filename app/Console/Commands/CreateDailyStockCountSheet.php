<?php

namespace App\Console\Commands;

use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaStockCheckFreeze;
use App\Model\WaStockCheckFreezeItem;
use App\Model\WaStockCount;
use App\Model\WaUnitOfMeasure;
use App\Models\WaLocationStoreUom;
use App\Models\WaStockCountVariation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDailyStockCountSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-daily-stock-count-sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a daily stock count sheet ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $locationAndStores = WaLocationAndStore::all();
            DB::table('wa_stock_check_freeze_items')->delete();
            DB::table('wa_stock_check_freezes')->delete();
            DB::table('wa_stock_counts')->delete();

            foreach ($locationAndStores as $locationAndStore){
                $entity_stock_check = new WaStockCheckFreeze();
                $entity_stock_check->wa_location_and_store_id = $locationAndStore->id;
                $entity_stock_check->user_id = 1;
                $entity_stock_check->save();
                $items = WaInventoryItem::where('wa_inventory_location_uom.location_id', $locationAndStore->id)
                    ->where('status', 1)
                    ->leftJoin('wa_inventory_location_uom', function ($e) {
                        $e->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id');
                    })
                    ->select('wa_inventory_items.*', 'wa_inventory_location_uom.location_id','wa_inventory_location_uom.uom_id',)
                    ->get();
                foreach($items  as $item){
                    //return from store
                    $available_quantity = getItemAvailableQuantity($item->stock_id_code, $locationAndStore->id);
                    $returnToSupplier = DB::table('wa_store_return_items')
                    ->select('wa_store_return_items.quantity as quantity')
                    ->leftJoin('wa_store_returns', 'wa_store_returns.id', '=', 'wa_store_return_items.wa_store_return_id')
                    ->where('wa_store_returns.approved', 0)
                    ->where('wa_store_returns.rejected', 0)
                    ->where('wa_store_return_items.wa_inventory_item_id', $item->id)
                    ->where('wa_store_returns.location_id', $locationAndStore->id)
                    ->get();
                    if($returnToSupplier){
                        foreach($returnToSupplier as $storeReturn){
                            $available_quantity = $available_quantity - $storeReturn->quantity;
                        }
                    }
                    //return from GRN
                    $returnFromGRN = DB::table('returned_grns')
                        ->select('returned_grns.returned_quantity as returned_quantity')
                        ->leftJoin('wa_grns', 'wa_grns.id', '=', 'returned_grns.grn_id')
                        ->leftJoin('wa_purchase_orders', 'wa_purchase_orders.id', '=', 'wa_grns.wa_purchase_order_id')
                        ->where('wa_purchase_orders.wa_location_and_store_id', $locationAndStore->id)
                        ->where('returned_grns.item_code', $item->stock_id_code)
                        ->where('returned_grns.approved', 0)
                        ->where('returned_grns.rejected', 0)
                        ->get();
                    if($returnFromGRN){
                        foreach($returnFromGRN as $grnReturn){
                            $available_quantity = $available_quantity - $grnReturn->returned_quantity;
                        }
                    }
                    $entity = new WaStockCheckFreezeItem();
                    $entity->wa_stock_check_freeze_id = $entity_stock_check->id;
                    $entity->wa_inventory_item_id = $item->id;
                    $entity->wa_location_and_store_id = $locationAndStore->id;
                    $entity->item_category_id = $item->wa_inventory_category_id;
                    $entity->quantity_on_hand = $available_quantity;
                    $entity->wa_unit_of_measure = $item->uom_id;
                    $entity->save();

                }
            }
            Log::info('New stock count sheet created successfully. ');

            $this->info('New stock count sheet created successfully.');
        } catch (\Exception $e) {
            Log::error('Error executing CreateDailyStockCountSheet command: ' . $e->getMessage());
            $this->error('An error occurred while creating the daily stock count sheet.');
        }

        
    }
}
