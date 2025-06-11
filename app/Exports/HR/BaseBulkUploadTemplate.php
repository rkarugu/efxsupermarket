<?php

namespace App\Exports\HR;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseBulkUploadTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $headings = [];
    
    public function __construct(protected Collection $data, protected string $type)
    {
        if ($type == 'error') {
            array_push($this->headings, 'Errors');
        }
    }
    
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
