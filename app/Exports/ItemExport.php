<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithLimit;

class ItemExport implements FromCollection, WithHeadings
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
            'Description',
            'Category',
            'Vortex Cost',
            'Standard Cost',
            'Vortex Price',
            'Selling Price',
            'VAT',
            'Gross weight',
            'Bin Location',
            'Store Location',
            'HS Code', 
        ];
    }
}
