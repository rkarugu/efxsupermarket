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
use Illuminate\Support\Facades\Validator;

class AbsentController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Absent';
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
            return view('admin.Absent.index',compact(
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
            'id', 'staff_number','first_name','date_of_birth','job_title','branch_id','date_of_birth','date_employed','last_name','Id_number'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Employee::select('wa_employee.*')->where('status','Active');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('staff_number', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('date_of_birth', 'LIKE', "%{$search}%");
                    
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
               $depData = WaDepartment::where('id',$row->department_id)->first();                 
               $job_Data = JobTitle::where('id',$row->job_title)->first();                 
               $branch_Data = Branch::where('id',$row->branch_id)->first();                 
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['staff_number'] = $row->staff_number;
                $nestedData['first_name'] = $row->first_name . ' '. $row->middle_name . ' '. $row->last_name;
                $nestedData['Id_number'] = $row->Id_number;
                $nestedData['nhif_no'] = $row->nhif_no;
                $nestedData['nssf_no'] = $row->nssf_no;
                $nestedData['action'] =  "<a href='" . route('PayrollAbsend.Create',['id'=> $row->id])." '><button class='btn btn-primary'>Proceed</button></a>";
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

  public function PayrollAbsend(Request $request,$payID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $empData = Employee::where('id',$payID)->first();
    $absentData = Absent::where('emp_id',$payID)->get();
    $absentEdit = Absent::where('id',$request->Edit)->first();
    return view('admin.Absent.manage',compact('empData','title','absentData','absentEdit'));
  }



     public function CreateAbsent(Request $request){
         // dd($request->all());
      try{
             $validator = Validator::make($request->all(), [
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'absent_days' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $monthAbsent = new Absent;
                $monthAbsent->emp_id = $request->emp_id;
                $monthAbsent->month = $request->month;
                $monthAbsent->year = $request->year;
                $monthAbsent->absent_days = $request->absent_days;
                $monthAbsent->save();
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

     public function PayrollAbsendEdit(Request $request,$PayrollAbsendEdit){
         // dd($request->all());
      try{
             $validator = Validator::make($request->all(), [
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'absent_days' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $monthAbsentUpdate = Absent::where('id',$PayrollAbsendEdit)->first();
                $monthAbsentUpdate->emp_id = $request->emp_id;
                $monthAbsentUpdate->month = $request->month;
                $monthAbsentUpdate->year = $request->year;
                $monthAbsentUpdate->absent_days = $request->absent_days;
                $monthAbsentUpdate->save();
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

     public function PayrollAbsendDelete($deleteID){
        Absent::where('id',$deleteID)->delete();
        Session::flash('success', 'Record deleted successfully.');
       return redirect()->back(); 
     }

}