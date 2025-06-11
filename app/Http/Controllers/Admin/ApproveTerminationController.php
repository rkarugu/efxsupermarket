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
use App\Model\ApporveTermnation;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;


class ApproveTerminationController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'ApproveTermination';
        $this->title = 'Employee';
        $this->pmodule = 'ApproveTermination';
        $this->pageUrl = 'ApproveTermination';

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
            return view('admin.ApproveTermination.index',compact('title','lists','model','breadcum','pmodule','permission'));
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

        $data_query =  Employee::where([['schedule_termination_status',true],['status','Active']])->select('wa_employee.*');
    
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
                $separation_termnationData = SeparationTermnation::where('emp_id',$row->id)->first();
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['staff_number'] = $row->staff_number;
                $nestedData['first_name'] = $row->first_name;
                $nestedData['last_name'] = $row->last_name;
                $nestedData['termination_date'] = @$separation_termnationData->termination_date;
                $nestedData['department_id'] = $depData->department_name;
                $nestedData['job_title'] = $job_Data->job_title;
                $nestedData['date_employed'] = $row->date_employed;
                $nestedData['date_of_birth'] = $row->date_of_birth;
                $nestedData['branch_id'] = $branch_Data->branch;
                $nestedData['action'] =  "<a href='" . route('ApproveTermination.Create',['id'=> $row->id])."'><button class='btn btn-primary'>Approve Termination</button></a>";
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

  public function ApporveTermnationCreate($emp_id){
    $separationTermnation = SeparationTermnation::where('emp_id',$emp_id)->first();
    $ApporveTermnation = ApporveTermnation::where('emp_id',$emp_id)->get();
  	$dataEmp3 = Employee::where('id',$emp_id)->first();
  	$separationTermnationData = ApporveTermnation::where('emp_id',$emp_id)->get();
  	$termination_types = TerminationTypes::pluck('separation_type','id');
  	  $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
  	return view('admin.ApproveTermination.create',compact('dataEmp3','title','termination_types','separationTermnationData','separationTermnation','ApporveTermnation'));
  }

  public function ApporveTermnationStore(Request $request){
  	try{
             $validator = Validator::make($request->all(), [
                'type_of_termination' => 'required|max:255',
                'termination_date' => 'required',
                'last_day_worked' => 'required',
                'eligible_for_rehire' => 'required',
                'notice_period' => 'required',
                'reason' => 'required',
                'comment' => 'required',
                'notice_given' => 'required',
                'cleared' => 'required',
                'termination_letter' => 'required',
                'termination_clearance' => 'required',
                'termination_service' => 'required',
                ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
  	        $apporveTermnationCreate = new ApporveTermnation;

             if (!empty($request->termination_letter)) {
              $image = $request->file('termination_letter');
              $name2 = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/uploads/ApporveTermnation');
              $image->move($destinationPath, $name2);
            }
            if (!empty($request->termination_clearance)) {
              $image = $request->file('termination_clearance');
              $termination_clearance = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/uploads/ApporveTermnation');
              $image->move($destinationPath, $termination_clearance);
            }
            if (!empty($request->termination_service)) {
              $image = $request->file('termination_service');
              $termination_service = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/uploads/ApporveTermnation');
              $image->move($destinationPath, $termination_service);
            }

            $apporveTermnationCreate->emp_id = $request->emp_id;
            $apporveTermnationCreate->type_of_termination = $request->type_of_termination;
            $apporveTermnationCreate->termination_date = $request->termination_date;
            $apporveTermnationCreate->last_day_worked = $request->last_day_worked;
            $apporveTermnationCreate->termination_letter = $name2;
            $apporveTermnationCreate->termination_clearance = $termination_clearance;
            $apporveTermnationCreate->termination_service = $termination_service;
            $apporveTermnationCreate->notice_period = $request->notice_period;
            $apporveTermnationCreate->reason = $request->reason;
            $apporveTermnationCreate->comment = $request->comment;
            
            if (!empty($request->eligible_for_rehire)) {
            	$apporveTermnationCreate->eligible_for_rehire = 'On';
            }else{
              $apporveTermnationCreate->eligible_for_rehire = 'Off';
            }  
            if (!empty($request->notice_given)) {
                $apporveTermnationCreate->notice_given = 'On';
            }else{
              $apporveTermnationCreate->notice_given = 'Off';
            }
            if (!empty($request->cleared)) {
                $apporveTermnationCreate->cleared = 'On';
            }else{
              $apporveTermnationCreate->cleared = 'Off';
            }
            
            if ($apporveTermnationCreate->save()) {
                $empUpdate = Employee::where('id',$request->emp_id)->first();
                $empUpdate->approve_termination = true;
                $empUpdate->status = 'DeActive';

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

     public function Delete2($deleteData333){
        try
         {
             ApporveTermnation::where('id',$deleteData333)->delete();
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
