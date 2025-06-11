<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class InventoryExport implements FromCollection, WithHeadings
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
            'ID',
            'Stock Id Code',
            'Title',
            'Item Category',
            'Pack Size',
            'Vortex Cost',
            'Standard Cost',
            'Vortex Price',
            'Selling Price',
            'Quantity',
            'Tax Category',
            'Default Store',
            'Gross Weight',
            'Bin Location(UOM)',
        ];
    }
}
