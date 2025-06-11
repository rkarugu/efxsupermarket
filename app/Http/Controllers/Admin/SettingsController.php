<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\Setting;
use Mockery\CountValidator\Exception;
use Session;


class SettingsController extends Controller
{

    
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'settings';
        $this->title = 'Settings';
         $this->pmodule = 'settings';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Setting::get();
            $breadcum = [$this->title=>route($this->model.'.index'),'Listing'=>''];
            return view('admin.settings.index',compact('title','lists','model','breadcum'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        
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
                $row =  Setting::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $model = $this->model;
                    $breadcum = [$this->title =>route($model.'.index'),'Edit'=>''];
                    return view('admin.settings.edit',compact('title','model','breadcum','row'));
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
            $row =  Setting::whereSlug($slug)->first();
            $row->name= $request->name;

            if($row->parameter_type == 'happyhours')
            {
                $request->description = $request->start_from.'-'.$request->end_from;
            }
            $row->description=$request->description;
           
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
       
    }



}
