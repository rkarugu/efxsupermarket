<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\Employee;
use App\Model\Branch;
use App\Model\LeaveType;
use App\Model\WaDepartment;
use App\Model\JobTitle;
use App\Model\EmploymentType;
use App\Model\AssignLeave;
use App\Model\Gender;
use Response;
use PDF;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\PostExport;


class LeaveStatusController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Leave Status';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveData = LeaveType::pluck('leave_type','id');
        $branchData = Branch::pluck('branch','id');
        $departmentsData = WaDepartment::pluck('department_name','id');
        $jobTitleData = JobTitle::pluck('job_title','id');
        $genderData = Gender::pluck('gender','id');
        $emptypeData = EmploymentType::pluck('type','id');
        $pendingRequestManager = AssignLeave::where('status','Approve')->get();
        $pendingRequestHr = AssignLeave::where('status','Pending')->get();

        if ($request->Types2 == 'EmployeeOnLeave') {
            $queryData = new AssignLeave;
            if(isset($request->leave_id)){
             $queryData = $queryData::Where('leave_id',$request->leave_id);
            }
            if (isset($request->from) && $request->to) {
              $queryData = $queryData->whereBetween('date',[$request->from,$request->to]);
              }
             $mainData = $queryData->get(); 
         }else{
            $mainData = []; 
         }
         if ($request->Types2 == 'ScheduledLeaves') {
             $scheduledLeavesData =  AssignLeave::where('manager_status','Approve');
             if (isset($request->leave_id)) {
                $scheduledLeavesData = $scheduledLeavesData->Where('leave_id',$request->leave_id);
             }
             if (isset($request->from) && $request->to) {
               $scheduledLeavesData = $scheduledLeavesData->WhereBetween('date',[$request->from,$request->to]);
              }
               $scheduledLeavesDataMainData = $scheduledLeavesData->get(); 
         }else{
            $scheduledLeavesDataMainData = []; 
         }
         if ($request->Types2 == 'CompletedLeaves') {
             $completedLeavesData =  AssignLeave::where('to','<=', Date('Y-m-d'));

            if (isset($request->leave_id)) {
                $completedLeavesData = $completedLeavesData->Where('leave_id',$request->leave_id);
             }
            if (isset($request->from) && $request->to) {
               $completedLeavesData = $completedLeavesData->WhereBetween('date',[$request->from,$request->to]);
              }
                $completedLeavesData2 = $completedLeavesData->get(); 
            }else{
               $completedLeavesData2 = []; 
         }
         if ($request->Types2 == 'DeclinedLeaves') {
             $declinedLeavesData =  AssignLeave::where('manager_status','Decline');

            if (isset($request->leave_id)) {
                $declinedLeavesData = $declinedLeavesData->Where('leave_id',$request->leave_id);
             }
            if (isset($request->from) && $request->to) {
               $declinedLeavesData = $declinedLeavesData->WhereBetween('date',[$request->from,$request->to]);
              }
                $declinedLeavesData2 = $declinedLeavesData->get(); 
            }else{
               $declinedLeavesData2 = []; 
         }

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LeaveStatus.manage',compact(
            'title','model','breadcum','pmodule','permission','leaveData','branchData','departmentsData','jobTitleData','genderData','emptypeData','pendingRequestHr','pendingRequestManager','mainData','scheduledLeavesDataMainData','completedLeavesData2','declinedLeavesData2'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function PdfDownloadEmployeeLeaveon(Request $request){
      if ($request->Types2 == 'EmployeeOnLeave') {
        $queryData = new AssignLeave;
        if(isset($request->leave_id)){
         $queryData = $queryData::Where('leave_id',$request->leave_id);
        }
        if (isset($request->from) && $request->to) {
          $queryData = $queryData->whereBetween('date',[$request->from,$request->to]);
          }
         $mainData = $queryData->get(); 
     }else{
        $mainData = []; 
     }
      $pdf = PDF::loadView('admin.LeaveStatus.EmployeeOnLeave',compact('mainData'));
      return $pdf->download('EmployeeOnLeave.pdf');
    }


    public function PdfDownloadScheduledLeaves(Request $request){
          if ($request->Types2 == 'ScheduledLeaves') {
             $scheduledLeavesData =  AssignLeave::where('manager_status','Approve');
             if (isset($request->leave_id)) {
                $scheduledLeavesData = $scheduledLeavesData->Where('leave_id',$request->leave_id);
             }
             if (isset($request->from) && $request->to) {
               $scheduledLeavesData = $scheduledLeavesData->WhereBetween('date',[$request->from,$request->to]);
              }
               $scheduledLeavesDataMainData = $scheduledLeavesData->get(); 
         }else{
            $scheduledLeavesDataMainData = []; 
         }
         $pdf = PDF::loadView('admin.LeaveStatus.ScheduledLeaves',compact('scheduledLeavesDataMainData'));
          return $pdf->download('ScheduledLeaves.pdf');

    }

    public function PdfDownloadCompletedLeaves(Request $request){
         if ($request->Types2 == 'CompletedLeaves') {
             $completedLeavesDataPdf =  AssignLeave::where('to','<=', Date('Y-m-d'));

             if (isset($request->leave_id)) {
                $completedLeavesDataPdf = $completedLeavesDataPdf->Where('leave_id',$request->leave_id);
             }
             if (isset($request->from) && $request->to) {
               $completedLeavesDataPdf = $completedLeavesDataPdf->WhereBetween('date',[$request->from,$request->to]);
              }
               $completedLeavesDataPdf2 = $completedLeavesDataPdf->get(); 
         }else{
            $completedLeavesDataPdf2 = []; 
         }
         $pdf = PDF::loadView('admin.LeaveStatus.CompletedLeaves',compact('completedLeavesDataPdf2'));
          return $pdf->download('CompletedLeaves.pdf');
    }


    public function PdfDownloadDeclinedLeaves(Request $request){
         if ($request->Types2 == 'DeclinedLeaves') {
             $declinedLeavesDataPdf =  AssignLeave::where('manager_status','Decline');

             if (isset($request->leave_id)) {
                $declinedLeavesDataPdf = $declinedLeavesDataPdf->Where('leave_id',$request->leave_id);
             }
             if (isset($request->from) && $request->to) {
               $declinedLeavesDataPdf = $declinedLeavesDataPdf->WhereBetween('date',[$request->from,$request->to]);
              }
               $declinedLeavesDataPdf2 = $declinedLeavesDataPdf->get(); 
         }else{
            $declinedLeavesDataPdf2 = []; 
         }
         $pdf = PDF::loadView('admin.LeaveStatus.DeclinedLeaves',compact('declinedLeavesDataPdf2'));
          return $pdf->download('DeclinedLeaves.pdf');
    }

      public function export(){
        return Excel::download(new PostExport, 'list.xlsx');
    }
}