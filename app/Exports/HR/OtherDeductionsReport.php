<?php

namespace App\Exports\HR;

use App\Models\Deduction;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OtherDeductionsReport implements FromCollection, ShouldAutoSize
{
    public function __construct(protected PayrollMonth $payrollMonth, protected $branch, protected Deduction $deduction)
    {
        // 
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $branchName = $this->branch?->name ? $this->branch?->name: 'ALL';

        $exportData = [
            ["BRANCH: $branchName - " . strtoupper($this->deduction->name) . " DEDUCTIONS"],
            [''],
            ['MONTH OF CONTRIBUTION: ' . Carbon::parse($this->payrollMonth->start_date)->format('Ym')],
            [''],
        ];

        $headings = [
            'PAYROLL NO.',
            'EMPLOYEE NAME',
            'PHONE NO.',
            'ID NO.',
            'KRA PIN',
            'CONTRIBUTION AMOUNT',
        ];

        $exportData[] = $headings;

        foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            $exportData[] = [
                $payrollMonthDetail->employee->payroll_no,
                $payrollMonthDetail->employee->full_name,
                $payrollMonthDetail->employee->phone_no,
                $payrollMonthDetail->employee->id_no,
                $payrollMonthDetail->employee->pin_no,
                $payrollMonthDetail->deductions->first(fn ($deduction) => $deduction->deduction_id == $this->deduction->id)?->amount ?? '0',
            ];
        }

        return collect($exportData);
    }
}
