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

class NationalitiesBulkUploadTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $headings = [
        'Nationality'
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

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);

        if ($this->type == 'error') {
            $sheet->getStyle('B')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
