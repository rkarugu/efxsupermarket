<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OutOfStockReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected $data
    ) {
    }

    public function collection()
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
            'RE-ORDER LEVEL',
            'QoH',
            'QOO',
            'Qty to Order',
            'Sales Qty(7 Days)',
            'Sales Qty(30 Days)',
            'Sales Qty(30 - 180 Days)',
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
            $record->qty_on_hand,
            $record->qty_on_order,
            $record->qty_to_order > 0 ? $record->qty_to_order: 0,
            $record->sales_7_days,
            $record->sales_30_days,
            $record->sales_180_days,
            $record->suppliers,
        ];
    }
}
