<?php

namespace App\Exports\HR;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BanksBulkUploadTemplate extends BaseBulkUploadTemplate
{
    protected $headings = [
        'Bank *',
        'Bank Code',
        'Branch *',
        'Branch Code',
        'Bank Ref'
    ];

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);

        if ($this->type == 'template') {
            $sheet->getStyle('A5:A7')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('F')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
