<?php

namespace App\Exports\HR;

use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayeDeductionsReport implements FromCollection, ShouldAutoSize
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
            ['P10A: ' . strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('M Y'))],
            [''],
        ];

        $headings = [
            'Pin No',
            'Employee Name',
            'Residential Status',
            'Type of Employee',
            'Basic',
            'House Allowance',
            'Transport Allowance',
            'Leave Pay',
            'Overtime Allowance',
            'Director\'s Fee',
            'Lumpsum Payment',
            'Other Income',
            'Total Cash Pay',
            'Car Benefit',
            'Other Non Cash Benefit',
            'Total non Cash Pay',
            'Global Income',
            'Type of Housing',
            'Rent of Home',
            'Computed Rent of Home',
            'Rent Recovered',
            'Net Value of Housing',
            'Total Gross',
            '30% of Cash Pay',
            'P/Actual Contribution',
            'P/Permissible Limit',
            'Mortgage Interest',
            'Deposit Home Saving',
            'Amount on benefit',
            'Taxable Pay',
            'Tax Payable',
            'Monthly Relief',
            'Insurance Relief',
            'Paye Tax',
            'Self Assessed Paye',
        ];

        $exportData[] = $headings;

        foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
            
            $exportData[] = [
                $payrollMonthDetail->employee->pin_no,
                $payrollMonthDetail->employee->full_name,
                'Resident',
                'Primary Employee',
                $payrollMonthDetail->basic_pay,
                $payrollMonthDetail->housing_levy,
                '',
                $payrollMonthDetail->earnings->first(fn ($earning) => $earning->earning->name == 'Leave Pay')?->amount ?? '',
                $payrollMonthDetail->earnings->filter(fn ($earning) => $earning->earning->name == 'Overtime 1' || $earning->earning->name == 'Overtime 2')->sum('amount') ?: '0',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Benefit not given',
                '',
                '',
                '',
                '',
                $payrollMonthDetail->gross_pay,
                '',
                $payrollMonthDetail->nssf,
                '',
                '',
                '',
                '',
                $payrollMonthDetail->taxable_pay,
                $payrollMonthDetail->paye + $payrollMonthDetail->tax_relief + $payrollMonthDetail->insurance_relief + $payrollMonthDetail->housing_levy_relief,
                $payrollMonthDetail->tax_relief,
                $payrollMonthDetail->insurance_relief ?? '0',
                $payrollMonthDetail->paye ?? '0',
                ''
            ];
        }

        return collect($exportData);
    }
}
