<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SalesSummaryReturnsExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $returnsData;

    public function __construct(Collection $returnsData)
    {
        $this->returnsData = $returnsData;
    }
    public function collection()
    {
        return $this->returnsData;

    }
       /**
     * Set the title of the sheet.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Returns Data';
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
            'RETURN',
            'INVOICE', 
            'VATABLE SALE', 
            'VAT', 
            'TOTAL', 
        ];
    }
}
