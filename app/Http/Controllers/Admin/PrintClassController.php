<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PrintClass;
use App\Model\FoodItemsPrintClassRelation;
use App\Http\Requests\Admin\RestaurentAddRequest;
use App\Http\Requests\Admin\RestaurentUpdateRequest;
use DB;
use Session;

class PrintClassController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'print-classes';
        $this->title = 'Print Class';
        $this->pmodule = 'print-classes';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = PrintClass::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.print_class.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.print_class.create',compact('title','model','breadcum'));
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
          
                $row = new PrintClass();
                $row->name= strtoupper($request->name);
                $row->jump_to_status= 'PREPARATION';
                 $row->ip= ($request->ip);
                 $row->port= ($request->port);
               
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
                $row =  PrintClass::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.print_class.edit',compact('title','model','breadcum','row')); 
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
           
                $row =  PrintClass::whereSlug($slug)->first();
                
                 $row->name= strtoupper($request->name);
                 $row->ip= ($request->ip);
                 $row->port= ($request->port);
                
               
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
            
           $row = PrintClass::whereSlug($slug)->first();
           $is_exist = FoodItemsPrintClassRelation::where('print_class_id',$row->id)->first();
           if($is_exist)
           {
                Session::flash('warning', 'Please delete their child data first');
                return redirect()->back();
           }
           else
           {
                PrintClass::whereSlug($slug)->delete();
                Session::flash('success', 'Deleted successfully.');
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
