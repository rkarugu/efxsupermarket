<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaSupplier;
use App\Model\ServiceHistory;
use App\Model\Issues;
use App\Model\IssuesType;
use App\Model\Subtype;
use App\Model\ServiceTask;
use App\Model\ServiceIssues;



use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class ServiceHistoryController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'servicehistory';
        $this->title = 'Service History';
        $this->pmodule = 'servicehistory';
    } 

    public function modulePermissions($permission,$type){
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
            $sortable_columns = ['id','vehicle','vendor_name'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = ServiceHistory::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            // $total = 0;
            foreach($data as $key => $re){
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['servicehistory___view']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('servicehistory.show',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }
                if(isset($permission['servicehistory___delete']) || $permission == 'superadmin'){
                     // $data[$key]['links'] .= '<form action="'.route('servicehistory.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     // <input type="hidden" value="DELETE" name="_method">
                     // '.csrf_field().'
                     // </form>';

            //         if(isset($permission[$this->pmodule.'___show']) || $permission == 'superadmin')
            // {
            //     $nestedData['view'] = getActionButtons([['key'=>'show','url'=>route('servicehistory.show',$list->id)]]);
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

                $data[$key]['photos'] = '<img src="'.asset('public/uploads/servicehistory/'.$re['photos']).'" width="50px" height="50px"alt="image">';
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
        
        return view('admin.servicehistory.index')->with($data);
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

    public function service_task(Request $request)
      {
        # Payment Accounts
        $data = ServiceTask::select(['id as id','name as text']);
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
            $servicehistory=ServiceHistory::all();
            $category_list = Vehicle::all();
            $vendor_list = WaSupplier::all();

           
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.servicehistory.create',compact('title','model','breadcum','pmodule','permission','servicehistory','category_list','vendor_list'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


     public function store(Request $request){

        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___add']) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }



        $last_odometer_reading=checkEnforceMeterReading($request->vehicle,'service_history');
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

            $new = new ServiceHistory;
            $new->vehicle = $request->vehicle;
            $new->odometer = $request->odometer;
            $new->start_date = $request->start_date;
            $new->completion_date = $request->complete_date;
            $new->vendor = $request->vendor;
            $new->reference = $request->reference;
            if ($request->hasFile('photos'))
                  {
                      $file = $request->file('photos');
                      $image = uploadwithresize($file, 'servicehistory');
                      $new->photos = $image;
                  }
            if ($request->hasFile('documents')){
                  $file = $request->file('documents');
                  $extension = $file->getClientOriginalExtension();
                  $filename = time().'.'.$extension;
                  $path=public_path("uploads/servicehistory/");
                  //$file->move('uploads/fuelentry/',$filename);
                  $file->move($path,$filename);
                   $new->documents=$filename;
               }
              $new->comments = $request->comments;
              $new->general_notes = $request->general_notes;
              $new->parts = $request->partss;
             $new->labor = $request->labors;
             $new->subtotal = $request->subtotals;
             $new->discount = $request->discount;
             $new->tax = $request->tax;
              $new->total = $request->total;

             $new->save();


         // if($request->issues =='')
         //   {
         //    $issues='null';
         //   }
         //   else
         //   {
         //   $issues=implode(",",$request->issues); 
         //   }
         //    $new = new IssuesType;
         //    $new->issues = $issues;
         //    $new->save();


          if(!empty($request->issues)){
              foreach ($request->issues as $key => $task) {
                  $issuestype= new IssuesType();
                  $issuestype->issues = $task;
                  $issuestype->servicehistory_id = $new->id;
                   $issuestype->save();

              }
          }   

           //   if($request->service_task =='')
           // {
           //  $service_task='null';
           // }
           // else
           // {
           // $service_task=implode(",",$request->service_task); 
           // }

           // foreach ($request->service_task as $task) {
           //    $news= ServiceIssues::create(['service_task'=>$task,'servicehistory_id'=>$new->id]);
               
           // }




           foreach ($request->service_id as $key => $task) {
              $news= new ServiceIssues();
              $news->service_task = $task;
              $news->parts = $request->parts[$key];
              $news->labor = $request->labor[$key];
              $news->subtotal = $request->subtotal[$key];
              $news->servicehistory_id = $new->id;

              $news->save();



               
           }
          


           
           


        if($new){

            saveOdometerHistory($request->vehicle,$request->odometer,'service_history');
            return response()->json([
                'result'=>1,
                'message'=>'Service History Stored Successfully',
                'location'=>route($this->model.'.index')
            ]);

                /*Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index')*/;
        }
              return response()->json([
                'result'=>-1,
                'message'=>'Somethine went wrong'
            ]);
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
 
       public function getissues(Request $request)
    {
        $id = $request->input('id');
        $issue = Issues::where('asset',$id)->get();

        $options='';
        // $checkbox='';
        $count=count($issue);
        if($count>0)
        {
            foreach($issue as $issues)
            {
                 $options .= "<tr>

                 
                                <td><input type='checkbox' name='issues[".$issues->summary."]' value='".$issues->summary."' /></td>
                                <td>".$issues->id ."</td>
                                <td>".$issues->summary."</td>
                                <td>".$issues->status."</td>
                                <td>".$issues->assigned."</td>
                                <td>".$issues->due_date??'-'."</td>

                                
                            </tr>";
                 /*$options .= "<ul>"."<input type='checkbox' name='issues[{{$issues->summary}}]' value='$issues->summary'>"."&nbsp;".$issues->summary."</input>"."</ul>";*/
            }
        
            
            return response()->json(array('status' => true, 'html'=>$options));
        }
        else
        {
            $options .="No issues Found";
            return response()->json(array('status' => false, 'html'=>$options));

        }
    }


     public function Addtask(Request $request)
    {
        $id = $request->input('id');
        // $task = Subtype::where('vehicle',$id)->get();
        $task = ServiceTask::get();
        $options='';
        $count=count($task);
        if($count>0)
        {
            foreach($task as $tasks)
            {
                 $options .="<select '$tasks->id'>"."$tasks->title"."</select>";
                 // $options[$tasks->id] = $tasks->title;
            }
            return response()->json(array('status' => true, 'html'=>$options));
        }
        else
        {
            $options .="No Task Found";
            return response()->json(array('status' => false, 'html'=>$options));

        }


    }
   
   public function service_work(Request $request)
    {
        $id = $request->input('id');
        $issue = ServiceTask::where('id',$id)->first();

        if($issue)
        {
           $view = view('admin.servicehistory.servicetask',compact('issue'))->render();
            return response()->json(array('status' => true, 'html'=>$view));
        }
        else
        {
            $view .="No issues Found";
            return response()->json(array('status' => false, 'html'=>$view));

        }
    }



    public function show($id)
    {
            
            $row =  ServiceHistory::where('id',$id)->with('Issues','servicetask')->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.servicehistory.show',compact('title','model','breadcum','row','LicensePlate','Issues')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }


   




   
}
