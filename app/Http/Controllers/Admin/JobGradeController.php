<?php

namespace App\Http\Controllers\Admin;

use DB;
use Excel;
use Session;
use Exception;
use App\Model\Gender;
use App\Models\JobGrade;
use Illuminate\Http\Request;
use App\Model\EmploymentStatus;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class JobGradeController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'JobGrade';
        $this->title = 'Job Grade';
        $this->pmodule = 'JobGrade';
        $this->pageUrl = 'JobGrade';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.JobGrade.index',compact(
                    'title','lists','model','breadcum','pmodule','permission'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function create(Request $request) {
       $title =$this->title; 
       $model = $this->model;
       return view('admin.JobGrade.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new JobGrade();
                $row->job_grade= $request->job_grade;
                $row->min_salary= $request->min_salary;
                $row->max_salary= $request->max_salary;
                $row->description= $request->description;
                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index');  
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'id', 'job_grade', 'description','min_salary','max_salary'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  JobGrade::select('wa_job_grade.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('job_grade', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('min_salary', 'LIKE', "%{$search}%")
                    ->orWhere('max_salary', 'LIKE', "%{$search}%");
                    
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
                $nestedData['job_grade'] = $row->job_grade;
                $nestedData['min_salary'] = $row->min_salary;
                $nestedData['max_salary'] = $row->max_salary;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('JobGrade.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('JobGrade.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
" . csrf_field() . "<span><button data-toggle='tooltip' title='Delete' type='submit' class='btn btn-danger small-btn'><i class='fa fa-trash' aria-hidden='true'></i></button></span></form>";
          
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

  

  public function edit(Request $request,$editID){
    $permission =  $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    $row =  JobGrade::where('id',$editID)->first();
    return view('admin.JobGrade.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  JobGrade::where('id',$updata)->first();
 try{
                $upDate->job_grade= $request->job_grade;
                $upDate->min_salary= $request->min_salary;
                $upDate->max_salary= $request->max_salary;
                $upDate->description= $request->description;
                $upDate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');  
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
  }


   public function delete($slug)
    {
        try
        {
            JobGrade::where('id',$slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    // API
    public function jobGradeList()
    {
        $jobGrades = JobGrade::with('jobLevel.jobGroup')->withCount('employees')->orderBy('name')->get();
        
        return response()->json($jobGrades);
    }

    public function jobGradeCreate(Request $request)
    {
        $data = $request->validate([
            'job_level_id' => 'required|integer|exists:job_levels,id',
            'name' => 'required|string|max:255',
            'min_salary' => 'required|integer',
            'max_salary' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        try {
            $jobGrade = JobGrade::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Grade added successfully',
            'data' => $jobGrade
        ], 201);
    }

    public function jobGradeEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wa_job_grade,id',
            'job_level_id' => 'required|integer|exists:job_levels,id',
            'name' => 'required|string|max:255',
            'min_salary' => 'required|integer',
            'max_salary' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        try {
            $jobGrade = JobGrade::find($request->id);

            $jobGrade->update([
                'job_level_id' => $request->job_level_id,
                'name' => $request->name,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Grade updated successfully',
            'data' => $jobGrade
        ]);
    }

    public function jobGradeDelete($id)
    {
        request()->validate([
            'id' => 'exists:job_grades,id'
        ]);
        
        $jobGrade = JobGrade::find($id);
        try {
            $jobGrade->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Grade deleted successfully',
        ]);
    }
}