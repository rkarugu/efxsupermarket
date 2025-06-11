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

class OperatingCostController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'operatingcostsummary';
        $this->title = 'Operating Cost Summary';
        $this->pmodule = 'operatingcostsummary';
       
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



        $lists = Vehicle::select(['*',
            DB::RAW('(select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_cost'),
            
            DB::RAW('(select SUM(fuelentry.total) from fuelentry where fuelentry.vehicle=vehicle_list.id and fuelentry.fuel_entry_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_fuel_cost'),
            
            DB::RAW('(select SUM(expensehistory.amount) from expensehistory where expensehistory.vehicle=vehicle_list.id and expensehistory.date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_other_cost'),
        ])->with('ServiceCost','FuelCost');
        
        /*$lists->leftJoin('servicehistory', 'vehicle_list.id','=', 'servicehistory.vehicle');
        $lists->leftJoin('fuelentry', 'vehicle_list.id','=', 'fuelentry.vehicle');
        $lists->leftJoin('expensehistory', 'vehicle_list.id','=', 'expensehistory.vehicle');*/

        /*if ($from_date){
            $query->whereBetween('age', [$ageFrom, $ageTo]);
            $lists = $lists->where('fuelentry.fuel_entry_date','>=',$from_date);
            $lists = $lists->where('expensehistory.date','>=',$from_date);
            $lists = $lists->where('servicehistory.completion_date','>=',$from_date);
        }
        if ($to_date) {
            $lists = $lists->where('fuelentry.fuel_entry_date','<=',$to_date);
            $lists = $lists->where('expensehistory.date','<=',$to_date);
            $lists = $lists->where('servicehistory.completion_date','<=',$to_date);
        }*/
        $lists = $lists->where('vehicle_list.status','active')->orderBy('vehicle_list.id', 'desc')->groupBy('vehicle_list.id')->get();

        $total_service_cost=$total_fuel_cost=$total_other_cost=$grand_total=0; 
        if($lists->count()>0){
            foreach ($lists as $key => $value) {
                $vehicle_service_cost=(int)$value->vehicle_service_cost??0;
                $vehicle_fuel_cost=(int)$value->vehicle_fuel_cost??0;
                $vehicle_other_cost=(int)$value->vehicle_other_cost??0;
                $total_service_cost+=$vehicle_service_cost;
                $total_fuel_cost+=$vehicle_fuel_cost;
                $total_other_cost+=$vehicle_other_cost;
                $total=(int)($vehicle_service_cost+$vehicle_fuel_cost+$vehicle_other_cost);
                $grand_total+=$total;
                $lists[$key]->total=$total;
            }    
        }

        //dd($lists->toArray());
        $myData = [];


        $chartPoints = array(
            array("label"=> "Grand Total", "y"=> $grand_total),
            array("label"=> "Service Cost", "y"=> $total_service_cost),
            array("label"=> "Fuel Cost", "y"=> $total_fuel_cost),
            array("label"=> "Other Cost", "y"=> $total_other_cost),
        );

        if($request->manage && $request->manage=="PDF"){
            $pdf = PDF::loadView('admin.operatingcostsummary.print',compact('title','lists','model','breadcum','pmodule','permission','request','total_service_cost','total_fuel_cost','total_other_cost','grand_total','chartPoints'));
            
            //return view('admin.operatingcostsummary.indexprint',compact('title','lists','model','breadcum','pmodule','permission','request','total_service_cost','total_fuel_cost','total_other_cost','grand_total'));


            return $pdf->download('Operating_Cost_Summary_Report_'.date('Y_m_d_h_i_s').'.pdf');
        }

        return view('admin.operatingcostsummary.index',compact('title','model','lists','total_service_cost','total_fuel_cost','total_other_cost','grand_total','chartPoints'));

    }


    public function pdfview(){ 
        $operatingcostsummary = Vehicle::all(); 
        return view('operatingcostsummary.pdf',compact('operatingcostsummary'));
    }



    public function createPDF(Request $request){  
        $operatingcostsummary = Vehicle::with('ServiceCost','FuelCost')->get();      
        $pdf = PDF::loadView('admin/operatingcostsummary/pdf',['operatingcostsummary'=>$operatingcostsummary])->setPaper('a4', 'portrait');
        return $pdf->download('OperatingAssigement.pdf');
    }
}


   