<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaTyrePurchaseOrder;
use App\Model\WaTyrePurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\TyreInventory;
use App\Model\WaGrn;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\TaxManager;
use App\Model\WaLocationAndStore;
use App\Model\WaPoiStockSerialMoves;
use App\Model\WaPoiStockSerialMovesHistory;

use App\Model\WaSuppTran;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ReceiveTyrePurchasedOrderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'receive-tyre-purchase-order';
        $this->title = 'Receive Tyre Purchase Order';
        $this->pmodule = 'receive-tyre-purchase-order';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaTyrePurchaseOrder::where('status', 'APPROVED');
            if ($permission != 'superadmin') {
                $lists = $lists->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
            }

            $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.receivetyrepurchaseorders.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create()
    {
    }


    public function store(Request $request)
    {
    }


    public function show($slug)
    {

        $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            $pmodule = $this->pmodule;
            $permission =  $this->mypermissionsforAModule();

            return view('admin.receivetyrepurchaseorders.show', compact('title', 'model', 'breadcum', 'row', 'permission', 'pmodule'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function print(Request $request)
    {
    }

    public function exportToPdf($slug)
    {
    }






    public function edit($slug)
    {
    }



    



    public function update(Request $request, $slug)
    {

        


        try {
            //echo "<pre>"; print_r($request->all()); die;
            $purchaseOrder =  WaTyrePurchaseOrder::whereSlug($slug)->first();

            $purchaseOrder->wa_location_and_store_id = $request->wa_location_and_store_id;
            $purchaseOrder->save();

            $series_module = WaNumerSeriesCode::where('module', 'GRN')->first();
            $SUPPLIER_INVOICE_NO_series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_INVOICE_NO')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $grn_number = getCodeWithNumberSeries('GRN');
            $dateTime = $request->supplier_invoice_date;//date('Y-m-d H:i:s');
            $vat_amount_arr = [];
            $cr_amount = [];

            foreach ($request->purchase_order_ids as $purchase_order_item_id) {
                $order_pirce = 'order_price_' . $purchase_order_item_id;
                $supplier_discount = 'supplier_discount_' . $purchase_order_item_id;
                //storing grn enteries start


                $purchaseOrderItem =  WaTyrePurchaseOrderItem::where('id', $purchase_order_item_id)->first();
				//echo "<pre>"; print_r($purchaseOrderItem); die;
                if ($request->wa_location_and_store_id) {
                    $checkcostcentre = WaLocationAndStore::where('id', $request->wa_location_and_store_id)->first()->is_cost_centre;

                    if ($checkcostcentre == "1") {
                        if($purchaseOrderItem->item_type == 'Stock'){
                            $accountno = @$purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                        }else
                        {
                            $accountno = @$purchaseOrderItem->getNonStockItemDetail->gl_code->account_code;
                        }
                    } else {
                        if($purchaseOrderItem->item_type == 'Stock'){
                            $accountno =  @$purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                        }else
                        {
                            $accountno = @$purchaseOrderItem->getNonStockItemDetail->gl_code->account_code;
                        }
                    }
                }
                
                $grn = new WaGrn();
                $grn->wa_purchase_order_item_id = $purchase_order_item_id;
                $grn->wa_purchase_order_id = $purchaseOrder->id;
                $grn->wa_supplier_id = $purchaseOrder->wa_supplier_id;
                $grn->grn_number =  $grn_number;
                $grn->item_code = $purchaseOrderItem->item_no;
                $grn->delivery_date = $dateTime;//date('Y-m-d');
                $grn->item_description = $purchaseOrderItem->getInventoryItemDetail->title;//change
                $delivered_quantity = 'delivered_quantity_' . $purchase_order_item_id;
                //~ $grn->qty_received = $purchaseOrderItem->quantity;
                if($purchaseOrderItem->controlled_items && count($purchaseOrderItem->controlled_items)>0)
                {
                    $grn->qty_received = count($purchaseOrderItem->controlled_items);
                }else
                {
                    $grn->qty_received = $request->$delivered_quantity;
                }
                $grn->qty_invoiced = $purchaseOrderItem->supplier_quantity;
                $grn->standart_cost_unit = $purchaseOrderItem->standard_cost;

                $invoice_calculation = ['order_price' => $request->$order_pirce, 'discount_percent' => 0, 'vat_rate' => $purchaseOrderItem->vat_rate, 'qty' => $grn->qty_received, 'unit' => $purchaseOrderItem->getSupplierUomDetail->title];
                if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                    $invoice_calculation['discount_percent'] = $request->$supplier_discount;
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
                $stockMove->wa_location_and_store_id = $purchaseOrder->wa_location_and_store_id;
                $stockMove->stock_id_code = $purchaseOrderItem->item_no;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->document_no = $grn_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->item_type = $purchaseOrderItem->item_type ;
                $stockMove->wa_inventory_item_id = $purchaseOrderItem->wa_inventory_item_id;

                $price = $request->$order_pirce;

                if($purchaseOrderItem->item_type == 'Stock'){
                    $wainventoryitem = TyreInventory::where('stock_id_code',$purchaseOrderItem->item_no)->first();
                    if($wainventoryitem->standard_cost == $price){
                        $price = $price;
                    }else{
                        $wainventoryitem->prev_standard_cost	= $wainventoryitem->standard_cost;
                        $wainventoryitem->standard_cost	= $price;
                        $wainventoryitem->cost_update_time	= date('d-m-Y H:i:s');
                        $wainventoryitem->save();
                    }
                }


                if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                    $discount_percent = $request->$supplier_discount;
                    $discount_amount = ($discount_percent * $price) / 100;
                    $price = $price - $discount_amount;
                    $stockMove->discount_percent = $discount_percent;
                }
                $stockMove->price = $price;
                $stockMove->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $stockMove->refrence = $purchaseOrder->getSupplier->supplier_code . '/' . $purchaseOrder->getSupplier->name . '/' . $purchaseOrder->purchase_no;
                $stockMove->qauntity =  $request->$delivered_quantity * $purchaseOrderItem->unit_conversion;
                //$stockMove->qauntity = $request->$delivered_quantity;;
                $stockMove->standard_cost = $purchaseOrderItem->standard_cost;
                $stockMove->save();
                //move to stock moves end
                if($purchaseOrderItem->controlled_items && count($purchaseOrderItem->controlled_items)>0)
                {
                $stockMove->qauntity =  count($purchaseOrderItem->controlled_items) * $purchaseOrderItem->unit_conversion;
                $stockMove->save();
                    $loggedUserData=getLoggeduserProfile();
                    foreach($purchaseOrderItem->controlled_items as $controlled_item)
                    {
                        
                        $findSerial=new WaPoiStockSerialMoves();
                        $findSerial->wa_stock_move_id=$stockMove->id;
                        $findSerial->wa_inventory_item_id=$stockMove->wa_inventory_item_id;
                        $findSerial->serial_no=$controlled_item->serial_no;
                        $findSerial->purchase_price=$controlled_item->purchase_price;
                        $findSerial->purchase_weight=$controlled_item->purchase_weight;
                        $findSerial->value=$controlled_item->value;
                        $findSerial->loc_code=$controlled_item->loc_code;
                        $findSerial->expiration_date=$controlled_item->expiration_date;
                        $findSerial->transtype='New';
                        $findSerial->status=$request->tyre_status;
                        $findSerial->user_id=$loggedUserData->id;
                        $findSerial->created_at=$controlled_item->created_at;
                        $findSerial->save();

                       


                        $pssm_history=new WaPoiStockSerialMovesHistory();
                        $pssm_history->vehicle_id=$request->vehicle;
                        $pssm_history->wa_poi_stock_serial_moves_id=$findSerial->id;
                        $pssm_history->serial_no=$findSerial->serial_no;
                        $pssm_history->wa_stock_move_id=$findSerial->wa_stock_move_id;
                        $pssm_history->user_id=$loggedUserData->id;
                        $pssm_history->status=$request->tyre_status;
                        $pssm_history->save();
                    }
                }

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
                $dr->amount = $price * ($request->$delivered_quantity);
                $dr->narrative = $purchaseOrder->purchase_no . '/' . $purchaseOrder->getSupplier->supplier_code . '/' . $purchaseOrderItem->item_no . '/' . $purchaseOrderItem->getInventoryItemDetail->title . '/' . $purchaseOrderItem->quantity . '@' . $price;
                $dr->save();
                //managae dr accounts end/

                $cr_amount[] = $price * ($request->$delivered_quantity);
                if ($purchaseOrderItem->vat_rate && $purchaseOrderItem->vat_rate > 0) {
                    $total_price = $price * ($request->$delivered_quantity);
                    $vat_amount_arr[] = ($purchaseOrderItem->vat_rate * $total_price) / 100;
                    $cr_amount[] = ($purchaseOrderItem->vat_rate * $total_price) / 100;
                }

                //get double entry for if location is a cost center start

                if ($purchaseOrder->getStoreLocation->is_cost_centre == '1') {

                    //   $drIssueGl =  new WaGlTran();
                    //    $drIssueGl->grn_type_number = $series_module->type_number;
                    //     $drIssueGl->grn_last_used_number = $series_module->last_number_used;
                    //     $drIssueGl->transaction_type = $series_module->description;
                    //     $drIssueGl->transaction_no = $grn_number;
                    //     $drIssueGl->trans_date = $dateTime;
                    //     $drIssueGl->wa_purchase_order_id = $purchaseOrder->id;
                    //     $drIssueGl->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                    //     $drIssueGl->supplier_account_number = null;
                    //     $drIssueGl->account = $purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                    //     $drIssueGl->amount = $price*$request->$delivered_quantity;
                    //     $drIssueGl->narrative = $purchaseOrder->purchase_no.'/'.$purchaseOrder->getSupplier->supplier_code.'/'.$purchaseOrderItem->item_no.'/'.$purchaseOrderItem->getInventoryItemDetail->title.'/'.$request->$delivered_quantity.'@'.$price;
                    //     $drIssueGl->save();

                    // $crStockGl =  new WaGlTran();
                    // $crStockGl->grn_type_number = $series_module->type_number;
                    // $crStockGl->grn_last_used_number = $series_module->last_number_used;
                    // $crStockGl->transaction_type = $series_module->description;
                    // $crStockGl->transaction_no = $grn_number;
                    // $crStockGl->trans_date = $dateTime;
                    // $crStockGl->wa_purchase_order_id = $purchaseOrder->id;
                    // $crStockGl->period_number = $WaAccountingPeriod?$WaAccountingPeriod->period_no:null;
                    // $crStockGl->supplier_account_number = null;
                    // $crStockGl->account = $purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                    // $crStockGl->amount = '-'.($price*$request->$delivered_quantity);
                    // $crStockGl->narrative = $purchaseOrder->purchase_no.'/'.$purchaseOrder->getSupplier->supplier_code.'/'.$purchaseOrderItem->item_no.'/'.$purchaseOrderItem->getInventoryItemDetail->title.'/'.$request->$delivered_quantity.'@'.$price;
                    // $crStockGl->save();

                }
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
                $vat->amount = array_sum($vat_amount_arr);
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
            $cr->amount = '-' . round(array_sum($cr_amount));
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
					$crdrAmnt = '+'.$roundOff;
				}else{
					$roundOff = '-'.round($roundOff,2);
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
            $suppTran->total_amount_inc_vat = round(array_sum($cr_amount));
            $suppTran->vat_amount =  array_sum($vat_amount_arr);
            $suppTran->wa_purchase_order_id = $purchaseOrder->id;
            $suppTran->save();

            //  supp trans entry end    
            $purchaseOrder->status = 'COMPLETED';
            $purchaseOrder->save();
            updateUniqueNumberSeries('GRN', $grn_number);
            Session::flash('success', 'GRN Processed Successfully');
            return redirect()->route($this->model . '.index')->withInput();
        } catch (\Exception $e) {
            dd($e);
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
    }

    public function EnterSerialNo(Request $request,$id){


        try {
            $data['item'] = WaTyrePurchaseOrderItem::with('getTyrePurchaseOrder')->findOrFail($id);          
           

            if($data['item']->getInventoryItemDetail->serialised != 'Yes')  
            {
                throw new Exception("Error Processing Request", 1);
            }
            $data['itemSerials'] = \App\Model\WaTyrePurchaseOrderItemControlled::where('wa_tyre_purchase_order_item_id',$id)->get();          
            $data['pmodule'] = $this->pmodule;
            $data['title'] = $this->title;
            $data['model'] = $this->model;            
            // dd($data);
            return view('admin.receivetyrepurchaseorders.EnterSerialNo')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->back();
        }
    }
    public function saveEnterSerialNo(Request $request)
    {


        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $inputArray = [               
            'id' => 'required|exists:wa_tyre_purchase_order_items,id',
            'serial.*'=>'nullable|string|min:1|max:100|unique:wa_tyre_purchase_order_item_controlleds,serial_no',     
            'price.*'=>'nullable',
            'weight.*'=>'nullable'       
        ];
        $uniqueSerial = [];
        if(!isset($request->serial) || count($request->serial) == 0 || strlen(implode($request->serial)) == 0)
        {
            return response()->json([
                'result' => -1,
                'message' => 'No Data Received'
            ]);
        }
        foreach($request->serial as $key => $val)
        {
            if($val != ''){
                $inputArray['price.'.$key] = 'required|numeric|min:1';
                $inputArray['weight.'.$key] = 'required|numeric|min:1';
                if(!in_array($val,$uniqueSerial))
                {
                    $uniqueSerial[] = $val;
                }else
                {
                    return response()->json([
                        'result' => 0,
                        'errors' => ['serial.'.$key => ['This serial no. is already in use']]
                    ]);
                }
            }
        }
        $validator = Validator::make($request->all(), $inputArray,[],['serial.*'=>'Serial No','price.*'=>'Purchase Price','weight.*'=>'Purchase Weight']);  
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }

        $save = DB::transaction(function () use ($request){      
            $item = WaTyrePurchaseOrderItem::findOrFail($request->id);
            foreach($request->serial as $key => $val)
            {
                if($val != '' && $request->price[$key] != '' && $request->weight[$key] != ''){
                    $addrec = new \App\Model\WaTyrePurchaseOrderItemControlled();

                    $addrec->wa_tyre_purchase_order_item_id=$request->id;    
                    $addrec->serial_no=$val;    
                    $addrec->purchase_price=$request->price[$key];    
                    $addrec->purchase_weight=$request->weight[$key];    
                    $addrec->status='New';    
                    $addrec->value=$request->price[$key]*$request->weight[$key];    
                    $addrec->loc_code=($item->getTyrePurchaseOrder ? ($item->getTyrePurchaseOrder->getStoreLocation ? $item->getTyrePurchaseOrder->getStoreLocation->location_name : 'Store') : 'Store');
                    $addrec->save();    

                    // \App\Model\WaTyrePurchaseOrderItemControlled::create([
                    //     'wa_tyre_purchase_order_item_id'=>$request->id,
                    //     'serial_no'=>$val,
                    //     'purchase_price'=>$request->price[$key],
                    //     'purchase_weight'=>$request->weight[$key],
                    //     'status'=>'New',
                    //     'value'=>$request->price[$key]*$request->weight[$key],
                    //     'loc_code'=>($item->getTyrePurchaseOrder ? ($item->getTyrePurchaseOrder->getStoreLocation ? $item->getTyrePurchaseOrder->getStoreLocation->location_name : 'Store') : 'Store')
                    // ]);
                }
            }
            return true;
        });
        if($save)
        {   
            return response()->json([
                'result' => 1,
                'location'=>route('tyre-receive.EnterSerialNo',$request->id),
                'message' => 'Serial Nos added successfully',
            ]);
        }
      
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function deleteEnterSerialNo($id,$controlled_id)
    {
        $save = \App\Model\WaTyrePurchaseOrderItemControlled::where('wa_tyre_purchase_order_item_id',$id)->find($controlled_id);
        if($save)
        {   
            $save->delete();
            return response()->json([
                'result' => 1,
                'location'=>route('tyre-receive.EnterSerialNo',$id),
                'message' => 'Serial Nos deleted successfully',
            ]);
        }
      
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function updateEnterSerialNo($id,Request $request)
    {
        $item = WaTyrePurchaseOrderItem::findOrFail($id);       
        


        $save = \App\Model\WaTyrePurchaseOrderItemControlled::where('wa_tyre_purchase_order_item_id',$id)->where('id',$request->controlled_id)->update([
            'status'=>'Approved'
        ]);
        if($save)
        {   
            return response()->json([
                'result' => 1,
                'location'=>route($this->model.'.show', $item->getTyrePurchaseOrder->slug),
                'message' => 'Serial Nos Added to items successfully',
            ]);
        }
      
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function downloadSerials()
    {
        try {
            $array = [];            
            $array[] = [
                    'serial_no'=>'',
                    'purchase_price'=>'',
                    'purchase_weight'=>'',
                ];
            
            return \Excel::create('new-serials-'.date('Y-m-d-H-i-s'), function($excel) use ($array) {
                $excel->sheet('mySheet', function($sheet) use ($array)
                {
                    $sheet->setWidth(array(
                        'A'     =>  50,
                        'B'     =>  50,
                        'C'     =>  50,
                    ));
                    $sheet->fromArray($array);
                });
            })->export('xls');            
        } catch (\Exception $th) {
            $request->session()->flash('danger','Something went wrong');
            return redirect()->route('maintain-items.index');
        }
    }
    public function importSerials(Request $request)
    {
        $inputArray = [               
            'import_serials' => 'required',   
            'id' => 'required|exists:wa_purchase_order_items,id',
        ];
        $validator = Validator::make($request->all(), $inputArray,[],
        ['import_serials'=>'Serials']);  
        if ($validator->fails()) 
        {
            $request->session()->flash('danger','Something went wrong');
            return back();
        }
        if($request->hasFile('import_serials')){
			\Excel::load($request->file('import_serials')->getRealPath(), function ($reader) use ($request) {
                if(count($reader->toArray())>0){
                    $item = WaTyrePurchaseOrderItem::findOrFail($request->id);
                    $lococde = ($item->getTyrePurchaseOrder ? ($item->getTyrePurchaseOrder->getStoreLocation ? $item->getTyrePurchaseOrder->getStoreLocation->location_name : 'Store') : 'Store');
                    $arraySerials = [];
                    foreach($reader->toArray() as $serials)
                    {
                        $arraySerials[] = $serials['serial_no'];
                    }
                    $getTotal = \App\Model\WaTyrePurchaseOrderItemControlled::whereIn('serial_no',$arraySerials)->get();
                    if(count($getTotal) > 0){
                        $request->session()->flash('danger','Invalid serial nos');
                    }else
                    {
                        foreach($reader->toArray() as $newSerials)
                            {
                                if($newSerials['serial_no'] != '' && $newSerials['purchase_price'] != '' && $newSerials['purchase_weight']!= ''){
                                    $new = new \App\Model\WaTyrePurchaseOrderItemControlled;
                                    $new->wa_purchase_order_item_id = $item->id;
                                    $new->serial_no = $newSerials['serial_no'];
                                    $new->purchase_price = (float)$newSerials['purchase_price'];
                                    $new->purchase_weight = (float)$newSerials['purchase_weight'];
                                    $new->value = ($new->purchase_price)*($new->purchase_weight);
                                    $new->status = 'New';
                                    $new->loc_code = $lococde;
                                    // $new->expiration_date = $check->expiration_date;
                                    $new->save();
                                }
                            }
                        $request->session()->flash('success','Serials added successfully');
                    }
                }
                else{
                    $request->session()->flash('danger','Something went wrong');
                }
            });
		}
		return back();
    }
}
