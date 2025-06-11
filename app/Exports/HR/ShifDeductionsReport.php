<?php

namespace App\Exports\HR;

use App\Model\Restaurant;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShifDeductionsReport implements FromCollection, ShouldAutoSize
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
            ["BRANCH: $branchName - SHIF DEDUCTIONS"],
            [''],
            ['MONTH OF CONTRIBUTION: ' . Carbon::parse($this->payrollMonth->start_date)->format('Ym')],
            [''],
        ];

        $headings = [
            'PAYROLL NO',
            'FIRSTNAME',
            'LASTNAME',
            'ID NUMBER',
            'KRA NUMBER',
            'SHIF NO',
            'CONTRIBUTION AMOUNT',
            'PHONE NO',
        ];

        $exportData[] = $headings;

        foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            $exportData[] = [
                $payrollMonthDetail->employee->payroll_no,
                $payrollMonthDetail->employee->first_name,
                $payrollMonthDetail->employee->last_name ?? $payrollMonthDetail->employee->middle_name,
                $payrollMonthDetail->employee->id_no,
                $payrollMonthDetail->employee->pin_no,
                $payrollMonthDetail->employee->nhif_no,
                $payrollMonthDetail->shif,
                $payrollMonthDetail->employee->phone_no,
            ];
        }

        return collect($exportData);
    }
}
