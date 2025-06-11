<?php

namespace App\Exports\HR;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JobGroupsBulkUploadTemplate extends BaseBulkUploadTemplate
{
    protected $headings = [
        'Job Group *',
        'Description',
    ];

    public function styles(Worksheet $sheet)
    {
        parent::styles($sheet);
        
        if ($this->type == 'template') {
            $sheet->getStyle('A5:A6')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('C')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
