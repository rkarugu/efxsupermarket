<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EsdDetailsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'ID',
            'INVOICE NUMBER',
            'CU SERIAL NUMBER',
            'CU INVOICE NUMBER',
            'VERIFY URL',
            'DESCRIPTION',
            'STATUS',
            'DATE',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
