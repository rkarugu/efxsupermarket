<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\NWaInternalRequisition;
use App\Model\NWaInternalRequisitionItem;
use App\Model\NWaInventoryLocationTransfer;
use App\Model\NWaInventoryLocationTransferItem;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaInventoryItem;
use App\Model\WaGlTran;
use App\Model\WaAccountingPeriod;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class NIssueFullfillRequisitionController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'n-issue-fullfill-requisition';
        $this->title = 'Issue/Fullfill Requisition';
        $this->pmodule = 'issue-fullfill-requisition';
    }

    public function index() {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = NWaInternalRequisition::where('status', '=', 'APPROVED');
            if ($permission != 'superadmin') {
                $lists = $lists->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
            }
            $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.nissuefullfillrequisition.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function show($slug) {

        $row = NWaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            return view('admin.nissuefullfillrequisition.show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function exportToPdf($slug) {
        

        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = NWaInternalRequisition::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.nissuefullfillrequisition.print', compact('title', 'model', 'breadcum', 'row'));
        $report_name = 'internal_requisition_' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }

    public function printPage(Request $request) {

        $slug = $request->slug;
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = NWaInternalRequisition::with('getRelatedToLocationAndStore')->whereSlug($slug)->first();
        return view('admin.nissuefullfillrequisition.print', compact('title', 'model', 'breadcum', 'row'));
    }

    public function destroy(Request $request,$slug)
    {
        $item = NWaInternalRequisitionItem::where('id',$request->id)->first();
        if($item){
            $internal_requisition_row = NWaInternalRequisition::where('id',$item->wa_internal_requisition_id)->first();
            $message = 'Deleted Successfully';
            $location = route('n-issue-fullfill-requisition.show',$internal_requisition_row->slug);
            $item->delete();
            // Session::flash('warning',$message);
            // return redirect()->back();
            return response()->json(['result'=>1,'message'=>$message,'location'=>$location]);
        }
        // Session::flash('warning','Something went wrong');
        //     return redirect()->back();
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);
    }

    public function update(Request $request, $slug) {
           ini_set('memory_limit', '512M');
           ini_set('max_execution_time', 120);

        try {
            $dateTime = date('Y-m-d H:i:s');
            $vat_amount_arr = [];
            $cr_amount = [];
            $internal_requisition_row = NWaInternalRequisition::whereSlug($slug)->first();
            $series_module = WaNumerSeriesCode::where('module','GRN')->first();
             $intr_smodule = WaNumerSeriesCode::where('module','Internal Requisition Store C')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
            $grn_number = getCodeWithNumberSeries('GRN');
            $logged_user_profile = getLoggeduserProfile();


            $internal_requisition_row = NWaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
            if(! $internal_requisition_row ){
                Session::flash('warning','Invalid Request');
                return redirect()->route($this->model . '.index');
            }
            $itemslist = NWaInternalRequisitionItem::with(['getInventoryItemDetail.getAllFromStockMoves','getInventoryItemDetail.getTaxesOfItem'])->where('wa_internal_requisition_id',$internal_requisition_row->id)->get();
          
            foreach($itemslist as $it){   
                if(!isset($it->getInventoryItemDetail->getAllFromStockMoves) || @$it->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$it->store_location_id)->sum('qauntity') < $it->quantity){
                    Session::flash('warning', @$it->getInventoryItemDetail->stock_id_code.' Item Available quantity is not enough');
                    return redirect()->back()->withInput();
                }
                if($it->getInventoryItemDetail && $it->getInventoryItemDetail->block_this == 1){
                    Session::flash('warning', $it->getInventoryItemDetail->stock_id_code.': The product has been blocked from sale due to a change in standard cost');
                    return redirect()->back()->withInput();
                }
   
            }


                $row = new NWaInventoryLocationTransfer();
                $row->transfer_no= $internal_requisition_row->requisition_no;
                 $row->transfer_date= $internal_requisition_row->requisition_date;
                $row->restaurant_id= $logged_user_profile->restaurant_id;
                $row->wa_department_id= $logged_user_profile->wa_department_id;
                $row->user_id = $logged_user_profile->id;
                $row->from_store_location_id = $internal_requisition_row->wa_location_and_store_id;
                $row->to_store_location_id = $internal_requisition_row->to_store_id;
                $row->vehicle_register_no = $internal_requisition_row->vehicle_register_no;
                $row->route = $internal_requisition_row->route;
                $row->customer = $internal_requisition_row->customer;
                $row->status = 'COMPLETED';                
                $row->save();
                
                $userA = \App\Model\User::where('wa_location_and_store_id',$internal_requisition_row->to_store_id)->first();
                // $shift_id = date('d/m/Y').'/'.$row->route;
                // $shift = \App\Model\WaShift::where('salesman_id',@$userA->id)->where('shift_date',date('Y-m-d'))->where('status','open')->orderBy('id','DESC')->first();
                // if(!$shift){
                //     $shift 						= new \App\Model\WaShift();
                //     $shift->shift_id 			= $shift_id;
                //     $shift->route				= $row->route;
                //     $shift->salesman_id			= @$userA->id;
                //     $shift->delivery_note		= $row->id;
                //     $shift->shift_date		    = date('Y-m-d');
                //     $shift->vehicle_register_no	= $row->vehicle_register_no;
                //     $shift->status = 'open';
                //     $shift->save();                
                // }


				// $itemslist = NWaInternalRequisitionItem::with(['getInventoryItemDetail.getTaxesOfItem'])->where('wa_internal_requisition_id',$internal_requisition_row->id)->get();
				foreach ($itemslist as $value) {
					
	                $item = new NWaInventoryLocationTransferItem ();
	                $item->wa_inventory_location_transfer_id = $row->id;
	                $item->wa_inventory_item_id = $value->wa_inventory_item_id;
	                $item->quantity = $value->quantity;
	                $item->note = "";
	                $item_detail = $value->getInventoryItemDetail;
	                $item->standard_cost = $item_detail->standard_cost;
	                $item->total_cost = $item_detail->standard_cost*$value->quantity;
	
	
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
	            }

            
//             foreach ($request->related_item_ids as $related_item_id) {
//                 $related_item_row = NWaInternalRequisitionItem::where('id', $related_item_id)->first();
//                 $stock_id_code = $related_item_row->getInventoryItemDetail->stock_id_code;
//                 $delivery_quantity = 'delivered_quantity_' . $related_item_id;
//                 $required_quantity = $request->$delivery_quantity;
//                 $lists = DB::select(DB::raw("SELECT SUM(`qauntity`) as total_quantity from wa_stock_moves where stock_id_code = '" . $stock_id_code . "' AND wa_location_and_store_id='". $internal_requisition_row->wa_location_and_store_id ."' group by `wa_location_and_store_id`"));
//                 $available_quantity = 0;
//                 if(!empty($lists[0]) && isset($lists[0]->total_quantity)){
//                     $available_quantity =$lists[0]->total_quantity;
//                 }
//                 //$available_quantity = 0;
// /*
//                 if($required_quantity > $available_quantity){
//                     $error = "Item No $stock_id_code does not have enough stock.";
//                     Session::flash('warning', $error);
//                     return redirect()->back();
//                 }
// */
//             }
            $relatedItems = NWaInternalRequisitionItem::with(['getInventoryItemDetail.getAllFromStockMoves'])->whereIn('id',$request->related_item_ids)->get();
            foreach ($request->related_item_ids as $related_item_id) {
                $related_item_row = $relatedItems->where('id', $related_item_id)->first();
                
                $delivery_quantity = 'delivered_quantity_' . $related_item_id;
                $issued_quantity = $request->$delivery_quantity;
                // if($issued_quantity <= 0){
                //     continue;
                // }
                $stockMove = new WaStockMove();
                $stockMove->user_id = $logged_user_profile->id;
                $stockMove->n_wa_internal_requistion_id = $internal_requisition_row->id;
                $stockMove->document_no = $internal_requisition_row->requisition_no;
                $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
                $stockMove->wa_location_and_store_id = $related_item_row->store_location_id;
                $stockMove->wa_inventory_item_id = $related_item_row->wa_inventory_item_id;
                $stockMove->standard_cost = $related_item_row->standard_cost;
                $stockMove->qauntity = -($request->$delivery_quantity);
                $stockMove->new_qoh = (@$related_item_row->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$related_item_row->store_location_id)->sum('qauntity') ?? 0.00)-($request->$delivery_quantity);
                $stockMove->stock_id_code = $related_item_row->getInventoryItemDetail->stock_id_code;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->price = $related_item_row->standard_cost;
                $stockMove->save();
                //positive entry
                $stockMove = new WaStockMove();
                $stockMove->user_id = $logged_user_profile->id;
                $stockMove->document_no = $internal_requisition_row->requisition_no;
                $stockMove->n_wa_internal_requistion_id = $internal_requisition_row->id;
                $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
                $stockMove->wa_location_and_store_id = $internal_requisition_row->to_store_id;
                $stockMove->wa_inventory_item_id = $related_item_row->wa_inventory_item_id;
                $stockMove->standard_cost = $related_item_row->standard_cost;
                $stockMove->new_qoh = (@$related_item_row->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$internal_requisition_row->to_store_id)->sum('qauntity') ?? 0) + $request->$delivery_quantity;
                $stockMove->qauntity = $request->$delivery_quantity;
                $stockMove->stock_id_code = $related_item_row->getInventoryItemDetail->stock_id_code;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->price = $related_item_row->standard_cost;
                $stockMove->save();
                
/*
                //negative entry
                $stockMove = new WaStockMove();
                $stockMove->user_id = $logged_user_profile->id;
                $stockMove->n_wa_internal_requistion_id = $internal_requisition_row->id;
                 $stockMove->document_no = $internal_requisition_row->requisition_no;
                $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
                $stockMove->wa_location_and_store_id = $internal_requisition_row->wa_location_and_store_id;
                $stockMove->wa_inventory_item_id = $related_item_row->wa_inventory_item_id;
                $stockMove->standard_cost = $related_item_row->standard_cost;
                $stockMove->qauntity = -($request->$delivery_quantity);
                $stockMove->stock_id_code = $related_item_row->getInventoryItemDetail->stock_id_code;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->price = $related_item_row->standard_cost;
                $stockMove->save();
*/
                
                // $dr =  new WaGlTran();
                // $dr->n_wa_internal_requistion_id = $internal_requisition_row->id;
                // $dr->grn_type_number = $series_module->type_number;
                // $dr->grn_last_used_number = $series_module->last_number_used;
                //  $dr->transaction_type = $intr_smodule->description;
                // $dr->transaction_no =  $internal_requisition_row->requisition_no;
                // $dr->trans_date = $dateTime;
                // $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                // $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                // $dr->account = $related_item_row->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                // $dr->amount = '-'.($related_item_row->standard_cost * $request->$delivery_quantity);
                // $dr->narrative = $internal_requisition_row->requisition_no.'/'.$related_item_row->getInventoryItemDetail->stock_id_code.'/'.$related_item_row->getInventoryItemDetail->title.'/'.$related_item_row->standard_cost.'@'.$request->$delivery_quantity;
                // $dr->save();
                
                
                
                // $dr =  new WaGlTran();
                // $dr->n_wa_internal_requistion_id = $internal_requisition_row->id;
                // $dr->grn_type_number = $series_module->type_number;
                // $dr->grn_last_used_number = $series_module->last_number_used;
                // $dr->transaction_type = $intr_smodule->description;
                // $dr->transaction_no =  $internal_requisition_row->requisition_no;
                // $dr->trans_date = $dateTime;
                // $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                // $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                // $dr->account = $related_item_row->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                // $camount = $related_item_row->standard_cost * $request->$delivery_quantity;
                // $dr->amount = $camount;
                // $dr->narrative = $internal_requisition_row->requisition_no.'/'.$related_item_row->getInventoryItemDetail->stock_id_code.'/'.$related_item_row->getInventoryItemDetail->title.'/'.$related_item_row->standard_cost.'@'.$request->$delivery_quantity;
                // $dr->save();
                
                
                $related_item_row->issued_quanity = $issued_quantity;
                $related_item_row->save();
            }
                
            $internal_requisition_row->status = 'COMPLETED';
            $internal_requisition_row->save();
            
           // updateUniqueNumberSeries('GRN',$grn_number);
            Session::flash('success', 'Processed Successfully');
            return redirect()->route($this->model . '.index')->withInput();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

}
