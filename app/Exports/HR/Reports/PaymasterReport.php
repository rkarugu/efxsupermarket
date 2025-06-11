<?php

namespace App\Exports\HR\Reports;

use App\Models\Earning;
use App\Models\Deduction;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymasterReport implements FromCollection, ShouldAutoSize, WithStyles
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
            [strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('F Y')) .   ' PAYMASTER REPORT'],
            [''],
            ["BRANCH: $branchName"],
            ['']
        ];

        $headings = [
            'Employee No.',
            'Employee Name',
            'ID No.',
            'Branch',
            'Basic Pay',
        ];

        foreach($earnings as $earning) {
            array_push($headings, $earning->name);
        }

        array_push($headings,
            'Gross Pay',
            'NSSF',
            'Taxable Pay',
            'SHIF',
            'Housing Levy'
        );

        foreach($deductions as $deduction) {
            array_push($headings, $deduction->name);
        }

        array_push($headings, 'Net Pay');

        $exportData[] = $headings;

        $totalBasicPay = 0;
        $totalGrossPay = 0;
        $totalNssf = 0;
        $totalTaxablePay = 0;
        $totalShif = 0;
        $totalHousingLevy = 0;
        $totalNetPay = 0;
        $earningsTotals = [];
        $deductionsTotals = [];
        foreach($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            $data = [
                $payrollMonthDetail->employee->employee_no,
                $payrollMonthDetail->employee->full_name,
                $payrollMonthDetail->employee->id_no,
                $payrollMonthDetail->employee->branch->name,
                $this->formatAmount($payrollMonthDetail->basic_pay),
            ];

            $totalBasicPay += $payrollMonthDetail->basic_pay;

            foreach($earnings as $i => $earning) {
                $earningAmount = $payrollMonthDetail->earnings->first(fn ($item) => $item->earning_id == $earning->id)?->amount ?? 0;
                
                array_push($data, $this->formatAmount($earningAmount));

                $earningsTotals[$i] = isset($earningsTotals[$i]) ? strval($earningsTotals[$i] + $earningAmount) : $earningAmount;
            }

            array_push($data,
                $this->formatAmount($payrollMonthDetail->gross_pay),
                $this->formatAmount($payrollMonthDetail->nssf),
                $this->formatAmount($payrollMonthDetail->taxable_pay),
                $this->formatAmount($payrollMonthDetail->shif),
                $this->formatAmount($payrollMonthDetail->housing_levy),
            );

            $totalGrossPay += $payrollMonthDetail->gross_pay;
            $totalNssf += $payrollMonthDetail->nssf;
            $totalTaxablePay += $payrollMonthDetail->taxable_pay;
            $totalShif += $payrollMonthDetail->shif;
            $totalHousingLevy += $payrollMonthDetail->housing_levy;

            foreach($deductions as $i => $deduction) {
                $deductionAmount = $payrollMonthDetail->deductions->first(fn ($item) => $item->deduction_id == $deduction->id)?->amount ?? 0;
                
                array_push($data, $this->formatAmount($deductionAmount));

                $deductionsTotals[$i] = isset($deductionsTotals[$i]) ? strval($deductionsTotals[$i] + $deductionAmount) : $deductionAmount;
            }

            array_push($data, $this->formatAmount($payrollMonthDetail->net_pay));

            $totalNetPay += $payrollMonthDetail->net_pay;

            $exportData[] = $data;
        }

        $exportData[] = [
            '',
            '',
            '',
            'TOTALS',
            $this->formatAmount($totalBasicPay),
            ...collect(array_values($earningsTotals))->map(fn ($total) => $this->formatAmount($total))->toArray(),
            $this->formatAmount($totalGrossPay),
            $this->formatAmount($totalNssf),
            $this->formatAmount($totalTaxablePay),
            $this->formatAmount($totalShif),
            $this->formatAmount($totalHousingLevy),
            ...collect(array_values($deductionsTotals))->map(fn ($total) => $this->formatAmount($total))->toArray(),
            $this->formatAmount($totalNetPay)
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
    }

    public function formatAmount($amount)
    {
        return number_format($amount, 2);
    }
}
