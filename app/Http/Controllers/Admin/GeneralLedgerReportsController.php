<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GeneralLedgerReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'general-ledger-reports';
        $this->title = 'General Ledger Reports';
        $this->pmodule = 'general-ledger-reports';
    }

    public function index()
    {
        $model = $this->model;
        $title = $this->title;
        $pmodule = $this->pmodule;
        $sidebartitle = 'General Ledger';

        $permission =  $this->mypermissionsforAModule();
        $mainpermission = 'sales-and-receivables-reports___view';

        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();
            return view('admin.purchases_reports_list.purchases_reports', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'mainpermission', 'sidebartitle', 'reports'));
            // return view('admin.general_ledger_reports.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
