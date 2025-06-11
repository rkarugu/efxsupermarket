<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\StockAdjustment;
use App\Model\WaAccountingPeriod;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaGlTran;
use App\Model\WaCategory;
use App\Model\WaCategoryItemPrice;
use App\Model\PackSize;
use App\Model\WaInventoryAdjustment;

use Excel;
use PDF;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class InventoryItemAdjustmentController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'inventory-item-adjustment';
        $this->title = 'Inventory items Adjustment';
        $this->pmodule = 'inventory-item-adjustment';
    } 
    
    public function modulePermissions($type)
    {
        $permission =  $this->mypermissionsforAModule();
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
        if(!$this->modulePermissions('view')){
            return redirect()->back();
        }
        $data['permission'] =  $this->mypermissionsforAModule();        
        if($request->ajax()){
            $sortable_columns = [
                'id',
                'users.name',
                'document_no'   ,
                'created_at'   ,
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaInventoryAdjustment::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '';
                $data[$key]['links'] .= '<a href="'.route('inventory-item-adjustment.show', $re['id']).'" onclick="printBill(this); return false;" class="printMe"><i class="fa fa-print" aria-hidden="true"></i></a>';
                
                $data[$key]['dated'] = getDateFormatted($re['created_at']);
                $data[$key]['no_of_adjustment'] = manageAmountFormat($re['no_of_adjustment']);
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        
        return view('admin.inventory_adjustment.list')->with($data);
    }
    public function create()
    {
         if(!$this->modulePermissions('add')){
            return redirect()->back();
        }

        $data['model'] = $this->model;
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        return view('admin.inventory_adjustment.new')->with($data);
    }


    public function show($id)
    {
        $data['model'] = $this->model;
        $data['data'] = WaInventoryAdjustment::with(['childs.item','childs.location'])->where('id',$id)->first();
        $data['stockmoves'] = WaStockMove::where('document_no',$data['data']->document_no)->get();
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        return view('admin.inventory_adjustment.show')->with($data);
    }

    public function inventoryItems(Request $request)
    {
        $data = \App\Model\WaInventoryItem::select([
            '*',
            \DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->where(function($q) use ($request){
            if ($request->search) {
                $q->where('title','LIKE',"%$request->search%");
                $q->orWhere('stock_id_code','LIKE',"%$request->search%");
            }
        })->where(function($e) use ($request){
            if($request->store_c){
                $e->where('store_c_deleted',0);
            }
        })->limit(20)->get();
        $view = '<table class="table table-bordered table-hover" id="stock_inventory" style="
        display: block;
        right: auto !important;
        position: absolute;
        min-width: 400px;
        left: 0 !important;
        max-height: 350px;
        margin-top: 4px!important;
        overflow: auto;
        padding: 0;
        background:#fff;
        ">';
        $view .= "<thead>";
        $view .= '<tr>';
        $view .= '<th style="width:20%">Code</th>';
        $view .= '<th style="width:70%">Description</th>';
        $view .= '<th style="width:10%">QOH</th>';
        $view .= '</tr>';
        $view .= '</thead>';
        $view .= "<tbody>";
        foreach ($data as $key => $value) {          
                $view .= '<tr onclick="fetchInventoryDetails(this)" data-id="'.$value->id.'" data-title="'.$value->title.'('.$value->stock_id_code.')">';
                $view .= '<td style="width:20%">'.$value->stock_id_code.'</td>';
                $view .= '<td style="width:70%">'.$value->title.'</td>';
                $view .= '<td style="width:10%">'.($value->quantity ?? 0).'</td>';
                $view .= '</tr>';            
        }
        $view .= '</tbody>';
        $view .= '</table>';
        return response()->json($view);
    }

    public function getInventryItemDetails(Request $request)
    {
        $data = WaInventoryItem::select([
            '*',
            \DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getTaxesOfItem','getUnitOfMeausureDetail'])->where('id',$request->id)->first();
        $view = '';
        $unidid = uniqid();
        if($data){
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id['.$unidid.']" class="itemid" value="'.$data->id.'">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="'.$data->stock_id_code.'">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td><input style="padding: 3px 3px;" readonly type="text" name="item_description['.$unidid.']" data-id="'.$unidid.'"  class="form-control" value="'.$data->description.'"></td>
            <td><input style="padding: 3px 3px;" readonly type="text" name="item_uom['.$unidid.']" data-id="'.$unidid.'"  class="form-control" value="'.$data->getUnitOfMeausureDetail?->title.'"></td>
            <td>
            <select class="form-control load_location adjustment_location" onchange="getAndUpdateItemAvailableQuantity(this)" name="adjustment_location['.$unidid.']"><option value="" selected disabled>Select Location</option></select>
            </td>
            <td><input style="padding: 3px 3px;" readonly type="text" name="bal_stock['.$unidid.']" data-id="'.$unidid.'"  class="bal_stock form-control" value=""></td>
            <td><input style="padding: 3px 3px;"  type="text" name="item_quantity['.$unidid.']" data-id="'.$unidid.'"  class="quantity form-control" value=""></td>
            <td>
            <textarea class="form-control" name="comment['.$unidid.']" rows="1" cols="1"></textarea>
            </td>
            <td>
            <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>
            </tr>';
        }
        return response()->json($view);
    }
    
    public function location_list(Request $request)
    {        
        $data = \DB::table('wa_location_and_stores')->select(['id as id',\DB::RAW('CONCAT(location_name," (",location_code,")") as text')]);
        if($request->q)
        {
            $data = $data->orWhere('location_name','LIKE',"%$request->q%");
            $data = $data->orWhere('location_code','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return response()->json($data);
    } 

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'item_id'=>'required|array',
            'item_id.*'=>'required|exists:wa_inventory_items,id',
            'adjustment_location'=>'required|array',
            'adjustment_location.*'=>'required|exists:wa_location_and_stores,id',
            'item_quantity.*'=>'required|numeric',
            'comment.*'=>'nullable|max:100',
            'request_type'=>'required|in:save,send_request'
        ],[],[
            'item_quantity.*'=>'Item Quantity',
            'adjustment_location.*'=>'Location',
            'item_id.*'=>'Item',
            'comment.*'=>'Comment',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $data = WaInventoryItem::select([
            '*',
            \DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getTaxesOfItem','getUnitOfMeausureDetail','getAllFromStockMoves',
        'getInventoryCategoryDetail.getStockGlDetail',
        'getInventoryCategoryDetail.getPricevarianceGlDetail',
        'getInventoryCategoryDetail.getusageGlDetail'])->whereIn('id',$request->item_id)->get();
        $errors = [];
        foreach($request->item_id as $key => $item_id){
            $d = $data->where('id',$item_id)->first();
            if($d){
                $quantity = ($d->getAllFromStockMoves->where('wa_location_and_store_id',@$request->adjustment_location[$key])->sum('qauntity') ?? 0);
                $newOH = $quantity + @$request->item_quantity[$key];
                // if($quantity < @$request->item_quantity[$key] || @$request->item_quantity[$key] < 1){
                //     $errors['item_quantity.'.$key] = ['Invalid Quantity'];
                // }
                if($newOH < 0){
                    $errors['item_quantity.'.$key] = ['Invalid Quantity'];
                }
            }
        }
        if(count($errors)>0){
            return response()->json([
                'result'=>0,
                'errors'=>$errors
            ]);
        }
        $check = DB::transaction(function () use ($request,$data){
            $number = getCodeWithNumberSeries('ITEM ADJUSTMENT');
            $getLoggeduserProfile = getLoggeduserProfile();
            $item_adj = WaNumerSeriesCode::where('module','ITEM ADJUSTMENT')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
            $dateTime = date('Y-m-d H:i:s');

            $main = new WaInventoryAdjustment;
            $main->user_id = $getLoggeduserProfile->id;
            $main->document_no = $number;
            $main->save();
            $stock_moves = [];
            $WaGlTran = [];
            foreach($request->item_id as $key => $item_id){
                $d = $data->where('id',$item_id)->first();
                $entity = new StockAdjustment();
                $entity->user_id = $getLoggeduserProfile->id;
                $entity->item_id = $item_id;
                $entity->wa_location_and_store_id = @$request->adjustment_location[$key];
                $entity->adjustment_quantity = @$request->item_quantity[$key];
                $entity->comments = @$request->comment[$key];
                $entity->item_adjustment_code = $number;
                $entity->wa_inventory_adjustment_id = $main->id;
                $entity->save();
                $stock_moves[] = [
                    'user_id' => $getLoggeduserProfile->id,
                    'stock_adjustment_id' => $entity->id,
                    'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                    'wa_location_and_store_id' => $entity->wa_location_and_store_id,
                    'wa_inventory_item_id' => $d->id,
                    'standard_cost' => $d->standard_cost,
                    'qauntity' => $entity->adjustment_quantity,
                    'new_qoh' => (@$d->getAllFromStockMoves->where('wa_location_and_store_id',@$entity->wa_location_and_store_id)->sum('qauntity') ?? 0) + $entity->adjustment_quantity,
                    'stock_id_code' => $d->stock_id_code,
                    'grn_type_number' => $item_adj->type_number,
                    'document_no' => $number,
                    'grn_last_nuber_used' => $item_adj->last_number_used,
                    'price' => $d->standard_cost,
                    'selling_price' => $d->selling_price,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'refrence' => $entity->comments,
                ];
                if($entity->adjustment_quantity < '0')
                    {
                        $account = @$d->getInventoryCategoryDetail->getusageGlDetail->account_code; 
                    }
                    else{
                        $account = @$d->getInventoryCategoryDetail->getStockGlDetail->account_code;
                    }

                $WaGlTran[] = [
                    'stock_adjustment_id' => $entity->id,
                    'grn_type_number' => $item_adj->type_number,
                    'transaction_type' => $item_adj->description,
                    'transaction_no' => $number,
                    'grn_last_used_number' => $item_adj->last_number_used,
                    'trans_date' => $dateTime,
                    'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                    'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,        
                    'account' => $account,
                    'amount' => abs($d->standard_cost * $entity->adjustment_quantity),    
                    'narrative' => $d->stock_id_code.'/'.$d->title.'/'.$d->standard_cost.'@'.$entity->adjustment_quantity,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
                if($entity->adjustment_quantity < '0')
                {
                    $account = @$d->getInventoryCategoryDetail->getStockGlDetail->account_code;
                }
                else{
                    $account = @$d->getInventoryCategoryDetail->getPricevarianceGlDetail->account_code; 
                }
                $WaGlTran[] = [
                    'stock_adjustment_id' => $entity->id,
                    'grn_type_number' => $item_adj->type_number,
                    'transaction_type' => $item_adj->description,
                    'transaction_no' => $number,
                    'grn_last_used_number' => $item_adj->last_number_used,
                    'trans_date' => $dateTime,
                    'restaurant_id' => $getLoggeduserProfile->restaurant_id,
                    'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null, 
                    'account' => $account,
                    'amount' => '-'.abs($d->standard_cost * $entity->adjustment_quantity),
                    'narrative' => $d->stock_id_code.'/'.$d->title.'/'.$d->standard_cost.'@'.$entity->adjustment_quantity,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
            }
            WaGlTran::insert($WaGlTran);
            WaStockMove::insert($stock_moves);
            updateUniqueNumberSeries('ITEM ADJUSTMENT',$number);
            return true;
        });
        if($check){
            return response()->json(['result'=>1,'message'=>'Inventory Adjustment Processed Successfully.','location'=>route('inventory-item-adjustment.index')]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']); 
    }
}
