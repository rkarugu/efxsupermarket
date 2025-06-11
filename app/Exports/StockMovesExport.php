<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'USER NAME',           
            'STORE LOCATION',
            'QUANTITY',
            'QTY IN',
            'QTY OUT',
            'NEW QOH',
            'SELLING PRICE  ',
            'REFRENCE',
            'DOCUMENT NO.',
            'TYPE',
          
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}

