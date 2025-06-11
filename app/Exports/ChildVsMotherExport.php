<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ChildVsMotherExport implements FromCollection, WithHeadings
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
            'Mother  Item Code',
            'Title',
            'Pack Size',
            'Selling Price',
            'Qoh',
            'Child Item Code',
            'Title',
            'Pack Size',
            'Selling Price',
            'Qoh',
            'Conversion Factor',          
        ];
    }
}
