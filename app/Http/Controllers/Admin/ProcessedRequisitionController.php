<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\NWaInternalRequisitionDemo;
use App\Model\WaInternalRequisitionItemDemo;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaGlTran;
use App\Model\WaAccountingPeriod;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ProcessedRequisitionController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'processed-requisition';
        $this->title = 'Processed Requisition';
        $this->pmodule = 'processed-requisition';
    }

    public function index() {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = NWaInternalRequisitionDemo::where('status', '=', 'COMPLETED');
            if ($permission != 'superadmin') {
                $lists = $lists->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
            }
            $lists = $lists->orderBy('id', 'desc')
            ->paginate(10);
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.processedrequisition.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function show($slug) {

        $row = NWaInternalRequisitionDemo::whereSlug($slug)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            return view('admin.issuefullfillrequisition.show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function exportToPdf($slug) {
        

        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = NWaInternalRequisitionDemo::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.processedrequisition.print', compact('title', 'model', 'breadcum', 'row'));
        $report_name = 'internal_requisition_' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }

    
    public function printPage(Request $request) {
        $slug = $request->slug;
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = NWaInternalRequisitionDemo::whereSlug($slug)->first();
        return view('admin.processedrequisition.print', compact('title', 'model', 'breadcum', 'row'));
    }

}
