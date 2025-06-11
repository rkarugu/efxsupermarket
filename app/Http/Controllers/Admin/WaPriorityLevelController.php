<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPriorityLevel;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class WaPriorityLevelController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'priority-level';
        $this->title = 'Priority Level';
        $this->pmodule = 'priority-level';
    } 
    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaPriorityLevel::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.priority_level.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
        {
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.priority_level.create',compact('title','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function store(Request $request)
    {
        $validations = Validator::make($request->all(),[
            'title'=>'required|max:250',
            'description'=>'required|max:250',
        ]);
        if ($validations->fails()) {
            return response()->json([
                'result'=>0,
                'errors'=>$validations->errors(),
            ]);
        }
        $new = new WaPriorityLevel;
        $new->title = $request->title;
        $new->description = $request->description;
        $new->save();
        return response()->json([
            'result'=>1,
            'message'=>'Priority Level Added successfully',
            'location'=>route($this->model.'.index'),
        ]);
    }

    public function edit($id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
        {
            $pack = WaPriorityLevel::findOrFail($id);
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.priority_level.edit',compact('title','model','pack','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }
    public function update($id,Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(!isset($permission[$pmodule.'___edit']) && !$permission == 'superadmin')
        {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted: You dont have enough permissions',
            ]);
        }
        $validations = Validator::make($request->all(),[
            'title'=>'required|max:250',
            'description'=>'required|max:250',
        ]);
        if ($validations->fails()) {
            return response()->json([
                'result'=>0,
                'errors'=>$validations->errors(),
            ]);
        }
        $new = WaPriorityLevel::findOrFail($id);
        $new->title = $request->title;
        $new->description = $request->description;
        $new->save();
        return response()->json([
            'result'=>1,
            'message'=>'Priority Level Updated successfully',
            'location'=>route($this->model.'.index'),
        ]);
    }

    public function destroy($id,Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(!isset($permission[$pmodule.'___delete']) && !$permission == 'superadmin')
        {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted: You dont have enough permissions',
            ]);
        }
        $new = WaPriorityLevel::findOrFail($id);
        // if($new->category_relation->count() > 0){
        //     return response()->json([
        //         'result'=>0,
        //         'message'=>'Restricted: Invalid Request',
        //     ]);
        // }
        $new->delete();
        return response()->json([
            'result'=>1,
            'message'=>'Priority Level deleted successfully',
            'location'=>route($this->model.'.index'),
        ]);
    }

    public function dropdown_search(Request $request){
        $l = '';
        if($request->id){
            $l = "AND category_id != ".$request->id;
        }
        return WaPriorityLevel::where(function($e) use ($request){
            if($request->search){
                $e->where('title','LIKE', "%$request->search%");
            }
        })
        // ->having(DB::RAW("(select count(*) from wa_inventory_category_sub_category_relation where sub_category_id = wa_priority_level.id ".$l.")"),'=','0')
        ->get();
    }
}