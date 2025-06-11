<?php

namespace App\Imports;

use App\Model\WaInventoryItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryItemImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $item = WaInventoryItem::where('stock_id_code', $row['stock_id_code'])->first();

        // If the item exists, update the item_count
        if ($item) {
            $item->item_count = $row['uom'];
            $item->save();
        }

        // Return null because we are not creating new items
        return null;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
