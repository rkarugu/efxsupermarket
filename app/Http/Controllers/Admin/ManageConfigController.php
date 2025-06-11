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
use Illuminate\Support\Facades\Validator;

class ManageConfigController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Manage Config';
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
        $updateData = LeaveType::where('id',$request->Edit)->first();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.ManageConfig.index',compact(
                    'title','lists','model','breadcum','pmodule','permission','leaveTypeData','updateData','holidaysData'
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

     public function Store(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'leave_type' => 'required|max:255',
                'default_entitlement' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'narration' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $leaveTypeCreate = new LeaveType;
                $leaveTypeCreate->default_entitlement = $request->default_entitlement;
                $leaveTypeCreate->narration = $request->narration;
                $leaveTypeCreate->leave_type = $request->leave_type;
                if (!empty($request->recurring == 'On')) {
                    $leaveTypeCreate->recurring = 'On';
                }else{
                    $leaveTypeCreate->recurring = 'Off';
                }
                $leaveTypeCreate->save();
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

     public function HoliDayCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'holiday_name' => 'required|max:255',
                'payrate' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'description' => 'required|max:255',
                'date' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $holidaysCreate = new Holidays;
                $holidaysCreate->holiday_name = $request->holiday_name;
                $holidaysCreate->description = $request->description;
                $holidaysCreate->payrate = $request->payrate;
                $holidaysCreate->date = $request->date;
                if (!empty($request->repeats_annually == 'On')) {
                    $holidaysCreate->repeats_annually = 'On';
                }else{
                    $holidaysCreate->repeats_annually = 'Off';
                }
                $holidaysCreate->save();
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

     public function Update(Request $request,$updateDataID){
      try{
             $validator = Validator::make($request->all(), [
                'leave_type' => 'required|max:255',
                'default_entitlement' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'narration' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $leaveTypeUpdateData =  LeaveType::where('id',$updateDataID)->first();
                $leaveTypeUpdateData->default_entitlement = $request->default_entitlement;
                $leaveTypeUpdateData->narration = $request->narration;
                $leaveTypeUpdateData->leave_type = $request->leave_type;
                if (!empty($request->recurring == 'On')) {
                    $leaveTypeUpdateData->recurring = 'On';
                }else{
                    $leaveTypeUpdateData->recurring = 'Off';
                }
                $leaveTypeUpdateData->save();
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

   

     public function Delete($deleteID){
        LeaveType::where('id',$deleteID)->delete();
        Session::flash('success', 'Record deleted successfully.');
       return redirect()->back(); 
     }


     public function HoliDayDelete($holiDayDeleteID){
        Holidays::where('id',$holiDayDeleteID)->delete();
        Session::flash('success', 'Record deleted successfully.');
       return redirect()->back(); 
     }

}