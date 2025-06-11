<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintainCustomersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'CUSTOMER CODE',
            'CUSTOMER NAME',
            'ROUTE',
            'TELEPHONE',
            'EQUITY TILL',
            'KCB TILL',
            'IS BLOCKED',
            'AMOUNT',
            
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
