<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSalesCommissionBand;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
class SalesCommissionBrandController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'sales-commission-bands';
        $this->title = 'Sales Commission Bands';
        $this->pmodule = 'sales-commission-bands';
    } 

    public function index(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___sales-commission-bands']) || $permission == 'superadmin')
        {

            $title = $this->title;
            $model = $this->model;
            $lists = WaSalesCommissionBand::orderBy('id', 'DESC')->get();
            if($request->has('action')){
	            $editdata = WaSalesCommissionBand::where('id',$request->get('id'))->first();	            
            }else{
	            $editdata = [];            
            }
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.sales_commission.index',compact('title','lists','model','breadcum','pmodule','permission','editdata'));
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
	    //echo "<pre>"; print_r($request->all()); die;
        try
        {
	        $model = 'sales-commission-bands';
	        if($request->has('id')){
		        $add		 		= WaSalesCommissionBand::where('id',$request->get('id'))->first();		        
	        }else{
		        $add		 		= new WaSalesCommissionBand();		        
	        }
		        $add->sales_from	= $request->get('sales_from');
		        $add->sales_to		= $request->get('sales_to');
		        $add->amount		= $request->get('amount');
		        $add->save();

	        if($request->has('id')){
	            Session::flash('success', "Sales Commission Updated Successfully.");
	            return redirect()->route($model.'.index');
	        }else{
	            Session::flash('success', "Sales Commission Added Successfully.");
	            return redirect()->back();
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
	        //
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
	        //
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
	        $model = 'sales-commission-bands';
			WaSalesCommissionBand::where('id',$slug)->delete();
            Session::flash('success', "Sales Commission Deleted Successfully.");
            return redirect()->route($model.'.index');

        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    
}
