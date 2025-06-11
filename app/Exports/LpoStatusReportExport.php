<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LpoStatusReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'PURCHASE NUMBER',
            'DATE CREATED',
            'BRANCH',
            'INITIATED BY',
            'SUPPLIER',
            'NOTE',
            'TOTAL LISTS',
            'STATUS',
            'GRN NUMBER',
            'TONNAGE',
            'LPO AMOUNT',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
