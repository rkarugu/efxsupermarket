<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\FdSeasonsAttribute;
use Excel;
use App\Model\Employee;
use App\Model\AssignLeave;
use App\Model\HrManager;
use App\Model\WaApprovedLeaveEmployee;
use App\Model\LeaveType;
use App\Model\LeaveRecalls;
use App\Model\LeaveReversal;
use App\Model\Entitlements;
use Illuminate\Support\Facades\Validator;
use Response;
use PDF;

class ManageReportController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Report';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function leaveHistory(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveTypeData = LeaveType::get();
        $query = AssignLeave::where('status','Complated');
        if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query->where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query->whereBetween('from', [$request->from, $request->to]);
        }
         $leaveDataAssign = $query->get();
        }else{
            $leaveDataAssign = [];
        }
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LeaveHistory.index',compact(
                    'title','lists','model','breadcum','pmodule','permission','leaveTypeData','leaveDataAssign'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function LeaveBalances(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveTypeData2 = LeaveType::get();
        $leaveConfig = LeaveType::first();

        $query = new  Entitlements;
        if (count($request->all()) > 0) {
        if (!empty($request->leave_type_id) ) {
            $query = $query->where('leave_type_id',$request->leave_type_id);
        }
        if (!empty($request->year) ) {
            $query = $query->where('leave_period',$request->year);
        }
        $leaveDataAssignBalace = $query->get();
        }else{
            $leaveDataAssignBalace = [];
        }
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LeaveHistory.LeaveBalances',compact(
                    'title','lists','model','breadcum','pmodule','permission','leaveTypeData2','leaveDataAssignBalace','leaveConfig'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function leaveHistoryPdf(Request $request){
        $filerArray = [
            'leave_type_id'=>$request->leave_type_id,
            'from'=>$request->from,
            'to'=>$request->to,
        ];
         $query = AssignLeave::where('status','Complated');
       if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query->where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query->whereBetween('from', [$request->from, $request->to]);
        }
        $leaveDataAssignPdf = $query->get();
        }else{
            $leaveDataAssignPdf = [];
        }
         $pdf = PDF::loadView('admin.LeaveHistory.reportPdf',compact('leaveDataAssignPdf','filerArray'))->setPaper(array(0, 0, 595, 941),'portrait');
      return $pdf->download('REPORT.pdf');
    }

    public function LeaveRecallsReport(Request $request){
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
       
        $permission = $this->mypermissionsforAModule();
        $leaveTypeData3 = LeaveType::get();
         $query = new LeaveRecalls;
        if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query::where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query::whereBetween('date_recalled', [$request->from, $request->to]);
        }
        $leaveRecallsData = $query->get();
        }else{
            $leaveRecallsData = [];
        }
        return view('admin.LeaveHistory.LeaveRecallsReport',compact(
        'title','lists','model','breadcum','pmodule','permission','leaveTypeData3','leaveRecallsData','filerArray'
    ));
    }


    public function ReversalReport(Request $request){
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;

        $permission = $this->mypermissionsforAModule();
        $leaveTypeData3 = LeaveType::get();
         $query =  new LeaveReversal;
        if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query::where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query::whereBetween('date_reversal', [$request->from, $request->to]);
        }
        $leaveReversalData = $query->get();
        }else{
            $leaveReversalData = [];
        }
        return view('admin.LeaveHistory.ReversalReport',compact(
        'title','lists','model','breadcum','pmodule','permission','leaveTypeData3','leaveReversalData'));
    }

    public function ReversalReportPdf(Request $request){
          
         $filerArray = [
            'leave_type_id'=>$request->leave_type_id,
            'from'=>$request->from,
            'to'=>$request->to,
        ];
          $query =  new LeaveReversal;
        if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query::where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query::whereBetween('date_reversal', [$request->from, $request->to]);
        }
        $leaveReversalDataPdf = $query->get();
        }else{
            $leaveReversalDataPdf = [];
        }
        $pdf = PDF::loadView('admin.LeaveHistory.ReversalReportPdf',compact('leaveReversalDataPdf','filerArray'))->setPaper(array(0, 0, 595, 941),'portrait');
      return $pdf->download('REPORT.pdf');
    }


    public function RecallReportPdf(Request $request){
         $filerArray = [
            'leave_type_id'=>$request->leave_type_id,
            'from'=>$request->from,
            'to'=>$request->to,
        ];
         $query = new LeaveRecalls;
        if (count($request->all()) > 0) {
        if (isset($request->leave_type_id) ) {
            $query = $query::where('leave_id',$request->leave_type_id);
        }
        if (isset($request->from)) {
            $query = $query::whereBetween('date_recalled', [$request->from, $request->to]);
        }
        $leaveRecallsDataPdf = $query->get();
        }else{
            $leaveRecallsDataPdf = [];
        }
        $pdf = PDF::loadView('admin.LeaveHistory.RecallReportPdfView',compact('leaveRecallsDataPdf','filerArray'))->setPaper(array(0, 0, 595, 941),'portrait');
      return $pdf->download('REPORT.pdf');
    }


    public function LeaveBlancePdf(Request $request){
        $leaveConfigData = LeaveType::first();
        $assignLeaveDataBasic = AssignLeave::where('from', 'like', '%' .Date('Y',strtotime("-1 years")). '%')->Where([['emp_id',$request->EmpID]])->groupBy('emp_id')->sum('day_taken');

        $query = new  Entitlements;
        if (count($request->all()) > 0) {
        if (!empty($request->leave_type_id) ) {
            $query = $query->where('leave_type_id',$request->leave_type_id);
        }
        if (!empty($request->year) ) {
            $query = $query->where('leave_period',$request->year);
        }
        $leaveDataAssignBalacePdf = $query->get();
        }else{
            $leaveDataAssignBalacePdf = [];
        }
      $pdf = PDF::loadView('admin.LeaveHistory.leaveBlancePdf',compact('leaveDataAssignBalacePdf','leaveConfigData','assignLeaveDataBasic'))->setPaper(array(0, 0, 595, 941),'portrait');
      return $pdf->download('REPORT.pdf');
    }
}

