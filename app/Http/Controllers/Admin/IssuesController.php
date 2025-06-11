<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaSupplier;
use App\Model\Issues;
use App\Model\User;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class IssuesController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'issues';
        $this->title = 'Issues';
        $this->pmodule = 'issues';
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
            $sortable_columns = ['id','asset','vendor_name','reported_date'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = Issues::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request); 
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
             // echo '<pre>';
             // print_r($data);die;
            foreach($data as $key => $re){

                $buttonText = $re['resolve'] == 'resolve'?'Resolve':'Open';
                
                $data[$key]['asset_type']="Vehicle";
                $data[$key]['labels']="-";

                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['issues___view']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('issues.show',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-warning btn-sm">'.$buttonText.'</a>';
                }
                if(isset($permission['issues___delete']) || $permission == 'superadmin'){
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
                $data[$key]['id'] = $re['id'].'#';
                $data[$key]['links'] .= '</div>';

                $data[$key]['dated'] = getDateFormatted($re['created_at']);

                $data[$key]['photos'] = '<img src="'.asset('public/uploads/issues/'.$re['photos']).'" width="50px" height="50px"alt="image">';


                // echo '<pre>';
                // print_r($data); die;

            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response'],

            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        
        return view('admin.issues.index')->with($data);
    }
         public function vehicle_list(Request $request)
    {
        # Payment Accounts
        $data = Vehicle::select(['id as id','license_plate as text']);
        if($request->q)
        {
            $data = $data->orWhere('license_plate','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

     public function User(Request $request)
    {
        # Payment Accounts
        $data = User::select(['id as id','name as text']);
        if($request->q)
        {
            $data = $data->orWhere('name','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

     public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $issues=Issues::all();
            $category_list = Vehicle::all();
            $vendor_list = WaSupplier::all();

           
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.issues.create',compact('title','model','breadcum','pmodule','permission','issues','category_list','vendor_list'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


     public function store(Request $request)
    {
            if(!isset($permission[$this->pmodule.'___add']) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
           if($request->flag =='')
           {
            $flag='null';
           }
           else
           {
            $flag=implode(",", $request->flag); 
           }
            $new = new Issues;
             
            $new->asset = $request->asset;
            $new->reported_date = $request->date;
            $new->time = $request->time;
            $new->summary = $request->summary;
            $new->description  = $request->description ;
            $new->reported_by = $request->reported_by;
            $new->assigned = $request->assigned;
            $new->due_date = $request->due_date;


            if ($request->hasFile('photos'))
                  {
                      $file = $request->file('photos');
                      $image = uploadwithresize($file, 'issues');
                      $new->photos = $image;
                  }
               if ($request->hasFile('documents')){
                  $file = $request->file('documents');
                  $extension = $file->getClientOriginalExtension();
                  $filename = time().'.'.$extension;
                  $path=public_path("uploads/issues/");
                  //$file->move('uploads/fuelentry/',$filename);
                  $file->move($path,$filename);
                   $new->documents=$filename;
               }

             $new->save();
        if($new){
            // return response()->json([
            //     'result'=>1,
            //     'message'=>'Vehicle List Stored Successfully',
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




     public function show($id)
    {
            
            $row =  Issues::where('id',$id)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.issues.show',compact('title','model','breadcum','row','User','Vehicle')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }


     public function resolve(Request $request)
     {
        //print_r($request->id);die();
        $id=$request->id;
          // dd($request);
           {
                $row = Issues::where('id',$id)->first();
                if($row)
                {   
                    $row = $row->resolve=='resolve'?'open':'resolve';
                    Issues::where('id',$id)->update(['resolve'=>$row]);
                    Session::flash('success', 'Changed successfully.');
                    return redirect()->back();
                }
                
        }
      }


         public function service(Request $request)
          {
        //print_r($request->id);die();
          $id=$request->id;
          // dd($request);
           {
                $row = Issues::where('id',$id)->first();
                if($row)
                {   
                    $row = $row->status=='1'?'0':'1';
                    Issues::where('id',$id)->update(['status'=>$row]);
                    Session::flash('success', 'Changed successfully.');
                    return redirect()->back();
                }
                
     }
}



  
   
    // // public function changeStatus($id)
    // // {
    //     try
    //     {
    //         $permission =  getMyAllPermissions();
    //         if(isset($permission[$this->pmodule.'___status']) || $permission == 'superadmin')
    //         {
    //             $row = MeetingType::where('id',$id)->first();
    //             if($row)
    //             {   
    //                 $row = $row->status=='1'?'0':'1';
    //                 MeetingType::where('id',$id)->update(['status'=>$row]);
    //                 Session::flash('success', 'Status changed successfully.');
    //                 return redirect()->back();
    //             }
    //             else
    //             {
    //                 Session::flash('warning', getErrorMessages('1'));
    //                 return redirect()->back();
    //             }
    //         }
    //         else
    //         {
    //             Session::flash('warning', getErrorMessages('1'));
    //             return redirect()->back(); 
    //         }
    //     }
    //     catch(\Exception $e)
    //     {
    //      $msg = $e->getMessage();
    //      Session::flash('warning', $msg);
    //      return redirect()->back();
    //  }
    // }




   
}
