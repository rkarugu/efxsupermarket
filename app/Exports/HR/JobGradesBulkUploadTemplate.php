<?php

namespace App\Exports\HR;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JobGradesBulkUploadTemplate extends BaseBulkUploadTemplate
{
    protected $headings = [
        'Job Level *',
        'Job Grade *',
        'Minimum Salary *',
        'Maximum Salary *',
        'Description',
    ];

    public function styles(Worksheet $sheet)
    {
        parent::styles($sheet);
        
        if ($this->type == 'template') {
            $sheet->getStyle('A5:A7')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('F')->getFont()->setColor(new Color('ff0000'));
        }

    }
}
