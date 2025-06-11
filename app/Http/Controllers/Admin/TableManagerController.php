<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TableManager;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class TableManagerController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'table-managers';
        $this->title = 'Table';
        $this->pmodule = 'table-managers';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = TableManager::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tablemanagers.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $restroList = $this->getRestaurantList();
           $tableBlockSection =  getTableBlockSection();
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.tablemanagers.create',compact('title','model','breadcum','restroList','tableBlockSection'));
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
                            
                            'name' => 'unique:table_managers,name,NULL,id,restaurant_id,'.$request->restaurant_id

                ]);
            if ($validator->fails()) 
                {
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                }
                else
                {
                     $row = new TableManager();
                $row->name= $request->name;
                $row->restaurant_id=$request->restaurant_id;
                $row->capacity=$request->capacity;
                $row->block_section=$request->block_section;
                $row->status='1';
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
                $row =  TableManager::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    $restroList = $this->getRestaurantList();
                     $tableBlockSection =  getTableBlockSection();
                    $booking_status_arr = ['FREE'=>'FREE','BLOCKED'=>'BLOCKED','BOOKED'=>'BOOKED'];
                    return view('admin.tablemanagers.edit',compact('title','model','breadcum','row','restroList','booking_status_arr','tableBlockSection')); 
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
             $row =  TableManager::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                            
                            'name' => 'unique:table_managers,name,'.$row->id.',id,restaurant_id,'.$request->restaurant_id

                ]);
            if ($validator->fails()) 
                {
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                }
                else
                {

            $row->name= $request->name;
            $row->restaurant_id=$request->restaurant_id;
            $row->capacity=$request->capacity;
            $row->booking_status=$request->booking_status;
            $row->block_section=$request->block_section;
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
            TableManager::whereSlug($slug)->delete();
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
            $row = TableManager::whereSlug($slug)->first();
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
