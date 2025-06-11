<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaGrn;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaLocationAndStore;
use App\Model\WaGlTran;
use App\Model\TaxManager;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\WaSuppTran;

class StockReturnController extends Controller
{
    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'stock-return';
        $this->title = 'Stock Return';
        $this->pmodule = 'stock-return';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaPurchaseOrder::select([
                'wa_purchase_orders.*',
                'wa_grns.grn_number',
                'wa_grns.delivery_date',
                'wa_grns.invoice_info',
                'wa_grns.is_printed as grn_is_printed',
            ])->with(['getRelatedGrn','getRelatedGlTran','getRelatedStockMoves.getRelatedUser','getStoreLocation','getSupplier','getSuppTran','getDepartment'])
            ->where('wa_purchase_orders.status','COMPLETED')->where('wa_grns.return_status','!=','Returned');
            if ($permission != 'superadmin') {
                $lists = $lists->where('wa_purchase_orders.restaurant_id', getLoggeduserProfile()->restaurant_id);
            }
            
             $lists = $lists->join('wa_grns',function($e){
                $e->on('wa_grns.wa_purchase_order_id','wa_purchase_orders.id');
             })->orderBy('wa_grns.id', 'desc')->groupBy('wa_grns.grn_number')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.stockreturn.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }
    public function returned_index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $model = 'stock-return_returned';
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaPurchaseOrder::where('status','COMPLETED')->where('return_status','Returned');
            if ($permission != 'superadmin') {
                $lists = $lists->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
            }
            
             $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('stock-return.index'),'Listing'=>''];
            return view('admin.stockreturn.returned_index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }
    public function create()
    {
        // ALTER TABLE `wa_purchase_orders` ADD `return_status` ENUM('Returned','Not Returned') NULL DEFAULT 'Not Returned' AFTER `status`;
       
        
    }


    public function store(Request $request)
    {
      
      
       
    }

   
    public function show(Request $request, $slug)
    {
        
            
        // $row =  WaPurchaseOrder::where('status','COMPLETED')->whereSlug($slug)->first();
        $row = WaPurchaseOrder::select([
            'wa_purchase_orders.*',
            'wa_grns.grn_number',
            'wa_grns.delivery_date',
            'wa_grns.invoice_info',
            'wa_grns.return_status',
        ])->with(['getSupplier','getSuppTran','getRelatedGrn','getRelatedGrn.getRelatedInventoryItem',
        'getRelatedGrn.getRelatedInventoryItem.getInventoryItemDetail'
        ])->join('wa_grns',function($e){
            $e->on('wa_grns.wa_purchase_order_id','wa_purchase_orders.id');
         })->where('wa_grns.grn_number',$request->grn)->where('wa_purchase_orders.slug',$slug)->groupBy('wa_grns.grn_number')->first();
        $grn = WaGrn::with(['getRelatedInventoryItem'])->where('grn_number',$request->grn)->where('wa_purchase_order_id',$row->id)->get();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            if($row->return_status == 'Returned')
            {
                $model = 'stock-return_returned';
            }
            $pmodule = $this->pmodule;
            $permission =  $this->mypermissionsforAModule();
            return view('admin.stockreturn.show', compact('title', 'model', 'breadcum', 'row', 'permission', 'pmodule','grn'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    






    public function edit($slug)
    {
        
    }


    public function update(Request $request, $slug)
    {
        try {
            $permission =  $this->mypermissionsforAModule();
            if(!isset($permission[$this->pmodule.'___process']) && $permission != 'superadmin')
            {
                throw new \Exception("You dont have permission for this", 1);       
            }
            $purchaseOrder =  WaPurchaseOrder::whereSlug($slug)->where('return_status','!=','Returned')->first();
            if( !$purchaseOrder )
            {
                throw new \Exception("Error Processing Request", 1);                
            }
            $purchaseOrder->return_status = 'Returned';
            // $purchaseOrder->save();

            $series_module = WaNumerSeriesCode::where('module', 'RETURN')->first();
            $SUPPLIER_INVOICE_NO_series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_INVOICE_NO')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $grn_number = getCodeWithNumberSeries('RETURN');
            $dateTime = $request->supplier_invoice_date;//date('Y-m-d H:i:s');
            $vat_amount_arr = [];
            $cr_amount = [];
            foreach ($request->purchase_order_ids as $purchase_order_item_id) {
                $order_pirce = 'order_price_' . $purchase_order_item_id;
                $supplier_discount = 'supplier_discount_' . $purchase_order_item_id;
                //storing grn enteries start


                $purchaseOrderItem =  WaPurchaseOrderItem::with([
                    'getInventoryItemDetail',
                    'getInventoryItemDetail.getAllFromStockMoves',
                    'getInventoryItemDetail.getInventoryCategoryDetail',
                    'getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
                    'getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail',
                    'getSupplierUomDetail'
                ])->where('id', $purchase_order_item_id)->first();
                $stock_qoh = @$purchaseOrderItem->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$purchaseOrderItem->store_location_id)->sum('qauntity') ?? 0;

				//echo "<pre>"; print_r($purchaseOrderItem); die;
                $accountno = '';
                if ($purchaseOrderItem->store_location_id) {
                    $checkcostcentre = WaLocationAndStore::where('id', $purchaseOrderItem->store_location_id)->first()->is_cost_centre;

                    if ($checkcostcentre == "1") {
                        $accountno = @$purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                        // if($purchaseOrderItem->item_type == 'Stock'){
                        // }else
                        // {
                        //     $accountno = @$purchaseOrderItem->getNonStockItemDetail->gl_code->account_code;
                        // }
                    } else {
                        $accountno =  @$purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                        // if($purchaseOrderItem->item_type == 'Stock'){
                        // }else
                        // {
                        //     $accountno = @$purchaseOrderItem->getNonStockItemDetail->gl_code->account_code;
                        // }
                    }
                }
                $delivered_quantity = 'delivered_quantity_' . $purchase_order_item_id;
                
                $old_grn = WaGrn::where('grn_number',$request->grn)->where('wa_purchase_order_item_id',$purchase_order_item_id)->first();
                if($purchaseOrderItem->controlled_items && count($purchaseOrderItem->controlled_items)>0)
                {
                    $old_grn->qty_received = $old_grn->qty_received - count($purchaseOrderItem->controlled_items);
                }else
                {
                    $old_grn->qty_received = $old_grn->qty_received - $request->$delivered_quantity;
                }
                $invoice_calculation = ['order_price' => '-'.$request->$order_pirce, 'discount_percent' => 0, 'vat_rate' => $purchaseOrderItem->vat_rate, 'qty' => $old_grn->qty_received, 'unit' => @$purchaseOrderItem->getSupplierUomDetail->title];
                if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                    $invoice_calculation['discount_percent'] = '-'.$request->$supplier_discount;
                }
                $old_grn->invoice_info = json_encode($invoice_calculation);
                $old_grn->save();
                $grn = new WaGrn();
                $grn->wa_purchase_order_item_id = $purchase_order_item_id;
                $grn->wa_purchase_order_id = $purchaseOrder->id;
                $grn->wa_supplier_id = $purchaseOrder->wa_supplier_id;
                $grn->grn_number =  $grn_number;
                $grn->item_code = $purchaseOrderItem->item_no;
                $grn->return_status = 'Returned';
                $grn->delivery_date = $dateTime;//date('Y-m-d');
                $grn->item_description = @$purchaseOrderItem->getInventoryItemDetail->title;//change
                //~ $grn->qty_received = $purchaseOrderItem->quantity;
                if($purchaseOrderItem->controlled_items && count($purchaseOrderItem->controlled_items)>0)
                {
                    $grn->qty_received = '-'.count($purchaseOrderItem->controlled_items);
                }else
                {
                    $grn->qty_received = '-'.$request->$delivered_quantity;
                }
                $grn->qty_invoiced = '-'.$purchaseOrderItem->supplier_quantity;
                $grn->standart_cost_unit = '-'.$purchaseOrderItem->standard_cost;

                $invoice_calculation = ['order_price' => '-'.$request->$order_pirce, 'discount_percent' => 0, 'vat_rate' => $purchaseOrderItem->vat_rate, 'qty' => $grn->qty_received, 'unit' => @$purchaseOrderItem->getSupplierUomDetail->title];
                if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                    $invoice_calculation['discount_percent'] = '-'.$request->$supplier_discount;
                }
                $grn->invoice_info = json_encode($invoice_calculation);
                $grn->save();

                // echo "<pre>"; print_r($grn); die;
                //storing grn enteries end

                //move to stock moves start
                $stockMove = new WaStockMove();
                $stockMove->user_id = getLoggeduserProfile()->id;
                $stockMove->wa_purchase_order_id = $purchaseOrder->id;
                $stockMove->restaurant_id = $purchaseOrder->restaurant_id;
                $stockMove->wa_location_and_store_id = $purchaseOrderItem->store_location_id;
                $stockMove->stock_id_code = $purchaseOrderItem->item_no;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->document_no = $grn_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                // $stockMove->item_type = $series_module->description;
                $stockMove->wa_inventory_item_id = $purchaseOrderItem->wa_inventory_item_id;

                $price = $request->$order_pirce;

                // if($purchaseOrderItem->item_type == 'Stock'){
                //     $wainventoryitem = WaInventoryItem::where('stock_id_code',$purchaseOrderItem->item_no)->first();
                //     if($wainventoryitem->standard_cost == $price){
                //         $price = $price;
                //     }else{
                //         $wainventoryitem->prev_standard_cost	= $wainventoryitem->standard_cost;
                //         $wainventoryitem->standard_cost	= $price;
                //         $wainventoryitem->cost_update_time	= date('d-m-Y H:i:s');
                //         $wainventoryitem->save();
                //     }
                // }


                if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                    $discount_percent = $request->$supplier_discount;
                    $discount_amount = ($discount_percent * $price) / 100;
                    $price = $price - $discount_amount;
                    $stockMove->discount_percent = $discount_percent;
                }
                $stockMove->price = '-'.$price;
                $stockMove->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $stockMove->refrence = $purchaseOrder->getSupplier->supplier_code . '/' . $purchaseOrder->getSupplier->name . '/' . $purchaseOrder->purchase_no;
                $stockMove->qauntity =  '-'.($request->$delivered_quantity * $purchaseOrderItem->unit_conversion);
                $stock_qoh -= ($request->$delivered_quantity * $purchaseOrderItem->unit_conversion);
                $stockMove->new_qoh = $stock_qoh;
                $stockMove->standard_cost = '-'.$purchaseOrderItem->standard_cost;
                $stockMove->save();
                //move to stock moves end


                //managae dr accounts start/

                $dr =  new WaGlTran();
                $dr->grn_type_number = $series_module->type_number;
                $dr->grn_last_used_number = $series_module->last_number_used;


                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $grn_number;
                $dr->trans_date = $dateTime;
                $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;

                $dr->wa_purchase_order_id = $purchaseOrder->id;
                $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $dr->supplier_account_number = null;
                $dr->account = $accountno;
                //$dr->amount = $price*$request->$delivered_quantity;
                $dr->amount = '-'.($price * ($request->$delivered_quantity));
                $dr->narrative = $purchaseOrder->purchase_no . '/' . $purchaseOrder->getSupplier->supplier_code . '/' . $purchaseOrderItem->item_no . '/' . @$purchaseOrderItem->getInventoryItemDetail->title . '/' . $purchaseOrderItem->quantity . '@' . $price;
                $dr->save();
                //managae dr accounts end/

                $cr_amount[] = ($price * ($request->$delivered_quantity));
                if ($purchaseOrderItem->vat_rate && $purchaseOrderItem->vat_rate > 0) {
                    $total_price = $price * ($request->$delivered_quantity);
                    $vat_amount_arr[] = (($purchaseOrderItem->vat_rate * $total_price) / 100);
                    $cr_amount[] = (($purchaseOrderItem->vat_rate * $total_price) / 100);
                }

                //get double entry for if location is a cost center start

                // if ($purchaseOrder->getStoreLocation->is_cost_centre == '1') {

                //       $drIssueGl =  new WaGlTran();
                //        $drIssueGl->grn_type_number = $series_module->type_number;
                //         $drIssueGl->grn_last_used_number = $series_module->last_number_used;
                //         $drIssueGl->transaction_type = $series_module->description;
                //         $drIssueGl->transaction_no = $grn_number;
                //         $drIssueGl->trans_date = $dateTime;
                //         $drIssueGl->wa_purchase_order_id = $purchaseOrder->id;
                //         $drIssueGl->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                //         $drIssueGl->supplier_account_number = null;
                //         $drIssueGl->account = $purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                //         $drIssueGl->amount = $price*$request->$delivered_quantity;
                //         $drIssueGl->narrative = $purchaseOrder->purchase_no.'/'.$purchaseOrder->getSupplier->supplier_code.'/'.$purchaseOrderItem->item_no.'/'.$purchaseOrderItem->getInventoryItemDetail->title.'/'.$request->$delivered_quantity.'@'.$price;
                //         $drIssueGl->save();

                //     $crStockGl =  new WaGlTran();
                //     $crStockGl->grn_type_number = $series_module->type_number;
                //     $crStockGl->grn_last_used_number = $series_module->last_number_used;
                //     $crStockGl->transaction_type = $series_module->description;
                //     $crStockGl->transaction_no = $grn_number;
                //     $crStockGl->trans_date = $dateTime;
                //     $crStockGl->wa_purchase_order_id = $purchaseOrder->id;
                //     $crStockGl->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                //     $crStockGl->supplier_account_number = null;
                //     $crStockGl->account = $purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                //     $crStockGl->amount = '-'.($price*$request->$delivered_quantity);
                //     $crStockGl->narrative = $purchaseOrder->purchase_no.'/'.$purchaseOrder->getSupplier->supplier_code.'/'.$purchaseOrderItem->item_no.'/'.$purchaseOrderItem->getInventoryItemDetail->title.'/'.$request->$delivered_quantity.'@'.$price;
                //     $crStockGl->save();

                // }
                //get double entry for if location is a cost center end
            }
            //vat entry start
            $taxVat = TaxManager::where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && count($vat_amount_arr) > 0) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series_module->type_number;
                $vat->transaction_type = $series_module->description;
                $vat->transaction_no = $grn_number;
                $vat->grn_last_used_number = $series_module->last_number_used;
                $vat->trans_date = $dateTime;
                $vat->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->supplier_account_number = null;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = '-'.(array_sum($vat_amount_arr));
                $vat->narrative = null;
                $vat->wa_purchase_order_id = $purchaseOrder->id;
                $vat->save();
            }
            //vat entry end 
            //cr entry start
            $cr = new WaGlTran();
            $cr->grn_type_number = $series_module->type_number;
            $cr->transaction_type = $series_module->description;
            $cr->transaction_no = $grn_number;
            $cr->grn_last_used_number = $series_module->last_number_used;
            $cr->trans_date = $dateTime;
            $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->supplier_account_number = null;
            $cr->account =  @$purchaseOrder->getBranch->getAssociateCompany->creditorControlGlAccount->account_code;
            $cr->amount = round(array_sum($cr_amount));
            $cr->narrative = null;
            $cr->wa_purchase_order_id = $purchaseOrder->id;
            $cr->save();
            //cr enter end

            //supp trans entry start

            $suppTran = new WaSuppTran();
            $suppTran->grn_type_number = $SUPPLIER_INVOICE_NO_series_module->type_number;
            $suppTran->supplier_no = $purchaseOrder->getSupplier->supplier_code;
            $suppTran->suppreference = $request->supplier_invoice_number;
            $suppTran->trans_date = $dateTime;//date('Y-m-d');
            $suppTran->document_no = $grn_number;

            $due_date_number = '1';
            //dd($purchaseOrder->getSupplier);
            if ($purchaseOrder->getSupplier->getPaymentTerm && $purchaseOrder->getSupplier->getPaymentTerm->due_after_given_month == '1') {

                $due_date_number =  $purchaseOrder->getSupplier->getPaymentTerm->days_in_following_months;
                // dd($due_date_number);
            }
            $suppTran->due_date = date('Y-m-d', strtotime($suppTran->trans_date . ' + ' . $due_date_number . ' days'));

            $suppTran->settled = '0';
            $suppTran->rate = '1';

			$total_cost_with_vat = array_sum($cr_amount);
			$roundOff = fmod($total_cost_with_vat, 1); //0.25
			if($roundOff!=0){
				if($roundOff > '0.50'){
					$roundOff = round((1-$roundOff),2);
					$crdrAmnt = '-'.$roundOff;
				}else{
					$roundOff = '+'.round($roundOff,2);
					$crdrAmnt = $roundOff;
				}
	            $cr = new WaGlTran();
	            $cr->grn_type_number = $series_module->type_number;
	            $cr->transaction_type = $series_module->description;
	            $cr->transaction_no = $grn_number;
	            $cr->grn_last_used_number = $series_module->last_number_used;
	            $cr->trans_date = $dateTime;
	            $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
	            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
	            $cr->supplier_account_number = null;
	            $cr->account =  "202021";	            
	            $cr->amount = $crdrAmnt;
	            $cr->narrative = null;
	            $cr->wa_purchase_order_id = $purchaseOrder->id;
	            $cr->save();
	            //cr enter end
			}
			
            $suppTran->round_off		    = $roundOff;            
            $suppTran->total_amount_inc_vat = '-'.(round(array_sum($cr_amount)));
            $suppTran->vat_amount =  '-'.(array_sum($vat_amount_arr));
            $suppTran->wa_purchase_order_id = $purchaseOrder->id;
            $suppTran->save();

            //  supp trans entry end    
            $purchaseOrder->status = 'COMPLETED';
            $purchaseOrder->save();
            updateUniqueNumberSeries('RETURN', $grn_number);
            Session::flash('success', 'RETURN Processed Successfully');
            return redirect()->route($this->model . '.index')->withInput();
        } catch (\Exception $e) {
            // dd($e);
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
       
    }

    
}
