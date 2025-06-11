<?php

namespace App\Http\Controllers\Admin;

use App\Models\Casual;
use App\Models\Gender;
use App\Models\Employee;
use App\Model\Restaurant;
use App\Models\Nationality;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HRManagementController extends Controller
{
    public function __construct(
        protected $model = 'hr-and-payroll-hr-management',
        protected $title = 'HR Management',

    ) {}

    public function employeeDrafts() {
        if (can('view', 'hr-management-employee-drafts')) {
            $title = $this->title . ' | Employee Drafts';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Employee Drafts' => ''];

            $user = Auth::user();

            $employeeDrafts = Employee::withoutGlobalScope('draft')->with('gender')->where('is_draft', true)->get();

            return view('admin.hr.management.employee-drafts',compact('title', 'model' ,'breadcum', 'user', 'employeeDrafts'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function employees() {
        if (can('view', 'hr-management-employees')) {
            $title = $this->title . ' | Employees List';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Employees' => ''];

            $user = Auth::user();

            return view('admin.hr.management.employees',compact('title', 'model' ,'breadcum', 'user'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function employeesCreate() {
        if (can('create', 'hr-management-employees')) {
            $draft = 'false';
            $draftId = request()->query('id');
            
            $employee = 'null';
            if ($draftId) {
                $employee = Employee::withoutGlobalScope('draft')
                    ->with('currentContract', 'primaryBankAccount', 'emergencyContacts', 'beneficiaries', 'documents')
                    ->where('id', (int) $draftId)
                    ->where('is_draft', true)
                    ->first();
                
                if (!$employee) {
                    Session::flash('warning', 'Invalid request');
                    return redirect()->back();
                }

                $draft = 'true';
            }
            
            $title = $this->title . ' | Create Employee';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Employees' => route('hr.management.employees'), 'Create' => ''];

            $user = Auth::user();

            return view('admin.hr.management.employees-create',compact('title', 'model' ,'breadcum', 'user', 'draft', 'employee'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function employeeDetails(Employee $employee) {
        if (can('details', 'hr-management-employees')) {
            if (!$employee) {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
            
            $title = $this->title . ' | Employee Details';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Employees' => route('hr.management.employees'), 'Employee Details' => ''];

            $user = Auth::user();

            $employee->load('gender', 'branch', 'department', 'employmentType', 'jobTitle', 'primaryBankAccount', 'beneficiaries', 'currentContract');

            return view('admin.hr.management.employee-details.index',compact('title', 'model' ,'breadcum', 'user', 'employee'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function casuals()
    {
        if (can('view', 'hr-management-casuals')) {
            $title = $this->title . ' | Casuals';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Casuals' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();

            $bulkUploadTemplate = route('hr.management.casuals-bulk-upload-template');

            return view('admin.hr.management.casuals.index',compact('title', 'model' ,'breadcum', 'user', 'branches', 'bulkUploadTemplate'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function casualsCreate()
    {
        if (can('create', 'hr-management-casuals')) {
            $title = $this->title . ' | Create Casual';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Casuals' => route('hr.management.casuals'), 'Create Casual' => ''];

            $genders = Gender::all();
            $nationalities = Nationality::all();
            $branches = Restaurant::all();

            return view('admin.hr.management.casuals.create',compact('title', 'model' ,'breadcum', 'genders', 'nationalities', 'branches'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function casualsEdit(Casual $casual)
    {
        if (can('edit', 'hr-management-casuals')) {
            $title = $this->title . ' | Edit Casual';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Management' => '', 'Casuals' => route('hr.management.casuals'), 'Edit Casual' => ''];

            $genders = Gender::all();
            $nationalities = Nationality::all();
            $branches = Restaurant::all();

            return view('admin.hr.management.casuals.edit',compact('title', 'model' ,'breadcum', 'genders', 'nationalities', 'branches', 'casual'));
        } else {
            return returnAccessDeniedPage();
        }
    }
}
