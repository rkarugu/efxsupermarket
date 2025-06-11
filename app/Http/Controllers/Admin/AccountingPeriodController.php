<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaAccountingPeriod;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\User;

class AccountingPeriodController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'accounting-periods';
        $this->title = 'Accounting Periods';
        $this->pmodule = 'accounting-periods';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaAccountingPeriod::orderBy('is_current_period', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.accountingperiods.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.accountingperiods.create',compact('title','model','breadcum'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    public function getItIsUnique($start,$end,$existing_id = null)
    {
        $row = WaAccountingPeriod::select('id')->where(function ($query) use ($start, $end) {

        $query->where(function ($q) use ($start, $end) {
            $q->where('start_date', '>=', $start)
               ->where('start_date', '<', $end);

        })->orWhere(function ($q) use ($start, $end) {
            $q->where('start_date', '<=', $start)
               ->where('end_date', '>', $end);

        })->orWhere(function ($q) use ($start, $end) {
            $q->where('end_date', '>', $start)
               ->where('end_date', '<=', $end);

        })->orWhere(function ($q) use ($start, $end) {
            $q->where('start_date', '>=', $start)
               ->where('end_date', '<=', $end);
        });

        })->first();
        if($row)
        {   
            if($existing_id)
            {
                if($row->id==$existing_id)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false; 
            }

        }
        else
        {
             return true; 
        }
    }

    public function setAccountingPeriodToAll($start,$end)
    {
       User::where('role_id','!=','11')->update(['accounting_period_start_date'=>$start,'accounting_period_end_date'=>$end]);
    }


    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'period_no' => 'required|unique:wa_accounting_periods',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
               if(strtotime($request->start_date) <= strtotime($request->end_date))
               {
                    $start = $request->start_date;
                     $end = $request->end_date;
                    
                  if($this->getItIsUnique($start,$end)==true)
                  {
                    $row = new WaAccountingPeriod();
                    $row->period_no= $request->period_no;
                    $row->start_date= $request->start_date;
                    $row->end_date= $request->end_date;
                    $row->is_current_period= $request->is_current_period?'1':'0';
                    $row->save();

                    if($row->is_current_period == '1')
                    {
                         WaAccountingPeriod::where('id','!=',$row->id)->update(['is_current_period'=>'0']);
                        $this->setAccountingPeriodToAll($start,$end);
                    }
                    Session::flash('success', 'Record added successfully.');
                    return redirect()->route($this->model.'.index');
                  }
                  else
                  {
                    Session::flash('warning', 'This accounting period already taken.');
                    return redirect()->back()->withInput();
                  }
                    
               }
               else
               {
                  Session::flash('warning', 'Invalid Date Selection.');
                    return redirect()->back()->withInput();
               }
               
               
           
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
                $row =  WaAccountingPeriod::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.accountingperiods.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaAccountingPeriod::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
              
                'period_no' => 'required|unique:wa_accounting_periods,period_no,' . $row->id,
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

               
              if(strtotime($request->start_date) <= strtotime($request->end_date))
               {
                    $start = $request->start_date;
                     $end = $request->end_date;
                    
                  if($this->getItIsUnique($start,$end,$row->id)==true)
                  {
                   
                    $row->period_no= $request->period_no;
                    $row->start_date= $request->start_date;
                    $row->end_date= $request->end_date;
                   // $row->is_current_period= $request->is_current_period?'1':'0';
                    $row->save();

                    if($row->is_current_period == '1')
                    {
                         WaAccountingPeriod::where('id','!=',$row->id)->update(['is_current_period'=>'0']);
                        $this->setAccountingPeriodToAll($start,$end);
                    }
                    Session::flash('success', 'Record added successfully.');
                    return redirect()->route($this->model.'.index');
                  }
                  else
                  {
                    Session::flash('warning', 'This accounting period already taken.');
                    return redirect()->back()->withInput();
                  }
                    
               }
               else
               {
                  Session::flash('warning', 'Invalid Date Selection.');
                    return redirect()->back()->withInput();
               }
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
            
            WaAccountingPeriod::whereSlug($slug)->delete();
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
