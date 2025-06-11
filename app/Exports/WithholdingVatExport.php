<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class WithholdingVatExport extends StringValueBinder implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Supplier',
            'PIN No',
            'Invoice No',
            'Invoice Date',
            'Excl',
            'Tax',
            'Pay Date',
            'Witholding Amount',
            'Voucher No',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->kra_pin,
            '\'' . $row->cu_invoice_number,
            (new Carbon($row->trans_date))->format('d/m/y'),
            round($row->vat_amount / 0.16, 2) - round($row->notes->sum('tax_amount')  / 0.16, 2),
            $row->vat_amount,
            (new Carbon($row->created_at))->format('d/m/y'),
            $row->withholding_amount - $row->notes->sum('withholding_amount'),
            $row->number,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' =>  NumberFormat::FORMAT_TEXT
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
