<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PrintClassUser;
use App\Http\Requests\Admin\PrintClassUserAddRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use DB;
use App\Model\TableManager;
use App\Model\EmployeeTableAssignment;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;  
class PrintClassUserController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'print-class-users';
        $this->title = 'Print Class User';
         $this->pmodule = 'print-class-users';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title =  $this->title;
            $model = $this->model;
            $lists = PrintClassUser::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.printclassusers.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


       public function changePassword($slug)
    {
       
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___change_password']) || $permission == 'superadmin')
            {
                $row =  PrintClassUser::whereSlug($slug)->first();
                if($row)
                {
                     $model = $this->model; 
                    $title = 'Change Password '.$this->title;
                    $breadcum = [$this->title=>route($model.'.index'),'Change Password'=>''];
                   
                    //echo 'here';
                    return view('admin.printclassusers.change_password',compact('title','model','breadcum','row')); 

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


    public function postchangePassword(Request $request,$slug)
    {
        $rules = array(
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {

            Session::flash('warning', 'Credentials does not match with new password');
            return redirect()->back();
        }
        else
        {
                $model = $this->model;
                $row =  PrintClassUser::whereSlug($slug)->first();
                $row->password = Hash::make(Input::get('new_password'));
                $row->save();
                Session::flash('success', 'Password updated successfully.');
                return redirect()->route($model.'.index');
          // Hash::make(Input::get('new_password'))
        }
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $restroList = $this->getRestaurantList();
            $printClassList = $this->getAllPrintClassesName();
            $breadcum = [$this->title=>route($model.'.index'),'Add'=>''];
            return view('admin.printclassusers.create',compact('title','model','breadcum','restroList','printClassList'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    

    public function store(PrintClassUserAddRequest $request)
    {
        try
        {
            $row = new PrintClassUser();
            $row->name= $request->name;
            $row->print_class_id=$request->print_class_id;
            $row->restaurant_id=$request->restaurant_id;
            $row->username=$request->username;
            $row->password=bcrypt($request->password);
            $row->can_cancle_item=$request->can_cancle_item?'1':'0';
            $row->can_print_bill=$request->can_print_bill?'1':'0';
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
                $row =  PrintClassUser::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model; 
                    $restroList = $this->getRestaurantList();
                    $printClassList = $this->getAllPrintClassesName();
                    return view('admin.printclassusers.edit',compact('title','model','breadcum','row','restroList','printClassList')); 
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
        $row =  PrintClassUser::whereSlug($slug)->first();
        //dd($request);
        try
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'print_class_id' => 'required',
                'restaurant_id' => 'required',
                'username' => 'required|unique:print_class_users,username,' . $row->id
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
              
                $row->name= $request->name;
                $row->print_class_id=$request->print_class_id;
                $row->restaurant_id=$request->restaurant_id;
                $row->username=$request->username;
                $row->can_cancle_item=$request->can_cancle_item?'1':'0';
                 $row->can_print_bill=$request->can_print_bill?'1':'0';

                
                
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
           
            PrintClassUser::whereSlug($slug)->delete();
            
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function changeStatus($slug, $status)
    {
        try 
        {
            $row = PrintClassUser::whereSlug($slug)->first();
            if($row)
            {
                $row->status = $status=='1'?'0':'1';
                $row->save();
                Session::flash('success', 'Status update successfully');
                return redirect()->route($this->model.'.index');
            }
            else
            {
                Session::flash('warning', 'Invalid request');
                 return redirect()->route($this->model.'.index');
            }

        } 
        catch (Exception $ex) 
        {
            Session::flash('warning', 'Invalid request');
             return redirect()->route($this->model.'.index');
        }
    }

   

    
}
