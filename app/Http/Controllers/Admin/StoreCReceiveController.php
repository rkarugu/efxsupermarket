<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaStoreCReceive;
use App\Model\WaStoreCReceiveItems;
use App\Model\WaStockMoveC;
use Session;
use DB;
class StoreCReceiveController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'store-c-receive';
        $this->title = 'Store C Receive';
        $this->pmodule = 'store-c-receive';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            if($request->ajax()){
                $sortable_columns = [
                    'wa_store_c_receives.id',
                    'users.name',
                    'wa_store_c_receives.receive_code',
                    // 'wa_store_c_receives.user_id',
                ];
                $limit          = $request->input('length');
                $start          = $request->input('start');
                $search         = $request['search']['value'];
                $orderby        = $request['order']['0']['column'] ?? 'id';
                $order          = $request['order']['0']['dir'] ?? "DESC";
                $draw           = $request['draw'];          
                $data = WaStoreCReceive::with(['items'])->select(['wa_store_c_receives.*','users.name as user_name'])->where(function($w) use ($user,$request,$search){
                    if($request->input('from') && $request->input('to')){
                        $w->whereBetween('date',[$request->input('from'),$request->input('to')]);
                    }          
                    // if($user->role_id != 1){
                    //     $w->where('user_id',$user->id);
                    // }          
                })->where(function($w) use ($search){
                    if($search){
                        $w->orWhere('wa_store_c_receives.receive_code','LIKE',"%$search%");
                        $w->orWhere('users.name','LIKE',"%$search%");
                    }
                })->leftjoin('users',function($join){
                    $join->on('users.id','=','wa_store_c_receives.user_id');
                })->orderBy($sortable_columns[$orderby],$order);                
                $response       = $data->limit($limit)->offset($start)->get()->map(function($item) use ($permission,$user){
                    $item->user_name = @$item->user_name;
                    $item->date_time = $item->date.' / '.$item->time;
                    $tot = $item->items->sum('qty');
                    $item->total = @$tot;
                    $item->links = '';
                    if ($item->status == 'PENDING'){
                        $item->links .= '<a style="margin: 2px;" class="btn btn-warning btn-sm" href="'.route('store-c-receive.edit',base64_encode($item->id)).'" title="Details"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }
                    $item->links .= '<a style="margin: 2px;"  class="btn btn-danger btn-sm" href="'.route('store-c-receive.show',base64_encode($item->id)).'" title="Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    if($item->status != 'PENDING'){
                        $item->links .= '<a style="margin: 2px;"  class="btn btn-primary btn-sm printBill" onclick="printBill(this); return false;" href="'.route('store-c-receive.invoice_print',base64_encode($item->id)).'" title="Print"><i class="fa fa-print" aria-hidden="true"></i></a>';
                        //$item->links .= '<a style="margin: 2px;"  class="btn btn-warning btn-sm" href="'.route('store-c-receive.exportToPdf',base64_encode($item->id)).'" title="PDF">
                        //             <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        //         </a>';
                                
                        // if (($item->items->where('store_location_id',$user->wa_location_and_store_id)->where('is_return',0)->count() != 0 && $permission != 'superadmin') || ($permission == 'superadmin' && $item->items->where('is_return',0)->count() != 0)){
                            // $item->links .= '<a  style="margin: 2px;" class="btn btn-success btn-sm" href="'.route('store-c-receive.return_items',base64_encode($item->id)).'" title="Return">
                            //                     <i class="fa fa-retweet" aria-hidden="true"></i>
                            //                 </a>';
                        // }
                    }
                    return $item;
                });      
                $total = 0;
                foreach ($response as $value) {
                    $total += $value->total;
                }      
                $totalCms       = $data->count();                
                $return = [
                    "draw"              =>  intval($draw),
                    "recordsFiltered"   =>  intval( $totalCms),
                    "recordsTotal"      =>  intval( $totalCms),
                    "data"              =>  $response,
                    'total'             =>  manageAmountFormat($total)
                ];
                return $return;
            }

            return view('admin.store_c_receive.index', compact('user','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.store_c_receive.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function getInventryItemDetails(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $editPermission = '';
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___edit-values']) && $permission != 'superadmin') {
            $editPermission = 'readonly';
        }
        $data = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->where(function($e) use ($request){
            if($request->store_c){
                $e->where('store_c_deleted',0);
            }
        })->with(['getTaxesOfItem','pack_size'])->where('id',$request->id)->first();
        $view = '';
        if($data){
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id['.$data->id.']" class="itemid" value="'.$data->id.'">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="'.$data->stock_id_code.'">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td><input style="padding: 3px 3px;" readonly type="text" name="item_description['.$data->id.']" data-id="'.$data->id.'"  class="form-control" value="'.$data->description.'"></td>
            <td>'.($data->quantity ?? 0).'</td>
            <td><input style="padding: 3px 3px;" readonly type="text" name="item_unit['.$data->id.']" data-id="'.$data->id.'"  class="form-control" value="'.($data->pack_size->title ?? NULL).'" readonly></td>
            <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)"  type="number" name="item_quantity['.$data->id.']" data-id="'.$data->id.'"  class="quantity form-control" value=""></td>
            <td><input type="hidden" name="store_location_id['.$data->id.']">'.(@$data->location->location_name).'</td>
            
           
            <td>
            <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>
            </tr>';
        }
        return response()->json($view);
    }


    public function store(Request $request)
    {
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___add']) && $permission != 'superadmin') {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted:You Dont have permissions'
            ]);
        }
        $validation = \Validator::make($request->all(),[
            'item_id'=>'array',
            'item_id.*'=>'required|exists:wa_inventory_items,id',
            'item_quantity.*'=>'required|min:1|numeric',         
            'time'=>'required',
            'request_type'=>'required|in:send_request,save'
        ],[
            'item_quantity.*.min'=>'Quantity must be greater than or equal to 1',
        ],[
            'item_id.*'=>'Item',
            'item_quantity.*'=>'Quantity',
        ]);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }        
        // dd('y');
        $allInventroy = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMovesC','getInventoryCategoryDetail','getInventoryCategoryDetail.getWIPGlDetail','getInventoryCategoryDetail.getStockGlDetail','getInventoryCategoryDetail.getIssueGlDetail'])->whereIn('id',$request->item_id)->get();
        if(count($allInventroy)==0){
            return response()->json([
                'result'=>-1,
                'message'=>'Inventroy Items is required'
            ]);
        }
        foreach ($allInventroy as $key => $value) {   
            if(!$value->store_location_id){
                return response()->json([
                    'result'=>0,
                    'errors'=>['store_location_id.'.$value->id=>['Location is required']]
                ]);
            }             
            if($request->request_type == 'send_request'){
                // if(!$request->item_quantity[$value->id] || $value->quantity < $request->item_quantity[$value->id]){
                //     return response()->json([
                //         'result'=>0,
                //         'errors'=>['item_quantity.'.$value->id=>['Quantity cannot be greater than balance stock']]
                //     ]);
                // }
            }
            if($value->block_this == 1){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_id.'.$value->id=>['The product has been blocked from sale due to a change in standard cost']]
                ]);
            }
        }

        // return response()->json([
        //     'result'=>1,
        //     'message'=>'Process is running'
        // ]);
        $check = DB::transaction(function () use ($allInventroy,$request){
            $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'Receive_Stock_storeC')->first();
            $sale_invoiceno = getCodeWithNumberSeries('Receive_Stock_storeC');
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            $parent = new WaStoreCReceive;
            $parent->date =date('Y-m-d');
            $parent->time = $request->time;
            $parent->receive_code = $sale_invoiceno;
            $parent->user_id = $getLoggeduserProfile->id;
            $parent->save();
            $childs = [];
            $glTrans = [];
            $total = 0;
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            foreach ($allInventroy as $key => $value) {
                $stock_qoh = $value->getAllFromStockMovesC->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0;
                $data = [];
                $data['wa_store_c_receive_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['qty'] = $request->item_quantity[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['current_stock_balance'] = $stock_qoh;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                    $total += (0);
                
                    $stock_qoh += $data['qty'];

                    $stockMove = new WaStockMoveC();
                    $stockMove->user_id = $getLoggeduserProfile->id;
                    $stockMove->wa_store_c_receive_id = $parent->id;
                    $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    $stockMove->stock_id_code = $value->stock_id_code;
                    $stockMove->wa_inventory_item_id = @$value->id;
                    $stockMove->document_no =   $parent->receive_code;
                    $stockMove->price = 0;
                    $stockMove->grn_type_number = $series_module->type_number;
                    $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    $stockMove->refrence = $parent->receive_code;
                    $stockMove->qauntity = $data['qty'];
                    $stockMove->new_qoh = $stock_qoh;
                    $stockMove->standard_cost = $value->standard_cost;
                    $stockMove->save();

                    
                }

            }
            WaStoreCReceiveItems::insert($childs);
            
            if($request->request_type == 'send_request'){
                $parent->status = 'Completed';
            }else {
                $parent->status = 'PENDING';
            }
            $parent->save();
            
            updateUniqueNumberSeries('Receive_Stock_storeC',$parent->receive_code);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                $requestty = 'send_request';
                $location = route('store-c-receive.index');
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('store-c-receive.index');
            }
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'requestty'=>$requestty]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }

    public function edit($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $getLoggeduserProfile = getLoggeduserProfile();
            $data = WaStoreCReceive::with(['items','user','items.item','items.item.getAllFromStockMovesC'=>function($w) use ($getLoggeduserProfile){
                $w->where('wa_location_and_store_id',$getLoggeduserProfile->wa_location_and_store_id);
            },'items.item.pack_size','items.location'])->where('status','PENDING')->where('id',$id)->first();
            if(!$data){
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $editPermission = '';
            if (!isset($permission[$pmodule . '___edit-values']) && $permission != 'superadmin') {
                $editPermission = 'readonly';
            }
            return view('admin.store_c_receive.edit', compact('title', 'model', 'breadcum', 'pmodule', 'permission','data','editPermission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function update(Request $request,$id)
    {
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___edit']) && $permission != 'superadmin') {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted:You Dont have permissions'
            ]);
        }
        $validation = \Validator::make($request->all(),[
            'item_id'=>'array',
            'item_id.*'=>'required|exists:wa_inventory_items,id',
            'item_quantity.*'=>'required|min:1|numeric',
            'time'=>'required',
            'id'=>'required|exists:wa_store_c_receives,id',
            'request_type'=>'required|in:send_request,save'
        ],[
            'item_quantity.*.min'=>'Quantity must be greater than or equal to 1',
        ],[
            'item_id.*'=>'Item',
            'item_quantity.*'=>'Quantity',
        ]);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }
        if($request->payment_method == 2 && $request->cash <= 0){
            return response()->json([
                'result'=>0,
                'errors'=>['cash'=>['Cash Amount must be greater than 0']]
            ]);
        }
        // dd('y');
        $allInventroy = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMovesC','getInventoryCategoryDetail','getInventoryCategoryDetail.getWIPGlDetail','getInventoryCategoryDetail.getStockGlDetail','getInventoryCategoryDetail.getIssueGlDetail'])->whereIn('id',$request->item_id)->get();
        if(count($allInventroy)==0){
            return response()->json([
                'result'=>-1,
                'message'=>'Inventroy Items is required'
            ]);
        }
        foreach ($allInventroy as $key => $value) {   
            if(!$value->store_location_id){
                return response()->json([
                    'result'=>0,
                    'errors'=>['store_location_id.'.$value->id=>['Location is required']]
                ]);
            }     
            if($request->request_type == 'send_request'){
                // if(!$request->item_quantity[$value->id] || $value->quantity < $request->item_quantity[$value->id]){
                //     return response()->json([
                //         'result'=>0,
                //         'errors'=>['item_quantity.'.$value->id=>['Quantity cannot be greater than balance stock']]
                //     ]);
                // }
            }
            if($value->block_this == 1){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_id.'.$value->id=>['The product has been blocked from sale due to a change in standard cost']]
                ]);
            }
        }
        $parent = WaStoreCReceive::where('id',$request->id)->first();
        if(!$parent || $parent->status != 'PENDING'){
            return response()->json(['result'=>-1,'message'=>'Something went wrong!']);  
        }
        $check = DB::transaction(function () use ($allInventroy,$request,$parent){
            $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'Receive_Stock_storeC')->first();
            $getLoggeduserProfile = getLoggeduserProfile();
            $parent->user_id = $getLoggeduserProfile->id;
            $parent->save();
            $childs = [];
            $glTrans = [];
            $total = 0;
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            $dateTime = date('Y-m-d H:i:s');
            WaStoreCReceiveItems::where('wa_store_c_receive_id',$parent->id)->delete();
            foreach ($allInventroy as $key => $value) {
                $stock_qoh = $value->getAllFromStockMovesC->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0;
                $data = [];
                $data['wa_store_c_receive_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['qty'] = $request->item_quantity[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['current_stock_balance'] = $stock_qoh;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                    $total += (0);                
                    $stock_qoh += $data['qty'];
                    $stockMove = new WaStockMoveC();
                    $stockMove->user_id = $getLoggeduserProfile->id;
                    $stockMove->wa_store_c_receive_id = $parent->id;
                    $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    $stockMove->stock_id_code = $value->stock_id_code;
                    $stockMove->wa_inventory_item_id = @$value->id;
                    $stockMove->document_no =   $parent->receive_code;
                    $stockMove->price = 0;
                    $stockMove->grn_type_number = $series_module->type_number;
                    $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    $stockMove->refrence = $parent->receive_code;
                    $stockMove->qauntity = $data['qty'];
                    $stockMove->new_qoh = $stock_qoh;
                    $stockMove->standard_cost = $value->standard_cost;
                    $stockMove->save();
                    
                }

            }
            WaStoreCReceiveItems::insert($childs);
            
            if($request->request_type == 'send_request'){
                $parent->status = 'Completed';
            }else {
                $parent->status = 'PENDING';
            }
            $parent->save();
           
            // updateUniqueNumberSeries('Receive_Stock_storeC',$parent->sales_no);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                $requestty = 'send_request';
                $location = route('store-c-receive.index');
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('store-c-receive.index');
            }
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'requestty'=>$requestty]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }


    //dispatch
 
    public function show($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaStoreCReceive::with(['items','user','items.item','items.item.pack_size','items.location'])->where('id',$id)->first();
            if(!$data){
                Session::flash('warning','Invalid Request');
                return redirect()->back();
            }
            return view('admin.store_c_receive.show', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function invoice_print($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = WaStoreCReceive::with(['items','user','items.item','items.item.pack_size','items.location'])->where('id',$id)->first();
        if(!$data){
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
        return view('admin.store_c_receive.print', compact('data','title', 'model','pmodule', 'permission'));
    }

    public function exportToPdf($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = WaStoreCReceive::with(['items','user','items.item','items.item.pack_size','items.location','items.dispatch_by'])->where('id',$id)->first();
        if(!$data){
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
        $pdf = \PDF::loadView('admin.store_c_receive.print', compact('data','title', 'model','pmodule', 'permission'));
        $report_name = 'store_c_receive_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
    }
  
}