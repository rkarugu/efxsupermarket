<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\NWaInternalRequisition;
use App\Model\NWaInternalRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class NInternalRequisitionController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'n-internal-requisitions';
        $this->title = 'Internal Requisitions';
        $this->pmodule = 'internal-requisitions';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = NWaInternalRequisition::where('status','!=','COMPLETED');
            if($permission != 'superadmin')
            {
                $lists = $lists->where('user_id', getLoggeduserProfile()->id);
            }
            $lists = $lists->whereDate('created_at','>=', date('Y-m-d',strtotime("-7 days")))->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.ninternalrequisition.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
                // $location = WaLocationAndStore::whereIn('id',[6,20,21,22])->pluck('location_name','id');
                $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
                return view('admin.ninternalrequisition.create',compact('title','model','breadcum'));
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
        $row =  NWaInternalRequisition::with(['getRelatedToLocationAndStore','getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail'])->whereSlug($slug)->first();
        return view('admin.ninternalrequisition.print',compact('title','model','breadcum','row')); 
    }

    public function exportToPdf($slug)
    {
      
       
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        $row =  NWaInternalRequisition::with(['getRelatedToLocationAndStore','getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail'])->whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.ninternalrequisition.print', compact('title','model','breadcum','row'));
        $report_name = 'internal_requisition_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
    }



    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                // 'route'=>'required',
                // 'vehicle_reg_no'=>'required',              
                'to_store_location_id'=>'required',
                'from_store_location_id'=>'required',
                // 'customer'=>'required',
                'item_id'=>'required|array',
                'item_quantity.*'=>'required|numeric|min:1',
                'item_standard_cost.*'=>'required|numeric|min:1',
                'item_vat.*'=>'nullable|exists:tax_managers,id',
                'item_discount_per.*'=>'nullable|numeric|min:0',
                'request_type'=>'required|in:save,send_request'
                ]);
            if ($validator->fails()) 
            {
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ]);
            }
            else
            {
          
	            $inventory = WaInventoryItem::with(['getUnitOfMeausureDetail','getAllFromStockMoves'])->whereIn('id',$request->item_id)->get();
                if(count($inventory)==0){
                    return response()->json(['result'=>0,'errors'=>['testIn'=>['Add items to proceed']]]);
                }				
                $errors = [];
                $itemsssid = array_flip($request->item_id);
                foreach ($inventory as $key => $value) {
                    
                    if((@$value->getAllFromStockMoves->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0) < @$request->item_quantity[@$itemsssid[$value->id]]){
                        $errors['item_id.'.$value->id] = ['This item don\'t have enough quantity to proceed'];
                    }
                }
                if(count($errors)>0){
                    return response()->json([
                        'result'=>0,
                        'errors'=>$errors
                    ]);
                }
                $check = DB::transaction(function () use ($inventory,$request){                
                    $getLoggeduserProfile = getLoggeduserProfile();
                    $series_module = WaNumerSeriesCode::where('module','GRN')->first();
                    $intr_smodule = WaNumerSeriesCode::where('module','Internal Requisition Store C')->first();
                    $purchase_no = getCodeWithNumberSeries('Internal Requisition Store C');
                    $row = new NWaInternalRequisition();
                    $row->requisition_no=  $purchase_no;
                    $row->restaurant_id= $getLoggeduserProfile->restaurant_id;
                    $row->wa_department_id= $getLoggeduserProfile->wa_department_id;
                    $row->user_id = $getLoggeduserProfile->id;
                    $row->to_store_id = $request->to_store_location_id;
                    $row->wa_location_and_store_id = $request->from_store_location_id;
                    $row->requisition_date = $request->requisition_date;
                    // $row->vehicle_register_no = $request->vehicle_reg_no;
                    // $row->route = $request->route;
                    // $row->customer = $request->customer;
                    $row->status = 'UNAPPROVED';
                    $row->save();
                    $purchase_no = $intr_smodule->code.'-'.manageOrderidWithPad($row->id);
                    $row->requisition_no=  $purchase_no;
                    $row->save();
                    foreach ($request->item_id as $key => $it) {
                        $val = $inventory->where('id',$it)->first();
                        $item = [];
                        $item['wa_internal_requisition_id'] = $row->id;
                        $item['wa_inventory_item_id'] = $val->id;
                        $item['quantity'] = $request->item_quantity[$key];
                        $item['note'] = "";
                        $item['prev_standard_cost'] = $val->prev_standard_cost;
                        $item['selling_price'] = $val->selling_price;
                        $item['order_price'] = $request->item_standard_cost[$key];
                        $item['supplier_uom_id'] = $val->wa_unit_of_measure_id;
                        $item['pack_size_id'] = $val->pack_size_id;
                        $item['issued_quanity'] = $request->item_quantity[$key];
                        $item['unit_conversion'] = 1;
                        $item['item_no'] = $val->stock_id_code;
                        $item['is_exclusive_vat'] = isset($request->item_vat[$key])?'Yes':'No';
                        $item['unit_of_measure'] = @$val->getUnitOfMeausureDetail->id;  
                        $item['standard_cost'] = $val->standard_cost;
                        $item['store_location_id'] = $val->store_location_id;
                        $item['total_cost'] = $request->item_standard_cost[$key]*$request->item_quantity[$key];
                        $item['discount_amount'] = ($request->item_discount_per[$key]) ? (($item['total_cost']*$request->item_discount_per[$key])/100) : 0;
                        $item['discount_percentage'] = $request->item_discount_per[$key];
                        $item['total_cost'] = $item['total_cost']-$item['discount_amount'];
                        $item['tax_manager_id']= $request->item_vat[$key] ?? NULL;
                        $item['vat_rate']= $request->item_vat_percentage[$key];
                        $item['vat_amount']= ($request->item_vat_percentage[$key]) ? ($item['total_cost'] - ($item['total_cost']*100) / ($request->item_vat_percentage[$key]+100)) : 0;
                        $item['total_cost'] = $item['total_cost'] - $item['vat_amount'];
                        $total_cost_with_vat = $item['total_cost']+$item['vat_amount'];
                        $roundOff = fmod($total_cost_with_vat, 1); //0.25
                        if($roundOff!=0){
                            if($roundOff > '0.50'){
                                $roundOff = round((1-$roundOff),2);
                            }else{
                                $roundOff = '-'.round($roundOff,2);
                            }
                        }
                        $item['round_off']		   =  $roundOff;
                        $item['total_cost_with_vat'] =  round($total_cost_with_vat);                
                        $item['created_at'] = date('Y-m-d H:i:s');                
                        $item['updated_at'] = date('Y-m-d H:i:s');                
                        $items[] = $item; 
                        if($request->request_type == 'send_request'){
                            // $stockMove = new WaStockMove();
                            // $stockMove->user_id = $getLoggeduserProfile->id;
                            // $stockMove->n_wa_internal_requistion_id = $row->id;
                            // $stockMove->document_no = $purchase_no;
                            // $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                            // $stockMove->wa_location_and_store_id = $val->store_location_id;
                            // $stockMove->wa_inventory_item_id = $val->id;
                            // $stockMove->standard_cost = $val->standard_cost;
                            // $stockMove->qauntity = -($request->item_quantity[$key]);
                            // $stockMove->new_qoh = (@$val->getAllFromStockMoves->where('wa_location_and_store_id',$val->store_location_id)->sum('qauntity') ?? 0.00)-($request->item_quantity[$key]);
                            // $stockMove->stock_id_code = $val->stock_id_code;
                            // $stockMove->grn_type_number = $series_module->type_number;
                            // $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                            // $stockMove->price = $item['order_price'];
                            // $stockMove->save();
                        }
                    }
                    NWaInternalRequisitionItem::insert($items);
                    if($request->request_type == 'send_request'){
                        $row->status = 'PENDING';
                        $row->save();
                        addInternalRequisitionPermissions_N($row->id,$row->wa_department_id);
                    }
                    updateUniqueNumberSeries('Internal Requisition Store C', $purchase_no);
                    return true;
                });
                if($check){
                    if($request->request_type == 'send_request'){
                        $message = 'Internal Requisition Store C Processed successfully';
                    }
                    else {
                        $message = 'Internal Requisition Store C Saved successfully';
                    }
                    return response()->json(['result'=>1,'message'=>$message,'location'=>route($this->model.'.index')]);         
                }
                return response()->json(['result'=>-1,'message'=>'Something went wrong']); 
            }            
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           return response()->json(['result'=>-1,'message'=>$msg]); 
        }
    }

    public function sendRequisitionRequest(Request $request,$requisition_no)
    {
       try
        {
			
			
            $row =  NWaInternalRequisition::where('status','UNAPPROVED')->where('requisition_no',$requisition_no)->first();
            if($row)
            {
                $row->to_store_id = $request->to_store_id;
                $row->wa_location_and_store_id = $request->wa_location_and_store_id;
                $row->customer = $request->customer;
                $row->save();
	         foreach($row->getRelatedItem as $key=> $val){
				if($this->checkQuantity($row->wa_location_and_store_id,$val->getInventoryItemDetail->stock_id_code,$val->quantity)=='1'){
				//	return response()->json(['status'=>false,'message'=>'('.$val->stockcode.') out of stock.']);	
		           Session::flash('warning',$val->getInventoryItemDetail->stock_id_code.') out of stock.');
		           return redirect()->back();					
				}
			 }
            $series_module = WaNumerSeriesCode::where('module','GRN')->first();
             $intr_smodule = WaNumerSeriesCode::where('module','INTERNAL REQUISITIONS')->first();
                $logged_user_profile = getLoggeduserProfile();

	         foreach($row->getRelatedItem as $key=> $related_item_row){
                //negative entry
                $stockMove = new WaStockMove();
                $stockMove->user_id = $logged_user_profile->id;
                $stockMove->wa_internal_requisition_id = $row->id;
                 $stockMove->document_no = $row->requisition_no;
                $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
                $stockMove->wa_location_and_store_id = $row->wa_location_and_store_id;
                $stockMove->wa_inventory_item_id = $related_item_row->wa_inventory_item_id;
                $stockMove->standard_cost = $related_item_row->standard_cost;
                $stockMove->qauntity = -($related_item_row->quantity);
                $stockMove->stock_id_code = $related_item_row->getInventoryItemDetail->stock_id_code;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->price = $related_item_row->standard_cost;
                $stockMove->save();
			 }

                $row->status = 'PENDING';
                $row->save();
                addInternalRequisitionPermissions_N($row->id,$row->wa_department_id);
//                updateUniqueNumberSeries('TRAN',$requisition_no);
                // updateUniqueNumberSeries('INTERNAL REQUISITIONS',$requisition_no);
                Session::flash('success', 'Request sent successfully.');
                return redirect()->route($this->model.'.index');
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


    public function show($slug)
    {
            
            $row =  NWaInternalRequisition::whereSlug($slug)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.ninternalrequisition.show',compact('title','model','breadcum','row')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }


    public function edit($slug)
    {
        try
        {
            
                $row =  NWaInternalRequisition::with(['getRelatedItem.getInventoryItemDetail'])->whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.ninternalrequisition.edit',compact('title','model','breadcum','row')); 
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


    public function update(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                // 'route'=>'required',
                // 'vehicle_reg_no'=>'required',              
                'to_store_location_id'=>'required',
                // 'customer'=>'required',
                'item_id'=>'required|array',
                'item_quantity.*'=>'required|numeric|min:1',
                'item_standard_cost.*'=>'required|numeric|min:1',
                'item_vat.*'=>'nullable|exists:tax_managers,id',
                'id'=>'nullable|exists:n_wa_internal_requisitions,id',
                'item_discount_per.*'=>'nullable|numeric|min:0',
                'request_type'=>'required|in:save,send_request'
                ]);
            if ($validator->fails()) 
            {
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ]);
            }
            else
            {
          
	            $inventory = WaInventoryItem::with(['getUnitOfMeausureDetail','getAllFromStockMoves'])->whereIn('id',$request->item_id)->get();
                if(count($inventory)==0){
                    return response()->json(['result'=>0,'errors'=>['testIn'=>['Add items to proceed']]]);
                }				
                $errors = [];
                $itemsssid = array_flip($request->item_id);
                foreach ($inventory as $key => $value) {                    
                    if((@$value->getAllFromStockMoves->where('wa_location_and_store_id',$value->store_location_id)->sum('qauntity') ?? 0) < @$request->item_quantity[@$itemsssid[$value->id]]){
                        $errors['item_id.'.$value->id] = ['This item don\'t have enough quantity to proceed'];
                    }
                }
                if(count($errors)>0){
                    return response()->json([
                        'result'=>0,
                        'errors'=>$errors
                    ]);
                }
                $check = DB::transaction(function () use ($inventory,$request){                
                    $getLoggeduserProfile = getLoggeduserProfile();
                    $series_module = WaNumerSeriesCode::where('module','GRN')->first();
                    $intr_smodule = WaNumerSeriesCode::where('module','INTERNAL REQUISITIONS')->first();
                    // $purchase_no = getCodeWithNumberSeries('INTERNAL REQUISITIONS');
                    $row = NWaInternalRequisition::where('id',$request->id)->first();
                    // $row->requisition_no=  $purchase_no;
                    $row->restaurant_id= $getLoggeduserProfile->restaurant_id;
                    $row->wa_department_id= $getLoggeduserProfile->wa_department_id;
                    $row->user_id = $getLoggeduserProfile->id;
                    $row->to_store_id = $request->to_store_location_id;
                    // $row->wa_location_and_store_id = $request->from_store_location_id;
                    $row->requisition_date = $request->requisition_date;
                    // $row->vehicle_register_no = $request->vehicle_reg_no;
                    // $row->route = $request->route;
                    // $row->customer = $request->customer;
                    $row->status = 'UNAPPROVED';
                    $row->save();
                    NWaInternalRequisitionItem::where('wa_internal_requisition_id',$row->id)->delete();
                    foreach ($request->item_id as $key => $it) {
                        $val = $inventory->where('id',$it)->first();
                        $item = [];
                        $item['wa_internal_requisition_id'] = $row->id;
                        $item['wa_inventory_item_id'] = $val->id;
                        $item['quantity'] = $request->item_quantity[$key];
                        $item['note'] = "";
                        $item['prev_standard_cost'] = $val->prev_standard_cost;
                        $item['selling_price'] = $val->selling_price;
                        $item['order_price'] = $request->item_standard_cost[$key];
                        $item['supplier_uom_id'] = $val->wa_unit_of_measure_id;
                        $item['pack_size_id'] = $val->pack_size_id;
                        $item['issued_quanity'] = $request->item_quantity[$key];
                        $item['unit_conversion'] = 1;
                        $item['item_no'] = $val->stock_id_code;
                        $item['is_exclusive_vat'] = isset($request->item_vat[$key])?'Yes':'No';
                        $item['unit_of_measure'] = @$val->getUnitOfMeausureDetail->id;  
                        $item['standard_cost'] = $val->standard_cost;
                        $item['store_location_id'] = $val->store_location_id;
                        $item['total_cost'] = $request->item_standard_cost[$key]*$request->item_quantity[$key];
                        $item['discount_amount'] = ($request->item_discount_per[$key]) ? (($item['total_cost']*$request->item_discount_per[$key])/100) : 0;
                        $item['discount_percentage'] = $request->item_discount_per[$key];
                        $item['total_cost'] = $item['total_cost']-$item['discount_amount'];
                        $item['tax_manager_id']= $request->item_vat[$key] ?? NULL;
                        $item['vat_rate']= $request->item_vat_percentage[$key];
                        $item['vat_amount']= ($request->item_vat_percentage[$key]) ? ($item['total_cost'] - ($item['total_cost']*100) / ($request->item_vat_percentage[$key]+100)) : 0;
                        $item['total_cost'] = $item['total_cost'] - $item['vat_amount'];
                        $total_cost_with_vat = $item['total_cost']+$item['vat_amount'];
                        $roundOff = fmod($total_cost_with_vat, 1); //0.25
                        if($roundOff!=0){
                            if($roundOff > '0.50'){
                                $roundOff = round((1-$roundOff),2);
                            }else{
                                $roundOff = '-'.round($roundOff,2);
                            }
                        }
                        $item['round_off']		   =  $roundOff;
                        $item['total_cost_with_vat'] =  round($total_cost_with_vat);                
                        $item['created_at'] = date('Y-m-d H:i:s');                
                        $item['updated_at'] = date('Y-m-d H:i:s');                
                        $items[] = $item; 
                        if($request->request_type == 'send_request'){
                            // $stockMove = new WaStockMove();
                            // $stockMove->user_id = $getLoggeduserProfile->id;
                            // $stockMove->n_wa_internal_requistion_id = $row->id;
                            // $stockMove->document_no = $row->requisition_no;
                            // $stockMove->restaurant_id = $getLoggeduserProfile->restaurant_id;
                            // $stockMove->wa_location_and_store_id = $val->store_location_id;
                            // $stockMove->wa_inventory_item_id = $val->id;
                            // $stockMove->standard_cost = $val->standard_cost;
                            // $stockMove->qauntity = -($request->item_quantity[$key]);
                            // $stockMove->new_qoh = (@$val->getAllFromStockMoves->where('wa_location_and_store_id',$val->store_location_id)->sum('qauntity') ?? 0.00)-($request->item_quantity[$key]);
                            // $stockMove->stock_id_code = $val->stock_id_code;
                            // $stockMove->grn_type_number = $series_module->type_number;
                            // $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                            // $stockMove->price = $item['order_price'];
                            // $stockMove->save();
                        }
                    }
                    NWaInternalRequisitionItem::insert($items);
                    if($request->request_type == 'send_request'){
                        $row->status = 'PENDING';
                        $row->save();
                        addInternalRequisitionPermissions_N($row->id,$row->wa_department_id);
                    }
                    // updateUniqueNumberSeries('INTERNAL REQUISITIONS', $purchase_no);
                    return true;
                });
                if($check){
                    if($request->request_type == 'send_request'){
                        $message = 'INTERNAL REQUISITIONS Processed successfully';
                    }
                    else {
                        $message = 'INTERNAL REQUISITIONS Saved successfully';
                    }
                    return response()->json(['result'=>1,'message'=>$message,'location'=>route($this->model.'.index')]);         
                }
                return response()->json(['result'=>-1,'message'=>'Something went wrong']); 
            }            
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           return response()->json(['result'=>-1,'message'=>$msg]); 
        }
    }

    public function destroy($slug)
    {
        try
        {
            NWaInternalRequisition::whereSlug($slug)->delete();
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
            NWaInternalRequisitionItem::whereId($id)->delete();


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

         $row =  NWaInternalRequisition::where('requisition_no',$requisition_no)->first();

      $pdf = PDF::loadView('admin.ninternalrequisition.print', compact('row'));
      return $pdf->download($purchase_no.'.pdf');

      
    }


    public function editPurchaseItem($requisition_no,$id)
    {
        try
        {
           
                $row =  NWaInternalRequisition::where('requisition_no',$requisition_no)
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
                    return view('admin.ninternalrequisition.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
           
          
            $item =  NWaInternalRequisitionItem::where('id',$id)->first();
          
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
            return redirect()->route($this->model.'.edit', $item->getInternalPurchaseId->slug);
            
        }
        catch(\Exception $e)
        {
            dd($e);
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


    public function checkQuantity($locationid,$itemid,$qty)
    {
        try
        {
          $qtyOnHand = WaStockMove::where('wa_location_and_store_id',$locationid)->where('stock_id_code',$itemid)->sum('qauntity');
		 // echo $qtyOnHand; die;
          $item = WaInventoryItem::select('stock_id_code','id')->where('stock_id_code',$itemid)->first();

          $item_id = $item->id;


          $myqty = $qty;
          $qtyOnHand = $qtyOnHand;
          if($myqty <= $qtyOnHand )
          {
             return '0';
          }
          else
          {
            
            return '1';
          }
        }
        catch(\Exception $e)
        {
           
            return '1';
        }
    }
    

    
}
