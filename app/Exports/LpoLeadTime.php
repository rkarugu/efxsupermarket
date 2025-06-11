<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LpoLeadTime implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'LPO DATE',
            'LPO NO.',
            'LPO USER',
            'GRN  NO.',
            'GRN DATE',
            'GRN  USER',
            'INVOICE NO.',
            'SUPPLIER',
            'SUPPLIER INVOICE  NUMBER',
            'SUPPLIER  INVOICE DATE',
            'CU INVOICE NO',
            'INVOICE USER',
            'LPO TOTAL',
            'GRN TOTAL',
            'INVOICE AMOUNT',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
