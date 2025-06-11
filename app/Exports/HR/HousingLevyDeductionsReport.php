<?php

namespace App\Exports\HR;

use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HousingLevyDeductionsReport implements FromCollection, ShouldAutoSize
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
            ["BRANCH: $branchName - HOUSING LEVY DEDUCTIONS"],
            [''],
            ['MONTH OF CONTRIBUTION: ' . Carbon::parse($this->payrollMonth->start_date)->format('Ym')],
            [''],
        ];

        $headings = [
            'ID NUMBER',
            'NAME',
            'KRA PIN',
            'GROSS PAY',
            'BASIC',
        ];

        $exportData[] = $headings;

        foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            $exportData[] = [
                $payrollMonthDetail->employee->id_no,
                $payrollMonthDetail->employee->full_name,
                $payrollMonthDetail->employee->pin_no,
                $payrollMonthDetail->gross_pay,
                $payrollMonthDetail->basic_pay,
            ];
        }

        return collect($exportData);
    }
}
