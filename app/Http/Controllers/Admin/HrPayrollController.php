<?php

namespace App\Http\Controllers\Admin;

use App\Models\Relief;
use App\Models\Earning;
use App\Model\Restaurant;
use App\Models\Deduction;
use App\Models\PayrollMonth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PayrollMonthDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HR\Reports\PaymasterReport;
use App\Models\JobTitle;

class HrPayrollController extends Controller
{
    public function __construct(protected $title = 'Payroll', protected $model = 'hr-and-payroll-payroll',)
    {
        // 
    }

    public function payrollMonths()
    {
        if (can('view', 'payroll-payroll-months')) {
            $title = $this->title . ' | Payroll Months';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Months' => ''];

            $user = Auth::user();

            return view('admin.hr.payroll.payroll-months', compact('title', 'model', 'breadcum', 'user'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function payrollMonthDetails($id)
    {
        if (can('view', 'payroll-payroll-month-details')) {
            $title = $this->title . ' | Payroll Month Details';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Months' => route('hr.payroll.payroll-months'), 'Payroll Month Details' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $jobTitles = JobTitle::all();

            $earnings = Earning::all();
            $deductions = Deduction::all();

            return view('admin.hr.payroll.payroll-month-details', compact('title', 'model', 'breadcum', 'user', 'id', 'branches', 'jobTitles', 'earnings', 'deductions'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function paymasterReport(Request $request, PayrollMonth $payrollMonth)
    {
        $branchId = $request->query('branch_id');

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth->load([
            'payrollMonthDetails' => function($query) use ($branchId) {
                $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                    ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
            }
        ]);
        
        $export = new PaymasterReport($payrollMonth, $branch);

        return Excel::download($export, "paymaster-report.xlsx");
    }

    public function payslip(PayrollMonthDetail $payrollMonthDetail)
    {
        if (can('view-payslip', 'payroll-payroll-month-details')) {
            $title = $this->title . ' | Payslip';
            $model = $this->model;
            $breadcum = [
                'HR and Payroll' => '', 
                'Payroll' => '', 
                'Payroll Months' => route('hr.payroll.payroll-months'), 
                'Payroll Month Details' => route('hr.payroll.payroll-month-details', $payrollMonthDetail->id)
            ];

            $payrollMonthDetail->load(
                'payrollMonth', 
                'employee.branch', 
                'employee.jobTitle', 
                'employee.paymentMode', 
                'employee.primaryBankAccount.bank', 
                'employee.primaryBankAccount.bankBranch', 
                'earnings.earning', 
                'deductions.deduction'
            );

            $reliefs = Relief::all();

            $pdf = Pdf::loadView('admin.hr.payroll.payslip', compact('payrollMonthDetail', 'reliefs'));
    
            return $pdf->stream("employee-payslip.pdf");

        } else {
            return returnAccessDeniedPage();
        }
    }
}
