<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaAccountSection;
use App\Model\PaymentMethod;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class AccountSectionController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'account-sections';
        $this->title = 'Account Sections';
        $this->pmodule = 'account-sections';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaAccountSection::orderBy('section_number', 'asc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.accountsection.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.accountsection.create',compact('title','model','breadcum'));
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
             $validator = Validator::make($request->all(), [
                'section_name' => 'required|max:255',
                'section_number' => 'required|unique:wa_account_sections',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new WaAccountSection();
                $row->section_name= strtoupper($request->section_name);
                $row->section_number= $request->section_number;
                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }

               
           
            
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
                $row =  WaAccountSection::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.accountsection.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaAccountSection::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
                'section_name' => 'required|max:255',
                'section_number' => 'required|unique:wa_account_sections,section_number,' . $row->id,
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

             
                $row->section_name= strtoupper($request->section_name);
                $row->section_number= $request->section_number;
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
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
            
            WaAccountSection::whereSlug($slug)->delete();
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
