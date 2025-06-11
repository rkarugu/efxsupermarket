<?php

namespace App\Exports\Finance;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TrialBalanceAccountGroupedTransactionExport implements FromCollection, WithHeadings,WithEvents, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $data;
    protected $info;

    public function __construct($data,$info)
    {
        $this->data = $data;
        $this->info = $info;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            [$this->info['company_name']], //1
            [], // 2
            ['Trial Balance :: Account Data, Grouped By Transaction'], // 3
            [], // 4
            ['Name: '.$this->info['name'].'                 '], // 5
            ['Code: '.$this->info['code'],'','Group: '.$this->info['group'],''], // 6
            ['Section: '.$this->info['section'],'','Sub-Section: '.$this->info['sub_section'],''], // 7
            [], //8
            ['FROM:'.$this->info['start_date'],'TO:'.$this->info['end_date'],'BRANCH:'.$this->info['branch'],'TRANSACTION TYPE:'.$this->info['transaction_type']],//9
            [], //10
            ['Transaction No.', 'Date', 'Transaction Type', 'Amount',] //11
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->mergeCells('A2:D2');
                $sheet->getStyle('A2:D2')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->mergeCells('A3:D3');
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A4:D4');
                $sheet->getStyle('A4:D4')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A5:D5');
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A6:B6');
                $sheet->mergeCells('C6:D6');
                $sheet->getStyle('A6:D6')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('B6')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A7:B7');
                $sheet->mergeCells('C7:D7');
                $sheet->getStyle('A7:D7')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('B7')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A8:D8');
                $sheet->getStyle('A8:D8')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A9:D9')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A9')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('B9')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('C9')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('A10:D10');

                // Left-align the merged cells
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Make the last cell in column 'D' bold
                $highestRow = $sheet->getHighestRow(); // e.g. 10
                $sheet->getStyle('C' . $highestRow)->getFont()->setBold(true);
                $sheet->getStyle('D' . $highestRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]], 
            3    => ['font' => ['bold' => true, 'size' => 12]], 
            4    => ['font' => ['bold' => true, 'size' => 9]],
            5    => ['font' => ['bold' => true, 'size' => 9]],  
            6    => ['font' => ['bold' => true, 'size' => 9]],  
            7    => ['font' => ['bold' => true, 'size' => 9]],
            9    => ['font' => ['bold' => true, 'size' => 9]],
            11    => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, 
        ];
    }
}
