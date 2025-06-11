<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\FdSeasonsAttribute;
use App\Model\Nationality;
use App\Model\TerminationTypes;
use App\Model\JobTitle;
use App\Model\PaymentFrequency;
use App\Model\Bank;
use App\Model\PaymentModes;
use Excel;
use App\Model\Employee;
use App\Model\SeparationTermnation;
use App\Model\PayrollWaPayment;
use App\Model\WaDepartment;
use App\Model\WaCurrencyManager;
use App\Model\LoanType;
use App\Model\Branch;
use App\Model\Commission;
use App\Model\Allowance;
use App\Model\PayrollLoanType;
use App\Model\PayrollCommission;
use App\Model\Absent;
use App\Model\PayrollAllowances;
use App\Model\LeaveType;
use App\Model\Holidays;
use App\Model\AssignLeave;
use Illuminate\Support\Facades\Validator;

class AssignLeaveController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Assign Leave';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveTypeData = LeaveType::get();
        $holidaysData = Holidays::get();
        $empData = Employee::where('status','Active')->pluck('first_name','id');
        $leave_data = LeaveType::pluck('leave_type','id');
        $assignLeaveData = AssignLeave::get();
        $updataData = AssignLeave::where('id',$request->Edit)->first();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.AssignLeave.index',compact(
                    'title','lists','model','breadcum','pmodule','permission','leaveTypeData','holidaysData','empData','leave_data','assignLeaveData','updataData'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        return view('admin.ManageConfig.manage');
  }

    

     public function CreateStore(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'from' => 'unique:wa_assign_leave,from',
                'half_day' => 'required|max:255',
                'acting_staff' => 'required|max:255',
                'leave_period' => 'required|max:255',
                'leave_id' => 'required|max:255',
                'to' => 'required|max:255',
                'purpose' => 'required|max:255',
                'leave_balance' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
               if (!empty($request->attach_document)) {
                   $image = $request->file('attach_document');
                   $name = time().'.'.$image->getClientOriginalExtension();
                   $destinationPath = public_path('/uploads/AssignLeaveImage');
                   $image->move($destinationPath, $name);
                }
               
                 $assignLeaveCreate = new AssignLeave;
                 $date1 = $request->from;
                 $date2 = $request->to;
                 $dayGenretae = dateDiff($date1,$date2);
                   $assignLeaveDataBasic = AssignLeave::where('from', 'like', '%' .Date('Y',strtotime("-1 years")). '%')->Where([['emp_id',$request->emp_id]])->groupBy('emp_id')->sum('day_taken');

                  $assignLeaveData = AssignLeave::where('from', 'like', '%' .Date('Y') . '%')->Where([['emp_id',$request->emp_id]])->groupBy('emp_id')->sum('day_taken');
                    $leaveConfig = LeaveType::first();
                    $val2 = $leaveConfig->default_entitlement - @$assignLeaveData;
                    $val = $leaveConfig->default_entitlement - @$assignLeaveDataBasic;
                    $mainValue =  $val + $val2 - $dayGenretae;
                    if ($mainValue == 0  || $mainValue < 0) {
                       Session::flash('warning', 'You Not Applied For Leave.');
                        return redirect()->back(); 
                    }else{
                      $assignLeaveCreate->emp_id = $request->emp_id;
                      $assignLeaveCreate->from = date("Y-m-d", strtotime($date1));
                      $assignLeaveCreate->half_day = $request->half_day;
                      $assignLeaveCreate->acting_staff = $request->acting_staff;
                      $assignLeaveCreate->leave_period = $request->leave_period;
                      $assignLeaveCreate->leave_id = $request->leave_id;
                      $assignLeaveCreate->to = date("Y-m-d", strtotime($date2));
                      $dayGenretae = dateDiff($date1,$date2);
              if ($request->half_day == 'Yes') {
                    $assignLeaveCreate->day_taken = $dayGenretae + .50;
                }else{
                  $assignLeaveCreate->day_taken = $dayGenretae;
                }
               
                  $leaveConfigData = LeaveType::first();
                $total = $leaveConfig->default_entitlement + $leaveConfig->default_entitlement;
                $assignLeaveCreate->leave_balance = $total;
                $assignLeaveCreate->purpose = $request->purpose;
              if (!empty($request->attach_document)) {
                $assignLeaveCreate->attach_document = $name;
              }
                $assignLeaveCreate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
          }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }


     public function AssignLeaveUpdate(Request $request,$updateID){
      try{
             $validator = Validator::make($request->all(), [
                'from' => 'required|max:255',
                'half_day' => 'required|max:255',
                'acting_staff' => 'required|max:255',
                'leave_period' => 'required|max:255',
                'leave_id' => 'required|max:255',
                'to' => 'required|max:255',
                'purpose' => 'required|max:255',
                'leave_balance' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $assignLeaveUpdate = AssignLeave::where('id',$updateID)->first();  
                $name2 = $assignLeaveUpdate->attach_document;
               if (!empty($request->attach_document)) {
                   $image = $request->file('attach_document');
                   $name2 = time().'.'.$image->getClientOriginalExtension();
                   $destinationPath = public_path('/uploads/AssignLeaveImage');
                   $image->move($destinationPath, $name2);
                }
                $date1 = date_format($request->from,"Y-m-d");
                $date2 = date_format($request->to,"Y-m-d");

                $assignLeaveUpdate->emp_id = $request->emp_id;
                $assignLeaveUpdate->from = date("m/d/Y", strtotime($date1));
                $assignLeaveUpdate->half_day = $request->half_day;
                $assignLeaveUpdate->acting_staff = $request->acting_staff;
                $assignLeaveUpdate->leave_period = $request->leave_period;
                $assignLeaveUpdate->leave_id = $request->leave_id;
                $assignLeaveUpdate->to =  date("m/d/Y", strtotime($date2));
                $dayGenretae2 = dateDiff($date1,$date2);
                if ($request->half_day == 'Yes') {
                    $assignLeaveUpdate->day_taken = $dayGenretae2 + .50;
                }else{
                  $assignLeaveUpdate->day_taken = $dayGenretae2;
                }
             
                $assignLeaveUpdate->purpose = $request->purpose;
                $assignLeaveUpdate->attach_document = $name2;
                $assignLeaveUpdate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function AssignLeaveDelete($deleteID){
        AssignLeave::where('id',$deleteID)->delete();
        Session::flash('success', 'Record deleted successfully.');
       return redirect()->back(); 
     }
   

    public function AssignLeaveGet(Request $request){
       $assignLeaveDataBasic = AssignLeave::where('from', 'like', '%' .Date('Y',strtotime("-1 years")). '%')->Where([['emp_id',$request->EmpID]])->groupBy('emp_id')->sum('day_taken');

        $assignLeaveData = AssignLeave::where('from', 'like', '%' .Date('Y') . '%')->Where(
          'emp_id',$request->EmpID)->groupBy('emp_id')->sum('day_taken');
      $leaveConfig = LeaveType::first();
      $val2 = $leaveConfig->default_entitlement - @$assignLeaveData;
      $val = $leaveConfig->default_entitlement - @$assignLeaveDataBasic;

      $lastRecordGet = AssignLeave::where([['emp_id',$request->EmpID]])->orderBy('id', 'desc')->first();
      $abc = @$lastRecordGet->to;
      $abc2 = @$lastRecordGet->to;
      $data = [
        'start_date'=> @date("m/d/Y", strtotime($abc2)),
        'end_date'=> @date("m/d/Y", strtotime($abc)),
        'TotalDaya'=> $val + $val2,
     ];
      return $data;
    }


}

