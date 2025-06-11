<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSupremeStoreRequisition;
use App\Model\WaSupremeStoreRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaStockMoveC;

use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class SupremeStoreRequisitionController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'supreme-store-requisitions';
        $this->title = 'Supreme Store Reqeust';
        $this->pmodule = 'supreme-store-requisitions';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaSupremeStoreRequisition::where('status','!=','COMPLETED')->with(['getrelatedEmployee','getRelatedToLocationAndStore','getDepartment','getRelatedItem']);
            if($permission != 'superadmin')
            {
                $lists = $lists->where('user_id', getLoggeduserProfile()->id);
            }
          $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.supreme_store_requisition.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function create()
    {
        if(getLoggeduserProfile()->wa_department_id && getLoggeduserProfile()->restaurant_id)
        {
             $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
            {
                $title = 'Add '.$this->title;
                $model = $this->model;
                $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
                return view('admin.supreme_store_requisition.create',compact('title','model','breadcum'));
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        }
        else
        {
             Session::flash('warning', 'Please update your branch and department');
                return redirect()->back();
        }
       
        
    }

    public function print(Request $request)
    {
      
        $slug = $request->slug;  
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        $row =  WaSupremeStoreRequisition::with(['getrelatedEmployee','getRelatedItem','getRelatedItem.getInventoryItemDetail','getRelatedItem.location','getRelatedItem.getInventoryItemDetail.getAllFromStockMovesC'])->whereSlug($slug)->first();
        return view('admin.supreme_store_requisition.print',compact('title','model','breadcum','row')); 
    }

    public function exportToPdf($slug)
    {
      
       
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        $row =  WaSupremeStoreRequisition::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.supreme_store_requisition.print', compact('title','model','breadcum','row'));
        $report_name = 'supreme_store__requisition_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
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
            'request_type'=>'required|in:send_request,save',
            'to_store_id'=>'required|exists:wa_location_and_stores,id',
            'manual_doc_no'=>'required|string|min:1|max:200'
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
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.wa_location_and_store_id = wa_inventory_items.store_location_id AND wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMovesC','getTaxesOfItem'])->whereIn('id',$request->item_id)->get();
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
        }
        $check = DB::transaction(function () use ($allInventroy,$request){
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'SUPREME STORE REQUISITION')->first();
            $sale_invoiceno = getCodeWithNumberSeries('SUPREME STORE REQUISITION');
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            $parent = new WaSupremeStoreRequisition;
            $parent->requisition_no = $sale_invoiceno;
            $parent->user_id = $getLoggeduserProfile->id;
            $parent->restaurant_id = $getLoggeduserProfile->restaurant_id;
            $parent->wa_department_id = $getLoggeduserProfile->wa_department_id;
            $parent->to_store_id = $request->to_store_id;
            $parent->manual_doc_no = $request->manual_doc_no;
            $parent->wa_location_and_store_id = NULL;//$getLoggeduserProfile->id;
            $parent->requisition_date = date('Y-m-d');
            $parent->save();
            $childs = [];     
            foreach ($allInventroy as $key => $value) {
                $stock_qoh = $value->quantity ?? 0;
                $data = [];
                $data['wa_supreme_store_requisitions_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['quantity'] = @$request->item_quantity[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['standard_cost'] = $value->standard_cost;
                $data['total_cost'] = $value->standard_cost*@$request->item_quantity[$value->id];
                $data['vat_rate'] = @$value->getTaxesOfItem->tax_value;
                $data['vat_amount'] = ($data['total_cost']*$data['vat_rate'])/100;
                $data['total_cost_with_vat'] = $data['total_cost']+$data['vat_amount'];
                $data['note'] = NULL;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                
                    // $stock_qoh -= $data['quantity'];

                    // $stockMove = new WaStockMoveC();
                    // $stockMove->user_id = $getLoggeduserProfile->id;
                    // $stockMove->wa_supreme_store_requisitions_id = $parent->id;
                    // $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    // $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    // $stockMove->stock_id_code = $value->stock_id_code;
                    // $stockMove->wa_inventory_item_id = @$value->id;
                    // $stockMove->document_no =   $parent->requisition_no;
                    // $stockMove->price = 0;
                    // $stockMove->grn_type_number = $series_module->type_number;
                    // $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    // $stockMove->refrence = $parent->requisition_no;
                    // $stockMove->qauntity = -$data['quantity'];
                    // $stockMove->new_qoh = $stock_qoh;
                    // $stockMove->standard_cost = $value->standard_cost;
                    // $stockMove->save();

                    
                }

            }
            WaSupremeStoreRequisitionItem::insert($childs);
            
            if($request->request_type == 'send_request'){
                $parent->status = 'PENDING';
                $parent->save();
                addsupreme_store_RequisitionPermissions($parent->id,$parent->wa_department_id);
            }
            
            updateUniqueNumberSeries('SUPREME STORE REQUISITION',$parent->requisition_no);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                $requestty = 'send_request';
                $location = route('supreme-store-requisitions.index');
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('supreme-store-requisitions.index');
            }
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'requestty'=>$requestty]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }


    public function show($slug)
    {
            
            $row =  WaSupremeStoreRequisition::with(['getRelatedItem',
            'getRelatedItem.getInventoryItemDetail',
            'getRelatedItem.getInventoryItemDetail.pack_size',
            'getRelatedItem.location',
            'getRelatedAuthorizationPermissions'
            ])->whereSlug($slug)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.supreme_store_requisition.show',compact('title','model','breadcum','row')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }


    public function edit($slug)
    {
        if (!isset($permission[$pmodule . '___edit']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        try
        {
            
                $row =  WaSupremeStoreRequisition::with(['getRelatedItem',
                'getRelatedItem.getInventoryItemDetail',
                'getRelatedItem.getInventoryItemDetail.pack_size',
                'getRelatedItem.location',
                ])->whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.supreme_store_requisition.edit',compact('title','model','breadcum','row')); 
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


    public function update(Request $request, $slug)
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
            'request_type'=>'required|in:send_request,save',
            'to_store_id'=>'required|exists:wa_location_and_stores,id',
            'manual_doc_no'=>'required|string|min:1|max:200'
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
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.wa_location_and_store_id = wa_inventory_items.store_location_id AND wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->with(['getAllFromStockMovesC','getTaxesOfItem'])->whereIn('id',$request->item_id)->get();
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
        }
        $parent = WaSupremeStoreRequisition::where('slug',$slug)->first();
        if(!$parent || $parent->status != 'UNAPPROVED'){
            return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
        }
        $check = DB::transaction(function () use ($allInventroy,$request,$parent){
            $series_module = \App\Model\WaNumerSeriesCode::where('module', 'SUPREME STORE REQUISITION')->first();
            // $sale_invoiceno = getCodeWithNumberSeries('SUPREME STORE REQUISITION');
            $getLoggeduserProfile = getLoggeduserProfile();
            $dateTime = date('Y-m-d H:i:s');
            // $parent->requisition_no = $sale_invoiceno;
            // $parent->user_id = $getLoggeduserProfile->id;
            // $parent->restaurant_id = $getLoggeduserProfile->restaurant_id;
            // $parent->wa_department_id = $getLoggeduserProfile->wa_department_id;
            $parent->to_store_id = $request->to_store_id;
            $parent->manual_doc_no = $request->manual_doc_no;
            // $parent->wa_location_and_store_id = NULL;//$getLoggeduserProfile->id;
            // $parent->requisition_date = date('Y-m-d');
            $parent->save();
            $childs = [];   
            WaSupremeStoreRequisitionItem::where('wa_supreme_store_requisitions_id',$parent->id)->delete();
            foreach ($allInventroy as $key => $value) {
                $stock_qoh = $value->quantity ?? 0;
                $data = [];
                $data['wa_supreme_store_requisitions_id'] = $parent->id;
                $data['wa_inventory_item_id'] = $value->id;
                $data['store_location_id'] = $value->store_location_id;
                $data['quantity'] = @$request->item_quantity[$value->id];
                $data['created_at'] = $dateTime;
                $data['updated_at'] = $dateTime;
                $data['standard_cost'] = $value->standard_cost;
                $data['total_cost'] = $value->standard_cost*@$request->item_quantity[$value->id];
                $data['vat_rate'] = @$value->getTaxesOfItem->tax_value;
                $data['vat_amount'] = ($data['total_cost']*$data['vat_rate'])/100;
                $data['total_cost_with_vat'] = $data['total_cost']+$data['vat_amount'];
                $data['note'] = NULL;
                $childs[] = $data;
                if($request->request_type == 'send_request'){
                
                    // $stock_qoh -= $data['quantity'];

                    // $stockMove = new WaStockMoveC();
                    // $stockMove->user_id = $getLoggeduserProfile->id;
                    // $stockMove->wa_supreme_store_requisitions_id = $parent->id;
                    // $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    // $stockMove->wa_location_and_store_id = $value->store_location_id; //$getLoggeduserProfile->wa_location_and_store_id;
                    // $stockMove->stock_id_code = $value->stock_id_code;
                    // $stockMove->wa_inventory_item_id = @$value->id;
                    // $stockMove->document_no =   $parent->requisition_no;
                    // $stockMove->price = 0;
                    // $stockMove->grn_type_number = $series_module->type_number;
                    // $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    // $stockMove->refrence = $parent->requisition_no;
                    // $stockMove->qauntity = -$data['quantity'];
                    // $stockMove->new_qoh = $stock_qoh;
                    // $stockMove->standard_cost = $value->standard_cost;
                    // $stockMove->save();

                    
                }

            }
            WaSupremeStoreRequisitionItem::insert($childs);
            
            if($request->request_type == 'send_request'){
                $parent->status = 'PENDING';
                $parent->save();
                addsupreme_store_RequisitionPermissions($parent->id,$parent->wa_department_id);
            }
            
            // updateUniqueNumberSeries('SUPREME STORE REQUISITION',$parent->requisition_no);
            return $parent;
        });
        if($check){
            if($request->request_type == 'send_request'){
                $message = 'Sales processed successfully.';
                $requestty = 'send_request';
                $location = route('supreme-store-requisitions.index');
            }else {
                $message = 'Sales Saved successfully.';
                $requestty = 'save';
                $location = route('supreme-store-requisitions.index');
            }
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'requestty'=>$requestty]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
    }


    public function destroy($slug)
    {
        if (!isset($permission[$pmodule . '___delete']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        try
        {
            $row = WaSupremeStoreRequisition::whereSlug($slug)->first();
            if($row){
                WaSupremeStoreRequisitionItem::where('wa_supreme_store_requisitions_id',$row->id)->delete();
                $row->delete();
            }
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

     public function getDapartments(Request $request)
    {
      $rows = WaDepartment::where('restaurant_id',$request->branch_id)->orderBy('department_name','asc')->get();
      $data = '<option  value="">Please select department</option>';
      foreach($rows as $row)
      {
        $data .= '<option  value="'.$row->id.'">'.$row->department_name.'</option>';
      }

      return $data;

    }

    public function getItems(Request $request)
    {
      $rows = WaInventoryItem::where('wa_inventory_category_id',$request->selected_inventory_category)->orderBy('title','asc')->get();
      $data = '<option  value="">Please select item</option>';
      foreach($rows as $row)
      {
        $data .= '<option  value="'.$row->id.'">'.$row->title.'</option>';
      }

      return $data;

    }

    public function getItemDetail(Request $request)
    {
      $rows = WaInventoryItem::where('id',$request->selected_item_id)->first();

      if($rows->minimum_order_quantity <1)
      {
        $rows->minimum_order_quantity = 1;
      }
     

      return json_encode(['stock_id_code'=>$rows->stock_id_code,'unit_of_measure'=>$rows->wa_unit_of_measure_id?$rows->wa_unit_of_measure_id:'','minimum_order_quantity'=>$rows->minimum_order_quantity]);

    }
    public function deletingItemRelation($purchase_no,$id)
    {
        try
        {
            WaSupremeStoreRequisitionItem::whereId($id)->delete();


            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }



    public function downloadPrint($requisition_no)
    {

         $row =  WaSupremeStoreRequisition::where('requisition_no',$requisition_no)->first();

      $pdf = PDF::loadView('admin.supreme_store_requisition.print', compact('row'));
      return $pdf->download($purchase_no.'.pdf');

      
    }


    public function editPurchaseItem($requisition_no,$id)
    {
        try
        {
           
                $row =  WaSupremeStoreRequisition::where('requisition_no',$requisition_no)
                            ->whereHas('getRelatedItem',function ($sql_query) use($id) {  
                                $sql_query->where('id', $id);
                        })

                        ->first();
                if($row)
                {
                 
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),$row->purchase_no=>'','Edit'=>''];
                    $model =$this->model;


                    $form_url = [$model.'.updatePurchaseItem', $row->getRelatedItem->find($id)->id];
                    return view('admin.supreme_store_requisition.editItem',compact('title','model','breadcum','row','id','form_url')); 
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

    public function updatePurchaseItem(Request $request,$id)
    {
        try
        {
           
          
            $item =  WaSupremeStoreRequisitionItem::where('id',$id)->first();
          
            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item_detail = WaInventoryItem::where('id',$request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item_detail->standard_cost*$request->quantity;
            $vat_rate = 0;
            $vat_amount = 0;
            if($item_detail->tax_manager_id && $item_detail->getTaxesOfItem)
            {
                $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                if($item->total_cost > 0)
                {
                    $vat_amount = ($item_detail->getTaxesOfItem->tax_value*$item->total_cost)/100;
                }
            }
            $item->vat_rate = $vat_rate;
            $item->vat_amount = $vat_amount;
            $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.edit', @$item->getInternalPurchaseId->slug);
            
        }
        catch(\Exception $e)
        {
            // dd($e);
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }



    public function getItemQohAjax(Request $request){
        $item_id = $request->item_id;
        $location_id = $request->location_id;
        $quantity = '';
        if(!empty($item_id) && !empty($location_id)){
            $inventory_items_row = \App\Model\WaInventoryItem::where('id', $item_id)->first();
            if(!empty($inventory_items_row->stock_id_code)){
                $quantity = getItemAvailableQuantity($inventory_items_row->stock_id_code, $location_id);
            }
        }
        echo json_encode(['quantity'=>$quantity]);
        die;
    }

    
    public function inventoryItems(Request $request)
    {
        $data = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.wa_location_and_store_id = wa_inventory_items.store_location_id AND wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->where(function($q) use ($request){
            if ($request->search) {
                $q->where('title','LIKE',"%$request->search%");
                $q->orWhere('stock_id_code','LIKE',"%$request->search%");
            }
        })->where('supreme_store_deleted',0)->having('quantity','>',0)->limit(20)->get();
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
        $permission =  $this->mypermissionsforAModule();
        $editPermission = '';
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___edit-values']) && $permission != 'superadmin') {
            $editPermission = 'readonly';
        }
        $data = WaInventoryItem::select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves_C.qauntity) FROM wa_stock_moves_C where wa_stock_moves_C.wa_location_and_store_id = wa_inventory_items.store_location_id AND wa_stock_moves_C.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->where('supreme_store_deleted',0)->with(['getTaxesOfItem','pack_size'])->where('id',$request->id)->first();
        $view = '';
        if($data && $data->quantity != NULL && $data->quantity > 0){
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
}
