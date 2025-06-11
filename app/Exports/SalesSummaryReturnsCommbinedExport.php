<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class SalesSummaryReturnsCommbinedExport implements WithMultipleSheets
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
            'Sales' => new SalesSummaryExport($this->salesData),
            'Returns' => new SalesSummaryReturnsExport($this->returnsData),
        ];
    }
}
