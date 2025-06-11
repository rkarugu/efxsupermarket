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
use App\Model\LeaveRecalls;
use Response;
use PDF;
use Illuminate\Support\Facades\Validator;

class LeaveRecallsController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Leave Recalls';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveAssignData = AssignLeave::where([['manager_status','Approve'],['to','>',Date('Y-m-d')]])->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LeaveRecalls.index',compact(
            'title','model','leaveAssignData'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    } 


    public function manage(Request $request,$id) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveAssignDataView = AssignLeave::where('id',$id)->first();
        $leaveRecalls = LeaveRecalls::where('assign_leave_id',$id)->with('EmpData')->get();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LeaveRecalls.manage',compact(
            'title','model','leaveAssignDataView','leaveRecalls'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function RecallsLeave(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                'date_recalled' => 'required',
                'recalled_by' => 'required',
                'reason' => 'required',
                ]);
            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $row = new LeaveRecalls();
                $row->assign_leave_id = $request->assign_leave_id;
                $row->date_recalled = $request->date_recalled;
                $row->reason = $request->reason;
                $leaveRecallsData = LeaveRecalls::where('assign_leave_id',$request->assign_leave_id)->get();
                $findData = AssignLeave::where('id',$request->assign_leave_id)->first();
                $date1 = $findData->from;
                $date2 = $findData->to;
                $row->emp_id = $findData->emp_id;
                $uData = \Session::get('userdata');
                $row->recalled_by = $uData->id;
                $row->leave_id = $findData->leave_id;
                $holiDate [] = $request->date_recalled;
                $beetweenCaculate = dateDiff2($date1,$date2,$holiDate);
               if ($row->save()) {
                  $findData = AssignLeave::where('id',$request->assign_leave_id)->first();
                  $findData->day_taken = $beetweenCaculate;
                  $findData->save();
                   Session::flash('success', 'Record added successfully.');
                return redirect()->route('LeaveRecalls.Index');
               }
               
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

}