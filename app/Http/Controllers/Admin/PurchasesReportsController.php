<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PurchasesReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'purchases-reports';
        $this->title = 'Purchases Reports';
        $this->pmodule = 'purchases-reports';
    }

    public function index()
    {
        $model = $this->model;
        $title = $this->title;
        $pmodule = $this->pmodule;
        $sidebartitle = 'Purchases';

        $permission =  $this->mypermissionsforAModule();
        $mainpermission = 'purchases-reports___view';

        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();
            return view('admin.purchases_reports_list.purchases_reports', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'mainpermission', 'sidebartitle', 'reports'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function createPurchasesReportsCategory()
    {
        $sidebartitle = request()->sidebar_title;
        $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->first();
        $modeulereportcategory = ModuleReportCategory::create([
            'module_id' => $reports->id,
            'category_title' => request()->category_title
        ]);
        return ['modeulereportcategory' => $modeulereportcategory];
    }

    public function createPurchasesReports()
    {

        // $requestData = request()->all();
        // try {
        //     $report = new ModuleReport();

        //     $report->module_report_category_id = $requestData['module_report_category_id'];
        //     $report->report_title = $requestData['report_title'];
        //     $report->report_model = $requestData['report_model'];
        //     $report->report_permission = $requestData['report_permission'];
        //     $report->report_route = $requestData['report_route'];
        //     $report->save();
        //     return response()->json(['message' => 'Report created successfully', 'report' => $report], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Failed to create report'], 500);
        // }

        try {
            $modeulereportcategory = ModuleReportCategory::where('id', request()->module_report_category_id)->first();
            $modeulereportcategory->category_title = request()->category_title;
            $modeulereportcategory->save();
            return response()->json(['message' => 'Report created successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePurchasesReportsPosition()
    {
        try {
            $modulereport = ModuleReport::find(request()->report_id);
            $modulereport->module_report_category_id = request()->category_id;
            $modulereport->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to create report'], 500);
        }
    }

    public function getReportDetails()
    {
        $modulereport = ModuleReport::with('modulereportcategory')->find(request()->report_id);
        return $modulereport;
    }

    public function updatePurchasesReports()
    {
        $requestData = request()->all();

        try {
            $report = ModuleReport::find($requestData['report_id']);

            $report->module_report_category_id = $requestData['module_report_category_id'];
            $report->report_title = $requestData['report_title'];
            $report->report_model = $requestData['report_model'];
            $report->report_permission = $requestData['report_permission'];
            $report->report_route = $requestData['report_route'];
            $report->save();
            if (request()->has('category_title') && request()->filled('category_title')) {
                $modulereportcategory = ModuleReportCategory::where('id', request()->module_report_category_id)->first();
                $modulereportcategory->category_title = request()->category_title;
                $modulereportcategory->save();
            }
            return response()->json(['message' => 'Report created successfully', 'report' => $report], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create report'], 500);
        }
    }
    public function deletePurchasesReports()
    {
        $report = ModuleReport::find(request()->report_id);
        $report->delete();
    }
}
