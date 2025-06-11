<?php

namespace App\Console\Commands;

use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaStockCheckFreezeItem;
use App\Model\WaStockCount;
use App\Models\WaStockCountVariation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PopulateNceStockCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-nce-stock-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $locationAndStores = WaLocationAndStore::all();
            foreach ($locationAndStores as $store) {
                $counts = WaStockCount::where('wa_location_and_store_id',$store->id)->pluck('wa_inventory_item_id')->toArray();
                $dd = array_unique($counts);
                $freezeItems = WaStockCheckFreezeItem::where('wa_stock_check_freeze_items.wa_location_and_store_id', $store->id)
                ->whereNotIn('wa_stock_check_freeze_items.wa_inventory_item_id',$dd)
                ->get();
                foreach($freezeItems as $freezeItem){
                    // $inventoryItem = WaInventoryItem::find($freezeItem->wa_inventory_item_id);
                    $uom = WaInventoryLocationUom::latest()
                        ->where('inventory_id', $freezeItem->wa_inventory_item_id)
                        ->where('location_id', $store->id)->first();
                    // $entity = WaStockCount::firstOrNew(
                    //     [
                    //         'wa_location_and_store_id' => $store->id,
                    //         'wa_inventory_item_id' => $freezeItem->wa_inventory_item_id,
                    //     ]
                    // );
                    // $entity->user_id = 1;
                    // $entity->item_name = $inventoryItem->title;
                    // $entity->category_id = $inventoryItem->wa_inventory_category_id;
                    // $entity->uom = $uom->uom_id;
                    // $entity->reference = 'system generated';
                    // $entity->save();

                    $variance = new WaStockCountVariation();
                    $variance->user_id = 1; //rep system
                    $variance->wa_location_and_store_id = $store->id;
                    $variance->wa_inventory_item_id = $freezeItem->wa_inventory_item_id;
                    $variance->category_id = $freezeItem->item_category_id;
                    $variance->current_qoh = $freezeItem->quantity_on_hand;
                    $variance->variation = null;
                    $variance->uom_id =  $uom->uom_id;
                    $variance->reference = 'system generated';
                    $variance->save();
                }
            }
            Log::info('Stock Count NCE created successfully. ');

            $this->info('Stock Counts NCE sheet created successfully.');
        } catch (\Throwable $e) {
            Log::error('Error executing CreateDailyStockCountSheet command: ' . $e->getMessage());
            $this->error('An error occurred while creating the daily stock count NCE.');
        }
        
    }
}
