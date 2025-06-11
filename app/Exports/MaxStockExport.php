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

class MaxStockExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithColumnFormatting
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
            'ITEM CODE',
            'ITEM NAME',
            'CATEGORY',
            'BIN',
            'MAX STOCK',
            'RE-ORDER VALUE',
            'QoH',
            'SALES QTY (7 Days)',
            'SALES QTY (30 Days)',
            'USERS',
            'SUPPLIERS',
        ];
    }

    public function map($record): array
    {
        return [
            $record->id,
            $record->stock_id_code,
            $record->title,
            $record->category->category_description,
            $record->bin_title,
            $record->max_stock,
            $record->re_order_level,
            $record->quantity,
            $record->sales_qty_7,
            $record->sales_qty_30,
            $record->suppliers,
            $record->users,
        ];
    }


    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }

    public function columnFormats(): array
    {
        return [
            'F' => "#,##0.00",
            'G' => "#,##0.00",
            'H' => "#,##0.00",
            'I' => "#,##0.00",
        ];
    }
}
