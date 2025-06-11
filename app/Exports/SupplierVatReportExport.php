<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierVatReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'SUPPLIER KRA PIN',
            'INVOICE NO.',
            'SUPPLIER ',
            'SUPPLIER INVOICE NO.',
            'SUPPLIER INVOICE DATE',
            'CU  INVOICE NO.',
            'VAT AMOUNT',
            'TOTAL AMOUNT'
          
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}

