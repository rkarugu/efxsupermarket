<?php

namespace App\Exports\Finance;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class TrialBalanceReportExport implements FromCollection, WithHeadings, WithEvents, WithStyles, WithColumnFormatting, ShouldAutoSize
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
            [$this->info['company_name']], // 1
            [], // 2
            ['Trial Balance'], // 3
            [], // 4
            ['FROM:'.$this->info['start_date'],'TO:'.$this->info['end_date'],'BRANCH:'.$this->info['branch'],''],//5
            [], //6
            ['ACCOUNT CODE', 'ACCOUNT NAME','PERIOD DEBITS','PERIOD CREDITS'] //7
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
                $sheet->getStyle('A5')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('B5')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getStyle('C5')->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                // $sheet->mergeCells('A5:D5');
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                $sheet->mergeCells('A6:D6');
                

                // Left-align the merged cells
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $highestRow = $sheet->getHighestRow(); // e.g. 10
                $sheet->getStyle('B' . $highestRow)->getFont()->setBold(true);
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
            5    => ['font' => ['bold' => true]], 
            7    => ['font' => ['bold' => true]], 
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, 
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, 
        ];
    }

}
