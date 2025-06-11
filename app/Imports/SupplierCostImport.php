<?php

namespace App\Imports;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplierData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;

class SupplierCostImport implements ToCollection, WithHeadingRow, WithChunkReading
{

    private $inventoryItems;
    private $supplier;
    public function __construct($inventoryItems, $supplier){
        $this->inventoryItems = $inventoryItems;
        $this->supplier = $supplier;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        DB::transaction(function($collection){
            foreach ($collection as $row) {
                if($row['STANDARD COST'] > 0){
                    $inventory = $this->inventoryItems->where('stock_id_code',$row['ITEM CODE'])->first();
                    $item = WaInventoryItemSupplierData::where('wa_supplier_id', $this->supplier)->where('wa_inventory_item_id', $inventory->id)->first();
                    if($item){
                        $item->price = $row['STANDARD COST'];
                        $item->save();
                    }
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
