<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountPayablesReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'account-payables-reports';
        $this->title = 'Account Paybales Reports';
        $this->pmodule = 'account-payables-reports';
    }

    public function index()
    {
        $model = $this->model;
        $title = $this->title;
        $pmodule = $this->pmodule;
        $sidebartitle = 'Account Payables';

        $permission =  $this->mypermissionsforAModule();
        $mainpermission = 'account-payables-reports___view';

        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();
            return view('admin.purchases_reports_list.purchases_reports', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'mainpermission', 'sidebartitle', 'reports'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
