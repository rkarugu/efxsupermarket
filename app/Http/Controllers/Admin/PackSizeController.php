<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PackSize;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class PackSizeController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'pack-size';
        $this->title = 'Pack Size';
        $this->pmodule = 'pack-size';
    } 
    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = PackSize::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.packsize.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.packsize.create',compact('title','model','breadcum','pmodule','permission'));
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
            'pack_size' => 'required'
        ]);
        if ($validations->fails()) {
            return response()->json([
                'result'=>0,
                'errors'=>$validations->errors(),
            ]);
        }
        $new = new PackSize;
        $new->title = $request->title;
        $new->description = $request->description;
        if($request->canorder === null){
            
            $new->can_order = false;
        }else{
            $new->can_order = $request->canorder;
        }

        if ($request->has('ctn')) {
            $new->ctn = true;
            $new->dzn = false;
        } else if ($request->has('dzn')) {
            $new->dzn = true;
            $new->ctn = false;
        }

        if ($request->pack_size == 'FULL PACK') {
            $new->pack_size = 'FULL PACK';
        } else {
            $new->pack_size = 'SMALL PACK';
        }

        $new->save();
        return response()->json([
            'result'=>1,
            'message'=>'Pack Size Added successfully',
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
            $pack = PackSize::findOrFail($id);
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.packsize.edit',compact('title','model','pack','breadcum','pmodule','permission'));
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
        $new = PackSize::findOrFail($id);
        $new->title = $request->title;
        $new->description = $request->description;
        $new->pack_size = $request->pack_size;
        if($request->canorder ===null){
            
            $new->can_order = false;
        }else{
            $new->can_order = $request->canorder;
        }

        if ($request->has('ctn')) {
            $new->ctn = true;
            $new->dzn = false;
        } else if ($request->has('dzn')) {
            $new->dzn = true;
            $new->ctn = false;
        }

        if ($request->pack_size == 'FULL PACK') {
            $new->pack_size = 'FULL PACK';
        } else {
            $new->pack_size = 'SMALL PACK';
        }

        $new->save();
        return response()->json([
            'result'=>1,
            'message'=>'Pack Size Updated successfully',
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
        $new = PackSize::findOrFail($id);
        $new->delete();
        return response()->json([
            'result'=>1,
            'message'=>'Pack Size deleted successfully',
            'location'=>route($this->model.'.index'),
        ]);
    }
}