<?php

namespace App\Exports\HR;

use App\Models\Earning;
use App\Models\Deduction;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessedPayroll implements FromCollection, ShouldAutoSize, WithStyles
{
    public function __construct(protected PayrollMonth $payrollMonth, protected $branch){}
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $earnings = Earning::all();
        $deductions = Deduction::all();

        $branchName = $this->branch?->name ? $this->branch?->name: 'ALL';
        
        $exportData = [
            [strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('F Y')) . ' PAYROLL  SUMMARY'],
            [''],
            ["BRANCH: $branchName"],
            [''],
            ['EARNINGS'],
            [
                'Basic Pay',
                number_format($this->payrollMonth->payrollMonthDetails->sum('basic_pay'), 2)
            ]
        ];

        foreach($earnings as $earning) {
            $exportData[] = [
                $earning->name,
                number_format(
                    $this->payrollMonth->payrollMonthDetails->sum(function ($payrollMonthDetail) use ($earning) {
                        return $payrollMonthDetail->earnings->first(fn ($payrollEarning) => $payrollEarning->earning_id == $earning->id)?->amount;
                    })
                    , 2
                )
            ];
        }

        $exportData[] = [''];
        $exportData[] = ['DEDUCTIONS'];
        $exportData[] = [
            'Gross Pay',
            number_format($this->payrollMonth->payrollMonthDetails->sum('gross_pay'), 2)
        ];
        $exportData[] = [
            'NSSF',
            number_format($this->payrollMonth->payrollMonthDetails->sum('nssf'), 2)
        ];
        $exportData[] = [
            'Taxable Pay',
            number_format($this->payrollMonth->payrollMonthDetails->sum('taxable_pay'), 2)
        ];
        $exportData[] = [
            'SHIF',
            number_format($this->payrollMonth->payrollMonthDetails->sum('shif'), 2)
        ];
        $exportData[] = [
            'Housing Levy',
            number_format($this->payrollMonth->payrollMonthDetails->sum('housing_levy'), 2)
        ];

        foreach($deductions as $deduction) {
            $exportData[] = [
                $deduction->name,
                number_format(
                    $this->payrollMonth->payrollMonthDetails->sum(function ($payrollMonthDetail) use ($deduction) {
                        return $payrollMonthDetail->deductions->first(fn ($payrollDeduction) => $payrollDeduction->deduction_id == $deduction->id)?->amount;
                    })
                    , 2
                )
            ];
        }

        $exportData[] = [''];
        $exportData[] = [
            'NET PAY',
            number_format($this->payrollMonth->payrollMonthDetails->sum('net_pay'), 2)
        ];

        $exportData[] = [
            [''],
            [
                'Prepared By',
                '',
                '',
                'Signature',
                '',
                '',
                'Date',
            ],
            [''],
            [
                'Reviewed By',
                '',
                '',
                'Signature',
                '',
                '',
                'Date',
            ],
            [''],
            [
                'Authorized By',
                '',
                '',
                'Signature',
                '',
                '',
                'Date',
            ],
        ];
        
        return collect($exportData);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(3)->getFont()->setBold(true);
        $sheet->getStyle(5)->getFont()->setBold(true);
        $sheet->getStyle(12)->getFont()->setBold(true);
        $sheet->getStyle(23)->getFont()->setBold(true);
    }
}
