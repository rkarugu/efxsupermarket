<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SalesAndReceivablesMainReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Sales and Receivables Reports';
        $this->pmodule = 'sales-and-receivables-reports';
    }

    public function index()
    {
        $model = $this->model;
        $title = $this->title;
        $pmodule = $this->pmodule;
        $sidebartitle = 'Sales & Receivables';

        $permission =  $this->mypermissionsforAModule();
        $mainpermission = 'sales-and-receivables-reports___view';

        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();
            return view('admin.purchases_reports_list.purchases_reports', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'mainpermission', 'sidebartitle', 'reports'));
        
            // return view('admin.sales_and_receivables_reports.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesandreceivablesreports'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
