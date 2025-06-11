<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class SalesSummaryExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $salesData;

    public function __construct(Collection $salesData)
    {
        $this->salesData = $salesData;
    }
    public function collection()
    {
        return $this->salesData;

    }
     /**
     * Set the title of the sheet.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Sales Data';
    }
     /**
     * Set the headings for the sheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ROUTE', 
            'INVOICE', 
            'VATABLE SALE', 
            'VAT', 
            'TOTAL', 
        ];
    }
}
