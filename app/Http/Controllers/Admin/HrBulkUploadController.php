<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gender;
use App\Models\Earning;
use App\Models\JobGrade;
use App\Models\JobGroup;
use App\Models\JobLevel;
use App\Models\JobTitle;
use App\Model\Restaurant;
use App\Models\Deduction;
use App\Models\Salutation;
use App\Model\WaDepartment;
use App\Models\Nationality;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use App\Models\EducationLevel;
use App\Models\EmploymentType;
use App\Imports\HR\BanksImport;
use App\Imports\HR\CasualsImport;
use App\Imports\HR\EmployeeImport;
use App\Models\PayrollMonthDetail;
use App\Imports\HR\JobGradesImport;
use App\Imports\HR\JobGroupsImport;
use App\Imports\HR\JobLevelsImport;
use App\Imports\HR\JobTitlesImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HR\EmployeesBulkUpload;
use App\Imports\HR\NationalitiesImport;
use Illuminate\Support\Facades\Session;
use App\Exports\HR\BanksBulkUploadTemplate;
use App\Exports\HR\CasualsBulkUploadTemplate;
use App\Exports\HR\EmployeesBulkUploadErrors;
use App\Exports\HR\JobGradesBulkUploadTemplate;
use App\Exports\HR\JobGroupsBulkUploadTemplate;
use App\Exports\HR\JobLevelsBulkUploadTemplate;
use App\Exports\HR\JobTitlesBulkUploadTemplate;
use App\Imports\HR\EarningsAndDeductionsImport;
use App\Exports\HR\NationalitiesBulkUploadTemplate;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\HR\EarningsAndDeductionsUploadTemplate;

class HrBulkUploadController extends Controller
{
    public function bulkUploadEmployeesTemplate()
    {
        if (can('bulk-upload', 'hr-management-employees')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Enter "yes" or "no" for relevant columns'],
                ['3. Any column that can have numbers with leading zeros should be formatted as general text'],
                ['4. Gender available options: ' . implode(',', Gender::all()->pluck('name')->toArray())],
                ['5. Salutation available options: ' . implode(',', Salutation::all()->pluck('name')->toArray())],
                ['6. Marital Status available options: ' . implode(',', Salutation::all()->pluck('name')->toArray())],
                ['7. Nationality available options: ' . implode(',', Nationality::all()->pluck('name')->toArray())],
                ['8. Education Level available options: ' . implode(',', EducationLevel::all()->pluck('name')->toArray())],
                ['9. Branch available options: ' . implode(',', Restaurant::all()->pluck('name')->toArray())],
                ['10. Department available options: ' . implode(',', WaDepartment::all()->pluck('department_name')->toArray())],
                ['11. Employment Type available options: ' . implode(',', EmploymentType::all()->pluck('name')->toArray())],
                ['12. Job Title available options: ' . implode(',', JobTitle::all()->pluck('name')->toArray())],
                ['13. Job Grade available options: ' . implode(',', JobGrade::all()->pluck('name')->toArray())],
                ['14. Payment Mode available options: ' . implode(',', PaymentMode::all()->pluck('name')->toArray())],
            ];

            $export = new EmployeesBulkUpload(collect($excelData), 'template');
            
            return Excel::download($export, "employees_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        } 
    }
    
    public function bulkUploadEmployees(Request $request)
    {
        return $this->performBulkUpload($request, EmployeeImport::class, EmployeesBulkUploadErrors::class);
    }

    public function nationalitiesBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-nationality')) {
            $excelData = [
                [''],
            ];

            $export = new NationalitiesBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "nationalities_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadNationalities(Request $request)
    {
        return $this->performBulkUpload($request, NationalitiesImport::class, NationalitiesBulkUploadTemplate::class);
    }

    public function banksBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-bank')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Use "Bank Ref" in place of bank code and branch code ']
            ];

            $export = new BanksBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "banks_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadBanks(Request $request)
    {
        return $this->performBulkUpload($request, BanksImport::class, BanksBulkUploadTemplate::class);
    }

    public function jobGroupsBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-job-group')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required']
            ];

            $export = new JobGroupsBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "job_groups_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadJobGroups(Request $request)
    {
        return $this->performBulkUpload($request, JobGroupsImport::class, JobGroupsBulkUploadTemplate::class);
    }

    public function jobLevelsBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-job-level')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Job Group available options: ' . implode(',', JobGroup::all()->pluck('name')->toArray())],
            ];

            $export = new JobLevelsBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "job_levels_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadJobLevels(Request $request)
    {
        return $this->performBulkUpload($request, JobLevelsImport::class, JobLevelsBulkUploadTemplate::class);
    }

    public function jobGradesBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-job-grade')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Job Level available options: ' . implode(',', JobLevel::all()->pluck('name')->toArray())],
            ];

            $export = new JobGradesBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "job_grades_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadJobGrades(Request $request)
    {
        return $this->performBulkUpload($request, JobGradesImport::class, JobGradesBulkUploadTemplate::class);
    }

    public function jobTitlesBulkUploadTemplate()
    {
        if (can('bulk-upload', 'hr-and-payroll-configurations-job-title')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Job Level available options: ' . implode(',', JobLevel::all()->pluck('name')->toArray())],
            ];

            $export = new JobTitlesBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "job_titles_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function bulkUploadJobTitles(Request $request)
    {
        return $this->performBulkUpload($request, JobTitlesImport::class, JobTitlesBulkUploadTemplate::class);
    }

    public function bulkUploadCasualsTemplate()
    {
        if (can('bulk-upload', 'hr-management-casuals')) {
            $excelData = [
                [''],
                [''],
                [''],
                ['{--- Delete this line and everything below it ---}'],
                ['1. Columns marked with "*" are required'],
                ['2. Enter "yes" or "no" for relevant columns'],
                ['3. Gender available options: ' . implode(',', Gender::all()->pluck('name')->toArray())],
                ['4. Nationality available options: ' . implode(',', Nationality::all()->pluck('name')->toArray())],
                ['5. Branch available options: ' . implode(',', Restaurant::all()->pluck('name')->toArray())],
            ];

            $export = new CasualsBulkUploadTemplate(collect($excelData), 'template');
            
            return Excel::download($export, "casuals_bulk_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        } 
    }

    public function bulkUploadCasuals(Request $request)
    {
        return $this->performBulkUpload($request, CasualsImport::class, CasualsBulkUploadTemplate::class);
    }

    public function earningsAndDeductionsTemplate(Request $request, $id)
    {
        if (can('upload-earnings-and-deductions', 'payroll-payroll-month-details')) {
            $edCode = $request->query('ed_code');

            $parameter = Deduction::where('code', $edCode)->first();
            
            if (!$parameter) {
                $parameter = Earning::where('code', $edCode)->first();
            }

            if (!$parameter) {
                Session::flash('warning', 'Invalid request.');
                return redirect()->back();
            }

            $headings = [
                'Payroll No.',
                'Employee Name',
                $parameter->name
            ];
            
            $payrollMonthDetails = PayrollMonthDetail::with('employee')->where('payroll_month_id', $id)->get();
            
            $excelData = [];

            foreach ($payrollMonthDetails as $payrollMonthDetail) {
                $excelData[] = [
                    $payrollMonthDetail->employee->payroll_no,
                    $payrollMonthDetail->employee->full_name,
                    ''
                ];
            }

            $export = new EarningsAndDeductionsUploadTemplate(collect($excelData), $headings, '');
            
            return Excel::download($export, "payroll_month_earnings_deductions_upload_template.xlsx");
        } else {
            return returnAccessDeniedPage();
        } 
    }

    public function earningsAndDeductionsUpload(Request $request, $id)
    {
        $request->validate([
            'uploaded_file' => 'required|file|mimes:xlsx'
        ]);

        $edCode = $request->query('ed_code');

        $parameter = Deduction::where('code', $edCode)->first();
        
        if (!$parameter) {
            $parameter = Earning::where('code', $edCode)->first();
        }

        $headings = [
            'Payroll No.',
            'Employee Name',
            $parameter->name
        ];
        
        try {
            $import = new EarningsAndDeductionsImport($id, $parameter);
            $import->import($request->file('uploaded_file'));

            $payrollMonthDetails = PayrollMonthDetail::where('payroll_month_id', $id)->get();

            $payrollMonthController = new PayrollMonthController();

            $payrollMonthController->getPayrollCalculationInformation();

            $payrollMonthDetails->each(fn ($payrollMonthDetail) => $payrollMonthController->calculatePayrollMonthDetails($payrollMonthDetail));
            
            if ($import->failures()->count()) {
                
                $values = $this->getErrorValues($import->failures());

                array_push($values, ...[
                    [''],
                    [''],
                    [''],
                    ['{--- Delete this line and everything below it ---}'],
                    ['The above rows have errors. Check the errors column for additional information.'],
                ]);

                $file = Excel::raw(new EarningsAndDeductionsUploadTemplate(collect($values), $headings, 'error'), \Maatwebsite\Excel\Excel::XLSX);

                return response($file, 201);
            }

            return response()->json([
                'message' => 'Data uploaded successfully'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function performBulkUpload(Request $request, $importClass, $templateClass)
    {
        $request->validate([
            'uploaded_file' => 'required|file|mimes:xlsx'
        ]);
        
        try {
            $import = new $importClass();
            $import->import($request->file('uploaded_file'));

            if ($import->failures()->count()) {
                
                $values = $this->getErrorValues($import->failures());

                array_push($values, ...[
                    [''],
                    [''],
                    [''],
                    ['{--- Delete this line and everything below it ---}'],
                    ['The above rows have errors. Check the errors column for additional information.'],
                ]);

                $file = Excel::raw(new $templateClass(collect($values), 'error'), \Maatwebsite\Excel\Excel::XLSX);

                return response($file, 201);
            }

            return response()->json([
                'message' => 'Data uploaded successfully'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    public function getErrorValues($failures)
    {
        $data = [];
        foreach($failures as $failure) {
            
            $row = $failure->row();
            $errors = str_replace('.', '', $failure->errors()[0]);

            if (array_key_exists($row, $data)) {
                $data[$row]['errors'] .= ', ' . $errors;
            } else {
                $data[$row] = [
                        ...$failure->values(),
                        'errors' => $errors
                ];
            }
        }

        return array_map(function($dataItem) {
            return array_values($dataItem);
        }, array_values($data));
    }
}
