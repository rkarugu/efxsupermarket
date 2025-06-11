<?php

namespace App\Exports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class DailySalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithStrictNullComparison
{

    public function __construct(protected Collection $data)
    {
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() : Collection
    {
        return $this->data;

    }
    public function headings(): array
    {
        return [
            'ROUTE',
            'SALESMAN',
            'TON',
            'TON_TARG',
            'TON_PER%',
            'AMOUNT',
            'AMOUNT_TARG',
            'AMOUNT_PER%',
            'CTN',
            'CTN_TARG',
            'CTN_PER%',
            'DZN',
            'DZN_TARG',
            'DZN_PER%',
            'CUST',
            'MET',
            'UNMET'
        ];
    }
    public function styles(Worksheet $sheet): void
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
