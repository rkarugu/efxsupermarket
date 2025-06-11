<?php

namespace App\Imports;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PriceUploads implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            $item = WaInventoryItem::find((int)$row['item_id']);
            $item->wa_inventory_category_id = (int)$row['category'];
            $item->selling_price = (double)$row['selling_price'];
            $item->tax_manager_id = (int)$row['tax_manager_id'];
            $item->hs_code = (int)$row['hs_code'];
            $item->save();

            
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
