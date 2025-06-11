<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsWithNoSuppliers implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
         
        return [
            'Stock ID',
            'Title',
            'Category',
            'Pack Size',
            'Standard Cost',
            'Selling Price',
            'Qoh',
            // 'VAT',
            // 'Gross weight',
            // 'Bin Location',
            // 'Store Location',
            // 'HS Code', 
        ];
    }
}
