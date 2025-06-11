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
use App\Model\PayrollLoanType;
use App\Model\PayrollCommission;
use App\Model\Absent;
use App\Model\AssignLeave;
use App\Model\HrLeaveView;
use App\Model\LeaveType;
use Illuminate\Support\Facades\Validator;
use Response;
use PDF;

class HrApprovalController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Hr Approval';
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
            return view('admin.HrAppoval.index',compact(
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
        $data_query = HrLeaveView::select('hr_leaveView.*'); 
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('emp_number', 'LIKE', "%{$search}%")
                ->orWhere('FirstName', 'LIKE', "%{$search}%")
                ->orWhere('LastName', 'LIKE', "%{$search}%")
                ->orWhere('LeaveType', 'LIKE', "%{$search}%")
                ->orWhere('MiddleName', 'LIKE', "%{$search}%");
                    
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
                $nestedData['first_name'] = $row->FirstName . ' '. $row->MiddleName . ' '. $row->LastName;
                $nestedData['LeaveType'] = $row->LeaveType;
                $nestedData['to'] = $row->to;
                $nestedData['from'] = $row->from;
                $nestedData['Days'] = $row->day_taken;
                $nestedData['purpose'] = $row->purpose;
                $nestedData['purpose'] = $row->purpose;
                $nestedData['action'] =  "<a href='" . route('HrApproval.manage',['id'=> $row->emp_id])." '><button class='btn btn-primary'>Approve</button></a>";
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

  public function ManageIndex($mainID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $data_queryMange = DB::select('select *,wa_employee.first_name AS FirstName,wa_employee.id as emp_id,wa_employee.middle_name as MiddleName ,wa_employee.last_name As LastName,wa_employee.staff_number As emp_number,leave_type.leave_type as LeaveType FROM `wa_assign_leave` LEFT JOIN wa_employee ON wa_assign_leave.emp_id = wa_employee.id LEFT JOIN leave_type on wa_assign_leave.leave_id = leave_type.id WHERE wa_employee.id = '.$mainID);   
        $data = $data_queryMange[0];   
        $leaveAssignEmp = AssignLeave::where([['status','Pending'],['emp_id',$mainID]])->first();
        $approveData = AssignLeave::where([['emp_id',$mainID],['status','Approve']])->get();
        // dd($approveData);
        $declineData = AssignLeave::where([['emp_id',$mainID],['status','Decline']])->get();
    return view('admin.HrAppoval.manage',compact('title','data_queryMange','data','approveData','declineData','leaveAssignEmp'));
  }

 



     public function ApprovalHr(Request $request){
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
                 $uData = \Session::get('userdata');
              $waApprovedLeaveEmployeeAdd = AssignLeave::where('id',$request->leave_Assign_id)->first();
              // dd($dayGenretae);
             $waApprovedLeaveEmployeeAdd->emp_id = $request->emp_id;
             $waApprovedLeaveEmployeeAdd->from = $request->approved_start_date;
             $waApprovedLeaveEmployeeAdd->to = $request->approved_end_date;
             $waApprovedLeaveEmployeeAdd->acting_staff = $request->acting_staff;
             $waApprovedLeaveEmployeeAdd->days_applied = $request->days_applied;
             $waApprovedLeaveEmployeeAdd->date = $request->date;
             $waApprovedLeaveEmployeeAdd->comments = $request->comments;
             $waApprovedLeaveEmployeeAdd->purpose = $request->purpose;
             $waApprovedLeaveEmployeeAdd->leave_id = $request->leave_type_id;
             $waApprovedLeaveEmployeeAdd->status = $request->Status;
             $waApprovedLeaveEmployeeAdd->day_taken = $request->day_taken;
             if ($request->half_day_ == 'Yes') {
                 $dayGenretae2 = $dayGenretae + .5;
             }else{
               $dayGenretae2 = $dayGenretae;
             }

             if ($request->Status == 'Approve') {
                $waApprovedLeaveEmployeeAdd->date_approved = Date('Y-m-d');
                $waApprovedLeaveEmployeeAdd->approved_by = $uData->id;
             }elseif($request->Status == 'Decline'){
                $waApprovedLeaveEmployeeAdd->reject_date = Date('Y-m-d');
                $waApprovedLeaveEmployeeAdd->reject_id = $uData->id;
             }
              if ($waApprovedLeaveEmployeeAdd->save()) {
                if ($waApprovedLeaveEmployeeAdd->status == 'Approve') {
                    Session::flash('success', 'Leave Approve successfully.');
                    return redirect()->route('HrApproval.index'); 
                }else{
                    Session::flash('success', 'Leave Decline successfully.');
                     return redirect()->route('HrApproval.index'); 
                }
              }
             }
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }


      public function LeaveLetterPdf(Request $request){
          $data_queryMange = DB::select('select *,wa_employee.first_name AS FirstName,wa_employee.id as emp_id,wa_employee.middle_name as MiddleName ,wa_employee.last_name As LastName,wa_employee.staff_number As emp_number,leave_type.leave_type as LeaveType FROM `wa_assign_leave` LEFT JOIN wa_employee ON wa_assign_leave.emp_id = wa_employee.id LEFT JOIN leave_type on wa_assign_leave.leave_id = leave_type.id WHERE wa_employee.id = '.$request->id);
        $data = $data_queryMange[0]; 
        // dd($data);
        $empData = Employee::where('id',$request->id)->first();
        $assignLeavepdf = AssignLeave::get();
        $pdf = PDF::loadView('admin.leaveLetterpdf',compact('empData','data'))->setPaper(array(0, 0, 595, 941),
          'portrait');
      return $pdf->download('LEAVE LETTER REPORT.pdf');

  }

  public function Calcuction(Request $request){
    $date1 = $request->StratDate;
    $date2 = $request->EndDate;
    $dayGenretae = dateDiff($date1,$date2);

    if ($request->HalfDaty  == 'Yes') {
       $dayGenretae = $dayGenretae+.5;
    }else{
      $dayGenretae;
    }
    return $dayGenretae;

  }

}
