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

class EarningsReport implements FromCollection, ShouldAutoSize, WithStyles
{
    public function __construct(protected PayrollMonth $payrollMonth, protected $earning, protected bool $customEarning){}
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $branches = Restaurant::all();
        
        $exportData = [
            [strtoupper(Carbon::parse($this->payrollMonth->start_date)->format('F Y') . ' ' . $this->earning) . ' REPORT'],
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

            $earningTotal = 0;
            foreach ($this->payrollMonth->payrollMonthDetails as $payrollMonthDetail) {
                if ($payrollMonthDetail->employee->branch_id == $branch->id) {
                    $amount = $this->customEarning ? $payrollMonthDetail[Str::slug($this->earning, '_')] : $payrollMonthDetail->earnings[0]->amount;
                    
                    $exportData[] = [
                        $payrollMonthDetail->employee->employee_no,
                        $payrollMonthDetail->employee->full_name,
                        $payrollMonthDetail->employee->id_no,
                        $payrollMonthDetail->employee->phone_no,
                        number_format($amount, 2),
                    ];

                    $earningTotal += $amount;
                    $grandTotal += $amount;
                }
            }

            $exportData[]= [''];   

            $exportData[] = [
                '',
                '',
                '',
                'TOTAL',
                number_format($earningTotal, 2)
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
