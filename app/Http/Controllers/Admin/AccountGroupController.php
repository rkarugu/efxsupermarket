<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaAccountGroup;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class AccountGroupController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'account-groups';
        $this->title = 'Account Groups';
        $this->pmodule = 'account-groups';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaAccountGroup::orderBy('sequence_in_tb', 'asc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.accountgroup.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.accountgroup.create',compact('title','model','breadcum'));
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
                'group_name' => 'required|max:255',
                'sequence_in_tb' => 'required|unique:wa_account_groups',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
          
                $row = new WaAccountGroup();
                $row->group_name= strtoupper($request->group_name);
                $row->wa_account_section_id= $request->wa_account_section_id;
                $row->profit_and_loss= $request->profit_and_loss;
                $row->sequence_in_tb= $request->sequence_in_tb;
                $row->parent_id= $request->parent_id?$request->parent_id:null;
                $row->is_parent= $request->parent_id?'0':'1';
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
                $row =  WaAccountGroup::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.accountgroup.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaAccountGroup::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
                'group_name' => 'required|max:255',
                'sequence_in_tb' => 'required|unique:wa_account_groups,sequence_in_tb,' . $row->id,
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

               
                $row->group_name= strtoupper($request->group_name);
                $row->wa_account_section_id= $request->wa_account_section_id;
                $row->profit_and_loss= $request->profit_and_loss;
                $row->sequence_in_tb= $request->sequence_in_tb;
                $row->parent_id= $request->parent_id?$request->parent_id:null;
                $row->is_parent= $request->parent_id?'0':'1';
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
            
            WaAccountGroup::whereSlug($slug)->delete();
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
