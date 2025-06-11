<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ProfitAbilitySummary;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\User;

class ProfitAbilitySummaryController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'profit-ability-summary';
        $this->title = 'Profit Ability Summary';
        $this->pmodule = 'profit-ability-summary';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = ProfitAbilitySummary::where('status','1')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.'.$model.'.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            return view('admin.'.$model.'.create',compact('title','model','breadcum'));
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
                'route' => 'required',
                'date' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new ProfitAbilitySummary();
                $row->route=$request->route;
                $row->date=$request->date;
                $row->tonnage=$request->tonnage;
                $row->amount_ratio=$request->amount_ratio;
                $row->ctns=$request->ctns;
                $row->lines=$request->lines;
                $row->time_posted=date('H:i:s');
                $row->unmet=$request->unmet;
                $row->dd_per_week=$request->dd_per_week;
                $row->travel=$request->travel;
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


    public function edit($id)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  ProfitAbilitySummary::whereId($id)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.'.$model.'.edit',compact('title','model','breadcum','row')); 
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


    public function update(Request $request, $id)
    {
        try
        {
            $row =  ProfitAbilitySummary::whereId($id)->first();
             $validator = Validator::make($request->all(), [
                'route' => 'required',
                'date' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row->route=$request->route;
                $row->date=$request->date;
                $row->tonnage=$request->tonnage;
                $row->amount_ratio=$request->amount_ratio;
                $row->ctns=$request->ctns;
                $row->lines=$request->lines;
                $row->time_posted=date('H:i:s');
                $row->unmet=$request->unmet;
                $row->dd_per_week=$request->dd_per_week;
                $row->travel=$request->travel;
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


    public function destroy($id)
    {
        try
        {
            ProfitAbilitySummary::whereId($id)->update(['status'=>'0']);
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
