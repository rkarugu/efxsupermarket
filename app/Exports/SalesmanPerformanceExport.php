<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesmanPerformanceExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $excelData;
    protected $from;
    protected $to;
    protected $totalExpectedRewards;
    protected $totalActualRewards;

    public function __construct($excelData, $from, $to, $totalExpectedRewards, $totalActualRewards)
    {
        $this->excelData = $excelData;
        $this->from = $from;
        $this->to = $to;
        $this->totalExpectedRewards = $totalExpectedRewards;
        $this->totalActualRewards = $totalActualRewards;
    }

    public function collection()
    {
        return collect($this->excelData);
    }

    public function headings(): array
    {
        return [
            'ROUTE', 'SALESMAN', 'SHIFT TONNAGE TARGET', 'EXPECTED SHIFTS', 'ACTUAL SHIFTS', 
            'EXPECTED TONNAGE', 'ACHIEVED TONNAGE', 'TONNAGE REWARD', 'CTN TONNAGE', 
            'DZN TONNAGE', 'BULK TONNAGE', 'CATEGORIZED TONNAGE REWARD', 'EXPECTED MET', 
            'ACTUAL MET', 'MET %', 'MET REWARD', 'FULLY ONSITE SHIFT', 'FULLY ONSITE REWARD', 
            'SHIFTS OPENED ON TIME', 'ONTIME REWARD', 'SHIFTS CLOSED PAST TIME', 
            'TIME MANAGEMENT REWARD', 'RETURNS', 'RETURNS REWARD', 'EXPECTED REWARD', 
            'EARNED REWARDS'
        ];
    }

    // public function styles(Worksheet $sheet)
    // {
    //     $highestRow = $sheet->getHighestRow();

    //     for ($row = 2; $row <= $highestRow; $row++) {
    //         $tonnageReward = $sheet->getCell("H$row")->getValue();
    //         $categoryTonnageReward = $sheet->getCell("L$row")->getValue();
    //         $metReward = $sheet->getCell("P$row")->getValue();
    //         $fullyOnsiteReward = $sheet->getCell("R$row")->getValue();
    //         $ontimeReward = $sheet->getCell("T$row")->getValue();
    //         $timeManagementReward = $sheet->getCell("V$row")->getValue();
    //         $returnsReward = $sheet->getCell("X$row")->getValue();
    //         if ($tonnageReward > 0) {
    //             $sheet->getStyle("H$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($categoryTonnageReward > 0) {
    //             $sheet->getStyle("L$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($metReward > 0) {
    //             $sheet->getStyle("P$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($fullyOnsiteReward > 0) {
    //             $sheet->getStyle("R$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($ontimeReward > 0) {
    //             $sheet->getStyle("T$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($timeManagementReward > 0) {
    //             $sheet->getStyle("V$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //         if ($returnsReward > 0) {
    //             $sheet->getStyle("X$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                   ->getStartColor()->setARGB('FF00FF00');
    //         }
    //     }
    // }
    public function styles(Worksheet $sheet)
    {
        return [
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:Z1')->setCellValue('A1', getAllSettings()['COMPANY_NAME']);
                $sheet->mergeCells('A2:Z2')->setCellValue('A2', 'SALESMAN PERFORMANCE REPORT');
                $sheet->mergeCells('A3:Z3');
                if ($this->from && $this->to) {
                    $sheet->setCellValue('A3', "PERIOD: {$this->from} - {$this->to}");
                }

                $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                foreach (range('A', 'Z') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                $highestRow = $sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $this->applyConditionalFormatting($sheet, $row, 'H'); // Tonnage Reward
                    $this->applyConditionalFormatting($sheet, $row, 'L'); // Categorized Tonnage Reward
                    $this->applyConditionalFormatting($sheet, $row, 'P'); // MET Reward
                    $this->applyConditionalFormatting($sheet, $row, 'R'); // Fully Onsite Reward
                    $this->applyConditionalFormatting($sheet, $row, 'T'); // Ontime Reward
                    $this->applyConditionalFormatting($sheet, $row, 'V'); // Time Management Reward
                    $this->applyConditionalFormatting($sheet, $row, 'X'); // Returns Reward
                }
            $highestRow = $sheet->getHighestRow();

            $totalsRow = $highestRow + 1;

            $sheet->mergeCells("A{$totalsRow}:X{$totalsRow}");

            $sheet->setCellValue("A{$totalsRow}", "Total");

            $sheet->getStyle("A{$totalsRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$totalsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue("Y{$totalsRow}", $this->totalExpectedRewards);
            $sheet->setCellValue("Z{$totalsRow}", $this->totalActualRewards);

            $sheet->getStyle("Y{$totalsRow}:Z{$totalsRow}")->getFont()->setBold(true);
            $sheet->getStyle("Y{$totalsRow}:Z{$totalsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        ];
    }

    private function applyConditionalFormatting($sheet, $row, $column)
    {
        $cell = "{$column}{$row}";
        $value = $sheet->getCell($cell)->getValue();
        if ($value > 0) {
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF00FF00'); 
        }
    }
}

