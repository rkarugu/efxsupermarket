<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierLedgerReport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'Transaction Date,',
            'Supplier No',
            'Supplier Name',
            'Transaction No',
            'Reference',
            'CU Invoice Number',
            'Description',
            'VAT',
            'Withholding VAT',
            'Total Amount',
        ];
    }

    public function map($row): array
    {
        return [
            $row->trans_date?->format('Y-m-d'),
            $row->supplier_no,
            $row->supplier->name,
            $row->document_no,
            $row->suppreference,
            $row->cu_invoice_number,
            $row->description,
            $row->vat_amount,
            $row->withholding_amount,
            $row->total_amount_inc_vat,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
