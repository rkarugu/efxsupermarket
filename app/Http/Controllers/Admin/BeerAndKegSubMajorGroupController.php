<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\BeerKegCategory;
use App\Model\Category;
use App\Model\BeerAndKegCategoryRelation;





use App\Http\Requests\Admin\SubMajorGroupAddRequest;

use DB;
use Session;

class BeerAndKegSubMajorGroupController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'beer-and-keg-sub-major-group';
        $this->title = 'Beer And Keg Sub Major Group';
        $this->pmodule = 'beer-and-keg-sub-major-group';
    } 

    public function index()
    {
        
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = BeerKegCategory::whereLevel(1)->orderBy('id', 'DESC')->get();
            $breadcum = [$title =>route($model.'.index'),'Listing'=>''];
            return view('admin.beerandkeg.index',compact('title','lists','model','breadcum','permission','pmodule'));
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
            $getParentList = Category::where('id','4')->pluck('name','id');
            $breadcum = [$this->title=>route($model.'.index'),'Add'=>''];
            return view('admin.beerandkeg.create',compact('title','model','breadcum','getParentList'));
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
            $row = new BeerKegCategory();
            $row->name= $request->name;
            $row->description= $request->description;
            $row->level = 1;
            
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'beerandkeg','100');
                $row->image= $image;
            }
            $row->save();
            BeerAndKegCategoryRelation::updateOrCreate(
                    ['beer_and_keg_category_id' => $row->id],  ['parent_id' => $request->parent_id]
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
                $row =  BeerKegCategory::whereSlug($slug)->first();
                if($row)
                {
                   $getParentList = Category::where('id','4')->pluck('name','id');
                    $title = 'Edit '.$this->title;
                    $model = $this->model; 
                    $breadcum = [$this->title =>route($model.'.index'),'Edit'=>''];
                    return view('admin.beerandkeg.edit',compact('title','model','breadcum','row','getParentList')); 
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
            
            $row =  BeerKegCategory::whereSlug($slug)->first();
            if($row) 
            {
                $row->name= $request->name;
                 $row->description= $request->description;
                $previous_row = $row;
                if($request->file('image_update'))
                {
                    $file = $request->file('image_update');
                    $image = uploadwithresize($file,'beerandkeg','100');
                   
                    if($previous_row->image)
                    {
                        unlinkfile('beerandkeg',$previous_row->image);
                    }
                    $row->image= $image; 
                }
                
                $row->save();

                 BeerAndKegCategoryRelation::updateOrCreate(
                    ['beer_and_keg_category_id' => $row->id],  ['parent_id' => $request->parent_id]
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
        
    }

    
}
