<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WaStockMovesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{

    public function __construct(protected Collection $data)
    {
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'DATE',
            'STOCK ID CODE',
            'TITLE',  
            'DOCUMENT NO.',         
            'REFERENCE',
            'QUANTITY',
            'NEW QOH',
            'STANDARD COST',
            'PRICE',
            'SELLING PRICE',
            'TOTAL COST',
            'USER ID',
            'TRANSACTION TYPE',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}


