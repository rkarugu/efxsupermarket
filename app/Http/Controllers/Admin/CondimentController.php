<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Condiment;
use DB;
use Session;

class CondimentController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'condiments';
        $this->title = 'Condiments';
        $this->pmodule = 'member';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Condiment::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.condiments.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        //     $pluNumberList = $this->getPluList();
            return view('admin.condiments.create',compact('title','model','breadcum','pluNumberList'));
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
            $row = new Condiment();
            $row->title= $request->title;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'condiments','200');
                $row->image= $image;
            }
            $row->plu_number = $request->plu_number?$request->plu_number:null;
            $row->save();
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
                $row =  Condiment::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                  //   $pluNumberList = $this->getPluList();
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.condiments.edit',compact('title','model','breadcum','row','pluNumberList')); 
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
            $row =  Condiment::whereSlug($slug)->first();
            $previous_row = $row;
            $row->title= $request->title;
            
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'condiments','200');
                if($previous_row->image)
                {
                    unlinkfile('condiments',$previous_row->image);
                }
                $row->image= $image;
            }
             $row->plu_number = $request->plu_number?$request->plu_number:$row->plu_number;
            $row->save();
           
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
            $row = Condiment::whereSlug($slug)->first();
            Condiment::whereSlug($slug)->delete();
            if($row->image)
            {
                unlinkfile('condiments',$row->image);
            }    
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
