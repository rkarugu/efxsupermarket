<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryLocationStockReport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(
        protected $query,
        protected $locations
    ) {
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return array_merge([
            'Stock Id Code',
            'Title',
            'Total',
        ], $this->locations->pluck('location_name')->toArray());
    }

    public function map($record): array
    {
        $locationValues = [];
        $total = 0;

        foreach ($this->locations as $location) {
            $parameter = "qty_inhand_{$location->id}";
            $locationValues[] = $record->$parameter ?? '0';
            $total += $record->$parameter;
        }

        return array_merge([
            $record->stock_id_code,
            $record->title,
            $total
        ], $locationValues);
    }

    public function columnFormats(): array
    {
        $letter = 'C';
        $columnFormats['C'] = "#,##0.00";

        for($i=0; $i <= sizeof($this->locations); $i++){
            $columnFormats[$letter++] = "#,##0.00";
        }

        return $columnFormats;
    }
}
