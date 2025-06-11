<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HRConfigurationsController extends Controller
{
    public function __construct(
        protected $model = 'hr-and-payroll-configurations',
        protected $title = 'HR and Payroll Configurations',

    ) {}

    public function general(Request $request) {
        if (can('view', 'hr-and-payroll-configurations-general')) {
            $title = $this->title;
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Configurations' => ''];

            $user = Auth::user();

            return view('admin.hr.configurations.general.index',compact('title', 'model' ,'breadcum', 'user'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function payroll(Request $request) {
        if (can('view', 'hr-and-payroll-configurations-payroll')) {
            $title = $this->title;
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Configurations' => ''];

            $user = Auth::user();

            return view('admin.hr.configurations.payroll', compact('title', 'model' ,'breadcum', 'user'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function banking(Request $request) {
        if (can('view', 'hr-and-payroll-configurations-banking')) {
            $title = $this->title;
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Configurations' => ''];

            $user = Auth::user();

            return view('admin.hr.configurations.banking.index',compact('title', 'model' ,'breadcum', 'user'));
        } else {
            return returnAccessDeniedPage();
        }
    }
}
