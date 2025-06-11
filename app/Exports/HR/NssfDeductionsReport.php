<?php

namespace App\Exports\HR;

use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NssfDeductionsReport implements FromCollection, ShouldAutoSize
{
    public function __construct(protected PayrollMonth $payrollMonth, protected $branch)
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
            ["BRANCH: $branchName - NSSF DEDUCTIONS"],
            [''],
            ['MONTH OF CONTRIBUTION: ' . Carbon::parse($this->payrollMonth->start_date)->format('Ym')],
            [''],
        ];

        $headings = [
            'PAYROLL NUMBER',
            'SURNAME',
            'OTHERNAMES',
            'ID NO',
            'KRA PIN',
            'NSSF NUMBER',
            'GROSS PAY',
            'VOLUNTARY',
        ];

        $exportData[] = $headings;

        foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            $exportData[] = [
                $payrollMonthDetail->employee->payroll_no,
                $payrollMonthDetail->employee->last_name,
                $payrollMonthDetail->employee->first_name . ' ' . $payrollMonthDetail->employee->middle_name,
                $payrollMonthDetail->employee->id_no,
                $payrollMonthDetail->employee->pin_no,
                $payrollMonthDetail->employee->nssf_no,
                $payrollMonthDetail->gross_pay,
                '0',
            ];
        }

        return collect($exportData);
    }
}
