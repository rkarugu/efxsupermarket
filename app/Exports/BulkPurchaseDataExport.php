<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BulkPurchaseDataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
            'ITEM CODE',
            'ITEM NAME',
            'STANDARD COST',
            
        ];
    }

    public function map($record): array
    {
        return [
            $record->stock_id_code,
            $record->title,
            ""
        ];
    }


    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
