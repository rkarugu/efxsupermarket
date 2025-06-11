<?php

namespace App\Imports\HR;

use App\Models\Earning;
use App\Models\Deduction;
use Illuminate\Support\Str;
use App\Models\PayrollMonthDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EarningsAndDeductionsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $parameterName;
    
    public function __construct(protected $payrollMonthId, protected $parameter)
    {
        $this->parameterName = Str::slug($parameter->name, '_');
    }
    public function collection(Collection $rows)
    {
        foreach($rows as $row) {
            $payrollMonthDetail = PayrollMonthDetail::where('payroll_month_id', $this->payrollMonthId)
                ->whereHas('employee', fn ($employee) => $employee->where('payroll_no', $row['payroll_no']))
                ->first();

            if ($this->parameter instanceof Deduction) {
                $payrollMonthDetail->deductions()->updateOrCreate(
                    [
                        'deduction_id' => $this->parameter->id
                    ], 
                    [
                        'amount' => $row[$this->parameterName]
                    ]
                );

            } else if ($this->parameter instanceof Earning) {
                $payrollMonthDetail->earnings()->updateOrCreate(
                    [
                        'earning_id' => $this->parameter->id
                    ], 
                    [
                        'amount' => $row[$this->parameterName]
                    ]
                );
            }
        }
    }

    public function rules(): array
    {
        return [
            'payroll_no' => 'required|int|exists:employees,payroll_no',
            $this->parameterName => 'required|numeric'
        ];
    }
}
