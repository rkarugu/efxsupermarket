<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\FdSeasonsAttribute;
use Excel;
use App\Model\Employee;
use App\Model\WaDepartment;
use App\Model\Branch;
use App\Model\JobTitle;
use App\Model\OvertimeHours;
use Illuminate\Support\Facades\Validator;

class OvertimeHoursController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Overtime Hours';
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
            return view('admin.OvertimeHours.index',compact(
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
                $nestedData['action'] =  "<a href='" . route('OvertimeHour.manage',['id'=> $row->id])." '><button class='btn btn-primary'>Add Overtime</button></a>";
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

  public function Manage($payID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $empData = Employee::where('id',$payID)->first();
    $overtimeData = OvertimeHours::where('emp_id',$payID)->get();
    return view('admin.OvertimeHours.manage',compact('empData','title','overtimeData'));
  }



     public function OvertimeHoursCreate(Request $request){
        // dd($request->all());
      try{
             $validator = Validator::make($request->all(), [
                'overtime_type' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'hours_worked' => 'required|integer',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $overtimeHoursCreate = new OvertimeHours;
                $overtimeHoursCreate->emp_id = $request->emp_id;
                $overtimeHoursCreate->overtime_type = $request->overtime_type;
                $overtimeHoursCreate->year = $request->year;
                $overtimeHoursCreate->month = $request->month;
                $overtimeHoursCreate->hours_worked = $request->hours_worked;
                $overtimeHoursCreate->save();
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

   public function OvertimeHoursDelete($deleteid){
     OvertimeHours::where('id',$deleteid)->delete();
         Session::flash('success', 'Record Deleted successfully.');
                return redirect()->back(); 
   }
}