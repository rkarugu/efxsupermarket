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
use App\Model\Entitlements;
use App\Model\WaEntitlementsDepartment;
use App\Model\Holidays;
use App\Model\AssignLeave;
use Illuminate\Support\Facades\Validator;

class EntitlementsController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Entitlements';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $leaveTypeData = LeaveType::pluck('leave_type','id');
        $holidaysData = Holidays::get();
        $empData = Employee::where('status','Active')->pluck('first_name','id');
        $departmentData = WaDepartment::pluck('department_name','id');
        $updateData = LeaveType::where('id',$request->Edit)->first();
        $entitlementsDataa = Entitlements::get();
        $updateData = Entitlements::where('id',$request->Edit)->first();
        $wa_entitlementsDepartmentData = WaEntitlementsDepartment::get();
        

// dd($assignLeaveDataBasic);
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Entitlements.index',compact(
                    'title','lists','model','breadcum','pmodule','permission','leaveTypeData','updateData','holidaysData','departmentData','leaveTypeData','empData','entitlementsDataa','updateData','wa_entitlementsDepartmentData'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

     public function CreateStore(Request $request){
       $messages = [
        'unique'    => 'Entitlements for that leave and period already in the database.',
      ];
      try{
             $validator = Validator::make($request->all(), [
                'entitlement' => 'required|max:255',
                'employee_id' => 'required|unique:wa_entitlements',
                'opening_balance' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'leave_period' => 'required|max:255',
                'default_entitlement' => 'required|max:255',
                'leave_type_id' => 'required|max:255',
                ],$messages);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $entitlementsCreate = new Entitlements;
                $entitlementsCreate->employee_id = $request->employee_id;
                $entitlementsCreate->entitlement = $request->entitlement;
                $entitlementsCreate->opening_balance = $request->opening_balance;
                $entitlementsCreate->leave_period = $request->leave_period;
                $entitlementsCreate->default_entitlement = $request->default_entitlement;
                $entitlementsCreate->leave_type_id = $request->leave_type_id;
                $entitlementsCreate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

 public function EntitlementsUpdate(Request $request,$updateData){
  $messages = [
        'unique'    => 'Entitlements for that leave and period already in the database.',
   ];

    try{
            $validator = Validator::make($request->all(), [
              'employee_id'  =>  'required|unique:wa_entitlements,employee_id,'.$updateData,
              'entitlement' => 'required',
              'opening_balance' => "required|regex:/^\d+(\.\d{1,2})?$/",
              'leave_period' => 'required|max:255',
              'default_entitlement' => 'required|max:255',
              'leave_type_id' => 'required|max:255',
              ],$messages);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $entitlementsUpdate = Entitlements::where('id',$updateData)->first();
                $entitlementsUpdate->employee_id = $request->employee_id;
                $entitlementsUpdate->entitlement = $request->entitlement;
                $entitlementsUpdate->opening_balance = $request->opening_balance;
                $entitlementsUpdate->leave_period = $request->leave_period;
                $entitlementsUpdate->default_entitlement = $request->default_entitlement;
                $entitlementsUpdate->leave_type_id = $request->leave_type_id;
                $entitlementsUpdate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route('Entitlements.index'); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }   
 }

 public function DepartmentsCreate(Request $request){
    try{
             $validator = Validator::make($request->all(), [
                'department_id' => 'required|max:255',
                'leave_period' => "required",
                'leave_type_id' => "required",
                'entitlement' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                 $empGetByDep = Employee::where('department_id',$request->department_id)->first();
                $wa_entitlements_departmentCreate = new WaEntitlementsDepartment;
                $wa_entitlements_departmentCreate->department_id = $request->department_id;
                $wa_entitlements_departmentCreate->leave_period = $request->leave_period;
                $wa_entitlements_departmentCreate->leave_type_id = $request->leave_type_id;
                $wa_entitlements_departmentCreate->emp_id = $empGetByDep->id;
                $wa_entitlements_departmentCreate->entitlement = $request->entitlement;
                $wa_entitlements_departmentCreate->save();
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

  public function EntitlementsDelete($deleteID){
         Entitlements::where('id',$deleteID)->delete();
        Session::flash('success', 'Record Delete successfully.');
        return redirect()->back(); 

  }

  public function HolidaysDelete($deleteID){
         WaEntitlementsDepartment::where('id',$deleteID)->delete();
        Session::flash('success', 'Record Delete successfully.');
        return redirect()->back(); 

  }


     public function LeaveTypeGet(Request $request){
        $dataGetAjax = LeaveType::where('id',$request->LeaveID)->first();
        return response()->json($dataGetAjax);
     }


     public function YearCalcution(Request $request){
      $assignLeaveDataBasic = AssignLeave::where('from', 'like', '%' .Date('Y',strtotime("-1 years")). '%')->Where([['manager_status','Approve'],['emp_id',$request->Emp]])->count();
      $leaveConfig = LeaveType::first();
      $val = $leaveConfig->default_entitlement - $assignLeaveDataBasic;
      return $val;
     }

}