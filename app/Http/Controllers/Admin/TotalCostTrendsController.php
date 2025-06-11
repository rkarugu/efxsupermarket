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

class TotalCostTrendsController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'totalcosttrends';
        $this->title = 'Total Cost Trends';
        $this->pmodule = 'totalcosttrends';
       
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


        $cur_start_date=date('Y-m-01');
        $cur_to_date=date('Y-m-d');

        $last_month_from_date=date('Y-m-01',strtotime('-1 month'));
        $last_month_to_date=date('Y-m-t',strtotime('-1 month'));
        

        $second_last_month_from_date=date('Y-m-01',strtotime('-2 month'));
        $second_last_month_to_date=date('Y-m-t',strtotime('-2 month'));

       

        
        

        $lists = Vehicle::select(['*',
            DB::RAW('(select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'") as vehicle_service_cost'),
            
            DB::RAW('(select SUM(fuelentry.total) from fuelentry where fuelentry.vehicle=vehicle_list.id and fuelentry.fuel_entry_date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'") as vehicle_fuel_cost'),
            
            DB::RAW('(select SUM(expensehistory.amount) from expensehistory where expensehistory.vehicle=vehicle_list.id and expensehistory.date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'") as vehicle_other_cost'),

            DB::RAW('((select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'")

                + (select SUM(fuelentry.total) from fuelentry where fuelentry.vehicle=vehicle_list.id and fuelentry.fuel_entry_date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'")

                + (select SUM(expensehistory.amount) from expensehistory where expensehistory.vehicle=vehicle_list.id and expensehistory.date BETWEEN "'.$cur_start_date.'" AND "'.$cur_to_date.'")) as current_month_cost'),


            DB::RAW('( (select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$last_month_from_date.'" AND "'.$last_month_to_date.'") + (select SUM(fuelentry.total) from fuelentry where fuelentry.vehicle=vehicle_list.id and fuelentry.fuel_entry_date BETWEEN "'.$last_month_from_date.'" AND "'.$last_month_to_date.'") +(select SUM(expensehistory.amount) from expensehistory where expensehistory.vehicle=vehicle_list.id and expensehistory.date BETWEEN "'.$last_month_from_date.'" AND "'.$last_month_to_date.'")) as last_total_cost'),

            DB::RAW('( (select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$second_last_month_from_date.'" AND "'.$second_last_month_to_date.'") + (select SUM(fuelentry.total) from fuelentry where fuelentry.vehicle=vehicle_list.id and fuelentry.fuel_entry_date BETWEEN "'.$second_last_month_from_date.'" AND "'.$second_last_month_to_date.'") +(select SUM(expensehistory.amount) from expensehistory where expensehistory.vehicle=vehicle_list.id and expensehistory.date BETWEEN "'.$second_last_month_from_date.'" AND "'.$second_last_month_to_date.'")) as second_last_total_cost'),
        
        ])->with('ServiceCost','FuelCost');
        //->havingRaw('(vehicle_service_cost+vehicle_fuel_cost+vehicle_other_cost) as vehicle_total_cost')
        $lists = $lists->where('vehicle_list.status','active')->orderBy('vehicle_list.id', 'desc')->groupBy('vehicle_list.id')->get();

        //dd($lists->toArray());

        $grand_current_month_cost=$grand_last_total_cost=$grand_second_last_total_cost=0; 
        if($lists->count()>0){
            foreach ($lists as $key => $value) {
                $current_month_cost=(int)$value->current_month_cost??0;
                $last_total_cost=(int)$value->last_total_cost??0;
                $second_last_total_cost=(int)$value->second_last_total_cost??0;
               
                $grand_current_month_cost+=$current_month_cost;
                $grand_last_total_cost+=$last_total_cost;
                $grand_second_last_total_cost+=$second_last_total_cost;
            }    
        }

        //dd($lists->toArray());
        $myData = [];

        if($request->manage && $request->manage=="PDF"){
            $pdf = PDF::loadView('admin.totalcosttrends.print',compact('title','lists','model','breadcum','pmodule','permission','request','grand_current_month_cost','grand_last_total_cost','grand_second_last_total_cost'));
            
            //return view('admin.totalcosttrends.indexprint',compact('title','lists','model','breadcum','pmodule','permission','request','total_service_cost','total_fuel_cost','total_other_cost','grand_total'));


            return $pdf->download('Operating_Cost_Summary_Report_'.date('Y_m_d_h_i_s').'.pdf');
        }

        return view('admin.totalcosttrends.index',compact('title','model','lists','grand_current_month_cost','grand_last_total_cost','grand_second_last_total_cost'));

    }

}


   