<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithLimit;

class PriceUploads implements FromCollection, WithHeadings
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
            'Item Id',
            'Stock ID',
            'Description',
            'Category',
            'Selling Price',
            'Tax',
            'Tax Manager ID',
            'HS Code', 
        ];
    }
}
