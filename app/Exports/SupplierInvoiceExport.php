<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;


class SupplierInvoiceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{

    public function __construct(
        protected Collection $data
    ){}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'GRN NO.',
            'DATE RECEIVED',           
            'ORDER NO.',
            'RECEIVED BY',
            'SUPPLIER',
            'STORE LOCATION',
            'SUPPLIER INVOICE NO.',
            'CU_INVOICE_NO.',
            'VAT',
            'TOTAL AMOUNT',          
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(20); 
            },
        ];
    }
}

