<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaCurrencyManager;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class CurrencyManagerController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'currency-managers';
        $this->title = 'Currency Managers';
        $this->pmodule = 'currency-managers';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaCurrencyManager::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.currencymanagers.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.currencymanagers.create',compact('title','model','breadcum'));
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
                'ISO4217' => 'required|unique:wa_currency_managers',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
          
                $row = new WaCurrencyManager();
                $row->ISO4217= $request->ISO4217;
                $row->country= $request->country;
                $row->decimal_places= $request->decimal_places;
                $row->show_in_webshop= $request->show_in_webshop;
                $row->exchange_rate= $request->exchange_rate;
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
                $row =  WaCurrencyManager::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.currencymanagers.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaCurrencyManager::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
               
                'country' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

               
                //$row->ISO4217= $request->ISO4217;
                $row->country= $request->country;
                $row->decimal_places= $request->decimal_places;
                $row->show_in_webshop= $request->show_in_webshop;
                $row->exchange_rate= $request->exchange_rate;
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
            
            WaCurrencyManager::whereSlug($slug)->delete();
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
