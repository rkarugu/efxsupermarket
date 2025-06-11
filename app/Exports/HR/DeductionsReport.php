<?php

namespace App\Exports\HR;

use App\Model\Restaurant;
use Illuminate\Support\Str;
use App\Models\PayrollMonth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeductionsReport implements FromCollection, ShouldAutoSize, WithStyles
{
    public function __construct(protected PayrollMonth $payrollMonth, protected $deduction, protected bool $customDeduction){}
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $branches = Restaurant::all();
        
        $exportData = [
            [strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('F Y') . ' ' . $this->deduction) . ' REPORT'],
            [''],
        ];

        $grandTotal = 0;
        foreach ($branches as $branch) {
            $exportData[] = ['BRANCH: '. $branch->name];
            
            $exportData[] = [
                'Employee No.',
                'Name',
                'ID No.',
                'Phone No.',
                'Amount',
            ];

            $deductionTotal = 0;
            foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
                if ($payrollMonthDetail->employee->branch_id == $branch->id) {
                    $amount = $this->customDeduction ? $payrollMonthDetail[Str::slug($this->deduction, '_')] : $payrollMonthDetail->deductions[0]->amount;
                    
                    $exportData[] = [
                        $payrollMonthDetail->employee->employee_no,
                        $payrollMonthDetail->employee->full_name,
                        $payrollMonthDetail->employee->id_no,
                        $payrollMonthDetail->employee->phone_no,
                        number_format($amount, 2),
                    ];

                    $deductionTotal += $amount;
                    $grandTotal += $amount;
                }
            }

            $exportData[]= [''];   

            $exportData[] = [
                '',
                '',
                '',
                'TOTAL',
                number_format($deductionTotal, 2)
            ];
            
            $exportData[]= [''];
            $exportData[]= [''];
            
        }

        $exportData[]= [''];
        $exportData[]= [''];
        $exportData[] = [
            '',
            '',
            '',
            'GRAND TOTAL',
            number_format($grandTotal, 2)
        ];
        
        return collect($exportData);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
