<?php

namespace App\Exports\HR;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class JobLevelsBulkUploadTemplate extends BaseBulkUploadTemplate
{
    protected $headings = [
        'Job Group *',
        'Job Level *',
        'Description',
    ];

    public function styles(Worksheet $sheet)
    {
        parent::styles($sheet);
        
        if ($this->type == 'template') {
            $sheet->getStyle('A5:A7')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('D')->getFont()->setColor(new Color('ff0000'));
        }

    }
}
