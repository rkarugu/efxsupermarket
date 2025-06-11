<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockSalesExcel implements FromCollection,  WithTitle, WithHeadings, ShouldAutoSize
{
    protected $salesData;

    public function __construct(Collection $salesData)
    {
        $this->salesData = $salesData;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->salesData;

    }
    public function title(): string
    {
        return 'Stock Sales Data';
    }
    public function headings(): array
    {
        return [
            'DOCUMENT NO', 
            'VATABLE SALE', 
            'VAT', 
            'TOTAL', 
        ];
    }
}
