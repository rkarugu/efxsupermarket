<?php

namespace App\Imports;

use App\Model\WaCustomer;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaNumerSeriesCode;
use App\Model\WaStockBreaking;
use App\Model\WaStockBreakingItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GeneralExcelFileImport implements ToCollection, WithHeadingRow, WithChunkReading
{

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            $item = WaInventoryItem::find((int)$row['id']);
            $item->selling_price = (float)$row['new'];
            $item->save();
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function temp()
    {

    }
}
