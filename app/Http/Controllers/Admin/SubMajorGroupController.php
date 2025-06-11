<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Model\Restaurant;
use App\Http\Requests\Admin\SubMajorGroupAddRequest;
use App\Http\Requests\Admin\RestaurentUpdateRequest;
use DB;
use Session;

class SubMajorGroupController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'sub-major-groups';
        $this->title = 'Sub major Group';
        $this->pmodule = 'sub-major-groups';
    } 

    public function index()
    {
        
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Category::whereLevel(1)->orderBy('id', 'DESC')->get();
            $breadcum = [$title =>route($model.'.index'),'Listing'=>''];
            return view('admin.submajorgroups.index',compact('title','lists','model','breadcum','permission','pmodule'));
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
            $getParentList = $this->getParentList(0);
            $restaurant = Restaurant::pluck('name','id')->toArray();
            $breadcum = [$this->title=>route($model.'.index'),'Add'=>''];
            return view('admin.submajorgroups.create',compact('title','restaurant','model','breadcum','getParentList'));
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
            $row->restaurant_id= $request->restaurant_id;
            $row->level=1;
            
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'submajorgroups','100');
                $row->image= $image;
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
                    $getParentList = $this->getParentList(0);
                    $title = 'Edit '.$this->title;
                    $model = $this->model; 
                    $restaurant = Restaurant::pluck('name','id')->toArray();
                    $breadcum = [$this->title =>route($model.'.index'),'Edit'=>''];
                    return view('admin.submajorgroups.edit',compact('title','restaurant','model','breadcum','row','getParentList')); 
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
                 $row->restaurant_id= $request->restaurant_id;

                $previous_row = $row;
                if($request->file('image_update'))
                {
                    $file = $request->file('image_update');
                    $image = uploadwithresize($file,'submajorgroups','100');
                   
                    if($previous_row->image)
                    {
                        unlinkfile('submajorgroups',$previous_row->image);
                    }
                    $row->image= $image; 
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
                $is_valid = $this->canWeDeleteThis('SUBMAJORGROUP',$row->id);
                if($is_valid== true)
                {
                    Category::whereSlug($slug)->delete();
                    if($row->image)
                    {
                        unlinkfile('submajorgroups',$row->image);  
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
