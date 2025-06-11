<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesVatExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
 protected $customer;

    public function __construct(Collection $customer)
    {
        $this->customer = $customer;
    }

    public function collection()
    {
        return $this->customer;
    }

    public function headings(): array
    {
        return [
            'CUSTOMER PIN .',
            'CUSTOMER NAME .',           
            'DATE .',
            'CU INVOICE NUMBER .',
            'VAT AMOUNT',
            'TOTAL AMOUNT.',
            'TOTAL AMOUNT'
          
        ];
    }

     public function map($customer): array
    {
       
        return [
            $customer->customer_pin,
            $customer->customer_name,
            $customer->date,
            $customer->cu_invoice_number,
            $customer->tax_amount,
            $customer->amount_exclusive_of_vat,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}

