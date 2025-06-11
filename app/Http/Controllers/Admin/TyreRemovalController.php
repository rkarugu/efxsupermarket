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
use App\Model\TyreRemoval;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class TyreRemovalController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {

        $this->model = 'tyre_removal';
        $this->title = 'Tyre Removal';
        $this->pmodule = 'tyre_removal';
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



    public function tyre_removal_dropdown(Request $request){
       
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
                $tyre_serials = WaPoiStockSerialMoves::where('vehicle_id',$request->vehicle)->orderBy('created_at','DESC')->get();
            }


            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyre_removal.create',compact('title','model','breadcum','pmodule','permission','tyre_removal','tyre_serials','vehicle_reg_no','odometer'));
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




        $last_odometer_reading=checkEnforceMeterReading($request->vehicle,'tyre_removal');
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

        $param_arr=[];
        $param_arr['vehicle']=$request->vehicle??'';
        $param_arr['odometer']=$request->odometer??'';
        $param_arr['manage']=$request->manage??'filter';

        $removal_status_arr=$request->removal_status;


        //dd($request->all());
        $loggedUserData=getLoggeduserProfile();
        if(!empty($request->deallocate_serial)){
            foreach($request->deallocate_serial as $tyre_serial_id => $serial_items){
                $removal_status=$removal_status_arr[$tyre_serial_id];
                $findSerial=WaPoiStockSerialMoves::findOrFail($tyre_serial_id);
                $findSerial->status=$removal_status;
                $findSerial->vehicle_id=NULL;
                $findSerial->save();
                
                $pssm_history=new WaPoiStockSerialMovesHistory();
                $pssm_history->wa_poi_stock_serial_moves_id=$findSerial->id;
                $pssm_history->serial_no=$findSerial->serial_no;
                $pssm_history->wa_stock_move_id=$findSerial->wa_stock_move_id;
                $pssm_history->user_id=$loggedUserData->id;
                $pssm_history->status=$removal_status;
                $pssm_history->save();   
            }    
            
            
            
            saveOdometerHistory($request->vehicle,$request->odometer,'tyre_removal');

            return response()->json([
                'result'=>1,
                'location'=>route($this->model.'.create',$param_arr),
                'message'=>'Indicate Tyre De-allocated successfully'
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
                    $tyre_removal = VehicleType::all();
                    $make = Make::all();
                    $modal= Modal::all();
                    $bodytype=Bodytype::all();

                   
                   
                    return view('admin.tyre_removal.edit',compact('title','model','breadcum','row','tyre_removal','make','modal','bodytype')); 
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
            // $row->tyre_removal_name= $request->tyre_removal_name;
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
                      $image = uploadwithresize($file, 'tyre_removallist');
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
                    
                    $insepection_history=InspectionHistory::select('inspection_history_items.item_detail')->leftjoin('inspection_history_items','inspection_history.id','=','inspection_history_items.inspection_history_id')->where('tyre_removal_id',$id)->get();

                    

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
                    $tyre_removal = VehicleType::all();
                    $make = Make::all();
                    $modal= Modal::all();
                    $bodytype=Bodytype::all();

                   
                   
                    return view('admin.tyre_removal.show',compact('title','model','breadcum','row','tyre_removal','make','modal','bodytype','issueOpenCount','issueOverDueCount','remaindersOverdueCount','remaindersSnoozedCount','remaindersDueCount','inspectionPassCount','inspectionFailCount')); 
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
