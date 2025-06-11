<?php

namespace App\Console\Commands\Utilities;

use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaLocationAndStore;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class CopyLocationStockStatus extends Command implements PromptsForMissingInput
{
    protected $signature = 'stock:copy-stock-status {store}';

    protected $description = 'Copy max stock and reorder level from Nampak to other store';

    public function handle()
    {
        $this->comment('Begin copying max stock and reorder level');

        $store = $this->argument('store');

        if (!WaLocationAndStore::find($store)) {
            return $this->error('The location does not exist');
        }

        $inventoryStatuses = WaInventoryLocationStockStatus::where('wa_location_and_stores_id', 46)->get();

        $bar = $this->output->createProgressBar(count($inventoryStatuses));

        $bar->start();

        foreach ($inventoryStatuses as $inventoryStatus) {
            WaInventoryLocationStockStatus::firstOrCreate([
                "wa_inventory_item_id" => $inventoryStatus->wa_inventory_item_id,
                "wa_location_and_stores_id" => $store,
            ], [
                "max_stock" => $inventoryStatus->wa_inventory_item_id,
                "re_order_level" => $inventoryStatus->wa_inventory_item_id,
            ]);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine();

        $this->comment('Copying location max stock and reorder level completed');
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'store' => 'Enter the store ID to copy to',
        ];
    }
}
