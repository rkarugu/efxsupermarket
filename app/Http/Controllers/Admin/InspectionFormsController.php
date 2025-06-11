<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\InspectionFormItem;
use App\Model\InspectionsForms;
use App\Model\InspectionItemTypes;

use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class InspectionFormsController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'inspection_forms';
        $this->title = 'Inspection Form';
        $this->pmodule = 'inspection_forms';
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

  

    public function index(Request $request){
        
        $permission =  $this->mypermissionsforAModule();
        $data=InspectionsForms::with('getRelatedItems')->where('is_archived','0')->get();
        $pmodule=$this->pmodule;
        $model=$this->model;
        $title=$this->title;
        return view('admin.'.$this->model.'.index',compact('model','title','pmodule','permission','data'));
    }


    public function create(){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        
        return view('admin.'.$this->model.'.create',compact('model','title','pmodule','permission'));

        
            
    }


    public function edit_items($form_id){

        $form_id=base64_decode($form_id);
        $item_types=InspectionItemTypes::where('status','1')->get();
        $items=InspectionFormItem::where('inspection_form_id',$form_id)->where('status','1')->get();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $pass_fail_html=view('admin.'.$this->model.'.item_type_templates.pass_fail',compact('model','title','pmodule','permission','item_types'))->render();    
        $pass_fail_html=str_replace(["\n\r", "\n", "\r"], '', $pass_fail_html);
        return view('admin.'.$this->model.'.create_items',compact('model','title','pmodule','permission','item_types','pass_fail_html','form_id','items'));
        
            
    }

    public function vehicle_schedule($form_id){
        $form_id=base64_decode($form_id);
        $item_types=InspectionItemTypes::where('status','1')->get();
        $items=InspectionFormItem::where('inspection_form_id',$form_id)->where('status','1')->get();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        return view('admin.'.$this->model.'.vehicle_schedule',compact('model','title','pmodule','permission','item_types','pass_fail_html','form_id','items'));     
    }



    public function store_items(Request $request){
        

        
        $data['permission'] =  $this->mypermissionsforAModule();        
        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }

        $validator = Validator::make($request->all(),[
             'title'=>'required|array',
        ]);

        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }


        $label_arr=$request->title;
        $short_description_arr=$request->short_description??[];
        $inspection_from_type_id_arr=$request->inspection_from_type_id??[];
        $instructions_arr=$request->instructions??[];
        $pass_label_arr=$request->pass_label??[];
        $fail_label_arr=$request->fail_label??[];
        $enable_for_submission_arr=$request->enable_for_submission??[];
        $require_photo_or_comment_for_pass_arr=$request->require_photo_or_comment_for_pass??[];
        $require_photo_or_comment_for_fail_arr=$request->require_photo_or_comment_for_fail??[];
        $require_secondary_meter_arr=$request->require_secondary_meter??[];
        $require_photo_verification_arr=$request->require_photo_verification??[];
        $passing_range_from_arr=$request->passing_range_from??[];
        $passing_range_to_arr=$request->passing_range_to??[];
        $date_arr=$request->date??[];
        $datetime_arr=$request->datetime??[];

        $inspection_form_id=base64_decode($request->inspection_form_id);
        
        InspectionFormItem::where('inspection_form_id',$inspection_form_id)->delete();
        
        if(!empty($label_arr)){
            foreach ($label_arr as $key => $items) {
                if(!empty($items)){
                    foreach ($items as $subkey => $label_item) {
                        $label=$label_item??NULL;
                        $new = new InspectionFormItem;    
                        $new->inspection_form_id = $inspection_form_id??NULL;
                        $new->inspection_from_type_id = @$inspection_from_type_id_arr[$key][$subkey]??NULL;
                        $new->title = $label;
                        $new->short_description = @$short_description_arr[$key][$subkey]??NULL;
                        $new->instructions = @$instructions_arr[$key][$subkey]??NULL;
                        $new->pass_label = @$pass_label_arr[$key][$subkey]??NULL;
                        $new->fail_label = @$fail_label_arr[$key][$subkey]??NULL;
                        $new->enable_for_submission = @$enable_for_submission_arr[$key][$subkey]??0;
                        $new->require_photo_or_comment_for_pass = @$require_photo_or_comment_for_pass_arr[$key][$subkey]??0;
                        $new->require_photo_or_comment_for_fail = @$require_photo_or_comment_for_fail_arr[$key][$subkey]??0;
                        $new->require_secondary_meter = @$require_secondary_meter_arr[$key][$subkey]??0;
                        $new->require_photo_verification = @$require_photo_verification_arr[$key][$subkey]??0;
                        $new->passing_range_from = @$passing_range_from_arr[$key][$subkey]??0;
                        $new->passing_range_to = @$passing_range_to_arr[$key][$subkey]??0;
                        $new->date = @$date_arr[$key][$subkey]??0;
                        $new->date_time = @$datetime_arr[$key][$subkey]??0;
                        $new->save(); 
                    }

                }    
            
            }
        }
        
        if($new){
            Session::flash('success', 'Items saved have successfully.');
            return redirect()->route($this->model.'.edit.items',['form_id'=>$request->inspection_form_id]);
        }
        return response()->json([
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);
    }
    

    public function store(Request $request){

        
        $data['permission'] =  $this->mypermissionsforAModule();        
        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }

        $validator = Validator::make($request->all(),[
             'title'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        $new = new InspectionsForms;
        $new->title = $request->title;
        $new->description = $request->description;
        $new->save();
        if($new){
            Session::flash('success', 'Added have successfully.');
            return redirect()->route($this->model.'.edit.items',['form_id'=>base64_encode($new->id)]);
        }
        return response()->json([
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);
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
