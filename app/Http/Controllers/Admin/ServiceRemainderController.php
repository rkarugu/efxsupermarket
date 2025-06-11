<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ServiceRemainder;
use App\Model\Vehicle;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class ServiceRemainderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'service_remainder';
        $this->title = 'Service Remainder';
        $this->pmodule = 'service_remainder';
    } 

    public function modulePermissions($permission,$type){
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }

  

    public function index(Request $request){

        
        $data['pmodule'] = $this->pmodule;
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }


        if($request->ajax()){
            $sortable_columns = ['service_remainders.vehicle_id'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = ServiceRemainder::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            

            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            $total = 0;
            foreach($data as $key => $re){
                $data[$key]['last_completed'] = $re['updated_at']; 
                $data[$key]['links'] = '<div style="display:flex">';
                if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '&nbsp; <a href="'.route($this->model.'.show',base64_encode($re['id'])).'" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }

                if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '&nbsp; <a href="'.route($this->model.'.edit',base64_encode($re['id'])).'" class="btn btn-danger btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                }
                $data[$key]['links'] .= '&nbsp; <a href="'.route($this->model.'.destroy',base64_encode($re['id'])).'"  class="btn btn-primary delete-confirm" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';

                $data[$key]['links'] .= '</div>';
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response'],
                "total"              =>  manageAmountFormat($total)

            ];
            return $return;
        }

        $forms=ServiceRemainder::where('is_archived','0')->get();

        $pmodule=$this->pmodule;
        $model=$this->model;
        $title=$this->title;
        return view('admin.'.$this->model.'.index',compact('model','title','pmodule','permission','data','forms'));
    }


    public function create(){
        $permission =  $this->mypermissionsforAModule();
        $vehicles=Vehicle::all();
        
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        
        return view('admin.'.$this->model.'.create',compact('model','title','pmodule','permission','vehicles'));        
    }


    public function show($id){
        $id=base64_decode($id);


        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $row=ServiceRemainder::with(['vehicle','service_task'])->where('id',$id)->first();

        
        return view('admin.'.$this->model.'.show',compact('model','title','pmodule','permission','row'));
    }

    public function store(Request $request){
        try{

            $data['permission'] =  $this->mypermissionsforAModule();        
            if(!$this->modulePermissions($data['permission'],'add')){
                return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
            }
            $validator = Validator::make($request->all(),[
                'vehicle_id'=>'required|exists:vehicle_list,id',
                'service_task_id'=>'required|exists:servicetask,id',
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ]);
            }

            
            $inspection_from_type_id_arr=$request->inspection_from_type_id;

            $new=new ServiceRemainder();
            $new->vehicle_id=$request->vehicle_id;
            $new->service_task_id=$request->service_task_id;
            $new->time_enterval =$request->time_enterval;
            $new->time_duesoon_threshold =$request->time_duesoon_threshold;
            $new->primary_meter_interval =$request->primary_meter_interval;
            $new->primary_meter_duesoon_threshold =$request->primary_meter_duesoon_threshold;
            $new->time_enterval_type =$request->time_enterval_type;
            $new->time_duesoon_threshold_type =$request->time_duesoon_threshold_type;
            $new->status ='overdue';
            $new->save();

            
            if($new){
                Session::flash('success', 'Added Have successfully.');
                return redirect()->route($this->model.'.index');
            }else{
                Session::flash('warning', 'Something went wrong! Please try again.');
                return redirect()->back()->withInput();     
            }
        }catch(\Exception $e){

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
        
    }


    public function edit($id){
        $id=base64_decode($id);
        try{
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin'){
                $row =  ServiceRemainder::where('id',$id)->first();
                if($row){
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.'.$model.'.edit',compact('title','model','breadcum','row')); 
                }
                else{
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


    public function update(Request $request, $id){
        $id=base64_decode($id);
        $validator = Validator::make($request->all(),[
            'vehicle_id'=>'required|exists:vehicle_list,id',
            'service_task_id'=>'required|exists:servicetask,id',
        ]);

        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        
        try
        {  
            $row =  ServiceRemainder::findOrFail($id);
            $row->vehicle_id=$request->vehicle_id;
            $row->service_task_id=$request->service_task_id;
            $row->time_enterval =$request->time_enterval;
            $row->time_duesoon_threshold =$request->time_duesoon_threshold;
            $row->primary_meter_interval =$request->primary_meter_interval;
            $row->primary_meter_duesoon_threshold =$request->primary_meter_duesoon_threshold;
            $row->time_enterval_type =$request->time_enterval_type;
            $row->time_duesoon_threshold_type =$request->time_duesoon_threshold_type;
            $row->status ='overdue';
            $row->save();
            if($row){
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



    public function destroy($id){
        if(!$this->modulePermissions($data['permission'],'delete')){
            return redirect()->back();
        }
        $id=base64_decode($id);
        try{
            $find=ServiceRemainder::findOrFail($id);
            //$find->is_archived="1";
            $find->delete();
            Session::flash('success', 'Delete Have successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e){
            Session::flash('warning', $e->getMessage());
            return redirect()->back()->withInput();
        }
        
    }


    



    


    

}
