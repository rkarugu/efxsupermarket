<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AverageSalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithColumnFormatting
{
    protected $data;
    protected $action;
    protected $intent;

    public function __construct(Collection $data, $action, $intent)
    {
        $this->data = $data;
        $this->action = $action;
        $this->intent = $intent;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->action == 'excel' || $this->intent == 'EXCEL') {
            return [
                'Item Code',
                'Item Name',
                'Category',
                'Bin',
                'Current Max Stock',
                'Current Re-Order Level',
                'Opening Stock',
                'Purchases',
                'Transfers In',
                'Transfers Out',
                'Sales',
                'Returns',
                'Pack Sales',
                'NET SALES',
                'STOCK AT HAND',
                'LPO Qty',
                'Over Stock',
                'Suggested Max Stock',
                'Suggested Reorder Level',
                'Users',
                'Suppliers',
            ];
        } else if ($this->action == 'download') {
            return [
                'ITEM ID',
                'ITEM CODE',
                'ITEM DESCRIPTION',
                'SUGGESTED MAX STOCK',
                'SUGGESTED REORDER LEVEL',
            ];
        } else {
            return [];
        }
    }

    public function map($record): array
    {
        if ($this->action == 'excel' || $this->intent == 'EXCEL') {
            return [
                $record->stock_id_code,
                $record->title,
                $record->category->category_description,
                $record->bin_title,
                $record->max_stock,
                $record->re_order_level,
                $record->opening_stock_count,
                $record->purchases_count,
                $record->transfers_in_count,
                $record->transfers_out_count,
                $record->excl_total_sales,
                $record->returns_count,
                $record->pack_sales,
                $record->total_sales,
                $record->qoh,
                $record->qty_on_order,
                $record->variance,
                $record->suggested_max_stock,
                $record->suggested_reorder,
                $record->users,
                $record->suppliers,
            ];
        } else if ($this->action == 'download') {
            return [
                $record->id,
                $record->stock_id_code,
                $record->description,
                $record->suggested_max,
                $record->suggested_reorder,
            ];
        } else {
            return [];
        }
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
        ];
    }
}
