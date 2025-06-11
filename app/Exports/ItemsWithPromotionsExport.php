<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsWithPromotionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'START DATE',
            'END DATE',           
            'ITEM CODE',
            'TITLE',
            'PACK SIZE',
            'PRICE',
            'SALE QUANTITY',
            'PROMO ITEM CODE',
            'PROMO ITEM TITLE',
            'PACK SIZE',
            'PRICE',
            'PROMO QUANTITY',
            'CREATED BY',
           
          
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}



