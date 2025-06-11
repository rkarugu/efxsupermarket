<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DispatchLoadedProducts;
use PDF;
use Session;
use DB;
class DispatchedLoadingSheetsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct(){
        $this->model = 'dispatched-loading-sheets';
        $this->title = 'Dispatched Loading Sheets';
        $this->pmodule = 'dispatched-loading-sheets';
    }

    public function index(Request $request)
    {
        // pre($request->all());
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            if($request->ajax()){
                $sortable_columns = [
                    'dispatch_loaded_products.id',
                    'users.name',
                ];
                $limit          = $request->input('length');
                $start          = $request->input('start');
                $search         = $request['search']['value'];
                $orderby        = $request['order']['0']['column'] ?? 'id';
                $order          = $request['order']['0']['dir'] ?? "DESC";
                $draw           = $request['draw'];          

                $data = DispatchLoadedProducts::with('getSalesMan','getShift','getStoreLocation','getInventoryItem')->where(function($w) use ($user,$request,$search){
                    
                     if($request->input('from') && $request->input('to')){
                         $w->whereDate('dispatch_loaded_products.created_at',[$request->input('from'),$request->input('to')]);
                     }          
                              
                })->select('dispatch_loaded_products.*',
                    DB::RAW(' ( COUNT(dispatch_loaded_products.id) ) as no_of_line_items'),
                    DB::RAW(' ( Select COUNT(dlp.id) from dispatch_loaded_products as dlp where dlp.shift_id=dispatch_loaded_products.shift_id and dlp.store_location_id=dispatch_loaded_products.store_location_id and dlp.balance_qty > 0 Group By dlp.shift_id ) as un_fullfilled'),
                    DB::RAW(' ( Select COUNT(dlp.id) from dispatch_loaded_products as dlp where dlp.document_no=dispatch_loaded_products.document_no and dlp.store_location_id=dispatch_loaded_products.store_location_id and dlp.balance_qty > 0 Group By dlp.document_no ) as un_fullfilled_doc')
                )->where(function($w) use ($search){
                    if($search){
                        $w->orWhere('users.name','LIKE',"%$search%");
                    }
                })->leftjoin('users',function($join){
                    $join->on('users.id','=','dispatch_loaded_products.user_id');
                })->orderBy($sortable_columns[$orderby],$order)->groupBy('dispatch_loaded_products.shift_id','dispatch_loaded_products.store_location_id','dispatch_loaded_products.document_no');
                // ->having('un_fullfilled_doc','>','0')->orHaving('un_fullfilled','>','0');
                //  ->having(function($q) {
                //     $q->where('un_fullfilled_doc', '>', 0)
                //       ->orWhere('un_fullfilled', '>', 0);
                // });
                 
        
                $totalCms       = count($data->get());


            
              
                $response       = $data->limit($limit)->offset($start)->get()->map(function($item) use ($permission,$user){

                    // if($item->un_fullfilled > 0 && $item->un_fullfilled_doc > 0){
                                $item->created_at = date('Y-m-d',strtotime($item->created_at));
                                $item->shift_no = @$item->getShift->shift_id;
                                $item->store_locationId = @$item->store_location_id;
                                $item->store_location_id = @$item->getStoreLocation->location_name;
                                $item->no_of_line_items = @$item->no_of_line_items;
                                $item->new_un_fullfilled = @$item->un_fullfilled > 0?$item->un_fullfilled:$item->un_fullfilled_doc;
                                $item->document_no = @$item->document_no;
                                $item->is_requisition_done = @$item->is_requisition_done == 1?'<i class="fa fa-check" aria-hidden="true"></i>':'<i class="fa fa-close" aria-hidden="true"></i>';
                                $tot = 0;
                                
                                //$item->total = @$tot;
                                $item->links = '';

                                if(isset($item->document_no) && $item->document_no!=""){
                                    if ($item->un_fullfilled_doc > 0 ){

                                        if($item->status == 2){
                                            $item->links .= '<a style="margin: 2px;" class="btn btn-primary" href="'.route('downloadpdf',['store_location_id' => base64_encode($item->store_locationId),'shift_id' => base64_encode($item->document_no),'type'=>"invoice" ]).'" title="Details"> <i class="fa fa-file-pdf"></i></a>';
                                        }else{  
                                            $item->links .= '<a style="margin: 2px;" class="btn btn-primary btn-sm" href="'.route('dispatched-loading-sheets.edit',base64_encode($item->id)).'" title="Details">Generate Store C Requisition</a>';
                                        }
                                    }
                                }else{

                                    if ($item->un_fullfilled > 0 ){
                                        if($item->status == 2){
                                            $item->links .= '<a style="margin: 2px;" class="btn btn-primary" href="'.route('downloadpdf',['store_location_id' => base64_encode($item->store_locationId),'shift_id' => base64_encode($item->shift_id),'type'=>"loading_sheet"]).'" title="Details"> <i class="fa fa-file-pdf"></i></a>';
                                        }else{  
                                            $item->links .= '<a style="margin: 2px;" class="btn btn-primary btn-sm" href="'.route('dispatched-loading-sheets.edit',base64_encode($item->id)).'" title="Details">Generate Store C Requisition</a>';
                                        }

                                       
                                    }
                                }

                                
                                return $item;
                     // }else{
                     //    $item = [];
                     //    return $item;
                     // }
                });      
                $total = 0;
                /*foreach ($response as $value) {
                    $total += $value->total;
                }*/      
                              
                $return = [
                    "draw"              =>  intval($draw),
                    "recordsFiltered"   =>  intval( $totalCms),
                    "recordsTotal"      =>  intval( $totalCms),
                    "data"              =>  $response,
                    'total'             =>  manageAmountFormat($total)
                ];
                return $return;
            }

            return view('admin.dispatched_loading_sheets.index', compact('user','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function edit($id)
    {
        $id = base64_decode($id);
        $formdata =  array('status' => 2);
        $data = DispatchLoadedProducts::where('id',$id)->update($formdata);
        if($data){
            \Session::flash('success', 'Generated Succesfully');
            return redirect()->back();
        }
    }


    public function downloadpdf($storelocationId, $shiftId,$type="loading_sheet"){
        $storelocationId = base64_decode($storelocationId);
        $shiftId = base64_decode($shiftId);

        

        $dataQry = DispatchLoadedProducts::with(['getSalesMan','getShift','getStoreLocation','getInventoryItem']);

        if($type=="invoice"){
            $dataQry->where(['store_location_id' => $storelocationId, 'document_no' => $shiftId]);
        }else if($type=="loading_sheet"){
            $dataQry->where(['store_location_id' => $storelocationId, 'shift_id' => $shiftId]);
        }

        $data=$dataQry->get();

        // pre($data->toArray()); 

        
        $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_loaded_requisition_report_pdf',compact('data'));
            $report_name = 'invoice_dispatch_report_' . date('Y_m_d_H_i_A');
            return $pdf->download($report_name.'.pdf');
    }


}