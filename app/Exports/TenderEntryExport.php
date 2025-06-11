<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenderEntryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {

        return [
            "Date",
            "Channel",
            "Customer No",
            "Customer Name",
            "User",
            "Reference",
            "Additional Info",
            "Paid By",
            "Amount",
        ];
    }

    public function map($record) : array
    {
        return [
            $record->trans_date->format('Y-m-d H:i:s'),
            $record->customer->customer_code,
            $record->customer->customer_name,
            $record->channel,
            $record->cashier->name,
            $record->reference,
            $record->additional_info,
            $record->paid_by,
            $record->amount,
        ];
    }
}
