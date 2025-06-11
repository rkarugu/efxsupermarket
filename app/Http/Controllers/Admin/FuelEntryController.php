<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaSupplier;
use App\Model\OdometerReadingHistory;
use Session;
use DB;
use URL;
use Illuminate\Support\Facades\Validator;

class FuelEntryController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'fuelentry';
        $this->title = 'Fuel Entry';
        $this->pmodule = 'fuelentry';
    } 

    public function modulePermissions($permission,$type){
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin'){
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }

    

    public function index(Request $request)
    {
        $data['pmodule'] = $this->pmodule;
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = ['id','vehicle','vendor_name'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = Fuelentry::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            $total = 0;
            foreach($data as $key => $re){

            

                $data[$key]['distance'] = '-';
                $data[$key]['fuel_used'] = '-';
                $data[$key]['rate'] = '-';
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('fuelentry.show',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }


                if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= ' &nbsp; <a href="'.route('fuelentry.edit',$re['id']).'" data-id="'.$re['id'].'" class="btn btn-danger btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                }


                if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<form action="'.route('fuelentry.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     <input type="hidden" value="DELETE" name="_method">
                     '.csrf_field().'
                     </form>';
                }


                if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin'){
                     // $data[$key]['links'] .= '<form action="'.route('fuelentry.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     // <input type="hidden" value="DELETE" name="_method">
                     // '.csrf_field().'
                     // </form>';

            //         if(isset($permission[$this->pmodule.'___show']) || $permission == 'superadmin')
            // {
            //     $nestedData['view'] = getActionButtons([['key'=>'show','url'=>route('fuelentry.show',$list->id)]]);
            // }

                // if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
                //             $lists = Fuelentry::where('id');
                //             if ($permission != 'superadmin') {
                //                 $lists = $lists->where('id');
                //             }
                //             $lists = $lists->orderBy('id', 'desc')->get();
                //             $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
                //             return view('admin.fuelentry.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
                //         } else {
                //             Session::flash('warning', 'Invalid Request');
                //             return redirect()->back();
                //         }

                }
                
                
                
                $data[$key]['links'] .= '</div>';

                $data[$key]['dated'] = getDateFormatted($re['created_at']);

                $data[$key]['photos'] = '<img src="'.asset('public/uploads/fuelentry/'.$re['photos']).'" width="50px" height="50px"alt="image">';

                
                
                 $total += $re['total'];

            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response'],
                "total"             =>  manageAmountFormat($total)

            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        
        return view('admin.fuelentry.index')->with($data);
    }
    
    public function vehicle_list(Request $request){
        # Payment Accounts
        $data = Vehicle::select(['id as id','license_plate as text']);
        if($request->q)
        {
            $data = $data->orWhere('license_plate','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

     public function WaSupplier(Request $request)
    {
        # Payment Accounts
        $data = WaSupplier::select(['id as id','supplier_code as text','name as text']);
        if($request->q)
        {
            $data = $data->orWhere('name','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

    public function create(){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $fuelentry=Fuelentry::all();
            $category_list = Vehicle::all();
            $vendor_list = WaSupplier::all();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.fuelentry.create',compact('title','model','breadcum','pmodule','permission','fuelentry','category_list','vendor_list'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


    public function store(Request $request){

       // dd($request->all());
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'add')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }


        $last_odometer_reading=checkEnforceMeterReading($request->vehicle,'fuel_history');
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


        if($request->flag ==''){
            $flag='null';
        }
        else{
            $flag=implode(",", $request->flag); 
        }
        $new = new Fuelentry;
         
        $new->vehicle = $request->vehicle;
        $new->fuel_entry_date = $request->date;
        $new->fuel_entry_time = $request->time;
        $new->odometer = $request->odometer;
         $pastentry = Fuelentry::where('vehicle',$request->vehicle)->first();

        $diff = 0;
        if ($pastentry) {
            $diff =  $request->odometer-$pastentry->odometer;
        
           // $entry =   $pastentry->first();
        }

        $new->meter = $diff;
        $new->gallons = $request->gallons;
        $new->price = $request->price;
        $total= $request->gallons * $request->price;
        $new->total = $total;
        $economy=0;
        if($diff > 0 && $request->gallons > 0){

        $economy = $diff / $request->gallons;
        }
        $new->fuel_economy = $economy;
        if($diff==0){
            $cost=$total;
        }
        else{
            $cost=0;
            if($total > 0 && $economy > 0){
            $cost = ($total / $economy);
            }
        }

        $new->cost_per_meter = $cost;
        $new->fuel_type = $request->fuel_type;
        $new->vendor_name = $request->vendor;
        $new->reference = $request->reference;
        $new->previous_odometer_reading = $request->previous_odometer_reading;
        $new->flags = $flag;
        if ($request->hasFile('photos')){
            $file = $request->file('photos');
            $image = uploadwithresize($file, 'fuelentry');
            $new->photos = $image;
        }
        if ($request->hasFile('documents')){
            $file = $request->file('documents');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $path=public_path("uploads/fuelentry/");
            //$file->move('uploads/fuelentry/',$filename);
            $file->move($path,$filename);
            $new->documents=$filename;
        }
        $new->comments = $request->comments;
        $new->save();
        if($new){
            saveOdometerHistory($request->vehicle,$request->odometer,'fuel_history');

            return response()->json([
                'result'=>1,
                'message'=>'Record added successfully',
                'location'=>route($this->model.'.index')
            ]);
            

            /*Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index');*/
        }
        return response()->json([
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);
    }


    public function getPreviousOdometer(Request $request){

        $vehicle_id=$request->vehicle_id;
        $previous_odometer_reading=0;
        $row =  Fuelentry::where('vehicle',$vehicle_id)->orderBy('id','DESC')->first();
        if($row){
            $previous_odometer_reading=$row->odometer;
        }
        
        return response()->json([
            'previous_odometer_reading'=>$previous_odometer_reading,
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);
    }

     public function show($id)
    {
            
            $row =  Fuelentry::where('id',$id)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.fuelentry.show',compact('title','model','breadcum','row')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }


    public function edit($id){    
        $row =  Fuelentry::where('id',$id)->first();
        if($row){
            $title = 'Edit '.$this->title;
            $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
            $model =$this->model;
            return view('admin.fuelentry.edit',compact('title','model','breadcum','row')); 
        }
        else{
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


   
    public function update(Request $request,$id){

       
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'edit')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }


        $last_odometer_reading=checkEnforceMeterReading($request->vehicle,'fuel_history');
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


        if($request->flag ==''){
            $flag='null';
        }
        else{
            $flag=implode(",", $request->flag); 
        }
        $new = Fuelentry::findOrFail($id);
        



        $new->vehicle = $request->vehicle;
        $new->fuel_entry_date = $request->date;
        $new->fuel_entry_time = $request->time;
        $new->odometer = $request->odometer;
         $pastentry = Fuelentry::where('vehicle',$request->vehicle)->first();

        $diff = 0;
        if ($pastentry) {
            $diff =  $request->odometer-$pastentry->odometer;
        
           // $entry =   $pastentry->first();
        }

        $new->meter = $diff;
        $new->gallons = $request->gallons;
        $new->price = $request->price;
        $total= $request->gallons * $request->price;
        $new->total = $total;
        $economy=0;
        if($diff > 0 && $request->gallons > 0){

        $economy = $diff / $request->gallons;
        }
        $new->fuel_economy = $economy;
        if($diff==0){
            $cost=$total;
        }
        else{
            $cost=0;
            if($total > 0 && $economy > 0){
            $cost = ($total / $economy);
            }
        }

        $new->cost_per_meter = $cost;
        $new->fuel_type = $request->fuel_type;
        $new->vendor_name = $request->vendor;
        $new->reference = $request->reference;
        $new->previous_odometer_reading = $request->previous_odometer_reading;
        $new->flags = $flag;
        /*if ($request->hasFile('photos')){
            $file = $request->file('photos');
            $image = uploadwithresize($file, 'fuelentry');
            $new->photos = $image;
        }
        if ($request->hasFile('documents')){
            $file = $request->file('documents');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $path=public_path("uploads/fuelentry/");
            //$file->move('uploads/fuelentry/',$filename);
            $file->move($path,$filename);
            $new->documents=$filename;
        }*/
        $new->comments = $request->comments;
        $new->save();
        if($new){
            saveOdometerHistory($request->vehicle,$request->odometer,'fuel_history');

            return response()->json([
                'result'=>1,
                'message'=>'Updated successfully',
                'location'=>URL::previous()
            ]);
        }
        return response()->json([
            'result'=>-1,
            'message'=>'Somethine went wrong'
        ]);
    }


    public function destroy($id){
        try
        {
            $find=Fuelentry::findOrFail($id);
            //$find->status="archived";
            $find->delete();

            
            /*$response['result'] = 1;
            $response['message'] = 'Fuel Entry have deleted successfully';
            return response()->json($response);*/

            Session::flash('success', 'Delete have successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }






   
}
