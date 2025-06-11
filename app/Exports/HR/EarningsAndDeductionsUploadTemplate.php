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

class EarningsAndDeductionsUploadTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithStrictNullComparison, WithColumnWidths
{
    public function __construct(protected Collection $data, protected array $headings, protected string $type)
    {
        if ($type == 'error') {
            array_push($this->headings, 'Errors');
        }
    }
    
    public function collection(): Collection
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
            'A' => 15
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);

        if ($this->type == 'error') {
            $sheet->getStyle('D')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
