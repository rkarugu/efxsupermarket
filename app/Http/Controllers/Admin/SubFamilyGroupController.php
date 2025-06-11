<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Http\Requests\Admin\SubMajorGroupAddRequest;
use App\Http\Requests\Admin\RestaurentUpdateRequest;
use DB;
use Session;

class SubFamilyGroupController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'alcoholic-sub-family-groups';
        $this->title = 'Alcoholic Sub Family Group';
        $this->pmodule = 'alcoholic-sub-family-groups';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Category::whereLevel(4)->orderBy('id', 'DESC')->get();
            $breadcum = [$title =>route($model.'.index'),'Listing'=>''];
            return view('admin.subfamilygroups.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $getParentList = $this->getParentList('getalcoholicfamilyGroups');
            $getGlDetails = $this->getGLDetail();
            $breadcum = [$this->title=>route($model.'.index'),'Add'=>''];
            return view('admin.subfamilygroups.create',compact('title','model','breadcum','getParentList','getGlDetails'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(SubMajorGroupAddRequest $request)
    {
        try
        {
            $row = new Category();
            $row->name= $request->name;
            $row->description= $request->description;
            $row->level=4;
            $row->gl_account_no = $request->gl_account_no?$request->gl_account_no:null;
            
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'subfamilygroups','100');
                $row->image= $image;
            }
            if($request->allow_happy_hours)
            {
                $row->allow_happy_hours = '1';
            }
            $row->save();
            CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    );   
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.index'); 
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {
        
    }


    public function edit($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  Category::whereSlug($slug)->first();
                if($row)
                {
                    $getParentList = $this->getParentList('getalcoholicfamilyGroups');
                    $title = 'Edit '.$this->title;
                    $model = $this->model; 
                     $getGlDetails = $this->getGLDetail();
                    $breadcum = [$this->title =>route($model.'.index'),'Edit'=>''];
                    return view('admin.subfamilygroups.edit',compact('title','model','breadcum','row','getParentList','getGlDetails')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            } 
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
        try
        {
            
            $row =  Category::whereSlug($slug)->first();
            if($row) 
            {
                $row->name= $request->name;
                $row->description= $request->description;
                $previous_row = $row;
                $row->gl_account_no = $request->gl_account_no?$request->gl_account_no:$row->gl_account_no;
                if($request->file('image_update'))
                {
                    $file = $request->file('image_update');
                    $image = uploadwithresize($file,'subfamilygroups','100');
                   
                    if($previous_row->image)
                    {
                        unlinkfile('subfamilygroups',$previous_row->image);
                    }
                    $row->image= $image; 
                }

                $row->allow_happy_hours = '0';
            if($request->allow_happy_hours)
            {
                $row->allow_happy_hours = '1';
            }
                
                $row->save();

                 CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    );   
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
            }
            else
            {
                Session::flash('warning', 'Please choose a correct location');
                return redirect()->back()->withInput();
            }
            
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin')
            {

                $row = Category::whereSlug($slug)->first();
                $is_valid = $this->canWeDeleteThis('SUBFAMILYGROUP',$row->id);
                if($is_valid== true)
                {
                    Category::whereSlug($slug)->delete();
                    if($row->image)
                    {
                        unlinkfile('subfamilygroups',$row->image);  
                    }
                    Session::flash('success', 'Deleted successfully.');
                }
                else
                {
                    Session::flash('warning', 'Please delete related data');
                }
                return redirect()->back();
            }
            else
            {
                Session::flash('warning','Invalid Request');
                return redirect()->back();
            }
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    
}
