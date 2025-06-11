<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceBalancingReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Date',
            'Route',
            'Invoice Amount',
            'Stocks Amount',
            'Debtors Amount'
        ];
    }

    public function map($record): array
    {
        return [
            $record->requisition_no,
            $record->created_at->format('d/m/y h:i:s'),
            $record->customer?->customer_name,
            $record->invoice_amount,
            $record->stocks_amount,
            $record->debtors_amount,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
