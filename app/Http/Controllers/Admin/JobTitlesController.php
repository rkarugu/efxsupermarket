<?php

namespace App\Http\Controllers\Admin;

use DB;
use Excel;
use Session;
use Exception;
use App\Model\Gender;
use App\Model\JobGrade;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class JobTitlesController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'JobTitles';
        $this->title = 'Job Title';
        $this->pmodule = 'JobTitles';
        $this->pageUrl = 'JobTitles';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.JobTitle.index',compact(
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
       return view('admin.JobTitle.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new JobTitle();
                $row->job_title= $request->job_title;
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
            'id', 'job_title', 'description','min_salary','max_salary'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  JobTitle::select('wa_job_title.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('job_title', 'LIKE', "%{$search}%")
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
                $nestedData['job_title'] = $row->job_title;
                $nestedData['min_salary'] = $row->min_salary;
                $nestedData['max_salary'] = $row->max_salary;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('JobTitles.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('JobTitles.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  JobTitle::where('id',$editID)->first();
    return view('admin.JobTitle.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  JobTitle::where('id',$updata)->first();
 try{
                $upDate->job_title= $request->job_title;
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
            JobTitle::where('id',$slug)->delete();
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
    public function jobTitleList()
    {
        $jobTitles = JobTitle::with('jobLevel.jobGroup')->withCount('employees')->orderBy('name')->get();
        
        return response()->json($jobTitles);
    }

    public function jobTitleCreate(Request $request)
    {
        $data = $request->validate([
            'job_level_id' => 'required|integer|exists:job_levels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobTitle = JobTitle::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Title added successfully',
            'data' => $jobTitle
        ], 201);
    }

    public function jobTitleEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:job_titles,id',
            'job_level_id' => 'required|integer|exists:job_levels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobTitle = JobTitle::find($request->id);

            $jobTitle->update([
                'job_level_id' => $request->job_level_id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Title updated successfully',
            'data' => $jobTitle
        ]);
    }

    public function jobTitleDelete($id)
    {
        request()->validate([
            'id' => 'exists:job_titles,id'
        ]);
        
        $jobTitle = JobTitle::find($id);
        
        try {
            $jobTitle->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Title deleted successfully',
        ]);
    }
}