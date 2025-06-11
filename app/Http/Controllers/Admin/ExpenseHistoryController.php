<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\Fuelentry;
use App\Model\Vehicle;
use App\Model\WaSupplier;
use App\Model\Expensehistory;
use App\Model\Expensetype;
use PDF;




use Session;
use DB;
use Illuminate\Support\Facades\Validator;

class ExpenseHistoryController extends Controller
{
	    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'expensehistory';
        $this->title = 'Expense  List';
        $this->pmodule = 'expensehistory';
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

    // public function index()
    // {
    //     $permission =  $this->mypermissionsforAModule();
    //     $pmodule = $this->pmodule;
    //     $title = $this->title;
    //     $model = $this->model;

    //     if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
    //     {
    //         $vehicle=VehicleType::all();
            
    //         $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
    //         return view('admin.vehicle.index',compact('title','model','breadcum','pmodule','permission','vehicle'));
    //     }
    //     else
    //     {
    //         Session::flash('warning', 'Invalid Request');
    //         return redirect()->back();
    //     } 
            
    // }

    // if($request->get('manage-request') && $request->get('manage-request') == 'PDF'){
    //             $pdf = PDF::loadView('admin.expensehistory.index',compact('user','title','lists','model','breadcum','pmodule','permission','request'));
    //             return $pdf->download('expensehistory'.date('Y_m_d_h_i_s').'.pdf');
    //         }






public function pdfview()
{ 
 $expensehistory = Expensehistory::all(); 
 // dd($expensehistory);
 return view('expensehistory.pdf',compact('expensehistory'));
}



    public function createPDF(Request $request)
{  
    // dd($request->input);
      $expensehistory = Expensehistory::with('LicensePlate','Type','VendorName')->get(); 
      // echo "<pre>";
      // print_r($expensehistory->toArray());die();
// dd($expensehistory);
    $pdf = PDF::loadView('admin/expensehistory/pdf',['expensehistory'=>$expensehistory])->setPaper('a4', 'portrait');
    return $pdf->download('expensehistory.pdf');
}


    public function index(Request $request)
    {
    
        $data['pmodule'] = $this->pmodule;
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = ['id','vehicle','date','expense_type','vendor','amount'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];
        // dd($response);

            $response = Expensehistory::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request); 
            // print_r($responses->toArray());die();           
            $totalCms       = $response['count'];
            $data = $response['response'];
            $json  = json_encode($data);
            $data = json_decode($json, true);
            // dd($array);
            $total = 0;
            foreach($data as $key => $re){
                $data[$key]['source'] = '-';
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['expensehistory___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('expensehistory.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if(isset($permission['expensehistory___delete']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<form action="'.route('expensehistory.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     <input type="hidden" value="DELETE" name="_method">
                     '.csrf_field().'
                     </form>';
                }
                $data[$key]['links'] .= '</div>';
               $total += $re['amount'];

                $data[$key]['dated'] = getDateFormatted($re['dated']);

                // $data[$key]['photo'] = '<img src="'.asset('public/uploads/expensehistory/'.$re['photo']).'" width="50px" height="50px"alt="image">';

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
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        // $vehicle['title']=$this->title;
        // $make['title']=$this->title;
        // $models['title']=$this->title;
        // $bodytype['title']=$this->title;

        
        return view('admin.expensehistory.index')->with($data);
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

     public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $expensehistory=Expensehistory::all();
            $category_list = Vehicle::all();
            $vendor_list = WaSupplier::all();
            $expensetype=Expensetype::all();


           
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.expensehistory.create',compact('title','model','breadcum','pmodule','permission','expensehistory','category_list','vendor_list','expensetype'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


     public function store(Request $request)
    {
         $data['permission'] = $permission = $this->mypermissionsforAModule();    
        if(!$this->modulePermissions($data['permission'],'add')){
            return redirect()->back();
        }
            // $amt= Expensehistory::where('vehicle',$request->vehicle)->select('amount')->first();
            $new = new Expensehistory;
            $new->vehicle = $request->vehicle;
            $new->expense_type = $request->expense_type;
            $new->vendor = $request->vendor;
            // $new->amount = ($request->amount+$amt->amount);
            $new->amount = $request->amount;
            $new->frequency = $request->frequency;
            $new->date = $request->date;
            $new->notes = $request->notes;
            
            if ($request->hasFile('photos'))
                  {
                      $file = $request->file('photos');
                      $image = uploadwithresize($file, 'expensehistory');
                      $new->photos = $image;
                  }
                  if ($request->hasFile('documents'))
                  {
                  $file = $request->file('documents');
                  $extension = $file->getClientOriginalExtension();
                  $filename = time().'.'.$extension;
                  $path=public_path("uploads/expensehistory/");
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



}