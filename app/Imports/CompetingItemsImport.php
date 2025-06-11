<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompetingItemsImport implements ToCollection, WithHeadingRow
{
    public $items = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->items[] = [
                'id'            => $row['id'],
                'stock_id_code' => $row['stock_id_code'],
                'title'         => $row['title'],
            ];
        }
    }

    public function getItems()
    {
        return $this->items;
    }
}
