<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CondimentGroup;
use App\Model\CondimentGroupRelation;
use DB;
use Session;

class CondimentGroupController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'condiment-groups';
        $this->title = 'Condiment Groups';
         $this->pmodule = 'groups';
    } 

    public function index()
    {
         $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = CondimentGroup::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.condimentsgroups.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $getCondimentList = $this->getCondimentList();
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.condimentsgroups.create',compact('title','model','breadcum','getCondimentList'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

    }


    public function store(Request $request)
    {
        try
        {
            $row = new CondimentGroup();
            $row->title= $request->title;
             $row->max_selection_limit= $request->max_selection_limit;
           
            $row->save();
            foreach($request->condiment_ids as $condiment_id)
            {
                
                CondimentGroupRelation::updateOrCreate(
                    ['condiment_group_id' => $row->id,'condiment_id' => $condiment_id]
                    );     
            }
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
                $row =  CondimentGroup::whereSlug($slug)->first();
                if($row)
                {
                     $getCondimentList = $this->getCondimentList();
                     //dd($row->getManyRelativeCondiments);
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.condimentsgroups.edit',compact('title','model','breadcum','row','getCondimentList')); 
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
            $row =  CondimentGroup::whereSlug($slug)->first();
           
            $row->title= $request->title;
            $row->max_selection_limit= $request->max_selection_limit;
            $row->save();
            CondimentGroupRelation::where('condiment_group_id',$row->id)->delete();
            foreach($request->condiment_ids as $condiment_id)
            { 
                CondimentGroupRelation::updateOrCreate(
                    ['condiment_group_id' => $row->id,'condiment_id' => $condiment_id]
                    );     
            }
           
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
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
            
            CondimentGroup::whereSlug($slug)->delete();
              
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
