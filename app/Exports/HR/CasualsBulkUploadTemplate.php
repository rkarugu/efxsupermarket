<?php

namespace App\Exports\HR;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CasualsBulkUploadTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithStrictNullComparison, WithColumnWidths
{
    protected $headings = [
        'First Name *',
        'Middle Name',
        'Last Name *',
        'Date of Birth *',
        'ID No. *',
        'Phone No. *',
        'Email',
        'Gender [] *',
        'Nationality [] *',
        'Branch [] *',
        'Active ? *'
    ];
    
    public function __construct(protected Collection $data, protected string $type)
    {
        if ($type == 'error') {
            array_push($this->headings, 'Errors');
        }
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
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

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        
        if ($this->type == 'template') {
            $sheet->getStyle('A5:A10')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('L')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
