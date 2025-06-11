<?php

namespace App\Exports\Suppliers;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersSuppliersExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
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
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFont()->setSize(14);
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $supplierNameRows = $this->getSupplierNameRows($sheet);

        foreach ($supplierNameRows as $row) {
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getFont()->setSize(12);

            if ($row > 1) {
                $previousRow = $row - 1;
                $sheet->getStyle('A' . $previousRow . ':B' . $previousRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $previousRow . ':B' . $previousRow)->getFont()->setSize(14);
                $sheet->getStyle('A' . $previousRow . ':B' . $previousRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
    }

    public function getSupplierNameRows(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $supplierNameRows = [];

        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            if ($cellValue === 'Supplier Name') {
                $supplierNameRows[] = $row;
            }
        }

        return $supplierNameRows;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
