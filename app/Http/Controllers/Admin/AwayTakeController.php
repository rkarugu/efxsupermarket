<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AwayTake;
use App\Model\AwayTakeHit;
use DB;

use Session;

class AwayTakeController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'take-away';
        $this->title = 'Take Away';
        $this->pmodule = 'take-away';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = AwayTake::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.take_away.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
              $restroList = $this->getRestaurantList();
            return view('admin.take_away.create',compact('title','model','breadcum','restroList'));
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
            $row = new AwayTake();
            $row->title= $request->title;
            $row->restaurant_id= $request->restaurant_id;
            $row->url= $request->url;
            $row->slug= strtotime(date('Y-m-d H:i:s')).rand(11111111,99999999999999);
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
                $row =  AwayTake::whereSlug($slug)->first();
                if($row)
                {
                      $restroList = $this->getRestaurantList();
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.take_away.edit',compact('title','model','breadcum','row','restroList')); 
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
           
                $row =  AwayTake::whereSlug($slug)->first();
                
                $row->title= $request->title;
                $row->restaurant_id= $request->restaurant_id;
                $row->url= $request->url;
                
               
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
            AwayTake::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
                    
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function getHitsList(Request $request,$slug)
     {
        try
        {
            $is_exist = AwayTake::select('id','title')->whereSlug($slug)->first();
            if($is_exist)
            {
               $row = AwayTakeHit::where('away_take_id',$is_exist->id)->get();
                $title =$this->title ;
                $breadcum = [$this->title=>route($this->model.'.index'),$is_exist->title=>''];
                $model =$this->model;
                return view('admin.take_away.hitslist',compact('title','model','breadcum','row','restroList')); 
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
