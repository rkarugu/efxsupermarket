<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\VehicleType;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaPoiStockSerialMoves;

use App\Model\WaPoiStockSerialMovesHistory;
use App\Model\Meterhistory;
use App\Model\TyreRetreading;
use App\Model\TyreInventory;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class TyreRetreadingController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {

        $this->model = 'tyre_retreading';
        $this->title = 'Tyre Retreading';
        $this->pmodule = 'tyre_retreading';
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



    public function tyre_retreading_dropdown(Request $request){
       
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
        $model = 'send_tyre_retreading';

        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin'){

            $tyre_inventory_id=$request->tyre_inventory_id;
            $tyre_serials=[];
            $vehicle_reg_no="";
            $odometer=$request->odometer;
            if($request->manage=="filter" && $request->tyre_inventory_id){
                
               // $vehicle=Vehicle::findOrFail($request->vehicle);
                //$vehicle_reg_no=$vehicle->registration_state_provine;
                $tyre_serials = WaPoiStockSerialMoves::where('wa_inventory_item_id',$request->tyre_inventory_id)->where('status','waiting_retread')->orderBy('created_at','DESC')->get();
            }


            $breadcum = [$title=>route($this->model.'.index'),'Listing'=>''];
            return view('admin.'.$this->model.'.create',compact('title','model','breadcum','pmodule','permission','tyre_retreading','tyre_serials','odometer'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }



    public function receive_retread_tyres(Request $request){


        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        

      



        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'receive_send_tyre_retreading';

        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin'){

            $tyre_inventory_id=$request->tyre_inventory_id;
            $tyre_serials=[];
            $vehicle_reg_no="";
            $odometer=$request->odometer;
            if($request->manage=="filter" && $request->tyre_inventory_id){
                
               // $vehicle=Vehicle::findOrFail($request->vehicle);
                //$vehicle_reg_no=$vehicle->registration_state_provine;
                $tyre_serials = WaPoiStockSerialMoves::where('wa_inventory_item_id',$request->tyre_inventory_id)->where('status','in_retread')->orderBy('created_at','DESC')->get();
            }


            $breadcum = [$title=>route($this->model.'.index'),'Listing'=>''];
            return view('admin.'.$this->model.'.receive_tyre',compact('title','model','breadcum','pmodule','permission','tyre_retreading','tyre_serials','odometer'));
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



        $param_arr=[];
        $param_arr['tyre_inventory_id']=$request->tyre_inventory_id??'';
        $param_arr['manage']=$request->manage??'filter';

       

        //dd($request->all());
        $loggedUserData=getLoggeduserProfile();
        if(!empty($request->deallocate_serial)){
            foreach($request->deallocate_serial as $tyre_serial_id => $serial_items){
                $findSerial=WaPoiStockSerialMoves::findOrFail($tyre_serial_id);
                $findSerial->status='in_retread';
                $findSerial->wa_supplier_id=$request->wa_supplier_id;
                $findSerial->vehicle_id=NULL;

                $findSerial->retread_cost=$request->retread_cost[$tyre_serial_id];
                $findSerial->save();


                $pssm_history=new WaPoiStockSerialMovesHistory();
                $pssm_history->wa_poi_stock_serial_moves_id=$findSerial->id;
                $pssm_history->serial_no=$findSerial->serial_no;
                $pssm_history->wa_stock_move_id=$findSerial->wa_stock_move_id;
                $pssm_history->user_id=$loggedUserData->id;
                $pssm_history->tyre_position_id=$findSerial->tyre_position_id;
                $pssm_history->wa_supplier_id=$request->wa_supplier_id;
                
                $pssm_history->status='in_retread';
                $pssm_history->retread_cost=$request->retread_cost[$tyre_serial_id];

                $pssm_history->save();
                
            }    

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
    }


    public function receive_retread_tyre_store(Request $request){

        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }



        $param_arr=[];
        $param_arr['tyre_inventory_id']=$request->tyre_inventory_id??'';
        $param_arr['manage']=$request->manage??'filter';

       

        //dd($request->all());
        $loggedUserData=getLoggeduserProfile();
        if(!empty($request->deallocate_serial)){
            foreach($request->deallocate_serial as $tyre_serial_id => $serial_items){
                

                $tyreItem=TyreInventory::findOrFail($request->tyre_inventory_id);
                $tyreItem->inventory_item_type="retread";
                $tyreItem->save();

                $findSerial=WaPoiStockSerialMoves::findOrFail($tyre_serial_id);
                $findSerial->status='retread_tyre_in_stock';
                $findSerial->vehicle_id=NULL;
                $findSerial->transtype='Retread';

                $findSerial->retread_cost=$request->retread_cost[$tyre_serial_id];
                $findSerial->save();


                $pssm_history=new WaPoiStockSerialMovesHistory();
                $pssm_history->wa_poi_stock_serial_moves_id=$findSerial->id;
                $pssm_history->serial_no=$findSerial->serial_no;
                $pssm_history->wa_stock_move_id=$findSerial->wa_stock_move_id;
                $pssm_history->user_id=$loggedUserData->id;
                $pssm_history->tyre_position_id=$findSerial->tyre_position_id;
                $pssm_history->wa_supplier_id=$request->wa_supplier_id;
                $pssm_history->status='retread_tyre_in_stock';
                $pssm_history->save();
                
            }    

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
    }


    


    
   


    




    


}
