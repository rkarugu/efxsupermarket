<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Advertisement;
use DB;
use Session;

class AdvertisementController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'advertisements';
        $this->title = 'Advertisements';
        $this->pmodule = 'advertisements';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Advertisement::orderBy('display_order', 'ASC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.advertisements.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.advertisements.create',compact('title','model','breadcum'));
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
            $row = new Advertisement();
            $row->title= $request->title;
            $row->display_order= $request->display_order;
           
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'advertisements','200');
                $row->image= $image;
            }
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
                $row =  Advertisement::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.advertisements.edit',compact('title','model','breadcum','row')); 
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
            $row =  Advertisement::whereSlug($slug)->first();
            $previous_row = $row;
            $row->title= $request->title;
            $row->display_order= $request->display_order;
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'advertisements','200');
                if($previous_row->image)
                {
                    unlinkfile('advertisements',$previous_row->image);
                }
                $row->image= $image;
            }
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
            $row = Advertisement::whereSlug($slug)->first();
            Advertisement::whereSlug($slug)->delete();
            if($row->image)
            {
                unlinkfile('advertisements',$row->image);
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
