<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\WaDepartment;
use App\Model\JobTitle;
use App\Model\Employee;
use App\Model\SeparationTermnation;
use App\Model\TerminationTypes;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;


class SeparationController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'separation';
        $this->title = 'Employee';
        $this->pmodule = 'separation';
        $this->pageUrl = 'separation';

    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Branch::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.separation.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
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
            'id', 'staff_number','first_name','date_of_birth','job_title','branch_id','date_of_birth','date_employed','last_name'
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
                $nestedData['first_name'] = $row->first_name;
                $nestedData['last_name'] = $row->last_name;
                $nestedData['department_id'] = $depData->department_name;
                $nestedData['job_title'] = $job_Data->job_title;
                $nestedData['date_employed'] = $row->date_employed;
                $nestedData['date_of_birth'] = $row->date_of_birth;
                $nestedData['branch_id'] = $branch_Data->branch;
                $nestedData['action'] =  "<a href='" . route('separation.SeparationTermnation',['id'=> $row->id])." '><button title='Separation Termination' class='btn btn-primary'>Schedule Termination</button></a>";
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

  public function SeparationTermnation($emp_id){
  	$dataEmp3 = Employee::where('id',$emp_id)->first();
  	$separationTermnationData = SeparationTermnation::where('emp_id',$emp_id)->get();
  	$termination_types = TerminationTypes::pluck('separation_type','id');
  	  $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
  	return view('admin.separation.separationtermnation',compact('dataEmp3','title','termination_types','separationTermnationData'));
  }

  public function create(Request $request){
  	try{
             $validator = Validator::make($request->all(), [
                'termination_date' => 'required|max:255',
                'last_day_worked' => 'required',
                'type_of_termination' => 'required',
                'reason' => 'required',
                'further_detail' => 'required',
                'notice_period' => 'required',
                ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
  	        $createSeparationTermnation = new SeparationTermnation;
            $createSeparationTermnation->emp_id = $request->emp_id;
            $createSeparationTermnation->type_of_termination = $request->type_of_termination;
            $createSeparationTermnation->termination_date = $request->termination_date;
            $createSeparationTermnation->last_day_worked = $request->last_day_worked;
            $createSeparationTermnation->reason = $request->reason;
            $createSeparationTermnation->further_detail = $request->further_detail;
            $createSeparationTermnation->notice_period = $request->notice_period;
            if (!empty($request->eligible_for_rehire)) {
            	$createSeparationTermnation->eligible_for_rehire = 'On';
            }else{
              $createSeparationTermnation->eligible_for_rehire = 'Off';
            }
            if (!empty($request->notice_given)) {
            	$createSeparationTermnation->notice_given = 'On';
            }else{
              $createSeparationTermnation->notice_given = 'Off';
            }
               
                if ($createSeparationTermnation->save()) {
                    $empUpdate = Employee::where('id',$request->emp_id)->first();
                    $empUpdate->schedule_termination_status = true;
                    $empUpdate->save();
                }
                Session::flash('success', 'Record created successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }

  }

   public function delete($eaEmpContactsID){
        try
         {
             SeparationTermnation::where('id',$eaEmpContactsID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }
}
