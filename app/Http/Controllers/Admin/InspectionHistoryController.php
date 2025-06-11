<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\InspectionsForms;
use App\Model\InspectionFormItem;
use App\Model\InspectionHistory;
use App\Model\InspectionHistoryItems;
use App\Model\Vehicle;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class InspectionHistoryController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'inspection_history';
        $this->title = 'Inspection History';
        $this->pmodule = 'inspection_history';
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
            $sortable_columns = ['inspection_history.vehicle_id'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = InspectionHistory::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            $total = 0;
            foreach($data as $key => $re){
                
               

                $data[$key]['vehicle_id'] = @$re['vehicle']['vin_sn'];
                $data[$key]['management'] = '-';
                $data[$key]['created_at'] = date('j, M d, Y H:ia',strtotime(@$re['created_at']));
                $data[$key]['inspection_form_id'] = @$re['form']['title'];
                $data[$key]['duration'] = '-';
                $data[$key]['user_id'] = @$re['user']['name'];
                $data[$key]['location'] = '<i class="fa fa-warning"></i>';
                $data[$key]['failed_item'] = '-';
                
               
                

                



                $data[$key]['links'] = '<div style="display:flex">';
                
                if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '&nbsp; <a href="'.route($this->model.'.show',base64_encode($re['id'])).'" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }
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

        $forms=InspectionsForms::where('is_archived','0')->get();
        $pmodule=$this->pmodule;
        $model=$this->model;
        $title=$this->title;
        return view('admin.'.$this->model.'.index',compact('model','title','pmodule','permission','data','forms'));
    }


    public function create($form_id){
        $form_id=base64_decode($form_id);
        $form_items=InspectionFormItem::where('inspection_form_id',$form_id)->get();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        
        return view('admin.'.$this->model.'.create',compact('model','title','pmodule','permission','form_items','form_id'));        
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
        $data=InspectionHistory::with(['vehicle','form','user'])->where('id',$id)->first();

        
        return view('admin.'.$this->model.'.show',compact('model','title','pmodule','permission','form_items','form_id','data'));
    }

    public function store(Request $request){
        try{

            $data['permission'] =  $this->mypermissionsforAModule();        
            if(!$this->modulePermissions($data['permission'],'add')){
                return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
            }
            $validator = Validator::make($request->all(),[
                'items'=>'required|array',
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ]);
            }

            $logged_user_info = getLoggeduserProfile();
            
            $inspection_from_type_id_arr=$request->inspection_from_type_id;

            $inspection_history=new InspectionHistory();

            $inspection_history->inspection_form_id=base64_decode($request->inspection_form_id);
            $inspection_history->vehicle_id=$request->vehicle_id;
            $inspection_history->user_id=$logged_user_info->id;
            $save_inspection=$inspection_history->save();

            
            if($save_inspection){
                if(!empty($request->items)){
                    foreach($request->items as $item_id => $item_value){
                    
                       
                        $insepection_history_item=new InspectionHistoryItems(); 
                        $insepection_history_item->inspection_history_id = $inspection_history->id;    
                        $insepection_history_item->inspection_type_id = @$inspection_from_type_id_arr[$item_id];    
                        $insepection_history_item->inspection_item_id = $item_id;    
                        $insepection_history_item->inspection_form_id = base64_decode($request->inspection_form_id);    
                        $insepection_history_item->item_detail = $item_value;    

                        $insepection_history_item->save();
                    }
                }
            }

            
            Session::flash('success', 'Added Have successfully.');
            return redirect()->route($this->model.'.index');
        
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
                $row =  InspectionsForms::where('id',$id)->first();
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
             'title'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        
        try
        {  
            $row =  InspectionsForms::findOrFail($id);
            $row->title = $request->title;
            $row->description = $request->description;
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
        $id=base64_decode($id);
        try{
            $find=InspectionsForms::findOrFail($id);
            $find->is_archived="1";
            $find->save();
            if($find){
                $response['result'] = 1;
                $response['message'] = 'Archived have successfully';
            }else{
                $response['result'] = 0;
                $response['message'] = 'Something went wrong';
            }
        }
        catch(\Exception $e){
            $response['result'] = 0;
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }


    



    


    

}
