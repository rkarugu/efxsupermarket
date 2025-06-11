<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Earning;
use App\Model\Restaurant;
use App\Models\Deduction;
use App\Models\PayrollMonth;
use Illuminate\Http\Request;
use App\Exports\HR\EarningsReport;
use App\Exports\HR\DeductionsReport;
use App\Exports\HR\ProcessedPayroll;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HR\NssfDeductionsReport;
use App\Exports\HR\PayeDeductionsReport;
use App\Exports\HR\ShifDeductionsReport;
use App\Exports\HR\OtherDeductionsReport;
use App\Exports\HR\Reports\PaymasterReport;
use App\Exports\HR\ConsolidatedPayrollReport;
use App\Exports\HR\HousingLevyDeductionsReport;

class HrPayrollReportController extends Controller
{
    public function __construct(protected $title = 'Payroll Reports', protected $model = 'payroll-reports',)
    {
        // 
    }

    public function reportsPage()
    {
        if (can('view', 'payroll-reports')) {
            $title = $this->title . ' | Payroll Reports';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => ''];
            
            $sidebartitle = 'HR And Payroll';
            $mainpermission = 'payroll-reports___view';
            $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();

            return view('admin.hr.payroll.payroll-reports.index', compact('title', 'model', 'breadcum', 'mainpermission', 'sidebartitle', 'reports'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function paymasterReport()
    {
        if (can('view', 'payroll-reports-paymaster')) {
            $title = $this->title . ' | Paymaster Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Paymaster Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.paymaster', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function payrollSummaryReport()
    {
        if (can('view', 'payroll-reports-payroll-summary')) {
            $title = $this->title . ' | Payroll Summary Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Payroll Summary Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.payroll-summary', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function earningsReport()
    {
        if (can('view', 'payroll-reports-earnings')) {
            $title = $this->title . ' | Earnings Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Earnings Report' => ''];

            $user = Auth::user();

            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();
            $earnings = Earning::all();

            return view('admin.hr.payroll.payroll-reports.earnings', compact('title', 'model', 'breadcum', 'user', 'payrollMonths', 'earnings'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function deductionsReport()
    {
        if (can('view', 'payroll-reports-deductions')) {
            $title = $this->title . ' | Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Deductions Report' => ''];

            $user = Auth::user();

            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();
            $deductions = Deduction::all();

            return view('admin.hr.payroll.payroll-reports.deductions', compact('title', 'model', 'breadcum', 'user', 'payrollMonths', 'deductions'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function consolidatedPayrollReport()
    {
        if (can('view', 'payroll-reports-consolidated-payroll')) {
            $title = $this->title . ' | Consolidated Payroll Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Consolidated Payroll Report' => ''];

            $user = Auth::user();

            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.consolidated-payroll', compact('title', 'model', 'breadcum', 'user', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function payeReport()
    {
        if (can('view', 'payroll-reports-paye-deductions')) {
            $title = $this->title . ' | PAYE Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'PAYE Deductions Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.paye-deductions', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function nssfReport()
    {
        if (can('view', 'payroll-reports-nssf-deductions')) {
            $title = $this->title . ' | NSSF Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'NSSF Deductions Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.nssf-deductions', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function shifReport()
    {
        if (can('view', 'payroll-reports-shif-deductions')) {
            $title = $this->title . ' | SHIF Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'SHIF Deductions Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.shif-deductions', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function housingLevyReport()
    {
        if (can('view', 'payroll-reports-housing-levy-deductions')) {
            $title = $this->title . ' | Housing Levy Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Housing Levy Deductions Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();

            return view('admin.hr.payroll.payroll-reports.housing-levy-deductions', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function otherDeductionsReport()
    {
        if (can('view', 'payroll-reports-housing-levy-deductions')) {
            $title = $this->title . ' | Housing Levy Deductions Report';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Payroll Reports' => route('hr.payroll.payroll-reports'), 'Housing Levy Deductions Report' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();
            $payrollMonths = PayrollMonth::orderBy('start_date', 'desc')->get();
            $deductions = Deduction::all();

            return view('admin.hr.payroll.payroll-reports.other-deductions', compact('title', 'model', 'breadcum', 'user', 'branches', 'payrollMonths', 'deductions'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    // APIs
    public function generatePaymasterReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new PaymasterReport($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generatePayrollSummaryReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();

        $file = Excel::raw(new ProcessedPayroll($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateEarningsReport(Request $request)
    {
        $customEarnings = [
            'Basic Pay'
        ];

        $isCustomEarning = in_array($request->earning, $customEarnings);

        if (!$isCustomEarning) {
            $earning = Earning::where('name', $request->earning)->first();
    
            $payrollMonth = PayrollMonth::with([
                    'payrollMonthDetails' => function($query) use ($earning) {
                        $query->with('employee', 'earnings')
                            ->whereHas('earnings', fn ($earnings) => $earnings->where('earning_id', $earning->id));
                    }
                ])
                    ->where('id', $request->payroll_month_id)
                    ->first();

            $earning = $earning->name;

        } else {
            $earning = $request->earning;

            $payrollMonth = PayrollMonth::with('payrollMonthDetails.employee')
                ->where('id', $request->payroll_month_id)
                ->first();
        }
        
        $file = Excel::raw(new EarningsReport($payrollMonth, $earning, $isCustomEarning), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateDeductionsReport(Request $request)
    {
        $customDeductions = [
            'PAYE',
            'NSSF',
            'SHIF',
            'Housing Levy'
        ];

        $isCustomDeduction = in_array($request->deduction, $customDeductions);

        if (!$isCustomDeduction) {
            $deduction = Deduction::where('name', $request->deduction)->first();
    
            $payrollMonth = PayrollMonth::with([
                    'payrollMonthDetails' => function($query) use ($deduction) {
                        $query->with('employee', 'deductions')
                            ->whereHas('deductions', fn ($deductions) => $deductions->where('deduction_id', $deduction->id));
                    }
                ])
                    ->where('id', $request->payroll_month_id)
                    ->first();

            $deduction = $deduction->name;

        } else {
            $deduction = $request->deduction;

            $payrollMonth = PayrollMonth::with('payrollMonthDetails.employee')
                ->where('id', $request->payroll_month_id)
                ->first();
        }
        
        $file = Excel::raw(new DeductionsReport($payrollMonth, $deduction, $isCustomDeduction), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateConsolidatedPayrollReport(Request $request)
    {
        $payrollMonth = PayrollMonth::with([
            'payrollMonthDetails' => function($query) {
                $query->with('employee.branch', 'earnings.earning', 'deductions.deduction');
            }
        ])
            ->where('id', $request->payroll_month_id)
            ->first();
        
        $file = Excel::raw(new ConsolidatedPayrollReport($payrollMonth), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generatePayeDeductionsReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new PayeDeductionsReport($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateNssfDeductionsReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new NssfDeductionsReport($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateShifDeductionsReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new ShifDeductionsReport($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateHousingLevyDeductionsReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new HousingLevyDeductionsReport($payrollMonth, $branch), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }

    public function generateOtherDeductionsReport(Request $request)
    {
        $branchId = $request->branch_id;

        $branch = Restaurant::where('id', $branchId)->first();
        $deduction = Deduction::find($request->deduction_id);

        $payrollMonth = PayrollMonth::with([
                'payrollMonthDetails' => function($query) use ($branchId) {
                    $query->with('employee.branch', 'earnings.earning', 'deductions.deduction')
                        ->when($branchId, fn ($query) => $query->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId)));
                }
            ])
                ->where('id', $request->payroll_month_id)
                ->first();
        
        $file = Excel::raw(new OtherDeductionsReport($payrollMonth, $branch, $deduction), \Maatwebsite\Excel\Excel::XLSX);

        return response($file);
    }
}
