<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ReservationEmail;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;

class ReservationEmailController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'reservation-emails';
        $this->title = 'Reservation Emails';
        $this->pmodule = 'reservation-emails';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = ReservationEmail::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.reservationmails.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.reservationmails.create',compact('title','model','breadcum'));
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
               
                'phone_number' => 'required|numeric',
               
                'email' => 'required|email'
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

                $row = new ReservationEmail();
                $row->phone_number= $request->phone_number;
                 $row->slug= strtotime(date('Y-m-d H:i:s'));
                $row->email= $request->email;
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
                $row =  ReservationEmail::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.reservationmails.edit',compact('title','model','breadcum','row')); 
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
             $validator = Validator::make($request->all(), [
               
                'phone_number' => 'required|numeric',
               
                'email' => 'required|email'
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row =  ReservationEmail::whereSlug($slug)->first();
                
                $row->phone_number= $request->phone_number;
                $row->email= $request->email;
                
               
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
            
            ReservationEmail::whereSlug($slug)->delete();
           
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
