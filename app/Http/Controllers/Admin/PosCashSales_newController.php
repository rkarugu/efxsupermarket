<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesItems;
use App\Model\PaymentMethod;
use App\Model\WaGlTran;
use App\Model\WaStockMove;
use App\Model\WaPosCashSalesDispatch;
use App\Model\WaInternalRequisitionDispatch;
use Session;
use DB;
class PosCashSales_newController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'pos-cash-sales-new';
        $this->title = 'POS Cash Sales';
        $this->pmodule = 'pos-cash-sales-new';
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
                    'wa_pos_cash_sales.id',
                    'users.name',
                    'wa_pos_cash_sales.sales_no',
                    'wa_pos_cash_sales.user_id',
                    'wa_pos_cash_sales.customer',
                    'wa_pos_cash_sales.customer',
                    'wa_pos_cash_sales.cash',
                    'wa_pos_cash_sales.change',
                ];
                $limit          = $request->input('length');
                $start          = $request->input('start');
                $search         = $request['search']['value'];
                $orderby        = $request['order']['0']['column'] ?? 'id';
                $order          = $request['order']['0']['dir'] ?? "DESC";
                $draw           = $request['draw'];          
                $data = WaPosCashSales::with(['items','payment'])->select(['wa_pos_cash_sales.*','users.name as user_name'])->where(function($w) use ($user,$request,$search){
                    if($request->input('from') && $request->input('to')){
                        $w->whereBetween('date',[$request->input('from'),$request->input('to')]);
                    }          
                    if($user->role_id != 1){
                        $w->where('user_id',$user->id);
                    }          
                })->where(function($w) use ($search){
                    if($search){
                        $w->orWhere('wa_pos_cash_sales.sales_no','LIKE',"%$search%");
                        $w->orWhere('wa_pos_cash_sales.customer','LIKE',"%$search%");
                        $w->orWhere('wa_pos_cash_sales.cash','LIKE',"%$search%");
                        $w->orWhere('wa_pos_cash_sales.change','LIKE',"%$search%");
                        $w->orWhere('users.name','LIKE',"%$search%");
                    }
                })->leftjoin('users',function($join){
                    $join->on('users.id','=','wa_pos_cash_sales.user_id');
                })->orderBy($sortable_columns[$orderby],$order);       
                $totalCms       = count($data->get());          
                $response       = $data->limit($limit)->offset($start)->get()->map(function($item) use ($permission,$user){
                    $item->user_name = @$item->user_name;
                    $item->date_time = $item->date.' / '.$item->time;
                    $item->payment_title = @$item->payment->title;
                    $tot = 0;
                    foreach ($item->items as $child){
                        $tot += ($child->qty*$child->selling_price) - $child->discount_amount;                                
                    }
                    $item->total = @$tot;
                    $item->links = '';
                    if ($item->status == 'PENDING'){
                        $item->links .= '<a style="margin: 2px;" class="btn btn-secondary btn-sm" href="'.route('pos-cash-sales-new.edit',base64_encode($item->id)).'" title="Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }
                    $item->links .= '<a style="margin: 2px;"  class="btn btn-danger btn-sm" href="'.route('pos-cash-sales-new.show',base64_encode($item->id)).'" title="Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    if($item->status != 'PENDING'){
                        if($permission == 'superadmin' || isset($permission['pos-cash-sales___print'])){
                            $item->links .= '<a style="margin: 2px;"  class="btn btn-primary btn-sm printBill" onclick="printBill(this); return false;" href="'.route('pos-cash-sales.invoice_print',base64_encode($item->id)).'" title="Print"><i class="fa fa-print" aria-hidden="true"></i></a>';
                        }
                        if($permission == 'superadmin' || isset($permission['pos-cash-sales___pdf'])){
                            $item->links .= '<a style="margin: 2px;"  class="btn btn-warning btn-sm" href="'.route('pos-cash-sales.exportToPdf',base64_encode($item->id)).'" title="PDF">
                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </a>';
                        }
                        if($permission == 'superadmin' || isset($permission['pos-cash-sales-new___return'])){
                        // if (($item->items->where('store_location_id',$user->wa_location_and_store_id)->where('is_return',0)->count() != 0 && $permission != 'superadmin') || ($permission == 'superadmin' && $item->items->where('is_return',0)->count() != 0)){
                            $item->links .= '<a  style="margin: 2px;" class="btn btn-success btn-sm" href="'.route('pos-cash-sales-new.return_items',base64_encode($item->id)).'" title="Return">
                                                <i class="fa fa-retweet" aria-hidden="true"></i>
                                            </a>';
                        }
                        // }
                    }
                    return $item;
                });      
                $total = 0;
                foreach ($response as $value) {
                    $total += $value->total;
                }      
                              
                $return = [
                    "draw"              =>  intval($draw),
                    "recordsFiltered"   =>  intval( $totalCms),
                    "recordsTotal"      =>  intval( $totalCms),
                    "data"              =>  $response,
                    'total'             =>  manageAmountFormat($total)
                ];
                return $return;
            }

            return view('admin.pos_cash_sales_new.index', compact('user','title', 'model', 'breadcum', 'pmodule', 'permission'));
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
            return view('admin.pos_cash_sales_new.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
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
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getTaxesOfItem','pack_size'])->where('id',$request->id)->first();
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
            <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)"  type="text" name="item_quantity['.$data->id.']" data-id="'.$data->id.'"  class="quantity form-control" value=""></td>
            <td><input style="padding: 3px 3px;" '.$editPermission.' onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_selling_price['.$data->id.']" data-id="'.$data->id.'"  class="selling_price form-control send_me_to_next_item" value="'.$data->selling_price.'"></td>
            <td><input type="hidden" name="store_location_id['.$data->id.']">'.(@$data->location->location_name).'</td>';
            
            $view .='<td><select class="form-control vat_list send_me_to_next_item" name="item_vat['.$data->id.']" '.$editPermission.'>';
            $per = 0;
            $vat = 0.00;
            if($data->getTaxesOfItem){
                $view .='<option value="'.$data->getTaxesOfItem->id.'" selected>'.$data->getTaxesOfItem->title.'</option>';
                $per = $data->getTaxesOfItem->tax_value;
                $vat = round($data->selling_price - (($data->selling_price*100) / ($per+100)),2);
            }
            $view .='</select>
            <input type="hidden" class="vat_percentage" value="'. $per .'"  name="item_vat_percentage['.$data->id.']">
            </td>';
            $view .='<td><input style="padding: 3px 3px;" '.$editPermission.' onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_discount_per['.$data->id.']" data-id="'.$data->id.'" class="discount_per form-control send_me_to_next_item" value="0.00"></td>
            <td><input style="padding: 3px 3px;" '.$editPermission.' type="text" name="item_discount['.$data->id.']" data-id="'.$data->id.'"  class="discount form-control send_me_to_next_item" value="0.00"></td>
           
            <td><span class="vat">'.($vat*0).'</span></td>
            <td><span class="total">'.($data->selling_price*0).'</span></td>
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
            'item_selling_price.*'=>'required|min:1|numeric',
            'item_vat.*'=>'required|exists:tax_managers,id',
            'item_discount_per.*'=>'nullable|min:0|numeric',
            'time'=>'required',
            'customer_name'=>'required|max:200|min:1',
            'payment_method'=>'required|exists:payment_methods,id',
            'cash'=>'required_if:payment_method,==,2|min:1',
            'request_type'=>'required|in:send_request,save',
            'esd'=>'required_if:request_type,==,send_request',
        ],[
            'cash.min'=>'Cash Amount must be greater than or equal to 1',
            'item_discount_per.*.min'=>'Discount must be greater than or equal to 0',
            'item_quantity.*.min'=>'Quantity must be greater than or equal to 1',
            'item_selling_price.*.min'=>'Selling Price must be greater than or equal to 1',
        ],[
            'item_id.*'=>'Item',
            'item_quantity.*'=>'Quantity',
            'item_selling_price.*'=>'Price',
            'item_discount_per.*'=>'Discount',
            'item_vat.*'=>'Vat',
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
        $allInventroy = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMoves','getInventoryCategoryDetail','getInventoryCategoryDetail.getWIPGlDetail','getInventoryCategoryDetail.getStockGlDetail','getInventoryCategoryDetail.getIssueGlDetail'])->whereIn('id',$request->item_id)->get();
        if(count($allInventroy)==0){
            return response()->json([
                'result'=>-1,
                'message'=>'Inventroy Items is required'
            ]);
        }
        $total = 0;
        

        foreach ($allInventroy as $key => $value) {   
            if(!$value->store_location_id){
                return response()->json([
                    'result'=>0,
                    'errors'=>['store_location_id.'.$value->id=>['Location is required']]
                ]);
            }                     
            if(!$request->item_selling_price[$value->id] || $value->standard_cost > $request->item_selling_price[$value->id]){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_selling_price.'.$value->id=>['Selling price must be greater than or equal to standard cost']]
                ]);
            }
            if($request->request_type == 'send_request'){
                if(!$request->item_quantity[$value->id] || $value->quantity < $request->item_quantity[$value->id]){
                    return response()->json([
                        'result'=>0,
                        'errors'=>['item_quantity.'.$value->id=>['Quantity cannot be greater than balance stock']]
                    ]);
                }
            }
            if($value->block_this == 1){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_id.'.$value->id=>['The product has been blocked from sale due to a change in standard cost']]
                ]);
            }
            $sum = ($request->item_selling_price[$value->id]*$request->item_quantity[$value->id]);
            $disAmount = ($sum*@$request->item_discount_per[$value->id])/100;
            $total += ($sum - $disAmount);

        }
        if($request->payment_method == 2 && ($request->cash - $total) < 0){
            return response()->json([
                'result'=>0,
                'errors'=>['cash_change'=>['Change Amount must be equal or greater than cash']]
            ]);
        }
        
        $check = DB::transaction(function () use ($allInventroy,$request){
            $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'CASH_SALES')->first();
            $paymentMethod = PaymentMethod::with(['paymentGlAccount'])->where('id', $request->payment_method)->first();
            // $sale_invoiceno = getCodeWithNumberSeries('CASH_SALES');
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            $parent = new WaPosCashSales;
            $parent->date =date('Y-m-d');
            $parent->time = $request->time;
            $parent->user_id = $getLoggeduserProfile->id;
            $parent->customer = $request->customer_name;
            $parent->payment_method_id = $request->payment_method;
            $parent->cash = $request->cash;
            $parent->change = 0;
            $file = '';
            $parent->save();
            $parent->sales_no = $series_module->code.'-'.str_pad($parent->id,5,"0",STR_PAD_LEFT);
            if($request->request_type == 'send_request'){
                $upData = \App\Model\WaEsd::where('signature',$request->esd)->first();
                if($upData){
                    $file = $upData->signature;
                    $upData->is_used = 1;
                    $upData->last_used_by = $getLoggeduserProfile->id;
                    $upData->document_no = ($upData->document_no != NULL) ? $parent->sales_no : $upData->document_no;
                    $upData->save();
                }else {
                    $upData = \App\Model\WaEsd::insert(['signature'=>$request->esd,
                            'is_used' => 1,
                            'user_id' => $getLoggeduserProfile->id,
                            'last_used_by' => $getLoggeduserProfile->id,
                            'document_no' => $parent->sales_no,                        
                        ]);
                    $file = $request->esd;
                }           
            }          
            $parent->upload_data = $file;            
            $parent->save();
            $childs = [];
            $glTrans = [];
            $total = 0;
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            foreach ($allInventroy as $key => $value) {
                $data = [];
                $data['wa_pos_cash_sales_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['qty'] = $request->item_quantity[$value->id];
                $data['selling_price'] = $request->item_selling_price[$value->id];
                $data['discount_percent'] = $request->item_discount_per[$value->id];
                $data['total'] = $data['selling_price']*$data['qty'];
                $data['discount_amount'] = ($data['total']*$data['discount_percent'])/100;
                $data['vat_percentage'] = $request->item_vat_percentage[$value->id];
                $data['vat_amount'] = ($data['total'] - $data['discount_amount']) - ((($data['total'] - $data['discount_amount'])*100) / ($data['vat_percentage']+100));
                $data['tax_manager_id'] = $request->item_vat[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['standard_cost'] = $value->standard_cost;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                    $total += ($data['total'] - $data['discount_amount']);
                
                    $stock_qoh = $value->getAllFromStockMoves->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0;
                    $stock_qoh -= $data['qty'];

                    $stockMove = new WaStockMove();
                    $stockMove->user_id = $getLoggeduserProfile->id;
                    $stockMove->wa_pos_cash_sales_id = $parent->id;
                    $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    $stockMove->stock_id_code = $value->stock_id_code;
                    $stockMove->wa_inventory_item_id = @$value->id;
                    $stockMove->document_no =   $parent->sales_no;
                    $stockMove->price = $data['total'] - $data['discount_amount'];
                    $stockMove->grn_type_number = $series_module->type_number;
                    $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    $stockMove->refrence = $parent->customer.' : '.$paymentMethod->paymentGlAccount->account_code;
                    $stockMove->qauntity = - ($data['qty']);
                    $stockMove->new_qoh = $stock_qoh;
                    $stockMove->standard_cost = $value->standard_cost;
                    $stockMove->save();

                    $description = $value->title;
                    $accno = @$value->getInventoryCategoryDetail->getWIPGlDetail->account_code;
                    //cr entries start
                
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $description,
                        'account' => $accno,
                        'amount' => '-' . (($data['total'] - $data['discount_amount']) - $data['vat_amount']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    if($data['vat_percentage']){
                        $value->standard_cost = (($value->standard_cost*100)/($data['vat_percentage']+100));
                    }
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $value->stock_id_code . '/' . $value->title . '/' . $value->standard_cost . '@' . $data['qty'],
                        'account' => @$value->getInventoryCategoryDetail->getStockGlDetail->account_code,
                        'amount' => '-' . ($value->standard_cost * $data['qty']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $value->stock_id_code . '/' . $value->title . '/' . $value->standard_cost . '@' . $data['qty'],
                        'account' => @$value->getInventoryCategoryDetail->getIssueGlDetail->account_code,
                        'amount' => ($value->standard_cost * $data['qty']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];



                    $total_invoice_amount[] = ($data['total'] - $data['discount_amount']);

                    if ($data['vat_amount'] > 0) {
                        $total_vat_amount += $data['vat_amount'];
                    }
                }

            }
            WaPosCashSalesItems::insert($childs);
            $parent->change =  ($parent->cash > 0) ? $parent->cash - $total : 0.00;
            if($request->request_type == 'send_request'){
                $parent->status = 'Completed';
            }else {
                $parent->status = 'PENDING';
            }
            $parent->save();
            if($request->request_type == 'send_request'){
                if ($total_vat_amount > 0) {
                    $taxVat = \App\Model\TaxManager::where('slug', 'vat')->first();
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => "VAT",
                        'account' => $taxVat->getInputGlAccount->account_code,
                        'amount' => '-' . $total_vat_amount,
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];        
                }
            
                if (count($total_invoice_amount) > 0) {

                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $parent->customer.' : '.$paymentMethod->paymentGlAccount->account_code,
                        'account' => $paymentMethod->paymentGlAccount->account_code,
                        'amount' => array_sum($total_invoice_amount),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    //dr entries end
                }
                if(count($glTrans)>0){
                   WaGlTran::insert($glTrans);
                }
            }
            // updateUniqueNumberSeries('CASH_SALES',$parent->sales_no);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                if (isset($permission[$pmodule . '___print']) || $permission == 'superadmin') {
                    $requestty = 'send_request';
                    $location = route('pos-cash-sales.invoice_print',base64_encode($check->id));
                }else {
                    $requestty = 'save';
                    $location = route('pos-cash-sales-new.index');
                }
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('pos-cash-sales-new.index');
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
            $data = WaPosCashSales::with(['items','items.tax_manager','user','items.item','items.item.getAllFromStockMoves'=>function($w) use ($getLoggeduserProfile){
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
            return view('admin.pos_cash_sales_new.edit', compact('title', 'model', 'breadcum', 'pmodule', 'permission','data','editPermission'));
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
            'item_selling_price.*'=>'required|min:1|numeric',
            'item_vat.*'=>'required|exists:tax_managers,id',
            'item_discount_per.*'=>'nullable|min:0|numeric',
            'time'=>'required',
            'id'=>'required|exists:wa_pos_cash_sales,id',
            'customer_name'=>'required|max:200|min:1',
            'payment_method'=>'required|exists:payment_methods,id',
            'cash'=>'required_if:payment_method,==,2|min:1',
            'request_type'=>'required|in:send_request,save'
        ],[
            'cash.min'=>'Cash Amount must be greater than or equal to 1',
            'item_discount_per.*.min'=>'Discount must be greater than or equal to 0',
            'item_quantity.*.min'=>'Quantity must be greater than or equal to 1',
            'item_selling_price.*.min'=>'Selling Price must be greater than or equal to 1',
        ],[
            'item_id.*'=>'Item',
            'item_quantity.*'=>'Quantity',
            'item_selling_price.*'=>'Price',
            'item_discount_per.*'=>'Discount',
            'item_vat.*'=>'Vat',
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
        $allInventroy = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMoves','getInventoryCategoryDetail','getInventoryCategoryDetail.getWIPGlDetail','getInventoryCategoryDetail.getStockGlDetail','getInventoryCategoryDetail.getIssueGlDetail'])->whereIn('id',$request->item_id)->get();
        if(count($allInventroy)==0){
            return response()->json([
                'result'=>-1,
                'message'=>'Inventroy Items is required'
            ]);
        }
        $total = 0; 
        foreach ($allInventroy as $key => $value) {   
            if(!$value->store_location_id){
                return response()->json([
                    'result'=>0,
                    'errors'=>['store_location_id.'.$value->id=>['Location is required']]
                ]);
            }                     
            if(!$request->item_selling_price[$value->id] || $value->standard_cost > $request->item_selling_price[$value->id]){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_selling_price.'.$value->id=>['Selling price must be greater than or equal to standard cost']]
                ]);
            }
            if($request->request_type == 'send_request'){
                if(!$request->item_quantity[$value->id] || $value->quantity < $request->item_quantity[$value->id]){
                    return response()->json([
                        'result'=>0,
                        'errors'=>['item_quantity.'.$value->id=>['Quantity cannot be greater than balance stock']]
                    ]);
                }
            }
            if($value->block_this == 1){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_id.'.$value->id=>['The product has been blocked from sale due to a change in standard cost']]
                ]);
            }
            $sum = ($request->item_selling_price[$value->id]*$request->item_quantity[$value->id]);
            $disAmount = ($sum*@$request->item_discount_per[$value->id])/100;
            $total += ($sum - $disAmount);

        }
        if($request->payment_method == 2 && ($request->cash - $total) < 0){
            return response()->json([
                'result'=>0,
                'errors'=>['cash_change'=>['Change Amount must be equal or greater than cash']]
            ]);
        }

        // return response()->json([
        //     'result'=>1,
        //     'message'=>'Restricted: Process not completed yet'
        // ]);
        $parent = WaPosCashSales::where('id',$request->id)->first();
        if(!$parent || $parent->status != 'PENDING'){
            return response()->json(['result'=>-1,'message'=>'Something went wrong!']);  
        }
        $check = DB::transaction(function () use ($allInventroy,$request,$parent){
            $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'CASH_SALES')->first();
            $paymentMethod = PaymentMethod::with(['paymentGlAccount'])->where('id', $request->payment_method)->first();
            // $sale_invoiceno = getCodeWithNumberSeries('CASH_SALES');
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            $parent->user_id = $getLoggeduserProfile->id;
            $parent->customer = $request->customer_name;
            $parent->payment_method_id = $request->payment_method;
            $parent->cash = $request->cash;
            $parent->change = 0;
            $file = '';
            if($request->request_type == 'send_request'){
                $upData = \App\Model\WaEsd::where('signature',$request->esd)->first();
                if($upData){
                    $file = $upData->signature;
                    $upData->is_used = 1;
                    $upData->last_used_by = $getLoggeduserProfile->id;
                    $upData->document_no = ($upData->document_no != NULL) ? $parent->sales_no : $upData->document_no;
                    $upData->save();
                }else {
                    $upData = \App\Model\WaEsd::insert(['signature'=>$request->esd,
                            'is_used' => 1,
                            'user_id' => $getLoggeduserProfile->id,
                            'last_used_by' => $getLoggeduserProfile->id,
                            'document_no' => $sale_invoiceno,                        
                        ]);
                    $file = $request->esd;
                }
            }
            $parent->upload_data = $file;
            $parent->save();
            $childs = [];
            $glTrans = [];
            $total = 0;
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            WaPosCashSalesItems::where('wa_pos_cash_sales_id',$parent->id)->delete();
            foreach ($allInventroy as $key => $value) {
                $data = [];
                $data['wa_pos_cash_sales_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['qty'] = $request->item_quantity[$value->id];
                $data['selling_price'] = $request->item_selling_price[$value->id];
                $data['discount_percent'] = $request->item_discount_per[$value->id];
                $data['total'] = $data['selling_price']*$data['qty'];
                $data['discount_amount'] = ($data['total']*$data['discount_percent'])/100;
                $data['vat_percentage'] = $request->item_vat_percentage[$value->id];
                $data['vat_amount'] = ($data['total'] - $data['discount_amount']) - ((($data['total'] - $data['discount_amount'])*100) / ($data['vat_percentage']+100));
                $data['tax_manager_id'] = $request->item_vat[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['standard_cost'] = $value->standard_cost;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                    $total += ($data['total'] - $data['discount_amount']);
                
                    $stock_qoh = $value->getAllFromStockMoves->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0 ?? 0;
                    $stock_qoh -= $data['qty'];

                    $stockMove = new WaStockMove();
                    $stockMove->user_id = $getLoggeduserProfile->id;
                    $stockMove->wa_pos_cash_sales_id = $parent->id;
                    $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    $stockMove->stock_id_code = $value->stock_id_code;
                    $stockMove->wa_inventory_item_id = @$value->id;
                    $stockMove->document_no =   $parent->sales_no;
                    $stockMove->price = $data['total'] - $data['discount_amount'];
                    $stockMove->grn_type_number = $series_module->type_number;
                    $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    $stockMove->refrence = $parent->customer.' : '.$paymentMethod->paymentGlAccount->account_code;
                    $stockMove->qauntity = - ($data['qty']);
                    $stockMove->new_qoh = $stock_qoh;
                    $stockMove->standard_cost = $value->standard_cost;
                    $stockMove->save();

                    $description = $value->title;
                    $accno = @$value->getInventoryCategoryDetail->getWIPGlDetail->account_code;
                    //cr entries start
                
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $description,
                        'account' => $accno,
                        'amount' => '-' . (($data['total'] - $data['discount_amount']) - $data['vat_amount']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    if($data['vat_percentage']){
                        $value->standard_cost = (($value->standard_cost*100)/($data['vat_percentage']+100));
                    }
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $value->stock_id_code . '/' . $value->title . '/' . $value->standard_cost . '@' . $data['qty'],
                        'account' => @$value->getInventoryCategoryDetail->getStockGlDetail->account_code,
                        'amount' => '-' . ($value->standard_cost * $data['qty']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $value->stock_id_code . '/' . $value->title . '/' . $value->standard_cost . '@' . $data['qty'],
                        'account' => @$value->getInventoryCategoryDetail->getIssueGlDetail->account_code,
                        'amount' => ($value->standard_cost * $data['qty']),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];



                    $total_invoice_amount[] = ($data['total'] - $data['discount_amount']);

                    if ($data['vat_amount'] > 0) {
                        $total_vat_amount += $data['vat_amount'];
                    }
                }

            }
            WaPosCashSalesItems::insert($childs);
            $parent->change =  ($parent->cash > 0) ? $parent->cash - $total : 0.00;
            if($request->request_type == 'send_request'){
                $parent->status = 'Completed';
            }else {
                $parent->status = 'PENDING';
            }
            $parent->save();
            if($request->request_type == 'send_request'){
                if ($total_vat_amount > 0) {
                    $taxVat = \App\Model\TaxManager::where('slug', 'vat')->first();
                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => "VAT",
                        'account' => $taxVat->getInputGlAccount->account_code,
                        'amount' => '-' . $total_vat_amount,
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];        
                }
            
                if (count($total_invoice_amount) > 0) {

                    $glTrans[] = [
                        'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                        'wa_pos_cash_sales_id'=>$parent->id,
                        'grn_type_number'=>$series_module->type_number,
                        'trans_date' => $dateTime,
                        'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                        'grn_last_used_number' => $series_module->last_number_used,
                        'transaction_type' => $series_module->description,
                        'transaction_no' => $parent->sales_no,
                        'narrative' => $parent->customer.' : '.$paymentMethod->paymentGlAccount->account_code,
                        'account' => $paymentMethod->paymentGlAccount->account_code,
                        'amount' => array_sum($total_invoice_amount),
                        'created_at'=>$dateTime,
                        'updated_at'=>$dateTime,
                    ];
                    //dr entries end
                }
                if(count($glTrans)>0){
                   WaGlTran::insert($glTrans);
                }
            }
            // updateUniqueNumberSeries('CASH_SALES',$parent->sales_no);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                if (isset($permission[$pmodule . '___print']) || $permission == 'superadmin') {
                    $requestty = 'send_request';
                    $location = route('pos-cash-sales.invoice_print',base64_encode($check->id));
                }else {
                    $requestty = 'save';
                    $location = route('pos-cash-sales-new.index');
                }
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('pos-cash-sales-new.index');
            }
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'requestty'=>$requestty]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }


    //dispatch
    public function dispatch_pos(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Dispatch';
        $model = 'dispatch';
        if (isset($permission['dispatch-pos-invoice-sales___dispatch']) || $permission == 'superadmin') {
            $breadcum = [$title => ''];
            $invoice = getCodeWithNumberSeries('DISPATCH-CASH-SALES');
            return view('admin.pos_cash_sales_new.dispatch', compact('title', 'model', 'breadcum', 'pmodule', 'permission','invoice'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function get_sales_list(Request $request)
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        if($request->type == "Cash Sales"){
            $data = WaPosCashSales::select(['id','sales_no as text'])->where(function($w) use ($request){
                if($request->q){
                    $w->where('sales_no','LIKE','%'.$request->q.'%');
                }
            })->whereHas('items',function($r) use ($getLoggeduserProfile){
                $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
            })->where('status','Completed')->orderBy('id','DESC')->get();
        }else{
            $data = \App\Model\WaInternalRequisition::select(['id','requisition_no as text'])->where(function($w) use ($request){
                if($request->q){
                    $w->where('requisition_no','LIKE','%'.$request->q.'%');
                }
                // $r->where('to_store_id',$getLoggeduserProfile->wa_location_and_store_id);
            })
            ->whereHas('getRelatedItem',function($r) use ($getLoggeduserProfile){
                $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
            })->where('status','COMPLETED')
            ->orderBy('id','DESC')->get();
        }
        return response()->json($data);
    }

    public function cash_sales_data($request)
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        $data = WaPosCashSales::with(['items','user','items.item','items.item.pack_size','items.dispatch_details'])->where('id',$request->id)->whereHas('items',function($r) use ($getLoggeduserProfile){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
        })->where('status','Completed')->first();
        $reponse['data']['customer'] = '';
        $reponse['data']['sold_by'] = '';
        $reponse['data']['amount'] = '';
        $reponse['data']['quantity'] = '';
        $reponse['result'] = 0;
        if(!$data){
            $reponse['message'] = 'Receipt Not found';
            return $reponse;
        }
        if($data->items->where('is_dispatched',0)->count() == 0){
            $reponse['message'] = 'All Items already dispatched';
            return $reponse;
        }
        $reponse['result'] = 1;
        $reponse['items'] = view('admin.pos_cash_sales_new.dispatchitem')->with(['data'=>$data,'getLoggeduserProfile'=>$getLoggeduserProfile])->render();
        $reponse['data']['customer'] = $data->customer;
        $reponse['data']['sold_by'] = @$data->user->name;
        $reponse['data']['amount'] = @$data->items->sum('total');
        $reponse['data']['quantity'] = @$data->items->count();
        return $reponse;
    }


    public function sales_invoice_data($request)
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        $data = \App\Model\WaInternalRequisition::with(['getRelatedItem','getRelatedItem.dispatch_details','getrelatedEmployee','getRelatedItem.getInventoryItemDetail','getRelatedItem.getInventoryItemDetail.pack_size'])->where('id',$request->id)
        ->whereHas('getRelatedItem',function($r) use ($getLoggeduserProfile){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
        })->where('status','COMPLETED')
        ->first();
        $reponse['data']['customer'] = '';
        $reponse['data']['sold_by'] = '';
        $reponse['data']['amount'] = '';
        $reponse['data']['quantity'] = '';
        $reponse['result'] = 0;
        if(!$data){
            $reponse['message'] = 'Receipt Not found';
            return $reponse;
        }
        if($data->getRelatedItem->where('is_dispatched',0)->count() == 0){
            $reponse['message'] = 'All Items already dispatched';
            return $reponse;
        }
        $reponse['result'] = 1;
        $reponse['items'] = view('admin.pos_cash_sales_new.sales_invoice_data')->with(['data'=>$data,'getLoggeduserProfile'=>$getLoggeduserProfile])->render();
        $reponse['data']['customer'] = $data->customer;
        $reponse['data']['sold_by'] = @$data->getrelatedEmployee->name;
        $reponse['data']['amount'] = @$data->getRelatedItem->sum('total_cost_with_vat');
        $reponse['data']['quantity'] = @$data->getRelatedItem->count();
        return $reponse;
    }

    public function get_sales_list_details(Request $request)
    {
        if($request->type == "Cash Sales"){
            $reponse = $this->cash_sales_data($request);
        }else{
            $reponse = $this->sales_invoice_data($request);
        }
        return response()->json($reponse);
    }

    public function post_dispatch(Request $request)
    {
        // dd(date('H:i:s',strtotime($request->time)));
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission['dispatch-pos-invoice-sales___dispatch']) && $permission != 'superadmin') {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted:You Don\'t have permissions'
            ]);
        }
        $validation = \Validator::make($request->all(),[
            'type'=>'required|in:Cash Sales,Sales Invoice',
        ]);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }
        if($request->type == 'Cash Sales'){
            return $this->cash_sales_dispatch($request);
        }
        if($request->type == 'Sales Invoice'){
            return $this->sales_invoice_dispatch($request);
        }
        return response()->json([
            'result'=>-1,
            'message'=>'Something went wrong',
        ]);
    }
    public function sales_invoice_dispatch($request)
    {
        $validation = \Validator::make($request->all(),[
            'receipt_no'=>'required|exists:wa_internal_requisitions,id',
            'time'=>'required',
            'item_id'=>'required|array',
            'item_id.*'=>'required|exists:wa_internal_requisition_items,id',
            'item_qty.*'=>'required|numeric',
            // 'disp_no'=>'required|unique:wa_internal_requisition_dispatch,desp_no'
        ],[
            // 'disp_no.unique'=>'This Disp No is in use, Refresh to get new one'
        ],['item_id.*'=>'Item','item_qty.*'=>'Qty']);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }
        $getLoggeduserProfile = getLoggeduserProfile();
        $data = \App\Model\WaInternalRequisition::with(['getRelatedItem'=>function($r) use ($getLoggeduserProfile,$request){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id)->whereIn('id',$request->item_id);
        },'getRelatedItem.dispatch_details'])->where('id',$request->receipt_no)->whereHas('getRelatedItem',function($r) use ($getLoggeduserProfile){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
        })->where('status','COMPLETED')->first();
        if(!$data){
            return response()->json(['result'=>0,'errors'=>['receipt_no'=>['Receipt Not found']]]);
        }
        if($data->getRelatedItem->where('is_dispatched',0)->count() == 0){
            return response()->json(['result'=>0,'errors'=>['receipt_no'=>['All Items already dispatched']]]);
        }
        $dispatchError = [];
        foreach($data->getRelatedItem->where('is_dispatched',0) as $item){
            $quantity = $item->quantity - @$item->dispatch_details->sum('dispatch_quantity');
            if(!isset($request->item_qty[$item->id]) || $quantity < @$request->item_qty[$item->id] || @$request->item_qty[$item->id] <= 0){
                $dispatchError['item_qty.'.$item->id] = ['Invalid quantity'];            
            }
        }
        if(count($dispatchError)>0){
            return response()->json(['result'=>0,'errors'=>$dispatchError]);
        }
        $check = DB::transaction(function () use ($request,$data,$getLoggeduserProfile){
            // $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'DISPATCH-CASH-SALES')->first();
            $sale_invoiceno = $request->disp_no = getCodeWithNumberSeries('DISPATCH-CASH-SALES');
            $dispatch = [];
            $ids = [];
            foreach($data->getRelatedItem->where('is_dispatched',0) as $positem){
                $dispatch[]=[
                    'desp_no'=>$sale_invoiceno,
                    'wa_internal_requisition_id'=>$data->id,
                    'wa_internal_requisition_item_id'=> $positem->id,
                    'dispatched_time'=>date('Y-m-d').' '.date('H:i:s',strtotime($request->time)),
                    'dispatched_by'=>$getLoggeduserProfile->id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'dispatch_quantity'=>@$request->item_qty[$positem->id]
                ];
                $quantity = $positem->quantity - @$positem->dispatch_details->sum('dispatch_quantity');
                if($quantity >= @$request->item_qty[$positem->id]){
                    $ids[] = $positem->id;
                }                
            }
            if(count($ids)>0){
                \App\Model\WaInternalRequisitionItem::whereIn('id',$ids)->update([
                    'is_dispatched'=>1,
                    'dispatched_by'=>$getLoggeduserProfile->id,
                    'dispatched_time'=>date('Y-m-d').' '.date('H:i:s',strtotime($request->time)),
                    'dispatch_no'=>$sale_invoiceno
                ]);
            }
            WaInternalRequisitionDispatch::insert($dispatch);
            updateUniqueNumberSeries('DISPATCH-CASH-SALES',$sale_invoiceno);
            return true;
        });
        if($check){
            return response()->json(['result'=>1,'message'=>'Dispatch processed successfully.','location'=>route('pos-cash-sales-new.dispatch')]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }
    public function cash_sales_dispatch($request)
    {
        $validation = \Validator::make($request->all(),[
            'receipt_no'=>'required|exists:wa_pos_cash_sales,id',
            'time'=>'required',
            'item_id'=>'required|array',
            'item_id.*'=>'required|exists:wa_pos_cash_sales_items,id',
            'item_qty.*'=>'required|numeric',
            // 'disp_no'=>'required|unique:wa_pos_cash_sales_dispatch,desp_no'
        ],['disp_no.unique'=>'This Disp No is in use, Refresh to get new one']);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }
        $getLoggeduserProfile = getLoggeduserProfile();
        $data = WaPosCashSales::with(['items'=>function($r) use ($getLoggeduserProfile,$request){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id)->whereIn('id',$request->item_id);
        },'user','items.item','items.item.pack_size','items.dispatch_details'])->where('id',$request->receipt_no)->whereHas('items',function($r) use ($getLoggeduserProfile){
            $r->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id);
        })->where('status','Completed')->first();
        if(!$data){
            return response()->json(['result'=>0,'errors'=>['receipt_no'=>['Receipt Not found']]]);
        }
        if($data->items->where('is_dispatched',0)->count() == 0){
            return response()->json(['result'=>0,'errors'=>['receipt_no'=>['All Items already dispatched']]]);
        }
        $dispatchError = [];
        foreach($data->items->where('is_dispatched',0) as $item){
            $qty = $item->qty - @$item->dispatch_details->sum('dispatch_quantity');
            if(!isset($request->item_qty[$item->id]) || $qty < @$request->item_qty[$item->id] || @$request->item_qty[$item->id] <= 0){
                $dispatchError['item_qty.'.$item->id] = ['Invalid quantity'];            
            }
        }
        if(count($dispatchError)>0){
            return response()->json(['result'=>0,'errors'=>$dispatchError]);
        }
        $check = DB::transaction(function () use ($request,$data,$getLoggeduserProfile){
            // $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'DISPATCH-CASH-SALES')->first();
            $sale_invoiceno = $request->disp_no = getCodeWithNumberSeries('DISPATCH-CASH-SALES');
            $dispatch = [];
            $ids = [];
            foreach($data->items->where('is_dispatched',0) as $positem){
                $dispatch[]=[
                    'desp_no'=>$sale_invoiceno,
                    'pos_sales_id'=>$data->id,
                    'pos_sales_item_id'=> $positem->id,
                    'dispatched_time'=>date('Y-m-d').' '.date('H:i:s',strtotime($request->time)),
                    'dispatched_by'=>$getLoggeduserProfile->id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'dispatch_quantity'=>@$request->item_qty[$positem->id]
                ];
                $qty = $positem->qty - @$positem->dispatch_details->sum('dispatch_quantity');
                if($qty >= @$request->item_qty[$positem->id]){
                    $ids[] = $positem->id;
                }
            }
            if(count($ids)>0){
                WaPosCashSalesItems::whereIn('id',$ids)->update([
                    'is_dispatched'=>1,
                    'dispatched_by'=>$getLoggeduserProfile->id,
                    'dispatched_time'=>date('Y-m-d').' '.date('H:i:s',strtotime($request->time)),
                    'dispatch_no'=>$sale_invoiceno
                ]);
            }
            WaPosCashSalesDispatch::insert($dispatch);
            updateUniqueNumberSeries('DISPATCH-CASH-SALES',$sale_invoiceno);
            return true;
        });
        if($check){
            return response()->json(['result'=>1,'message'=>'Dispatch processed successfully.','location'=>route('pos-cash-sales-new.dispatch')]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }
    public function show($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaPosCashSales::with(['items','user','items.item','items.item.pack_size','items.location','items.dispatch_by'])->where('id',$id)->first();
            if(!$data){
                Session::flash('warning','Invalid Request');
                return redirect()->back();
            }
            return view('admin.pos_cash_sales_new.show', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission'));
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
        $data = WaPosCashSales::with(['items','user','items.item','items.item.pack_size','items.location','items.dispatch_by'])->where('id',$id)->first();
        if(!$data){
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
        $data->print_count++;
        $data->save();
        return view('admin.pos_cash_sales_new.print', compact('data','title', 'model','pmodule', 'permission'));
    }

    public function exportToPdf($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = WaPosCashSales::with(['items','user','items.item','items.item.pack_size','items.location','items.dispatch_by'])->where('id',$id)->first();
        if(!$data){
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
        $data->print_count++;
        $data->save();
        $pdf = \PDF::loadView('admin.pos_cash_sales_new.print', compact('data','title', 'model','pmodule', 'permission'));
        $report_name = 'pos_cash_sales_new_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
    }
    public function return_items($id)
    {
        $user = getLoggeduserProfile();
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___return']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaPosCashSales::with(['items'=>function($e) use ($permission,$user){$e->where('is_return',0)->where(function($w) use ($permission,$user)
                {
                    // if($permission != 'superadmin'){
                    //     $w->where('store_location_id',$user->wa_location_and_store_id);
                    // }
                });},'user','items.item','items.item.pack_size','items.location','items.dispatch_by'])
            ->whereHas('items',function($e) use ($permission,$user){$e->where('is_return',0)->where(function($w) use ($permission,$user)
                {
                    // if($permission != 'superadmin'){
                    //     $w->where('store_location_id',$user->wa_location_and_store_id);
                    // }
                });
            })->where('status','Completed')
            ->where('id',$id)->first();
            if(!$data){
                Session::flash('warning','No Items for return available');
                return redirect()->back();
            }
            return view('admin.pos_cash_sales_new.return_items', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function return_items_post($id,Request $request)
    {
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___return']) && $permission != 'superadmin') {
            return response()->json([
                'result'=>-1,
                'message'=>'Restricted:You Dont have permissions'
            ]);
        }
        $validation = \Validator::make($request->all(),[
            'item'=>'array',
            'item.*'=>'required|exists:wa_pos_cash_sales_items,id',
            'quantity.*'=>'required|min:1',
            'id'=>'required|exists:wa_pos_cash_sales,id',
        ],[],[
            'item.*'=>'Item',
        ]);
        if($validation->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validation->errors()
            ]);
        }
        if(count($request->quantity) ==  0){
            return response()->json([
                'result'=>0,
                'errors'=>['item'=>['Items are need to process return']]
            ]);
        }
        foreach ($request->quantity as $tt => $vv) {
            if($vv <= 0){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item.'.$tt=>['Quantity needs to be greater than 0']]
                ]);
            }
        }
        // dd('Ok');
        $id = base64_decode($id);
        $user = getLoggeduserProfile();

        $pos = WaPosCashSales::with(['items'=>function($e) use ($permission,$user,$request){$e->whereIn('id',$request->item)->where('is_return',0)->where(function($w) use ($permission,$user)
            {
                // if($permission != 'superadmin'){
                //     $w->where('store_location_id',$user->wa_location_and_store_id);
                // }
            });},'user',
        'items.item',
        'items.item.location',
        'items.item.location.getBranchDetail',
        'items.item.getAllFromStockMoves',
        'items.item.getInventoryCategoryDetail.getWIPGlDetail',
        'items.item.getInventoryCategoryDetail.getStockGlDetail',
        'items.stock_moves',
        'items.dispatch_by'])
            ->whereHas('items',function($e) use ($permission,$user,$request){$e->whereIn('id',$request->item)->where('is_return',0)->where(function($w) use ($permission,$user)
                {
                    // if($permission != 'superadmin'){
                    //     $w->where('store_location_id',$user->wa_location_and_store_id);
                    // }
                });})->where('status','Completed')
            ->where('id',$id)->first();
        if(!$pos){
            return response()->json(['result'=>0,['receipt_no'=>'Receipt Not found']]);
        }
        if($pos->items->count() == 0){
            return response()->json(['result'=>0,['receipt_no'=>'No Items Available']]);
        }
        foreach ($pos->items as $it) {
            if(@$request->quantity[$it->id] > $it->qty){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item.'.$it->id=>['Quantity cannot be greater than '.$it->qty]]
                ]);
            }
        }
        $check = DB::transaction(function () use ($pos,$request,$user,$permission){
            $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'RETURN')->first();
            $paymentMethod = PaymentMethod::with(['paymentGlAccount'])->where('id', $pos->payment_method_id)->first();
            $sale_invoiceno = getCodeWithNumberSeries('RETURN');
            $getLoggeduserProfile = $user;
            $dateTime = date('Y-m-d H:i:s');
            $parent = $pos;
            $glTrans = [];
            $total = 0;
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            $inventory = $pos->items;
            $ids = [];
            foreach ($inventory as $key => $value) {
                $newqty = ($value->qty - @$request->quantity[$value->id]);
                WaPosCashSalesItems::where('id',$value->id)->update(['return_by'=>$getLoggeduserProfile->id,
                    'is_return'=>1,
                    'return_grn'=> $sale_invoiceno,
                    'return_date'=>$dateTime,
                    'original_quantity'=>$value->qty,
                    'return_quantity'=>@$request->quantity[$value->id],
                    'qty'=>$newqty,
                    'total'=>(($newqty*$value->selling_price) - (($newqty > 0) ? $value->discount_amount : 0))
                ]);
                $ids[] = $value->id;
                $data = [];
                $data['wa_pos_cash_sales_id'] = $value->wa_pos_cash_sales_id;
                $data['wa_inventory_item_id'] = $value->wa_inventory_item_id;
                $data['store_location_id'] = $value->store_location_id;
                $data['qty'] = -(@$request->quantity[$value->id]);
                $data['selling_price'] = -($value->selling_price);
                $data['discount_percent'] = $value->discount_percent;
                $data['total'] = -($value->selling_price*@$request->quantity[$value->id]);
                $data['discount_amount'] = -($value->discount_amount);
                $data['vat_percentage'] = $value->vat_percentage;
                $data['vat_amount'] = -($value->vat_amount);
                $data['tax_manager_id'] = $value->tax_manager_id;
                $total += ($data['total'] + $data['discount_amount']);
               
    
                $stock_qoh = @$value->item->getAllFromStockMoves->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0;
                $stock_qoh += @$request->quantity[$value->id];
                $stockMove = new WaStockMove();
                $stockMove->user_id = $getLoggeduserProfile->id;
                $stockMove->wa_pos_cash_sales_id = $value->wa_pos_cash_sales_id;
                $stockMove->restaurant_id = (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id);
                $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                $stockMove->stock_id_code = @$value->item->stock_id_code;
                $stockMove->wa_inventory_item_id = @$value->item->id;
                $stockMove->document_no =   $sale_invoiceno;
                $stockMove->price = $data['total'] - $data['discount_amount'];
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->refrence = $pos->customer.' : '.$paymentMethod->paymentGlAccount->account_code;
                $stockMove->qauntity = - ($data['qty']);
                $stockMove->new_qoh = $stock_qoh;
                $stockMove->standard_cost = -($value->standard_cost);
                $stockMove->save();

                $description = @$value->item->title;
                $accno = @$value->item->getInventoryCategoryDetail->getWIPGlDetail->account_code;
                //cr entries start
              
                $glTrans[] = [
                    'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                    'wa_pos_cash_sales_id'=>$value->wa_pos_cash_sales_id,
                    'grn_type_number'=>$series_module->type_number,
                    'trans_date' => $dateTime,
                    'restaurant_id' =>  (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id),
                    'grn_last_used_number' => $series_module->last_number_used,
                    'transaction_type' => $series_module->description,
                    'transaction_no' => $sale_invoiceno,
                    'narrative' => $description,
                    'account' => $accno,
                    'amount' => -(($data['total'] - $data['discount_amount']) - $data['vat_amount']),
                    'created_at'=>$dateTime,
                    'updated_at'=>$dateTime,
                ];

                if($data['vat_percentage']){
                    $value->standard_cost = (($value->standard_cost*100)/($data['vat_percentage']+100));
                }

                $glTrans[] = [
                    'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                    'wa_pos_cash_sales_id'=>$value->wa_pos_cash_sales_id,
                    'grn_type_number'=>$series_module->type_number,
                    'trans_date' => $dateTime,
                    'restaurant_id' =>  (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id),
                    'grn_last_used_number' => $series_module->last_number_used,
                    'transaction_type' => $series_module->description,
                    'transaction_no' => $sale_invoiceno,
                    'narrative' => $value->item->stock_id_code . '/' . $value->item->title . '/' . $value->standard_cost . '@' . $data['qty'],
                    'account' => @$value->item->getInventoryCategoryDetail->getStockGlDetail->account_code,
                    'amount' => (-$value->standard_cost * $data['qty']),
                    'created_at'=>$dateTime,
                    'updated_at'=>$dateTime,
                ];
                $glTrans[] = [
                    'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                    'wa_pos_cash_sales_id'=>$value->wa_pos_cash_sales_id,
                    'grn_type_number'=>$series_module->type_number,
                    'trans_date' => $dateTime,
                    'restaurant_id' =>  (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id),
                    'grn_last_used_number' => $series_module->last_number_used,
                    'transaction_type' => $series_module->description,
                    'transaction_no' => $sale_invoiceno,
                    'narrative' => $value->item->stock_id_code . '/' . $value->item->title . '/' . $value->standard_cost . '@' . $data['qty'],
                    'account' => @$value->item->getInventoryCategoryDetail->getIssueGlDetail->account_code,
                    'amount' => -(-$value->standard_cost * $data['qty']),
                    'created_at'=>$dateTime,
                    'updated_at'=>$dateTime,
                ];



                $total_invoice_amount[] = ($data['total'] - $data['discount_amount']);

                if ($data['vat_amount']) {
                    $total_vat_amount += $data['vat_amount'];
                }
               

            }
        
          
            if ($total_vat_amount) {
                $taxVat = \App\Model\TaxManager::where('slug', 'vat')->first();
                $glTrans[] = [
                    'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                    'wa_pos_cash_sales_id'=>$value->wa_pos_cash_sales_id,
                    'grn_type_number'=>$series_module->type_number,
                    'trans_date' => $dateTime,
                    'restaurant_id' =>  (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id),
                    'grn_last_used_number' => $series_module->last_number_used,
                    'transaction_type' => $series_module->description,
                    'transaction_no' => $sale_invoiceno,
                    'narrative' => "VAT",
                    'account' => $taxVat->getInputGlAccount->account_code,
                    'amount' => -$total_vat_amount,
                    'created_at'=>$dateTime,
                    'updated_at'=>$dateTime,
                ];        
            }
           
            if (count($total_invoice_amount) > 0) {

                $glTrans[] = [
                    'period_number'=>$accountuingPeriod ? $accountuingPeriod->period_no : null,
                    'wa_pos_cash_sales_id'=>$value->wa_pos_cash_sales_id,
                    'grn_type_number'=>$series_module->type_number,
                    'trans_date' => $dateTime,
                    'restaurant_id' =>  (($permission != 'superadmin') ? @$value->item->location->getBranchDetail->wa_branch_id : $getLoggeduserProfile->restaurant_id),
                    'grn_last_used_number' => $series_module->last_number_used,
                    'transaction_type' => $series_module->description,
                    'transaction_no' => $sale_invoiceno,
                    'narrative' => $pos->customer.' : '.$paymentMethod->paymentGlAccount->account_code,
                    'account' => $paymentMethod->paymentGlAccount->account_code,
                    'amount' => array_sum($total_invoice_amount),
                    'created_at'=>$dateTime,
                    'updated_at'=>$dateTime,
                ];
                //dr entries end
            }
            if(count($glTrans)>0){
                WaGlTran::insert($glTrans);
            }
            updateUniqueNumberSeries('RETURN',$sale_invoiceno);
            return true;
        });
        if($check){
            return response()->json(['result'=>1,'message'=>'Sales Returned successfully.','location'=>route('pos-cash-sales-new.index')]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }
    public function returned_cash_sales_list(Request $request)
    {
        $user = getLoggeduserProfile();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = "Cash Sales Return";
        $model = 'pos-return-list';
        if (isset($permission[$pmodule . '___return-list']) || $permission == 'superadmin') {
            $breadcum = [$title => route($pmodule . '.index'), 'Listing' => ''];
            $data = WaPosCashSalesItems::select(['*',DB::RAW('SUM(return_quantity) as rtn_qty'),
            DB::RAW('SUM(return_quantity * selling_price) as rtn_total')])->with(['item','parent','parent.user','returned_by'])->where('is_return',1)->where(function($w) use ($request,$permission,$user){
                if($request->input('start-date') && $request->input('end-date')){
                    $w->whereBetween('return_date',[$request->input('start-date').' 00:00:00',$request->input('end-date')." 23:59:59"]);
                }
                // if($permission != 'superadmin'){
                //     $w->where('store_location_id',$user->wa_location_and_store_id);
                // }
            })->orderBy('return_date','DESC')->groupBy('return_grn')->paginate(100);          
            return view('admin.pos_cash_sales_new.returned_cash_sales_list', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function dispatched_items_report(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Dispatch Report';
        $model = 'dispatched_items_report';
        if (isset($permission['dispatch-pos-invoice-sales___dispatch-report']) || $permission == 'superadmin') {
            $breadcum = [$title => ''];        
            if($request->request_type ){
                if($request->type=='Cash Sales'){
                    $data = WaPosCashSales::with(['items'=>function($e) use ($request){
                        if($request->inventory){
                            $e->where('wa_inventory_item_id',$request->inventory);
                        }
                    },'user','items.item',
                                                    'items.dispatch_details'=>function($e){$e->orderBy('id','DESC');},
                                                    'items.dispatch_details.dispatch_user'
                                                 ])
                                            ->where(function($w) use ($request){
                                                $w->whereBetween('date',[$request->from_date,$request->end_date]);
                                            })->whereHas('items',function($e) use ($request){
                                                if($request->inventory){
                                                    $e->where('wa_inventory_item_id',$request->inventory);
                                                }
                                            })->orderBy('created_at','desc')->get();
                }else {
                    $data = \App\Model\WaInternalRequisition::with(['getRelatedItem'=>function($e) use ($request){
                        if($request->inventory){
                            $e->where('wa_inventory_item_id',$request->inventory);
                        }
                    },
                                                    'getRelatedItem.dispatch_details'=>function($e){$e->orderBy('id','DESC');},
                                                    'getRelatedItem.getInventoryItemDetail','getRelatedItem.dispatch_details.dispatch_user','getrelatedEmployee'
                                                ])
                                                ->where(function($w) use ($request){
                                                    $w->whereBetween('requisition_date',[$request->from_date,$request->end_date]);
                                                })->orderBy('created_at','desc')->whereHas('getRelatedItem',function($e) use ($request){
                                                    if($request->inventory){
                                                        $e->where('wa_inventory_item_id',$request->inventory);
                                                    }
                                                })->get();
                }
                if($request->request_type == 'Filter'){
                    return response()->json([
                        'location'=>view('admin.pos_cash_sales_new.dispatch_report_table',compact('request','data'))->render(),
                    ]);
                } elseif ($request->request_type == 'PDF') {                
                    $pdf = \PDF::loadView('admin.pos_cash_sales_new.dispatch_report_pdf', compact('request','data'));
                    $report_name = 'dispatched_items_report-'.date('Y_m_d_H_i_A').'.pdf';
                    return $pdf->download($report_name);
                }
                elseif ($request->request_type == 'Print') {                
                    return view('admin.pos_cash_sales_new.dispatch_report_pdf', compact('request','data'));
                }
            }   
            
            return view('admin.pos_cash_sales_new.dispatch_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function inventory_item_list(Request $request)
    {
        $data = [];
        if($request->q){
            $data = WaInventoryItem::select(['id','title as text'])->where('block_this',0)->where('title','LIKE',"%$request->q%")->limit(50)->get();
        }
        return response()->json($data);
    }

    public function returned_cash_sales_print(Request $request,$id)
    {
        $data = WaPosCashSalesItems::with(['item','parent','parent.user','returned_by','location'])
        ->where('is_return',1)->where('return_grn',$id)->orderBy('return_date','DESC')->get();          
        return view('admin.pos_cash_sales_new.returned_print', compact('data'));
    }

    public function esd_upload(Request $request)
    {
        $user = getLoggeduserProfile();
        $a = [];
        $data = [];
        if($request->hasFile('upload_data')){
            foreach ($request->file('upload_data') as $key => $value) {
                if($value->getClientOriginalExtension() != 'txt'){
                    return response()->json(['result'=>-1,'message'=>'Only text files are allowed']);
                }    
                $data = file_get_contents($value->getRealPath());
                if(\App\Model\WaEsd::where('signature',$data)->count() == 0){          
                    $a[] = ['signature'=>file_get_contents($value->getRealPath()),
                            'is_used' => 0,
                            'user_id' => $user->id,
                            'last_used_by' => NULL,
                            'document_no' => NULL,                        
                            ];
                }
            }
        }else {
            return response()->json(['result'=>-1,'message'=>'Select files to upload']);
        }
        // if(\App\Model\WaEsd::whereIn('signature',$data)->count()>0){
        //     return response()->json(['result'=>-1,'message'=>'Some files are already uploaded']);
        // }
        if(count($a)>0){
            \App\Model\WaEsd::insert($a);
        }
        return response()->json(['result'=>1,'message'=>'Uploaded']);
        
    }

}