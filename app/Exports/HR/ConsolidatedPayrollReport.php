<?php

namespace App\Exports\HR;

use App\Models\Earning;
use App\Model\Restaurant;
use App\Models\Deduction;
use Illuminate\Support\Str;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsolidatedPayrollReport implements FromCollection, ShouldAutoSize, WithStyles
{
    public function __construct(protected PayrollMonth $payrollMonth){}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $branches = Restaurant::all();

        $earnings = Earning::all();
        $deductions = Deduction::all();
        
        $exportData = [
            [strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('F Y')) . ' CONSOLIDATED PAYROLL REPORT'],
            [''],
        ];

        $headings = [
            'DESCRIPTION'
        ];

        foreach ($branches as $branch) {
            $headings[] = $branch->name;
        };

        $exportData[] = $headings;

        $basicPayData = ['BASIC PAY'];

        foreach ($branches as $branch) {
            $sum = $this->payrollMonth->payrollMonthDetails->filter(function ($payrollDetail) use ($branch) {
                return $payrollDetail->employee->branch_id == $branch->id;
            })
                ->sum('basic_pay');

            $basicPayData[] = number_format($sum ?? '0', 2);
        }

        $exportData[] = $basicPayData;

        foreach ($earnings as $earning) {
            $earningData = [Str::upper($earning->name)];

            foreach ($branches as $branch) {
                $total = $this->payrollMonth->payrollMonthDetails->filter(function ($payrollMonthDetail) use ($branch) {
                    return $payrollMonthDetail->employee->branch_id == $branch->id;
                })
                    ->sum(function ($payrollMonthDetail) use ($earning) {
                        return $payrollMonthDetail->earnings->first(fn ($payrollEarning) => $payrollEarning->earning_id == $earning->id)?->amount ?? 0;
                    });
                
                $earningData[] = number_format($total ?? '0', 2);
            }

            $exportData[] = $earningData;
        }

        $payrollComponent = [
            'Gross Pay',
            'PAYE',
            'NSSF',
            'SHIF',
            'Housing Levy'
        ];

        foreach ($payrollComponent as $component) {
            $payrollData = [Str::upper($component)];
            
            foreach ($branches as $branch) {
                $sum = $this->payrollMonth->payrollMonthDetails->filter(function ($payrollDetail) use ($branch) {
                    return $payrollDetail->employee->branch_id == $branch->id;
                })
                    ->sum(Str::slug($component, '_'));
    
                $payrollData[] = number_format($sum ?? '0', 2);
            }

            $exportData[] = $payrollData;
        }

        foreach ($deductions as $deduction) {
            $deductionData = [Str::upper($deduction->name)];

            foreach ($branches as $branch) {
                $total = $this->payrollMonth->payrollMonthDetails->filter(function ($payrollMonthDetail) use ($branch) {
                    return $payrollMonthDetail->employee->branch_id == $branch->id;
                })
                    ->sum(function ($payrollMonthDetail) use ($deduction) {
                        return $payrollMonthDetail->deductions->first(fn ($payrollDeduction) => $payrollDeduction->deduction_id == $deduction->id)?->amount ?? 0;
                    });
                
                $deductionData[] = number_format($total ?? '0', 2);
            }

            $exportData[] = $deductionData;
        }

        $netPayData = ['NET PAY'];

        foreach ($branches as $branch) {
            $sum = $this->payrollMonth->payrollMonthDetails->filter(function ($payrollDetail) use ($branch) {
                return $payrollDetail->employee->branch_id == $branch->id;
            })
                ->sum('net_pay');

            $netPayData[] = number_format($sum ?? '0', 2);
        }

        $exportData[] = $netPayData;
        
        return collect($exportData);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(3)->getFont()->setBold(true);
    }
}
