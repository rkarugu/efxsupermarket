<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Nssf;
use App\Models\Paye;
use App\Models\Shif;
use App\Models\Relief;
use App\Models\Earning;
use App\Models\Employee;
use App\Jobs\SendPayslip;
use App\Model\Restaurant;
use App\Models\Deduction;
use App\Models\HousingLevy;
use App\Models\PayrollMonth;
use Illuminate\Http\Request;
use App\Models\PayrollSetting;
use Illuminate\Support\Carbon;
use App\Models\PayrollMonthDetail;
use Illuminate\Support\Facades\DB;
use App\Exports\HR\ProcessedPayroll;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PayrollMonthController extends Controller
{
    protected $nssfTiers = null;
    protected $payeTiers = null;
    protected $housingLevy = null;
    protected $shif = null;
    protected $taxReliefAmount = null;
    protected $insuranceReliefRate = null;
    protected $housingLevyReliefRate = null;
    
    public function payrollMonthsList()
    {
        return response()->json(PayrollMonth::orderBy('start_date', 'desc')->get());
    }

    public function openPayrollMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|int',
        ]);

        $previousPayrollMonth = PayrollMonth::where('start_date', Carbon::parse("$request->month $request->year")->subMonth()->format('Y-m-d'))->first();

        if ($previousPayrollMonth && $previousPayrollMonth->status == 'open') {
            return response()->json(['message' => 'Previous payroll month is still open'], 400);
        }

        $currentPayrollMonth = PayrollMonth::where('start_date', Carbon::parse("$request->month $request->year")->format('Y-m-d'))->first();

        if ($currentPayrollMonth) {
            if ($currentPayrollMonth->status == 'open') {
                return response()->json(['message' => 'Payroll month is already open'], 400);
            } else {
                return response()->json(['message' => 'Payroll month is already closed, please open a new one.'], 400);
            }
        }

        $deductions = Deduction::all();

        DB::beginTransaction();
        try {
            $payrollMonth = PayrollMonth::create([
                'start_date' => Carbon::parse("$request->month $request->year"),
                'end_date' => Carbon::parse("$request->month $request->year")->endOfMonth(),
            ]);

            $employees = Employee::whereHas('employmentStatus', fn ($employmentStatus) => $employmentStatus->where('name', 'Active'))
                ->get();

            $this->getPayrollCalculationInformation();

            $houseAllowance = Earning::where('name', 'House Allowance')->first();
            $overtimeOne = Earning::where('name', 'Overtime 1')->first();
            $overtimeTwo = Earning::where('name', 'Overtime 2')->first();
            $monthlyHours = PayrollSetting::where('name', 'Monthly Hours')->first();
            $overtimeOneHours = PayrollSetting::where('name', 'Overtime 1 Hours')->first();
            $overtimeOTwoHours = PayrollSetting::where('name', 'Overtime 2 Hours')->first();
            
            foreach ($employees as $employee) {
                $basicPay = $employee->basic_pay;

                $payrollMonthDetail = $payrollMonth->payrollMonthDetails()->create([
                    'employee_id' => $employee->id,
                    'basic_pay' => $basicPay,
                ]);

                if (!$employee->inclusive_of_house_allowance) {
                    if ($houseAllowance) {
                        $payrollMonthDetail->earnings()->create([
                            'earning_id' => $houseAllowance->id,
                            'amount' => round($basicPay * $houseAllowance->rate/100),
                        ]);
                    }
                }

                if ($employee->eligible_for_overtime) {
                    if ($overtimeOne && $overtimeOneHours && $monthlyHours) {
                        $payrollMonthDetail->earnings()->create([
                            'earning_id' => $overtimeOne->id,
                            'amount' => round($overtimeOne->ratio * $overtimeOneHours->value * $basicPay/$monthlyHours->value),
                        ]);
                    }

                    if ($overtimeTwo && $overtimeOneHours && $monthlyHours) {
                        $payrollMonthDetail->earnings()->create([
                            'earning_id' => $overtimeTwo->id,
                            'amount' => round($overtimeTwo->ratio * $overtimeOTwoHours->value * $basicPay/$monthlyHours->value),
                        ]);
                    }
                }

                if ($previousPayrollMonth) {
                    // COPY PREVIOUS EARNINGS
                    // 

                    // COPY PREVIOUS DEDUCTIONS
                    $deductions->each(function ($deduction) use ($payrollMonthDetail, $previousPayrollMonth, $employee) {
                        $previousPayrollMonthDetail = $previousPayrollMonth->payrollMonthDetails->first(fn ($previousDetail) => $previousDetail->employee_id == $employee->id);

                        if ($previousPayrollMonthDetail) {
                            $previousDeduction = $previousPayrollMonthDetail->deductions->first(fn ($previousDeduction) => $previousDeduction->deduction_id == $deduction->id);

                            if ($previousDeduction) {
                                $payrollMonthDetail->deductions()->create([
                                    'deduction_id' => $deduction->id,
                                    'amount' => $previousDeduction->amount
                                ]);
                            }
                        }
                        
                    });
                }

                $this->calculatePayrollMonthDetails($payrollMonthDetail);
            }

            DB::commit();

            return response()->json(['message' => 'Payroll period opened successfully'], 201);
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function payrollMonth(Request $request, $id)
    {
        $branchId = $request->query('branch_id');
        $jobTitleId = $request->query('job_title_id');
        
        $payrollMonth = PayrollMonth::with([
            'payrollMonthDetails' => function ($query) use ($branchId, $jobTitleId) {
                $query->with('earnings', 'deductions', 'employee.branch')
                    ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)))
                    ->when($jobTitleId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('job_title_id', $jobTitleId)));
            }
        ])
            ->find($id);

        return response()->json($payrollMonth);
    }

    public function payrollMonthClose(Request $request, $id)
    {
        $payrollMonth = PayrollMonth::with(['payrollMonthDetails' => function ($payrollMonthDetail) {
            $payrollMonthDetail->with(
                'payrollMonth', 
                'employee.branch', 
                'employee.jobTitle', 
                'employee.paymentMode', 
                'employee.primaryBankAccount.bank', 
                'employee.primaryBankAccount.bankBranch', 
                'earnings.earning', 
                'deductions.deduction'
            );
        }])
            ->find($id);

        $reliefs = Relief::all();

        try {
            $payrollMonth->update([
                'status' => 'closed',
            ]);

            $payrollMonth->payrollMonthDetails->each(function($payrollMonthDetail) use ($reliefs) {
                SendPayslip::dispatch($payrollMonthDetail, $reliefs);
            });

            return response()->json(['message' => 'Payroll period closed successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function payrollMonthDetailEdit(Request $request, PayrollMonthDetail $payrollMonthDetail)
    {
        $request->validate([
            'basic_pay' => 'required|numeric',
            'earnings' => 'required|array',
            'deductions' => 'required|array',
            'earnings.*.id' => 'required|int|exists:earnings,id',
            'deductions.*.id' => 'required|int|exists:deductions,id'
        ]);

        DB::beginTransaction();
        try {

            $payrollMonthDetail->update(['basic_pay' => $request->basic_pay]);
            
            foreach ($request->earnings as $earning) {
                if ($earning['amount']) {
                    $payrollMonthDetail->earnings()->updateOrCreate([
                        'earning_id' => $earning['id']
                    ], [
                        'amount' => $earning['amount'],
                    ]);
                } else {
                    $payrollMonthDetail->earnings()->where('earning_id', $earning['id'])->delete();
                }
            }

            foreach ($request->deductions as $deduction) {
                if ($deduction['amount']) {
                    $payrollMonthDetail->deductions()->updateOrCreate([
                        'deduction_id' => $deduction['id']
                    ], [
                        'amount' => $deduction['amount'],
                    ]);
                } else {
                    $payrollMonthDetail->deductions()->where('deduction_id', $deduction['id'])->delete();
                }
            }

            $this->getPayrollCalculationInformation();

            $this->calculatePayrollMonthDetails($payrollMonthDetail);
            
            DB::commit();

            return response()->json(['message' => 'Payroll month detail updated successfully']);
            
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function processPayroll(Request $request, PayrollMonth $payrollMonth)
    {
        $branchId = $request->query('branch_id');

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth->load([
            'payrollMonthDetails' => function($query) use ($branchId) {
                $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                    ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
            }
        ]);

        $this->getPayrollCalculationInformation();
        
        $payrollMonth->payrollMonthDetails->each(fn ($payrollMonthDetail) => $this->calculatePayrollMonthDetails($payrollMonthDetail));

        $file = Excel::raw(new ProcessedPayroll($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file, 201);
    }

    public function calculateNssf($grossPay, $nssfTiers)
    {
        $nssf = 0;
        foreach ($nssfTiers as $nssfTier) {
            $tierRange = ($nssfTier->to - $nssfTier->from) + 1;
            if ($grossPay > $tierRange) {
                $nssf += $tierRange * $nssfTier->rate/100;
                
                $grossPay -= $tierRange;
            } else {
                $nssf += $grossPay * $nssfTier->rate/100;

                break;
            }
        }

        return round($nssf, 2);
    }

    public function calculatePaye($taxablePay, $payeTiers, $reliefs)
    {
        $paye = 0;
        foreach ($payeTiers as $i => $payeTier) {
            $tierRange = ($payeTier->to - $payeTier->from) + 1;
            
            if ($taxablePay > $tierRange) {
                $paye += $tierRange * $payeTier->rate/100;

                $taxablePay -= $tierRange;

            } else {
                $paye += $taxablePay * $payeTier->rate/100;

                break;
            }
        }

        $paye = round($paye - array_sum($reliefs), 2);

        return $paye > 0 ? $paye : 0;
    }

    public function calculatePayrollMonthDetails(PayrollMonthDetail $payrollMonthDetail)
    {
        try {            
            $payrollMonthDetail->load('earnings', 'deductions');

            $grossPay = $payrollMonthDetail->basic_pay + $payrollMonthDetail->earnings->sum('amount');

            $nssf = $this->calculateNssf($grossPay, $this->nssfTiers);
            $taxablePay = $grossPay - $nssf;
            $housingLevyAmount = round($grossPay * $this->housingLevy->rate/100);
            $shifAmount = round($grossPay * $this->shif->rate/100);
            $insuranceReliefAmount = round($this->insuranceReliefRate/100 * $shifAmount, 2);
            $housingLevyReliefAmount = round($this->housingLevyReliefRate/100 * $housingLevyAmount);

            $paye = $this->calculatePaye($taxablePay, $this->payeTiers, [$this->taxReliefAmount, $housingLevyReliefAmount, $insuranceReliefAmount]);
            $netPay = $grossPay - ($paye + $nssf + $shifAmount + $housingLevyAmount + $payrollMonthDetail->deductions->sum('amount'));

            $payrollMonthDetail->update([
                'gross_pay' => $grossPay,
                'taxable_pay' => $taxablePay,
                'paye' => $paye,
                'tax_relief' => $this->taxReliefAmount,
                'insurance_relief' => $insuranceReliefAmount,
                'housing_levy_relief' => $housingLevyReliefAmount,
                'nssf' => $nssf,
                'shif' => $shifAmount,
                'housing_levy' => $housingLevyAmount,
                'net_pay' => $netPay,
            ]);

        } catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function getPayrollCalculationInformation()
    {
        $this->nssfTiers = Nssf::orderBy('from')->get();
        $this->payeTiers = Paye::orderBy('from')->get();
        $this->housingLevy = HousingLevy::first();
        $this->shif = Shif::first();

        $this->taxReliefAmount = Relief::where('name', 'Tax Relief')->first()?->amount ?? 0;
        $this->insuranceReliefRate = Relief::where('name', 'Insurance Relief')->first()?->rate ?? 0;
        $this->housingLevyReliefRate = Relief::where('name', 'Housing Levy Relief')->first()?->rate ?? 0;
    }

}
