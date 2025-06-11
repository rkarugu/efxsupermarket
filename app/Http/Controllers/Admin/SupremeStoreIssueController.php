<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSupremeStoreRequisition;
use App\Model\WaSupremeStoreRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaStockMoveC;
use App\Model\WaLocationAndStore;

use Illuminate\Support\Facades\Auth;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class SupremeStoreIssueController extends Controller
{
    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'supreme-store-issue';
        $this->title = 'Supreme Store Issue';
        $this->pmodule = 'supreme-store-issue';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaSupremeStoreRequisition::where('status','APPROVED')->with(['getrelatedEmployee','getBranch','getDepartment','getRelatedItem','getRelatedToLocationAndStore']);
            // if($permission != 'superadmin')
            // {
            //     $lists = $lists->where('user_id', getLoggeduserProfile()->id);
            // }
          $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.supreme_store_issue.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function edit($slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(!isset($permission[$pmodule.'___edit']) && $permission != 'superadmin')
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        try
        {            
            $row =  WaSupremeStoreRequisition::with(['getRelatedItem',
            'getRelatedItem.getInventoryItemDetail',
            'getRelatedItem.getInventoryItemDetail.pack_size',
            'getRelatedItem.getInventoryItemDetail.getAllFromStockMovesC',
            'getRelatedToLocationAndStore',
            'getRelatedItem.location',
            ])->whereSlug($slug)->whereNotIn('status',['COMPLETED'])->first();
            if($row)
            {
                $title = 'Edit '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                $model =$this->model;
                return view('admin.supreme_store_issue.edit',compact('title','model','breadcum','row')); 
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

    public function update(Request $request,$slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(!isset($permission[$pmodule.'___edit']) && $permission != 'superadmin')
        {
            return response()->json(['result'=>-1,'message'=>'Invalid Request']);
        }
        $validate = Validator::make($request->all(),[
            'item_id.*'=>'required|exists:wa_supreme_store_requisition_items,id',
            'item_quantity.*'=>'required|numeric',
        ]);
        if($validate->fails()){
            return response()->json(['result'=>0,'errors'=>$validate->errors()]);
        }
        $row =  WaSupremeStoreRequisition::with(['getRelatedItem',
                'getRelatedItem.getInventoryItemDetail',
                'getRelatedToLocationAndStore',
                'getRelatedItem.getInventoryItemDetail.getAllFromStockMovesC',
            ])->whereSlug($slug)->whereNotIn('status',['COMPLETED'])->first();
        if(!$slug || !$row){
            return response()->json(['result'=>-1,'message'=>'Something went wrong']);
        }
        foreach($row->getRelatedItem as $value){
            $stock_qoh = $value->getInventoryItemDetail->getAllFromStockMovesC->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity');
            if(!$request->item_quantity[$value->id] || $stock_qoh < $request->item_quantity[$value->id]){
                return response()->json([
                    'result'=>0,
                    'errors'=>['item_quantity.'.$value->id=>['Quantity cannot be greater than balance stock']]
                ]);
            }
        }
        $check = DB::transaction(function () use ($request,$row) {
            $row->status = 'COMPLETED';
            $row->save();
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'Internal Requisition Supreme Store')->first();
            foreach($row->getRelatedItem as $value){
                $stock_qoh = $value->getInventoryItemDetail->getAllFromStockMovesC->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity');
                $stock_qoh -= @$request->item_quantity[$value->id];
                $stockMove = new WaStockMoveC();
                $stockMove->user_id = $getLoggeduserProfile->id;
                $stockMove->wa_supreme_store_requisitions_id = $row->id;
                $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                $stockMove->stock_id_code = $value->getInventoryItemDetail->stock_id_code;
                $stockMove->wa_inventory_item_id = @$value->wa_inventory_item_id;
                $stockMove->document_no =   $row->requisition_no;
                $stockMove->price = 0;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->refrence = @$row->getRelatedToLocationAndStore->location_name;
                $stockMove->qauntity = -@$request->item_quantity[$value->id];
                $stockMove->new_qoh = $stock_qoh;
                $stockMove->standard_cost = $value->standard_cost;
                $stockMove->save();
                WaSupremeStoreRequisitionItem::where('id',$value->id)->update(['issued_quanity'=>@$request->item_quantity[$value->id]]);
            }
          
            return true;
        });
        if($check){
            return response()->json(['result'=>1,'message'=>'Processed Successfully!','location'=>route($this->model.'.index')]);
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);
    }
    public function completedIndex()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title.' Processed';
        $model = $this->model.'-processed';
        if(isset($permission[$pmodule.'___processed']) || $permission == 'superadmin')
        {
            $lists = WaSupremeStoreRequisition::where('status','COMPLETED')->with(['getrelatedEmployee','getBranch','getDepartment','getRelatedItem','getRelatedToLocationAndStore']);
            // if($permission != 'superadmin')
            // {
            //     $lists = $lists->where('user_id', getLoggeduserProfile()->id);
            // }
          $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('supreme-store-requisitions.completedIndex'),'Listing'=>''];
            return view('admin.supreme_store_issue.completedIndex',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function inventoryItems(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model.'-inventoryItems';
      
        if($request->ajax()){
            $columns = [
                'stock_id_code', 'title', 'uom', 'standard_cost', 'qauntity', 'qty_on_order'
            ];
            $totalData = WaInventoryItem::count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            //$data_query = WaInventoryItem::select('items.*')->where([['type', $item_types['feed']]]);
            $data_query = WaInventoryItem::select('wa_inventory_categories.id as cat_id','wa_inventory_categories.category_description','wa_inventory_items.*')->with('pack_size','getUnitOfMeausureDetail', 'getAllFromStockMovesC')
            ->join('wa_inventory_categories','wa_inventory_categories.id','=','wa_inventory_items.wa_inventory_category_id')->where('wa_inventory_items.supreme_store_deleted',0);
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $data_query = $data_query->where(function($data_query) use ($search) {
                    $data_query->where('stock_id_code', 'LIKE', "%{$search}%")
                        ->orWhere('title', 'LIKE', "%{$search}%")
                        ->orWhere('standard_cost', 'LIKE', "%{$search}%");
                });
                
            }
            
           // echo "<pre>"; print_r( $data_query); die;
            
            $data_query_count = $data_query;
            $totalFiltered = $data_query_count->count();
            $data_query = $data_query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            
            $data = array();
           // dd($data_query);
            if (!empty($data_query)) {
                foreach ($data_query as $key => $row) {
                    $user_link = '';
                    
                    $nestedData['stock_id_code'] = $row->stock_id_code;
                    $nestedData['item_category'] = $row->category_description;
                    $nestedData['title'] = $row->title;
                    $nestedData['uom'] = @$row->pack_size->title;
                    $nestedData['standard_cost'] = manageAmountFormat($row->standard_cost);
                    $nestedData['qauntity'] = manageAmountFormat(@$row->getAllFromStockMovesC->sum('qauntity'));
                    $nestedData['selling_price'] = manageAmountFormat($row->selling_price);
                    $action_text = '<a href="'.route('supreme-store-issue.stock-movements',$row->stock_id_code).'" class="btn btn-sm btn-biz-greenish" title="stock movement"><i class="fa fa-list"></i></a>';
                    if(isset($permission['supreme-store-inventory___delete']) || $permission == 'superadmin'){
                        $action_text .= '<a href="'.route('supreme-store-issue.inventory-item-delete',$row->id).'" class="btn btn-sm btn-biz-pinkish deleteMe" title="inventory item archive"><i class="fa fa-archive"></i></a>';
                    }
                    if(isset($permission['supreme-store-inventory___adjustment']) || $permission == 'superadmin'){
                        $link_popup = route('admin.table.adjust-item-stock-form', $row->slug);
                        $action_text .=  view('admin.maintaininvetoryitems.popup_link',[
                            'link_popup'=> $link_popup,
                            'id'=> $row->id,
                            'type'=> '1',
                            'class'=>'btn btn-sm btn-biz-purplish'
                            ]);
                    }
                    $nestedData['action'] = $action_text;    
                    $data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            return response()->json($json_data);
        }
        if(isset($permission['supreme-store-inventory___view']) || $permission == 'superadmin')
        {
            $breadcum = [$title=>route('supreme-store-issue.inventoryItems'),'Listing'=>''];
            return view('admin.supreme_store_issue.inventoryitem',compact('title','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }

    public function inventoryItem_manage_stock(Request $request){
        // dd('Processing');
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission['supreme-store-inventory___adjustment']) && $permission != 'superadmin'){
            Session::flash('warning', 'Restriced: You don\'t have permissions');
            return redirect()->back();
        }
        $item_row = \App\Model\WaInventoryItem::where('id', $request->item_id)->first();
        $adjustment_quantity = $request->adjustment_quantity;
        $current_available_quantity = getItemAvailableQuantity_C($request->stock_id_code, $request->wa_location_and_store_id);        
        $new_quantity = $current_available_quantity + $adjustment_quantity;
        if($new_quantity < 0){
            $error = "Item No $item_row->stock_id_code does not have enough stock.";
            Session::flash('warning', $error);
            return redirect()->back();
        }        
        $logged_user_profile = getLoggeduserProfile();
        $series_module = \App\Model\WaNumerSeriesCode::where('module','ITEM ADJUSTMENT')->first();
        $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
        $dateTime = date('Y-m-d H:i:s');
        
        $stockMove = new WaStockMoveC();
        $stockMove->user_id = $logged_user_profile->id;
        $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
        $stockMove->wa_location_and_store_id = $request->wa_location_and_store_id;
        $stockMove->wa_inventory_item_id = $item_row->id;
        $stockMove->standard_cost = $item_row->standard_cost;
        $stockMove->qauntity = $adjustment_quantity;
        $stockMove->new_qoh = ($item_row->getAllFromStockMovesC->where('wa_location_and_store_id',@$request->wa_location_and_store_id)->sum('qauntity') ?? 0) + $stockMove->qauntity;
        $stockMove->stock_id_code = $item_row->stock_id_code;
        $stockMove->grn_type_number = $series_module->type_number;
        $stockMove->document_no = $request->item_adjustment_code;
        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
        $stockMove->price = $item_row->standard_cost;
        $stockMove->refrence = $request->comments;
        $stockMove->save();
        updateUniqueNumberSeries('ITEM ADJUSTMENT',$request->item_adjustment_code);
        Session::flash('success', 'Processed Successfully');
        return redirect()->back();
    }
    public function inventoryItem_delete(Request $request,$id)
    {
        if(!$request->ajax()){
            Session::flash('warning', 'Restriced: You don\'t have permissions');
            return redirect()->back();
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            if(!isset($permission['supreme-store-inventory___delete']) && $permission != 'superadmin'){
                return response()->json(['status'=>-1,'message'=>'Restriced: You don\'t have permissions']);
            }
            $item = WaInventoryItem::where('id',$id)->where('wa_inventory_items.supreme_store_deleted',0)->first();
            if($item){
                $item->supreme_store_deleted = 1;
                $item->save();
                return response()->json(['status'=>1,'message'=>'Item archived Successfully']);
            }
            return response()->json(['status'=>-1,'message'=>'Item Not found']);
        } catch (\Throwable $th) {
            return response()->json(['status'=>-1,'message'=>'Something went wrong']);
        }
    }
    public function stockMovements($StockIdCode,Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission['supreme-store-inventory___view']) && $permission != 'superadmin'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model.'-inventoryItems';
        $location = WaLocationAndStore::where(['wa_branch_id'=>getLoggeduserProfile()->restaurant_id])->get();
        $lists = WaStockMoveC::with(['getRelatedUser','getLocationOfStore','getRequisition.getRelatedToLocationAndStore'])->where(function($w) use ($request){
            if($request->from && $request->to){
                $w->whereBetween('created_at',[$request->from.' 00:00:00',$request->to.' 23:59:59']);
            }
            if($request->location){
                $w->where('wa_location_and_store_id',$request->location);
            }
        });
        if(($request->from && $request->to) || $request->location){
            $lists = $lists->where('stock_id_code',$StockIdCode)->orderBy('id', 'asc')->get();
        }
        else {
            $lists = $lists->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->limit(20)->get();
            $lists = $lists->sort();
        }
        
        $row =  WaInventoryItem::where('stock_id_code',$StockIdCode)->first();
        $breadcum = [$title=>route('supreme-store-issue.inventoryItems'),'Stock Movement'=>'',$StockIdCode=>''];
        if($request->type == 'pdf'){
            $firstQoh = WaStockMoveC::where(function($w) use ($request){
                $w->where('created_at','<',$request->from.' 00:00:00');                
                if($request->location){
                    $w->where('wa_location_and_store_id',$request->location);
                }
            })
            ->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id'=>$request->location])->first();
            $pdf = \PDF::loadView('admin.supreme_store_issue.stockmovement_pdf',compact('firstQoh','currentLocation','request','row','location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
            $report_name = 'Stock-Card-Store-C'.date('Y_m_d_H_i_A');
            // return $pdf->stream();
            return $pdf->download($report_name.'.pdf');
        }
        if($request->type == 'print'){
            $firstQoh = WaStockMoveC::where(function($w) use ($request){
                $w->where('created_at','<',$request->from.' 00:00:00');                
                if($request->location){
                    $w->where('wa_location_and_store_id',$request->location);
                }
            })
            ->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id'=>$request->location])->first();
            return view('admin.supreme_store_issue.stockmovement_pdf',compact('firstQoh','currentLocation','request','row','location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
        }
        return view('admin.supreme_store_issue.stockmovement',compact('location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
    }


    public function stock_take_index() {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'supreme-store-stock-take';
        $title = 'store c stock take';
        $model = 'supreme-store-stock-take';
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = \App\Model\WaStockCheckFreezeC::with(['getAssociateLocationDetail','getAssociateUserDetail','unit_of_measure'])->withCount('getAssociateItems')->orderBy('id', 'desc')->get();
            $breadcum = [$title => route('admin.stock-takes.create-stock-take-sheet'), 'Listing' => ''];
            return view('admin.supreme_store_stock_takes.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function freezeTable(){
        $permission = $this->mypermissionsforAModule();
        $branch_id = Auth::user()->wa_location_and_store_id;
//        dd($branch_id);
        if($permission == 'superadmin') {
            DB::table('wa_stock_check_freezes_c')->delete();
            DB::table('wa_stock_counts_c')->delete();
            DB::table('wa_stock_check_freeze_c_items')->delete();
        }else{
            DB::table('wa_stock_check_freezes_c')->where('wa_location_and_store_id', $branch_id)->delete();
            DB::table('wa_stock_counts_c')->where('wa_location_and_store_id', $branch_id)->delete();
            DB::table('wa_stock_check_freeze_c_items')->where('wa_location_and_store_id', $branch_id)->delete();
        }

        Session::flash('success', 'Table has been truncated successfully.');
        return redirect()->back();
    }   
    public function addStockCheckFile(Request $request){
        // wa_unit_of_measure_id
        // store_location_id
        // wa_inventory_category_id
        DB::transaction(function () use ($request){
            
            $generateQuery = \App\Model\WaInventoryItem::with(['getUnitOfMeausureDetail','getInventoryCategoryDetail'])->groupBy('wa_unit_of_measure_id','store_location_id','wa_inventory_category_id')->where('supreme_store_deleted',0)->get();
            // $allItems = \App\Model\WaInventoryItem::where('supreme_store_deleted',0)->with(['getAllFromStockMovesC'])->get();
            $logged_user_profile = getLoggeduserProfile();
            $wa_inventory_category_ids = array_unique($generateQuery->pluck('wa_inventory_category_id')->toArray());
            $stock_check_row_created = 0;
            foreach($generateQuery as $query)
            {
                $items = \App\Model\WaInventoryItem::where('supreme_store_deleted',0)->with(['getAllFromStockMovesC'])->where('wa_unit_of_measure_id',$query->wa_unit_of_measure_id)->where('store_location_id',$query->store_location_id)->where('wa_inventory_category_id', $query->wa_inventory_category_id)->get();
                $exits_row = \App\Model\WaStockCheckFreezeCItem::where([['wa_unit_of_measure',@$query->getUnitOfMeausureDetail->title],['wa_location_and_store_id',$query->store_location_id], ['item_category_id', $query->wa_inventory_category_id]])->first();
                if($exits_row){
                    foreach($items as $key => $item_row){
                        $available_quantity = $item_row->getAllFromStockMovesC->where('wa_location_and_store_id',$item_row->store_location_id)->sum('qauntity') ?? NULL;//getItemAvailableQuantity_C($item_row->stock_id_code, $query->store_location_id);
                        if(!empty($request->quantities_zero) && empty($available_quantity)){
                            continue;
                        }
                        $stock_check_id = $exits_row->wa_stock_check_freeze_id; 
                        $entity = \App\Model\WaStockCheckFreezeCItem::firstOrNew(
                            [
                                'wa_inventory_item_id'=>$item_row->id,
                                'wa_stock_check_freeze_id'=>$stock_check_id,
                                'wa_location_and_store_id'=>$query->store_location_id,
                                'item_category_id'=> $query->wa_inventory_category_id,
                                'wa_unit_of_measure'=> @$query->getUnitOfMeausureDetail->title,
                            ]
                        );
    
                        $entity->quantity_on_hand = $available_quantity;
                        $entity->save();
                    }
                }
                else{
               //     echo "second"; die;
                    if($stock_check_row_created == 0){
                        $entity_stock_check = new \App\Model\WaStockCheckFreezeC();
                        $entity_stock_check->wa_location_and_store_id = NULL;//$query->store_location_id;
                        $entity_stock_check->user_id = $logged_user_profile->id;
                        $entity_stock_check->wa_unit_of_measure_id = NULL;//$query->wa_unit_of_measure_id;
                        $entity_stock_check->wa_inventory_category_ids = serialize($wa_inventory_category_ids);
                        $entity_stock_check->save();
                        $stock_check_row_created = 1;
                    }
                    foreach($items as $key => $item_row){
                        $available_quantity = $item_row->getAllFromStockMovesC->where('wa_location_and_store_id',$item_row->store_location_id)->sum('qauntity') ?? NULL;
                        if(!empty($request->quantities_zero) && empty($available_quantity)){
                            continue;
                        }
                        $entity = new \App\Model\WaStockCheckFreezeCItem();
                        
                        $entity->wa_stock_check_freeze_id = $entity_stock_check->id;
                        $entity->wa_inventory_item_id = $item_row->id;
                        $entity->wa_location_and_store_id = $query->store_location_id;
                        $entity->item_category_id = $query->wa_inventory_category_id;
                        $entity->quantity_on_hand = $available_quantity;
                        $entity->wa_unit_of_measure = @$query->getUnitOfMeausureDetail->title;
                        $entity->save();
                    }
                }
            }
        });
        Session::flash('success', 'Processed successfully.');
        return redirect()->back();
        // $wa_inventory_category_ids = array_filter($request->wa_inventory_category_id);
        // if(empty($wa_inventory_category_ids)){
        //     Session::flash('warning', 'Please select Inventory Categorios.');
        //     return redirect()->back();
        // }
       
        // $unit = \App\Model\WaUnitOfMeasure::where('id',$request->wa_unit_of_measure_id)->first();

        // foreach ($wa_inventory_category_ids as $category_id){
        //     $items = \App\Model\WaInventoryItem::where('wa_unit_of_measure_id',$request->wa_unit_of_measure_id)->where('store_location_id',$request->wa_location_and_store_id)->where('wa_inventory_category_id', $category_id)->get();
        //     $exits_row = \App\Model\WaStockCheckFreezeCItem::where([['wa_unit_of_measure',@$unit->title],['wa_location_and_store_id',$request->wa_location_and_store_id], ['item_category_id', $category_id]])->first();
        //     if($exits_row){
        //         foreach($items as $key => $item_row){
        //             $available_quantity = getItemAvailableQuantity_C($item_row->stock_id_code, $request->wa_location_and_store_id);
        //             if(!empty($request->quantities_zero) && empty($available_quantity)){
        //                 continue;
        //             }
        //             $stock_check_id = $exits_row->wa_stock_check_freeze_id; 
        //             $entity = \App\Model\WaStockCheckFreezeCItem::firstOrNew(
        //                 [
        //                     'wa_inventory_item_id'=>$item_row->id,
        //                     'wa_stock_check_freeze_id'=>$stock_check_id,
        //                     'wa_location_and_store_id'=>$request->wa_location_and_store_id,
        //                     'item_category_id'=> $category_id,
        //                     'wa_unit_of_measure'=> @$unit->title,
        //                 ]
        //             );

        //             $entity->quantity_on_hand = $available_quantity;
        //             $entity->save();
        //         }
        //     }
        //     else{
        //    //     echo "second"; die;
        //         if($stock_check_row_created == 0){
        //             $entity_stock_check = new \App\Model\WaStockCheckFreezeC();
        //             $entity_stock_check->wa_location_and_store_id = $request->wa_location_and_store_id;
        //             $entity_stock_check->user_id = $logged_user_profile->id;
        //             $entity_stock_check->wa_unit_of_measure_id = $request->wa_unit_of_measure_id;
        //             $entity_stock_check->wa_inventory_category_ids = serialize($wa_inventory_category_ids);
        //             $entity_stock_check->save();
        //             $stock_check_row_created = 1;
        //         }
        //         foreach($items as $key => $item_row){
        //             $available_quantity = getItemAvailableQuantity_C($item_row->stock_id_code, $request->wa_location_and_store_id);
        //             if(!empty($request->quantities_zero) && empty($available_quantity)){
        //                 continue;
        //             }
        //             $entity = new \App\Model\WaStockCheckFreezeCItem();
                    
        //             $entity->wa_stock_check_freeze_id = $entity_stock_check->id;
        //             $entity->wa_inventory_item_id = $item_row->id;
        //             $entity->wa_location_and_store_id = $request->wa_location_and_store_id;
        //             $entity->item_category_id = $category_id;
        //             $entity->quantity_on_hand = $available_quantity;
        //             $entity->wa_unit_of_measure = @$unit->title;
        //             $entity->save();
        //         }
        //     }
            
            
        // }
        // Session::flash('success', 'Processed successfully.');
        // return redirect()->back();
    }
    
    public function printToPdf($id) {
        $data = \App\Model\WaStockCheckFreezeC::with('getAssociateItems.getAssociateItemDetail.getInventoryCategoryDetail','getAssociateItems.getAssociateItemDetail.pack_size')->where('id', $id)->first();
        
        $freeze_items = $data->getAssociateItems;
        $items_by_category = $category_list = [];
        foreach($freeze_items as $key => $row){
            $category_id = $row->getAssociateItemDetail->getInventoryCategoryDetail->id;
            $category_list[$category_id] = [
                'category_description'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_description,
                'category_code'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_code
            ];
            $items_by_category[$category_id][] = $row;
        }
         //return view('admin.stock_takes.print', compact('items_by_category', 'category_list'));
       $pdf = PDF::loadView('admin.supreme_store_stock_takes.print', compact('items_by_category', 'category_list', 'data'));
        return $pdf->download('stock_check' . date('Y_m_d_h_i_s') . '.pdf');
    }
    
    public function printPage(Request $request)
    {
      
        $id = $request->id;  
        $pmodule = 'supreme-store-stock-take';
        $title = 'store c stock take';
        $model = 'supreme-store-stock-take';
        $breadcum = [$this->title =>route('admin.stock-takes.add-stock-check-file'),'Add'=>''];
        $data = \App\Model\WaStockCheckFreezeC::with('getAssociateItems.getAssociateItemDetail.pack_size','getAssociateItems.getAssociateItemDetail.getInventoryCategoryDetail')->where('id', $id)->first();
        
        $freeze_items = $data->getAssociateItems; 
        $items_by_category = $category_list = [];
        foreach($freeze_items as $key => $row){
            $category_id = $row->getAssociateItemDetail->getInventoryCategoryDetail->id;
            $category_list[$category_id] = [
                'category_description'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_description,
                'category_code'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_code
            ];
            $items_by_category[$category_id][] = $row;
        }
        $print_page = 1;
        return view('admin.supreme_store_stock_takes.print',compact('print_page', 'items_by_category', 'category_list', 'data', 'title','model','breadcum')); 
    }

    public function getCategories(Request $request)
    {
        $selectedCategory = [];
        if($request->selectedCategory){
            $selectedCategory = explode(',',$request->selectedCategory);
        }
        $selectedUNit = '';
        if($request->selectedUNit){
            $selectedUNit = $request->selectedUNit;
        }
        if($request->wa_location_and_store_id){
            $data = \App\Model\WaInventoryCategory::whereHas('getinventoryitems',function($w) use ($request){
                $w->where('store_location_id',$request->wa_location_and_store_id);
            })->orderBy('id','DESC')->get();
            $rec = '';
            
            foreach ($data as $key => $value) {
                $selectedC = '';
                if(in_array($value->id,$selectedCategory)){
                    $selectedC = 'selected';
                }
                $rec .= '<option value="'.$value->id.'" '.$selectedC.'>'.$value->category_description.'</option>';
            }
            $unit = \App\Model\WaUnitOfMeasure::whereHas('getinventoryitems',function($w) use ($request){
                $w->where('store_location_id',$request->wa_location_and_store_id);
            })->orderBy('id','DESC')->get();
            $rec1 = '';
            foreach ($unit as $valueS) {
                $selectedU = '';
                if($valueS->id == $selectedUNit){
                    $selectedU = 'selected';
                }
                $rec1 .= '<option value="'.$valueS->id.'" '.$selectedU.'>'.$valueS->title.'</option>';
            }
            return response()->json(['result'=>1,'data'=>$rec,'unit'=>$rec1]);
        }else {
            $data = \App\Model\WaInventoryCategory::orderBy('id','DESC')->get();
            $rec = '';
            foreach ($data as $key => $value) {
                $selectedC = '';
                if(in_array($value->id,$selectedCategory)){
                    $selectedC = 'selected';
                }
                $rec .= '<option value="'.$value->id.'" '.$selectedC.'>'.$value->category_description.'</option>';
            }
            $unit = \App\Model\WaUnitOfMeasure::orderBy('id','DESC')->get();
            $rec1 = '';
            foreach ($unit as $valueS) {
                $selectedU = '';
                if($valueS->id == $selectedUNit){
                    $selectedU = 'selected';
                }
                $rec1 .= '<option value="'.$valueS->id.'" '.$selectedU.'>'.$valueS->title.'</option>';
            }
            return response()->json(['result'=>1,'data'=>$rec,'unit'=>$rec1]);
        }
    }

    public function archivedinventoryItems(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model.'-un-inventoryItems';
      
        if($request->ajax()){
            $columns = [
                'stock_id_code', 'title', 'uom', 'standard_cost', 'qauntity', 'qty_on_order'
            ];
            $totalData = WaInventoryItem::count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            //$data_query = WaInventoryItem::select('items.*')->where([['type', $item_types['feed']]]);
            $data_query = WaInventoryItem::select('wa_inventory_categories.id as cat_id','wa_inventory_categories.category_description','wa_inventory_items.*')->with('pack_size','getUnitOfMeausureDetail', 'getAllFromStockMovesC')
            ->join('wa_inventory_categories','wa_inventory_categories.id','=','wa_inventory_items.wa_inventory_category_id')->where('wa_inventory_items.supreme_store_deleted',1);
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $data_query = $data_query->where(function($data_query) use ($search) {
                    $data_query->where('stock_id_code', 'LIKE', "%{$search}%")
                        ->orWhere('title', 'LIKE', "%{$search}%")
                        ->orWhere('standard_cost', 'LIKE', "%{$search}%");
                });
                
            }
            
           // echo "<pre>"; print_r( $data_query); die;
            
            $data_query_count = $data_query;
            $totalFiltered = $data_query_count->count();
            $data_query = $data_query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            
            $data = array();
           // dd($data_query);
            if (!empty($data_query)) {
                foreach ($data_query as $key => $row) {
                    $user_link = '';
                    
                    $nestedData['stock_id_code'] = $row->stock_id_code;
                    $nestedData['item_category'] = $row->category_description;
                    $nestedData['title'] = $row->title;
                    $nestedData['uom'] = @$row->pack_size->title;
                    $nestedData['standard_cost'] = manageAmountFormat($row->standard_cost);
                    $nestedData['qauntity'] = manageAmountFormat(@$row->getAllFromStockMovesC->sum('qauntity'));
                    $nestedData['selling_price'] = manageAmountFormat($row->selling_price);
                    $action_text = '';
                    if(isset($permission['supreme-store-inventory___delete']) || $permission == 'superadmin'){
                        $action_text .= '<a href="'.route('supreme-store-issue.inventory-item-un-archive',$row->id).'" class="btn btn-sm btn-biz-greenish deleteMe" title="inventory item un-archive"><i class="fa fa-archive"></i></a>';
                    }
                    
                    $nestedData['action'] = $action_text;    
                    $data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            return response()->json($json_data);
        }
        if(isset($permission['supreme-store-inventory___view']) || $permission == 'superadmin')
        {
            $breadcum = [$title=>route('supreme-store-issue.archivedinventoryItems'),'Listing'=>''];
            return view('admin.supreme_store_issue.archivedinventoryItems',compact('title','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }

    public function archived_inventoryItem_delete(Request $request,$id)
    {
        if(!$request->ajax()){
            Session::flash('warning', 'Restriced: You don\'t have permissions');
            return redirect()->back();
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            if(!isset($permission['supreme-store-inventory___delete']) && $permission != 'superadmin'){
                return response()->json(['status'=>-1,'message'=>'Restriced: You don\'t have permissions']);
            }
            $item = WaInventoryItem::where('id',$id)->where('wa_inventory_items.supreme_store_deleted',1)->first();
            if($item){
                $item->supreme_store_deleted = 0;
                $item->save();
                return response()->json(['status'=>1,'message'=>'Item un-archived Successfully']);
            }
            return response()->json(['status'=>-1,'message'=>'Item Not found']);
        } catch (\Throwable $th) {
            return response()->json(['status'=>-1,'message'=>'Something went wrong']);
        }
    }
}