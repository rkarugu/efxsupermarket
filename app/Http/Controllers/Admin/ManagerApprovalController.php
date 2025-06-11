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
use Illuminate\Support\Facades\Validator;
use Response;
use PDF;

class ManagerApprovalController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Manager Approval';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.ManagerApproval.index',compact(
                    'title','lists','model','breadcum','pmodule','permission'
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
        $columns = [
            'id', 'emp_id','emp_id','leave_id','from','to'
        ];
          $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $data_query = HrManager::select('Hr_Manager.*'); 
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('emp_number', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('middle_name', 'LIKE', "%{$search}%")
                ->orWhere('LeaveType', 'LIKE', "%{$search}%");
                    
            });
            
        }
        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) { 
                    $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['emp_number'] = $row->emp_number;
                $nestedData['first_name'] = $row->first_name . ' '. $row->middle_name . ' '. $row->last_name;
                $nestedData['LeaveType'] = $row->LeaveType;
                $nestedData['from'] = $row->from;
                $nestedData['to'] = $row->to;
                $nestedData['Days'] = $row->day_taken;
                $nestedData['purpose'] = $row->purpose;
                $nestedData['purpose'] = $row->purpose;
                $nestedData['action'] =  "<a href='" . route('ManagerApproval.manage',['id'=> $row->emp_id])." '><button class='btn btn-primary'>Approve</button></a>";
                $data[] = $nestedData;
            }
        
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
  }

  public function Manage($mainID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $data_queryMange = DB::select('select *,wa_employee.first_name AS FirstName,wa_employee.middle_name as MiddleName,wa_employee.id as emp_id,wa_employee.last_name As LastName,wa_employee.staff_number As emp_number,leave_type.leave_type as LeaveType FROM `wa_assign_leave` LEFT JOIN wa_employee ON wa_assign_leave.emp_id = wa_employee.id LEFT JOIN leave_type on wa_assign_leave.leave_id = leave_type.id WHERE wa_employee.id = '.$mainID);   
        $data = $data_queryMange[0];   
        $leaveAssignEmp = AssignLeave::where([['status','Approve'],['emp_id',$mainID]])->first();
        $approveData = AssignLeave::where([['emp_id',$mainID],['manager_status','Approve']])->get();
        $declineData = AssignLeave::where([['emp_id',$mainID],['manager_status','Decline']])->get();
    return view('admin.ManagerApproval.manage',compact('title','data_queryMange','approveData','declineData','data','approveData','leaveAssignEmp'));
  }


  public function ManagerApproval(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'purpose' => 'required|max:255',
                'comments' => 'required|max:255',
                'acting_staff' => 'required|max:255',
                'approved_start_date' => 'required|max:255',
                'approved_end_date' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
         else{
            $uData = \Session::get('userdata');
             $waApprovedLeaveEmployeeUpdate =  AssignLeave::where('id',$request->leave_Assign_id)->first();
             $date1 = $request->approved_start_date;
                 $date2 = $request->approved_end_date;
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
            $waApprovedLeaveEmployeeUpdate->emp_id = $waApprovedLeaveEmployeeUpdate->emp_id;
            $waApprovedLeaveEmployeeUpdate->leave_id = $request->leave_type_id;
            $waApprovedLeaveEmployeeUpdate->to = $request->approved_end_date;
            $waApprovedLeaveEmployeeUpdate->from = $request->approved_start_date;
            $waApprovedLeaveEmployeeUpdate->date = $request->date;
            $waApprovedLeaveEmployeeUpdate->comments = $request->comments;
            $waApprovedLeaveEmployeeUpdate->purpose = $request->purpose;
            $waApprovedLeaveEmployeeUpdate->days_applied = $request->days_applied;
            $waApprovedLeaveEmployeeUpdate->acting_staff = $request->acting_staff;
            $waApprovedLeaveEmployeeUpdate->day_taken = $request->day_taken;
            $waApprovedLeaveEmployeeUpdate->manager_status = $request->ManageStatus;
            $waApprovedLeaveEmployeeUpdate->total_days = $request->days_applied;
            if ($request->ManageStatus == 'Approve') {
                $waApprovedLeaveEmployeeUpdate->status = 'Complated';
            }
        
            if ($request->ManageStatus == 'Approve') {
                $waApprovedLeaveEmployeeUpdate->manage_approve_date = Date('Y-m-d');
                $waApprovedLeaveEmployeeUpdate->manage_approve_id = $uData->id;
                $waApprovedLeaveEmployeeUpdate->manager_status = $request->ManageStatus;

            }
            if ($request->ManageStatus == 'Decline') {
                 $waApprovedLeaveEmployeeUpdate->status = 'Complated';
                 $waApprovedLeaveEmployeeUpdate->manage_reject_date = Date('Y-m-d');
                 $waApprovedLeaveEmployeeUpdate->manage_reject_id = $uData->id;
                 $waApprovedLeaveEmployeeUpdate->manager_status = $request->ManageStatus;

            }
               $waApprovedLeaveEmployeeUpdate->save();
                if ($request->ManageStatus == 'Approve') {
                    Session::flash('success', 'Leave Approve successfully.');
                }else{
                Session::flash('success', 'Leave Decline successfully.');
                }
                return redirect()->route('ManagerApproval.index'); 
            }
         }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
  }

}
