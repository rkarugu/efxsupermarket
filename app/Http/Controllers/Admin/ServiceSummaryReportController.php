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

class ServiceSummaryReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'servicehistoryreport';
        $this->title = 'Service History Summary';
        $this->pmodule = 'servicehistoryreport';
       
    } 

    public function index(Request $request){

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        
        $from_date=isset($request->from)?$request->from:date('Y-m-01');
        $to_date=isset($request->to)?$request->to:date('Y-m-d');



        $lists = Vehicle::select(['*',
            DB::RAW('(select COUNT(servicehistory.id) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_entry'),


            DB::RAW('(select SUM(servicehistory.parts) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_parts'),
            
            DB::RAW('(select SUM(servicehistory.labor) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_labor'),


            DB::RAW('(select SUM(servicehistory.total) from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_total'),

            DB::RAW('(select servicehistory.start_date from servicehistory where servicehistory.vehicle=vehicle_list.id and servicehistory.start_date BETWEEN "'.$from_date.'" AND "'.$to_date.'" ORDER BY servicehistory.id DESC LIMIT 1 ) as last_service_date'),
            
            DB::RAW('(select COUNT(servicetask.id) from servicetask where servicetask.created_at BETWEEN "'.$from_date.'" AND "'.$to_date.'") as vehicle_service_task'),
        ]);
        
        
        $lists = $lists->where('vehicle_list.status','active')->orderBy('vehicle_list.id', 'desc')->groupBy('vehicle_list.id')->get();

        $total_service_entry=$total_service_parts=$total_service_labor=$total_service_total=$total_service_task=$grand_total=0; 
        if($lists->count()>0){
            foreach ($lists as $key => $value) {
                $vehicle_service_entry=(int)$value->vehicle_service_entry??0;
                $vehicle_service_parts=(int)$value->vehicle_service_parts??0;
                $vehicle_service_labor=(int)$value->vehicle_service_labor??0;
                $vehicle_service_total=(int)$value->vehicle_service_total??0;
                $vehicle_service_task=(int)$value->vehicle_service_task??0;

                $total_service_entry+=$vehicle_service_entry;
                $total_service_parts+=$vehicle_service_parts;
                $total_service_labor+=$vehicle_service_labor;
                $total_service_total+=$vehicle_service_total;
                $total_service_task+=$vehicle_service_task;
            }    
        }

      
        $myData = [];

        if($request->manage && $request->manage=="PDF"){
            $pdf = PDF::loadView('admin.servicehistoryreport.print',compact('title','lists','model','breadcum','pmodule','permission','request','total_service_entry','total_service_parts','total_service_labor','total_service_total','total_service_task'));
            return $pdf->download('Service_History_Summary_Report_'.date('Y_m_d_h_i_s').'.pdf');
        }

        return view('admin.servicehistoryreport.index',compact('title','model','lists','total_service_entry','total_service_parts','total_service_labor','total_service_total','total_service_task'));

    }


    public function pdfview(){ 
        $servicehistoryreport = Vehicle::all(); 
        return view('servicehistoryreport.pdf',compact('servicehistoryreport'));
    }



    public function createPDF(Request $request){  
        $servicehistoryreport = Vehicle::with('ServiceCost','FuelCost')->get();      
        $pdf = PDF::loadView('admin/servicehistoryreport/pdf',['servicehistoryreport'=>$servicehistoryreport])->setPaper('a4', 'portrait');
        return $pdf->download('OperatingAssigement.pdf');
    }
}


   