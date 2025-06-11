<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\User;
use App\Model\Vehicle;
use App\Model\Fuelentry;
use App\Model\ServiceHistory;
use Session;
use Excel;
use PDF;

class FuelSummaryReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'fuelsummary';
        $this->title = 'Fuel Summary Report';
        $this->pmodule = 'fuelsummary';
       
    } 

    public function modulePermissions($permission,$type){
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin'){
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }

    public function index(Request $request){

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }


        $from_date=isset($request->from)?$request->from:date('Y-m-01');
        $to_date=isset($request->to)?$request->to:date('Y-m-d');



        $lists = Fuelentry::query();
        
        /*$lists->leftJoin('servicehistory', 'vehicle_list.id','=', 'servicehistory.vehicle');*/

        if ($from_date){
            $lists = $lists->where('fuelentry.fuel_entry_date','>=',$from_date);
        }
        
        if ($to_date) {
            $lists = $lists->where('fuelentry.fuel_entry_date','<=',$to_date);
        }

        if ($request->vehicle && $request->vehicle!="All") {
            $lists = $lists->where('fuelentry.vehicle',$request->vehicle);
        }
        
        $lists = $lists->orderBy('fuelentry.id', 'desc')->get();

        $total_odometer=$total_price=$grand_total=$total_fuel_economy=0; 
        if($lists->count()>0){
            foreach ($lists as $key => $value) {
                $odometer=(int)$value->odometer??0;
                $fuel_economy=(int)$value->fuel_economy??0;
                $cost=(int)$value->price??0;
                $total=(int)$value->total??0;

                $total_odometer+=$odometer;
                $total_price+=$cost;
                $total_fuel_economy+=$fuel_economy;
                $grand_total+=$total;
            }    
        }

        //dd($lists->toArray());
        $myData = [];

        if($request->manage && $request->manage=="PDF"){
            $pdf = PDF::loadView('admin.fuelsummary.print',compact('title','lists','model','breadcum','pmodule','permission','request','grand_total','total_odometer','total_price','grand_total','total_fuel_economy'));
            return $pdf->download('Fuel_Summary_Report_'.date('Y_m_d_h_i_s').'.pdf');
        }
        return view('admin.fuelsummary.index',compact('title','model','lists','grand_total','total_odometer','total_price','grand_total','total_fuel_economy'));

    }


    public function pdfview(){ 
        $fuelsummary = Vehicle::all(); 
        return view('fuelsummary.pdf',compact('fuelsummary'));
    }



    public function createPDF(Request $request){  
        $fuelsummary = Vehicle::with('ServiceCost','FuelCost')->get();      
        $pdf = PDF::loadView('admin/fuelsummary/pdf',['fuelsummary'=>$fuelsummary])->setPaper('a4', 'portrait');
        return $pdf->download('OperatingAssigement.pdf');
    }
}


   