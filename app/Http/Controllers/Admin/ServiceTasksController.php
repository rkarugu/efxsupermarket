<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaSupplier;
use App\Model\ServiceHistory;
use App\Model\ServiceTask;
use App\Model\Subtype;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class ServiceTasksController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'servicetask';
        $this->title = 'Service Task';
        $this->pmodule = 'servicetask';
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
        $data['pmodule'] = $this->pmodule;
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = ['id','name'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = ServiceTask::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            // $total = 0;
            foreach($data as $key => $re){
                $data[$key]['links'] = '<div style="display:flex">';
                    if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin'){
                        $data[$key]['links'] .= '<a href="'.route('servicetask.show',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }
                
                    if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin'){
                        $data[$key]['links'] .= ' &nbsp; <a href="'.route('servicetask.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    }

                    if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin'){
                        $data[$key]['links'] .= ' &nbsp; <a href="'.route('servicetask.destroy',$re['id']).'" data-id="'.$re['id'].'"  class="btn btn-danger btn-sm delete-confirm"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    }

               
                $data[$key]['links'] .= '</div>';

                $data[$key]['dated'] = getDateFormatted($re['created_at']);

                // $data[$key]['photos'] = '<img src="'.asset('public/uploads/servicetask/'.$re['photos']).'" width="50px" height="50px"alt="image">';
                 // $total += $re['total'];

            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response'],
                // "total"             =>  manageAmountFormat($total)

            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        
        return view('admin.servicetask.index')->with($data);
    }
    
    public function servicetasks_list(Request $request){
        # Payment Accounts
        $data = ServiceTask::select(['id as id','name as text']);
        if($request->q)
        {
            $data = $data->orWhere('name','LIKE',"%$request->q%");
            $data = $data->orWhere('description','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

    public function subtype_list(Request $request)
    {
        # Payment Accounts
        $data = Subtype::select(['id as id','title as text']);
        if($request->q)
        {
            $data = $data->orWhere('title','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

    //  public function WaSupplier(Request $request)
    // {
    //     # Payment Accounts
    //     $data = WaSupplier::select(['id as id','supplier_code as text','name as text']);
    //     if($request->q)
    //     {
    //         $data = $data->orWhere('name','LIKE',"%$request->q%");
    //     }
    //     $data = $data->get();
    //     return $data;
    // }

     public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin'){
            $servicetask=ServiceTask::all();
            $category_list = Vehicle::all();
            $vendor_list = WaSupplier::all();
            $subtype = Subtype::all();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.servicetask.create',compact('title','model','breadcum','pmodule','permission','servicetask','category_list','vendor_list','subtype'));
        }
        else{
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


     public function store(Request $request)
    {       
            if(!$this->modulePermissions($data['permission'],'add')){
            return redirect()->back();
            }
            $subtype=implode(",", $request->subtype); 
  
            $new = new ServiceTask;
            $new->name = $request->name;
            $new->description = $request->description;
            $new->subtype = $subtype;
            // $new->completion_date = $request->completion_date;
            // $new->vendor = $request->vendor;
            // $new->reference = $request->reference;
            // if ($request->hasFile('photos'))
            //       {
            //           $file = $request->file('photos');
            //           $image = uploadwithresize($file, 'servicetask');
            //           $new->photos = $image;
            //       }
            // if ($request->hasFile('documents')){
            //       $file = $request->file('documents');
            //       $extension = $file->getClientOriginalExtension();
            //       $filename = time().'.'.$extension;
            //       $path=public_path("uploads/servicetask/");
            //       //$file->move('uploads/fuelentry/',$filename);
            //       $file->move($path,$filename);
            //        $new->documents=$filename;
            //    }
            //   $new->comments = $request->comments;
             $new->save();
        if($new){
            // return response()->json([
            //     'result'=>1,
            //     'message'=>'Service History Stored Successfully',
            //     'location'=>route('vehicle.index')
            // ]);

            Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index');
        }
              return response()->json([
                'result'=>-1,
                'message'=>'Somethine went wrong'
            ]);
    }


    public function edit($id)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  ServiceTask::where('id',$id)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    $subtype = Subtype::all();

                    $subtype_arr=json_encode(explode(',',$row->subtype));
                    //dd($subtype_arr);
                    return view('admin.servicetask.edit',compact('title','model','breadcum','row','subtype','subtype_arr')); 
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


    public function update(Request $request, $id){
        try{ 
            $row =  ServiceTask::findOrFail($id);
            $subtype=implode(",", $request->subtype); 
            $row = new ServiceTask;
            $row->name = $request->name;
            $row->description = $request->description;
            $row->subtype = $subtype;
            $row->save();
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
        try{
            $find=ServiceTask::findOrFail($id);
            $find->delete();
            Session::flash('success', 'Deleted have successfully');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e){
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }


    //  public function show($id)
    // {
            
    //         $row =  ServiceHistory::where('id',$id)->first();
    //         if($row)
    //         {
    //             $title = 'View '.$this->title;
    //             $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
    //             $model =$this->model;
    //             return view('admin.servicehistory.show',compact('title','model','breadcum','row')); 
    //         }
    //         else
    //         {
    //             Session::flash('warning', 'Invalid Request');
    //             return redirect()->back();
    //         }
        
    // }


   





   
}
