<?php

namespace App\Exports\Finance;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
;
class DebtorTransactionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'Trans Date',
            'Document No',
            'Channel',
            'Branch',
            'Route',
            'Reference',
            'Verification',
            'Amount',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
