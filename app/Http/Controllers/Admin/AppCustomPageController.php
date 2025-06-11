<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AppCustomPage;
use DB;
use Session;

class AppCustomPageController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'app-custom-pages';
        $this->title = 'App Custom Pages';
        $this->pmodule = 'app-custom-pages';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = AppCustomPage::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.appcustomepages.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.appcustomepages.create',compact('title','model','breadcum'));
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

            $row = new AppCustomPage();
            $row->title= $request->title;
            $description = [];
            $counter = 0;

            foreach ($request->heading as $key => $heading) {

                if($heading != "" && isset($request->description[$key]) && $request->description[$key] != "")
                {
                    $description[$counter]['heading'] = $heading;
                    $description[$counter]['description'] = $request->description[$key];
                    $counter++;

                }
                
            }
            $row->description= json_encode($description);

           
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'app_custom_pages','200');
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


    public function show($slug)
    {
       $row =  AppCustomPage::whereSlug($slug)->first();
       return view('admin.appcustomepages.show',compact('row')); 
               
    }

    public function showthepage($slug)
    {
       $row =  AppCustomPage::whereSlug($slug)->first();
       return view('admin.appcustomepages.show',compact('row')); 
               
    }

    


    public function edit($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  AppCustomPage::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.appcustomepages.edit',compact('title','model','breadcum','row')); 
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
            $row =  AppCustomPage::whereSlug($slug)->first();
            $previous_row = $row;
            $row->title= $request->title;
           
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'app_custom_pages','200');
                if($previous_row->image)
                {
                    unlinkfile('app_custom_pages',$previous_row->image);
                }
                $row->image= $image;
            }

            $description = [];
            $counter = 0;

            foreach ($request->heading as $key => $heading) {

                if($heading != "" && isset($request->description[$key]) && $request->description[$key] != "")
                {
                    $description[$counter]['heading'] = $heading;
                    $description[$counter]['description'] = $request->description[$key];
                    $counter++;

                }
                
            }
            $row->description= json_encode($description);
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
