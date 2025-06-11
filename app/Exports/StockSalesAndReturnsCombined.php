<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StockSalesAndReturnsCombined implements WithMultipleSheets
{
    protected $salesData;
    protected $returnsData;

    public function __construct($salesData, $returnsData)
    {
        $this->salesData = $salesData;
        $this->returnsData = $returnsData;
    }
    public function sheets(): array
    {
        return [
            'Sales' => new StockSalesExcel($this->salesData),
            'Returns' => new StockReturnsExcel($this->returnsData),
        ];
    }
}
