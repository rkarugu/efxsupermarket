<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockReturnsExcel implements FromCollection,  WithTitle, WithHeadings, ShouldAutoSize
{
    protected $returnsData;

    public function __construct(Collection $returnsData)
    {
        $this->returnsData = $returnsData;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->returnsData;

    }
    public function title(): string
    {
        return 'Returns Data';
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
