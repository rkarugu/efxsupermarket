<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SupplierBankListing implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        protected $data
    ) {}

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Supplier No', 
            'Supplier Name', 
            'Bank Name',
            'Bank Account No',
            'Bank Swift/Code',
            'Bank Branch',
            'KRA PIN',
            'Witholding Tax',
        ];
    }

    public function map($record): array
    {
        return [
            $record->supplier_code,
            $record->name,
            $record->bank_name,
            $record->bank_account_no,
            $record->bank_swift,
            $record->bank_branch,
            $record->kra_pin,
            $record->tax_withhold ? "Yes":"No",
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' =>  NumberFormat::FORMAT_TEXT
        ];
    }
}
