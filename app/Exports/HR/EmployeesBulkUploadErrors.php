<?php

namespace App\Exports\HR;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EmployeesBulkUploadErrors extends EmployeesBulkUpload implements WithColumnFormatting
{
    public function __construct(Collection $data, string $type)
    {
        parent::__construct($data, $type);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'N' => 20,
            'AK' => 20
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        parent::styles($sheet);
        
        $sheet->getStyle('AP')->getFont()->setColor(new Color('ff0000'));
    }
    
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'X' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Y' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
