<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\VehicleType;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\Make;
use App\Model\Modal;
use App\Model\Bodytype;
use App\Model\Expensehistory;
use App\Model\WaSupplier;
use App\Model\Issues;
use App\Model\Expensetype;
use App\Model\ServiceHistory;
use App\Model\InspectionHistory;
use App\Model\ServiceRemainder;
use App\Model\WaPoiStockSerialMoves;
use App\Model\WaPoiStockSerialMovesHistory;
use App\Model\Meterhistory;
use App\Model\TyreFitting;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class TyreFittingController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {

        $this->model = 'tyre_fitting';
        $this->title = 'Tyre Fitting';
        $this->pmodule = 'tyre_fitting';
    } 

     public function modulePermissions($permission,$type)
    {
        // $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }

  

    public function index(Request $request)
    {
        
    }



    public function tyre_fitting_dropdown(Request $request){
       
        $data = Vehicle::select(['id','vin_sn as text']);
        if($request->q){
            $data->where('vin_sn','LIKE',"%$request->q%");
            $data->orWhere('license_plate','LIKE',"%$request->q%");
            $data->orWhere('registration_state_provine','LIKE',"%$request->q%");
            $data->orWhere('year','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

    public function create(Request $request){



        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        

      



        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin'){

            $tyre_inventory_id=$request->tyre_inventory_id;
            $tyre_serials=[];
            $vehicle_reg_no="";
            $odometer=$request->odometer;
            if($request->manage=="filter" && $request->vehicle){
                
                $vehicle=Vehicle::findOrFail($request->vehicle);
                $vehicle_reg_no=$vehicle->registration_state_provine;
                $tyre_serials = WaPoiStockSerialMoves::where('wa_inventory_item_id',$tyre_inventory_id)->where('status','=','new_tyre_in_stock')->orWhere('status','=','retread_tyre_in_stock')->orderBy('created_at','DESC')->get();
                
                $allocated_data = WaPoiStockSerialMoves::where('vehicle_id',$request->vehicle)->where('status','=','in_motor_vehicle')->orderBy('created_at','DESC')->get();
            }

            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyre_fitting.create',compact('title','model','breadcum','pmodule','permission','tyre_fitting','tyre_serials','vehicle_reg_no','odometer','allocated_data'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


    public function store(Request $request){

        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }

        
        $last_odometer_reading=checkEnforceMeterReading($request->vehicle,'tyre_fitting');
        $next_odo_reading=$last_odometer_reading+1;
        $validator=Validator::make($request->all(),[
            'odometer'=>'required|numeric|min:'.$next_odo_reading,
        ]);

        
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }  
        
        $stock_id_code_arr=$request->stock_id_code;
        $serial_no_arr=$request->serial_no;
        $transtype_arr=$request->transtype;
        $status_arr=$request->status;
        $vehicle_reg_no_arr=$request->vehicle_reg_no;
        $tyre_position_id_arr=$request->tyre_position_id;

        $loggedUserData=getLoggeduserProfile();
        if(!empty($request->allocate_serial)){
            foreach($request->allocate_serial as $tyre_serial_id => $serial_items){

                $tyre_position_id = $tyre_position_id_arr[$tyre_serial_id];

                $findSerial=WaPoiStockSerialMoves::findOrFail($tyre_serial_id);
                $findSerial->status="in_motor_vehicle";
                $findSerial->vehicle_id=$request->vehicle;
                $findSerial->odometer=$request->odometer;
                $findSerial->tyre_position_id=$tyre_position_id;
                $findSerial->user_id=$loggedUserData->id;
                $findSerial->save();



                $pssm_history=new WaPoiStockSerialMovesHistory();
                $pssm_history->vehicle_id=$request->vehicle;
                $pssm_history->wa_poi_stock_serial_moves_id=$findSerial->id;
                $pssm_history->serial_no=$findSerial->serial_no;
                $pssm_history->wa_stock_move_id=$findSerial->wa_stock_move_id;
                $pssm_history->user_id=$loggedUserData->id;
                $pssm_history->tyre_position_id=$tyre_position_id;
                $pssm_history->status="in_motor_vehicle";
                $pssm_history->save();


                
                $stock_id_code = $stock_id_code_arr[$tyre_serial_id];
                $serial_no = $serial_no_arr[$tyre_serial_id];
                $transtype = $transtype_arr[$tyre_serial_id];
                $status = $status_arr[$tyre_serial_id];
                $vehicle_reg_no = $vehicle_reg_no_arr[$tyre_serial_id];

                $tyreFitting=new TyreFitting();     
                $tyreFitting->vehicle_id=$request->vehicle;
                $tyreFitting->odometer=$request->odometer;
                $tyreFitting->number_of_wheels=$request->number_of_wheels;
                $tyreFitting->tyre_inventory_id=$request->tyre_inventory_id;
                $tyreFitting->tyre_type=$request->tyre_type;
                $tyreFitting->stock_id_code=$stock_id_code;
                $tyreFitting->wa_poi_stock_serial_moves_id=$tyre_serial_id;
                $tyreFitting->serial_no=$serial_no;
                $tyreFitting->trans_type =$transtype;
                $tyreFitting->serial_status =$status;
                $tyreFitting->vehicle_register_no =$vehicle_reg_no;

                $tyreFitting->save();
            }    
                
            saveOdometerHistory($request->vehicle,$request->odometer,'tyre_fitting');    


            $param_arr=[];
            $param_arr['vehicle_date']=$request->vehicle_date??'';
            $param_arr['vehicle']=$request->vehicle??'';
            $param_arr['odometer']=$request->odometer??'';
            $param_arr['number_of_wheels']=$request->number_of_wheels??'';
            $param_arr['tyre_inventory_id']=$request->tyre_inventory_id??'';
            $param_arr['type']=$request->type??'';
            $param_arr['manage']=$request->manage??'filter';
            

            return response()->json([
                'result'=>1,
                'location'=>route($this->model.'.create',$param_arr),
                'message'=>'Tyre allocated successfully'
            ]);

            /*Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.create');*/
        }else{
            return response()->json([
                'result'=>-1,
                'location'=>route($this->model.'.create',$param_arr),
                'message'=>'Must be select 1 Serial Atleast!'
            ]);
        }


        
        
        

                
        /*return response()->json([
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);*/
    }


    public function edit($id)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  Vehicle::where('id',$id)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    $tyre_fitting = VehicleType::all();
                    $make = Make::all();
                    $modal= Modal::all();
                    $bodytype=Bodytype::all();

                   
                   
                    return view('admin.tyre_fitting.edit',compact('title','model','breadcum','row','tyre_fitting','make','modal','bodytype')); 
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
        // echo "test"; die;
        try
        {  
            $row =  Vehicle::where('id',$id)->first();
            // $row->tyre_fitting_name= $request->tyre_fitting_name;
            $row->acquisition_date= $request->acquisition_date;
            $row->vin_sn= $request->vin_sn;
            $row->license_plate= $request->license_plate; 
            $row->type= $request->type;
            $row->year= $request->year; 
            $row->make= $request->make;
            $row->model= $request->model;
            $row->trim= $request->trim; 
            $row->registration_state_provine= $request->registration_state_provine;
            if ($request->hasFile('photo'))
                  {
                      $file = $request->file('photo');
                      $image = uploadwithresize($file, 'tyre_fittinglist');
                      $row->photo = $image;
                  }
            $row->status= $request->status;
            $row->group= $request->group;
            $row->operator= $request->operator;
            $row->ownership= $request->ownership;
            $row->color= $request->color;
            $row->body_type= $request->bodytype;
            $row->msrp= $request->msrp;
            $row->linked_devices= $request->linked_devices;
            $row->update();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($id){
        try
        {
            $find=Vehicle::findOrFail($id);
            $find->status="archived";
            $find->save();

            
            $response['result'] = 1;
            $response['message'] = 'Archived have successfully';
            return response()->json($response);

            /*Session::flash('success', 'Archived have successfully.');
            return redirect()->back();*/
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }


    public function show(Request $request, $id){

        

        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin')
            {
                $row =  Vehicle::where('id',$id)->first();
                if($row)
                {

                    $issue_data = Issues::getDataVehicles(99999999999,0,'','issues.id','desc',$request,$id); 

                    $service_remainder=ServiceRemainder::getDataVehicles(999999999999,0,'','service_remainders.id','desc',$request,$id); 
                    
                    $insepection_history=InspectionHistory::select('inspection_history_items.item_detail')->leftjoin('inspection_history_items','inspection_history.id','=','inspection_history_items.inspection_history_id')->where('tyre_fitting_id',$id)->get();

                    

                    $issueOpenCount=0;
                    $issueOverDueCount=0;
                    if($issue_data['response']->count()>0){
                        foreach($issue_data['response'] as $issue){
                            if($issue->resolve=='open'){$issueOpenCount++;}
                            if($issue->due_date!=NULL){$issueOverDueCount++;}
                        }
                    }


                    $remaindersOverdueCount=0;
                    $remaindersSnoozedCount=0;
                    $remaindersDueCount=0;
                    if($service_remainder['response']->count()>0){
                        foreach($service_remainder['response'] as $service_remainder){
                            if($service_remainder->status=='overdue'){$remaindersOverdueCount++;}
                            if($service_remainder->status=='snoozed'){$remaindersSnoozedCount++;}
                            if($service_remainder->next_due_date!=NULL){$remaindersDueCount++;}
                        }
                    }


                    $inspectionPassCount=0;
                    $inspectionFailCount=0;
                    if($insepection_history->count()>0){
                        foreach($insepection_history as $item){
                            if($item->item_detail=='pass'){$inspectionPassCount++;}
                            if($item->item_detail=='fail'){$inspectionFailCount++;}
                        }
                    }


                   


                    $title = 'Show '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    $tyre_fitting = VehicleType::all();
                    $make = Make::all();
                    $modal= Modal::all();
                    $bodytype=Bodytype::all();

                   
                   
                    return view('admin.tyre_fitting.show',compact('title','model','breadcum','row','tyre_fitting','make','modal','bodytype','issueOpenCount','issueOverDueCount','remaindersOverdueCount','remaindersSnoozedCount','remaindersDueCount','inspectionPassCount','inspectionFailCount')); 
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




    


}
