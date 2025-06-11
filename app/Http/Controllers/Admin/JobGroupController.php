<?php

namespace App\Http\Controllers\Admin;

use DB;
use Excel;
use Session;
use Exception;
use App\Model\Gender;
use App\Model\JobGrade;
use App\Models\JobGroup;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class JobGroupController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'jobGroup';
        $this->title = 'JobGroup';
        $this->pmodule = 'jobGroup';
        $this->pageUrl = 'jobGroup';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.JobGroup.index',compact(
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
       return view('admin.JobGroup.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new JobGroup();
                $row->job_group= $request->job_group;
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
            'id', 'job_group', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  JobGroup::select('wa_job_group.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('job_group', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
                    
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
                $nestedData['job_group'] = $row->job_group;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('jobGroup.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('jobGroup.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  JobGroup::where('id',$editID)->first();
    return view('admin.JobGroup.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  JobGroup::where('id',$updata)->first();
 try{
                $upDate->job_group= $request->job_group;
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
            JobGroup::where('id',$slug)->delete();
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
    public function jobGroupList()
    {
        $jobGroups = JobGroup::withCount('jobLevels')->orderBy('name')->get();
        
        return response()->json($jobGroups);
    }

    public function jobGroupCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobGroup = JobGroup::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Group added successfully',
            'data' => $jobGroup
        ], 201);
    }

    public function jobGroupEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:job_groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobGroup = JobGroup::find($request->id);

            $jobGroup->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Group updated successfully',
            'data' => $jobGroup
        ]);
    }

    public function jobGroupDelete($id)
    {
        request()->validate([
            'id' => 'exists:job_groups,id'
        ]);
        
        $jobGroup = JobGroup::find($id);
        
        try {
            $jobGroup->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Group deleted successfully',
        ]);
    }


}