<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaExternalRequisition;
use App\Model\WaExternalRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaStockMove;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaInventoryLocationItemReturn;
use App\Model\WaEsdDetails;

use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;


class NewKRASignedInvoiceController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'new-kra-signed-invoices';
        $this->title = 'New KRA Signed Invoices';
        $this->pmodule = 'new-kra-signed-invoices';
    } 

    public function indexNew(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        // dd($user);
       // echo $permission[$pmodule.'___view']; die;
        if(isset($permission['print-invoice-delivery-note___view']) || $permission == 'superadmin'){
            $pos = " AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN '".$request->start_date."' AND '".$request->end_date."')";
            $sales = " AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN '".$request->start_date."' AND '".$request->end_date."')";
            $getUpUsers = \App\Model\User::where(['upload_data'=>1])->pluck('id')->toArray();

           

            $ids = implode(',',$getUpUsers);
            if($request->type == 'true'){
                $pos .= " AND exists (SELECT * from wa_pos_cash_sales 
                where wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id 
                AND wa_pos_cash_sales.user_id IN (". $ids ."))";
                $sales .= " AND exists (SELECT * from wa_inventory_location_transfers where wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id AND wa_inventory_location_transfers.user_id IN (". $ids ."))";
            }
            if($request->type == 'false'){
                $pos .= " AND exists (SELECT * from wa_pos_cash_sales 
                where wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id 
                AND wa_pos_cash_sales.user_id NOT IN (". $ids ."))";
                $sales .= " AND exists (SELECT * from wa_inventory_location_transfers where wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id AND wa_inventory_location_transfers.user_id NOT IN (". $ids ."))";
            }
            $allTrans = \App\Model\TaxManager::select([
                '*',
                DB::RAW('(SELECT SUM(selling_price * qty) FROM `wa_pos_cash_sales_items` 
                WHERE wa_pos_cash_sales_items.tax_manager_id = tax_managers.id '.$pos.') as pos_total'),
                DB::RAW('(SELECT SUM(
                    (selling_price * qty) - (((selling_price * qty)*100 ) / (vat_percentage+100))
                    ) FROM `wa_pos_cash_sales_items` WHERE wa_pos_cash_sales_items.tax_manager_id = tax_managers.id '.$pos.') as pos_vat_total'),

                DB::RAW('(SELECT SUM(selling_price * quantity) FROM 
                    `wa_inventory_location_transfer_items` WHERE wa_inventory_location_transfer_items.tax_manager_id = tax_managers.id '.$sales.') 
                    as sales_total'),
                DB::RAW('(SELECT SUM(
                    (selling_price * quantity) - (((selling_price * quantity)*100 ) / (vat_rate+100))
                ) FROM 
                `wa_inventory_location_transfer_items` WHERE wa_inventory_location_transfer_items.tax_manager_id = tax_managers.id '.$sales.') 
                as sales_vat_total')
            ])->get();  
            if($request->manage == 'pdf' || $request->manage == 'print'){
                $allTrans = $allTrans->map(function($item){
                    $item->saleso = ($item->pos_vat_total+$item->sales_vat_total);
                    $item->toto = ($item->pos_total+$item->sales_total);
                    $item->posto = ($item->toto)-($item->saleso);
                    return $item;
                });
                if($request->manage == 'pdf'){
                    $pdf = \PDF::loadView('admin.customer_aging_analysis.vatReportpdf',compact('allTrans'))->setPaper('a4','landscape');
                    return $pdf->download('vat-Report-'.$request->start_date.'-'.$request->end_date.'-'.time().'.pdf');
                }
                return view('admin.customer_aging_analysis.vatReportpdf',compact('allTrans'));
            } 
            $allTrans = $allTrans->map(function($item){
                $item->salesot = ($item->pos_vat_total+$item->sales_vat_total);
                $item->totot = ($item->pos_total+$item->sales_total);
                $item->postot = ($item->totot)-($item->salesot);
                $item->saleso = manageAmountFormat($item->salesot);
                $item->toto = manageAmountFormat($item->totot);
                $item->posto = manageAmountFormat(($item->totot)-($item->salesot));
                return $item;
            });

            dd($allTrans->toArray());
            return view('admin.new_kra_signed_invoices.index',compact('user','title','lists','model','breadcum','pmodule','permission','allTrans'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }


    public function index(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        // dd($user);
       // echo $permission[$pmodule.'___view']; die;
        if(isset($permission['print-invoice-delivery-note___view']) || $permission == 'superadmin'){
            DB::enableQueryLog();
            $lists = WaEsdDetails::select(['*',
                DB::RAW('(select SUM(wa_inventory_location_transfer_items.total_cost_with_vat) from wa_inventory_location_transfers LEFT JOIN wa_inventory_location_transfer_items ON  wa_inventory_location_transfers.id=wa_inventory_location_transfer_items.wa_inventory_location_transfer_id  where wa_inventory_location_transfers.transfer_no=wa_esd_details.invoice_number ) as esd_amount'),                
                DB::RAW(' (select wa_location_and_stores.location_name from wa_inventory_location_transfers LEFT JOIN wa_location_and_stores ON  wa_location_and_stores.id=wa_inventory_location_transfers.to_store_location_id  where wa_inventory_location_transfers.transfer_no=wa_esd_details.invoice_number) as esd_store_location ')
            ]);
           
           
            /*if ($request->salesman){
              $lists = $lists->where('to_store_location_id', $request->salesman);
            }*/
            if ($request->has('start_date') && $request->input('start_date')!=""){
                $lists = $lists->whereDate('created_at','>=',$request->input('start_date'));
            }

            if ($request->has('end_date') && $request->input('end_date')!="" ){
                $lists = $lists->whereDate('created_at','<=',$request->input('end_date'));
            }

            if ($request->has('esd_type')){
                $esd_type=$request->esd_type;

                $invoiceKeyword=($esd_type=="pos_cash_sales")?'CS-':'INV-';

                $lists = $lists->where('invoice_number','like','%'.$invoiceKeyword.'%');
            }

            //if ($request->has('description') && $request->input('description')=="signed_successfully"){
                $lists = $lists->where('description',"Signed successfully.");
            //}
            


            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            $lists = $lists->orderBy('id', 'desc')->get();
            
            //dd(DB::getQueryLog());

            if($request->get('manage-request') && $request->get('manage-request') == 'PDF'){
                $pdf = PDF::loadView('admin.new_kra_signed_invoices.indexprint',compact('user','title','lists','model','breadcum','pmodule','permission','request'));
                return $pdf->download('singned_invoices_'.date('Y_m_d_h_i_s').'.pdf');
            }
            if($request->get('request') && $request->get('request') == 'PRINT'){
                return view('admin.new_kra_signed_invoices.indexprint',compact('user','title','lists','model','breadcum','pmodule','permission','request'));
            }
            // dd($request->all());
            return view('admin.new_kra_signed_invoices.index',compact('user','title','lists','model','breadcum','pmodule','permission'));
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
                return view('admin.new_kra_signed_invoices.create',compact('title','model','breadcum'));
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
        $list =   WaInventoryLocationTransfer::with(['getRelatedItem','getRelatedItem.getInventoryItemDetail'])->where('transfer_no',$request->transfer_no)->first();
        if($list){
            $list->print_count++;
            $list->save();

             $esd_details = WaEsdDetails::where('invoice_number',$request->transfer_no)->first();
            return view('admin.new_kra_signed_invoices.print',compact('title','list','esd_details'));       
        }
        Session::flash('warning', 'Invalid request');
		return redirect()->back();
    }

    public function print_return(Request $request)
    {
        $data =  WaInventoryLocationTransferItem::with(['getInventoryItemDetail','getTransferLocation','returned_by','getTransferLocation.toStoreDetail'])
        ->where('is_return',1)->where('return_grn',$request->transfer_no)->get();
        return view('admin.new_kra_signed_invoices.print_return',compact('title','data','request'));       
    }

	public function refreshstockmoves(){
		
        $list =   WaInventoryLocationTransfer::select('transfer_no','shift_id')->get();		
        foreach($list as $key=> $val){
	    	WaStockMove::where('document_no',$val->transfer_no)->update(['shift_id'=>$val->shift_id]);    
        }
		Session::flash('success', 'Refreshed successfully.');
		return redirect()->back();
	}

    public function store(Request $request)
    {
     //   echo "<pre>"; print_r($request->all()); die;
        try
        {
            $validator = Validator::make($request->all(), [
               
                'transfer_no' => 'required|unique:wa_inventory_location_transfers',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
	            $checkexist = WaInventoryLocationTransfer::where('transfer_no',$request->transfer_no)->count();
	            if($request->has('type')){
		            $itemcode  = array_filter($request->get('item_code'));
					$checkitem = WaInventoryItem::whereIn('stock_id_code',$itemcode)->count();
					if(count($itemcode)!=$checkitem){
						Session::flash('warning', 'Please enter a valid item code.');
						return redirect()->back();
					}		            
	            }
	            if($checkexist>0){
					Session::flash('warning', 'Delivery Note No is already taken.');
					return redirect()->back();
	            }
				
                $row = new WaInventoryLocationTransfer();
                $row->transfer_no= $request->transfer_no;
                 $row->transfer_date= $request->transfer_date;
                $row->restaurant_id= getLoggeduserProfile()->restaurant_id;
                $row->wa_department_id= getLoggeduserProfile()->wa_department_id;
                $row->user_id = getLoggeduserProfile()->id;
                $row->from_store_location_id = $request->from_store_location_id;
                $row->to_store_location_id = $request->to_store_location_id;
                $row->vehicle_register_no = $request->vehicle_reg_no;
                $row->route = $request->route;
                $row->customer = $request->customer;
                $row->save();
			
			   foreach($request->qty as $key=> $val){
				if($val > 0){
		            if($request->has('type')){
			            $itemcode  = $request->get('item_code');
						$key = WaInventoryItem::where('stock_id_code',$itemcode[$key])->first()->id;
			         }else{
				        $key = $key; 
			         }

					
	                $item = new WaInventoryLocationTransferItem ();
	                $item->wa_inventory_location_transfer_id = $row->id;
	                $item->wa_inventory_item_id = $key;
	                $item->quantity = $val;
	                $item->note = "";
	                $item_detail = WaInventoryItem::where('id',$key)->first();
	                $item->standard_cost = $item_detail->standard_cost;
	                $item->total_cost = $item_detail->standard_cost*$val;
	
	
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
	           }
                updateUniqueNumberSeries('TRAN',$request->transfer_no);
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.edit', $row->slug);
           
            }
            
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function checkQtyWithHandForAll($inventoryTransfer)
    {
       
        $item_withqty = [];
        foreach($inventoryTransfer->getRelatedItem as $item_required)
        {

            if(isset($item_withqty[$item_required->wa_inventory_item_id]))
            {
                $item_withqty[$item_required->wa_inventory_item_id] = $item_withqty[$item_required->wa_inventory_item_id]+$item_required->quantity;
            }
            else
            {
                $item_withqty[$item_required->wa_inventory_item_id] =$item_required->quantity;

            }
        }

       


       
        $error = '';
       foreach ($item_withqty as $key => $value) 
       {
            

            $item = WaInventoryItem::select('stock_id_code','id')->where('id',$key)->first();
            $qtyOnHand = WaStockMove::where('wa_location_and_store_id',$inventoryTransfer->from_store_location_id)
            ->where('stock_id_code',$item->stock_id_code)
            ->sum('qauntity');
            if($value<=$qtyOnHand)
            {

            }
            else
            {
                if($error == '')
                {
                    $error = $item->stock_id_code.' have only '.$qtyOnHand;
                }
                else
                {
                    $error .= ', '.$item->stock_id_code.' have only '.$qtyOnHand;
                }
            }
       }
       if($error == '')
       {
            return 'ok';
       }
       else
       {
            return 'ok';
//            return $error;
       }

      
      

    }

    public function processTransfer($transfer_no)
    {
       try 
        {
            $checkcount =  WaInventoryLocationTransfer::where('status','PENDING')->where('transfer_no',$transfer_no)->count();
            
            if($checkcount > 1)
            {
                Session::flash('warning', 'Already exist transfer no.');
                return redirect()->back();	            
	        }

            $row =  WaInventoryLocationTransfer::where('status','PENDING')->where('transfer_no',$transfer_no)->first();
            if($row)
            {
               $qtyStatus =  $this->checkQtyWithHandForAll($row);
               if($qtyStatus == 'ok')
               {
                    $row->status = 'COMPLETED';
                    $row->save();
                    $internal_requisition_row =  WaInventoryLocationTransfer::where('transfer_no', $transfer_no)->first();
                    $series_module = WaNumerSeriesCode::where('module', 'TRAN')->first();
                    $intr_smodule = WaNumerSeriesCode::where('module', 'TRAN')->first();
                    $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
                    $dateTime = date('Y-m-d H:i:s');
                    foreach($row->getRelatedItem as $item)
                    {
                        $delivery_quantity = 'delivered_quantity_' . $item;
                    $from_entry = new WaStockMove();
                    $from_entry->user_id = getLoggeduserProfile()->id;
                    $from_entry->wa_inventory_location_transfer_id = $row->id;
                    $from_entry->restaurant_id = $row->restaurant_id;
                    $from_entry->wa_location_and_store_id = $row->from_store_location_id;
                    $from_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                    $from_entry->wa_inventory_item_id = $item->item;
                    $from_entry->qauntity = '-'.$item->quantity;
                    $from_entry->standard_cost = $item->standard_cost;
                    $from_entry->price = $item->standard_cost;
                    $from_entry->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                    $from_entry->document_no = $transfer_no;
                    $from_entry->save();
                    
                    $to_entry = new WaStockMove();
                    $to_entry->user_id = getLoggeduserProfile()->id;
                    $to_entry->wa_inventory_location_transfer_id = $row->id;
                    $to_entry->restaurant_id = $row->restaurant_id;
                    $to_entry->wa_location_and_store_id = $row->to_store_location_id;
                    $to_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                    $to_entry->wa_inventory_item_id = $item->getInventoryItemDetail->id;
                    $to_entry->qauntity = $item->quantity;
                    $to_entry->standard_cost = $item->standard_cost;
                    $to_entry->price = $item->standard_cost;
                    $to_entry->document_no = $transfer_no;
                    $to_entry->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                    $to_entry->save();




// $dr =  new WaGlTran();
// //$dr->wa_internal_requisition_id = $internal_requisition_row->id;
// $dr->grn_type_number = $series_module->type_number;
// $dr->grn_last_used_number = $series_module->last_number_used;
// $dr->transaction_type = $intr_smodule->description;
// $dr->transaction_no =  $internal_requisition_row->transfer_no;
// $dr->trans_date = $dateTime;
// $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
// $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
// $dr->account = $item->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
// $dr->amount = '-' . ($item->standard_cost * $item->quantity);
// $dr->narrative = $internal_requisition_row->transfer_no . '/' . $item->getInventoryItemDetail->stock_id_code . '/' . $item->getInventoryItemDetail->title . '/' . $item->standard_cost . '@' . $item->quantity;
// $dr->save();



// $dr =  new WaGlTran();
// //$dr->wa_internal_requisition_id = $internal_requisition_row->id;
// $dr->grn_type_number = $series_module->type_number;
// $dr->grn_last_used_number = $series_module->last_number_used;
// $dr->transaction_type = $intr_smodule->description;
// $dr->transaction_no =  $internal_requisition_row->transfer_no;
// $dr->trans_date = $dateTime;
// $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
// $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
// $dr->account = $item->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
// $camount = $item->standard_cost * $item->quantity;
// $dr->amount = $camount;
// $dr->narrative = $internal_requisition_row->transfer_no . '/' . $item->getInventoryItemDetail->stock_id_code . '/' . $item->getInventoryItemDetail->title . '/' . $item->standard_cost . '@' . $item->quantity;
// $dr->save();





                }
                Session::flash('success', 'Transfered successfully.');
                return redirect()->route($this->model.'.index');
               }
               else
               {
                 Session::flash('warning', $qtyStatus);
                 return redirect()->back();
               }
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
        
            $row =  WaInventoryLocationTransfer::with(['getRelatedItem',
            'getRelatedItem.getInventoryItemDetail',
            'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail',
            'getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail',
            ])->whereSlug($slug)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.new_kra_signed_invoices.show',compact('title','model','breadcum','row')); 
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
            $row =  WaInventoryLocationTransfer::whereSlug($slug)->first();
            if($row)
            {
                $title = 'Edit '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                $model =$this->model;
                return view('admin.new_kra_signed_invoices.edit',compact('title','model','breadcum','row')); 
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
        try
        {
            $row =  WaInventoryLocationTransfer::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'transfer_no' => 'required|unique:wa_inventory_location_transfers,transfer_no,' . $row->id,
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
	            if($request->has('type')){
		            $itemcode  = array_filter($request->get('item_code'));
					$checkitem = WaInventoryItem::whereIn('stock_id_code',$itemcode)->count();
					if(count($itemcode)!=$checkitem){
						Session::flash('warning', 'Please enter a valid item code.');
						return redirect()->back();
					}		            
	            }
				
			   foreach($request->qty as $key=> $val){
				if($val > 0){

		            if($request->has('type')){
			            $itemcode  = $request->get('item_code');
						$key = WaInventoryItem::where('stock_id_code',$itemcode[$key])->first()->id;
			         }else{
				        $key = $key; 
			         }

	                $item = new WaInventoryLocationTransferItem ();
	                $item->wa_inventory_location_transfer_id = $row->id;
	                $item->wa_inventory_item_id = $key;
	                $item->quantity = $val;
	                $item->note = "";//$request->note;
	                $item_detail = WaInventoryItem::where('id',$key)->first();
	                $item->standard_cost = $item_detail->standard_cost;
	                $item->total_cost = $item_detail->standard_cost*$val;
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
                }
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.edit', $row->slug);
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try
        {
            WaInventoryLocationTransfer::whereSlug($slug)->delete();
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
      return json_encode(['stock_id_code'=>$rows->stock_id_code,'unit_of_measure'=>$rows->wa_unit_of_measure_id?$rows->wa_unit_of_measure_id:'','minimum_order_quantity'=>$rows->minimum_order_quantity]);

    }
    public function deletingItemRelation($purchase_no,$id)
    {
        try
        {
            WaInventoryLocationTransferItem::whereId($id)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }



  

    public function printToPdf($transfer_no)
    {
         $list =   WaInventoryLocationTransfer::where('transfer_no',$transfer_no)->with(['getRelatedItem.getInventoryItemDetail' => function ($query) {
					    $query->orderBy('stock_id_code','DESC');
					}])->first();

            if(!$list){
                Session::flash('warning','No Invoice Found');
                        return redirect()->back();
            }
            $list->print_count++;
            $list->save();
         $itemsdata =   WaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id',$list->id)->with(['getInventoryItemDetail' => function ($query) {
					    $query->orderBy('stock_id_code','DESC');
					}])->get();

		//echo "<pre>"; print_r($itemsdata); die;			
        //return view('admin.new_kra_signed_invoices.print',compact('title','list')); 
        $is_pdf=1; 
        $esd_details = WaEsdDetails::where('invoice_number',$transfer_no)->first();
        $pdf = PDF::loadView('admin.new_kra_signed_invoices.print', compact('list','itemsdata','esd_details','is_pdf'));
        return $pdf->download('transfer_'.date('Y_m_d_h_i_s').'.pdf');

      
    }



    public function editPurchaseItem($transfer_no,$id)
    {
        try
        {
           
                $row =  WaInventoryLocationTransfer::where('transfer_no',$transfer_no)
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
                    return view('admin.new_kra_signed_invoices.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
           
          
            $item =  WaInventoryLocationTransferItem::where('id',$id)->first();
          
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
            return redirect()->route($this->model.'.edit', $item->getTransferLocation->slug);
            
        }
        catch(\Exception $e)
        {
           
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function checkQuantity(Request $request)
    {
        try
        {
          $qtyOnHand = WaStockMove::where('wa_location_and_store_id',$request->from_strore_location_id)->where('stock_id_code',$request->item_id)->sum('qauntity');
		 // echo $qtyOnHand; die;
          $item = WaInventoryItem::select('stock_id_code','id')->where('stock_id_code',$request->item_id)->first();

          $item_id = $item->id;


          $myqty = $request->quantity;
          $qtyOnHand = $qtyOnHand;
          if($myqty <= $qtyOnHand )
          {
             return '1';
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



    public function getManualItemsList(Request $request)
    {
		if($request->has('type')){
			$type = $request->get('type');
		}else{
			$type = '';			
		}
        $view_data = view('admin.new_kra_signed_invoices.manual_entry',compact('type'));
        return $view_data;

    }

    

    public function return_show($slug)
    {        
        $user = getLoggeduserProfile();
        $permission =  $this->mypermissionsforAModule();
        if (!isset($permission['print-invoice-delivery-note___return']) && $permission != 'superadmin') {
            Session::flash('warning', 'Restricted : You Don\'t have enough permissions');
            return redirect()->back();
        }
        $row =  WaInventoryLocationTransfer::with(['getRelatedItem'=>function($w) use ($user,$permission){
            $w->where('quantity','>',DB::RAW('wa_inventory_location_transfer_items.return_quantity'));
        },
        'getRelatedItem.getInventoryItemDetail',
        'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail',
        'getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail',
        'getBranch','getDepartment'
        ])->whereHas('getRelatedItem',function($w) use ($user,$permission){
            $w->where('quantity','>',DB::RAW('wa_inventory_location_transfer_items.return_quantity'));
            // if($permission != 'superadmin'){
            //     $w->where('store_location_id',$user->wa_location_and_store_id);
            // }
        })->whereSlug($slug)->first();
        if($row)
        {
            $title = 'View '.$this->title;
            $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
            $model =$this->model;
            return view('admin.new_kra_signed_invoices.return_show',compact('title','model','breadcum','row')); 
        }
        else
        {
            Session::flash('warning', 'No Record found to Return');
            return redirect()->back();
        }        
    }
    public function return_list(Request $request)
    {        
        $user = getLoggeduserProfile();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission['print-invoice-delivery-note___return']) && $permission != 'superadmin') {
            Session::flash('warning', 'Restricted : You Don\'t have enough permissions');
            return redirect()->back();
        }
        $data =  WaInventoryLocationTransferItem::select(['*',DB::RAW('SUM(return_quantity) as rtn_qty'),
        DB::RAW('SUM(return_quantity * selling_price) as rtn_total')])
        ->with(['getInventoryItemDetail','getTransferLocation','returned_by','getTransferLocation.toStoreDetail'])
        ->where(function($w)use ($request,$permission,$user){
            if($request->input('start-date') && $request->input('end-date')){
                $w->whereBetween('return_date',[$request->input('start-date').' 00:00:00',$request->input('end-date')." 23:59:59"]);
            }
            // if($permission != 'superadmin'){
            //     $w->where('store_location_id',$user->wa_location_and_store_id);
            // }
        })
        ->where('is_return',1)->where('to_store_location_id',$request->salesman)->orderBy('return_date','DESC')->groupBy('return_grn')->paginate(100);

        $title = 'Return '.$this->title;
        $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
        $model = 'return-'.$this->model;
        return view('admin.new_kra_signed_invoices.return_list',compact('title','model','breadcum','data'));               
    }

   
    public function checkQuantity_return($locationid,$itemid,$qty)
    {
        try
        {
          $qtyOnHand = WaStockMove::where('wa_location_and_store_id',$locationid)->where('stock_id_code',$itemid)->sum('qauntity');
          if($qty > $qtyOnHand )
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
