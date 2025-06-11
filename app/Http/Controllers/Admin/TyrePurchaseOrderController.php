<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaTyrePurchaseOrder;
use App\Model\WaTyrePurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\TyreInventory;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class TyrePurchaseOrderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct(){
        $this->model = 'tyre-purchase-orders';
        $this->title = 'Purchase Orders';
        $this->pmodule = 'tyre-purchase-orders';
    } 

    public function viewLastPurchasesPrice(Request $request){
        $item_id = $request->item_id;
        
        $grn_data = [];
        if($item_id){
            $item_row = TyreInventory::where('id', $item_id)->first();
            $item_code = $item_row->stock_id_code;
            if($item_code){
                $grn_data = \App\Model\WaGrn::where('item_code', $item_code)
                    ->orderBy('id', 'desc')
                    ->limit(3)
                    ->get();
            }
        }
        
        $view_data = view('admin.tyrepurchaseorders.last_prices',compact('grn_data'));
        return $view_data;
    }

    
    
    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaTyrePurchaseOrder::whereNotIn('status',['PRELPO','COMPLETED'])->where('is_hide','No');
            if($permission != 'superadmin')
            {
                if(getLoggeduserProfile()->id != '545')
                {
                     //$lists = $lists->where('user_id', getLoggeduserProfile()->id);
                }
               
            }
            $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyrepurchaseorders.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }
    public function hidepurchaseorder($slug){
        $row =  WaTyrePurchaseOrder::whereSlug($slug)->update(['is_hide'=>'Yes']);
        if ($row) {
            Session::flash('success', 'Unwanted purchase order hide successfully.');
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
                $data['title'] = 'Add '.$this->title;
                $data['model'] = $this->model;
                $data['breadcum'] = [$this->title =>route($this->model.'.index'),'Add'=>''];
                $data['units'] = \App\Model\WaUnitOfMeasure::get();
                $data['chart_of_accounts'] = \App\Model\WaChartsOfAccount::orderBy('id','DESC')->get();
                $data['assets'] = \App\Model\WaAssets::orderBy('id','DESC')->get();
                $data['asset_category'] = \App\Model\WaAssetCategory::orderBy('id','DESC')->get();
                $data['asset_location'] = \App\Model\WaAssetLocation::orderBy('id','DESC')->get();
                $data['vats'] = \App\Model\TaxManager::orderBy('id','DESC')->get();
                $data['asset_depreciation'] = \App\Model\WaAssetDepreciation::orderBy('id','DESC')->get();
                $data['profit_loss'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','PROFIT AND LOSS')->orderBy('id','DESC')->get();
                $data['gl'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','BALANCE SHEET')->orderBy('id','DESC')->get();
                

                return view('admin.tyrepurchaseorders.create')->with($data);
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


    public function store(Request $request)
    {
	    //cho "<pre>"; print_r($request->all()); die; 
        try
        {
                $request->purchase_no = getCodeWithNumberSeries('TYRE PURCHASE ORDERS');

                $check=DB::transaction(function () use ($request){
                    $row = new WaTyrePurchaseOrder();
                    $row->purchase_no= $request->purchase_no;
                    
                    $row->restaurant_id= $request->restaurant_id;
                    $row->wa_department_id= $request->wa_department_id;

                    /*$row->restaurant_id= getLoggeduserProfile()->restaurant_id;
                    $row->wa_department_id= getLoggeduserProfile()->wa_department_id;*/
                    $row->user_id = getLoggeduserProfile()->id;
                    $row->purchase_date = $request->purchase_date;
                    $row->wa_supplier_id = $request->wa_supplier_id;
                    $row->wa_location_and_store_id = $request->wa_location_and_store_id;
                    $row->save();
                    
   
                    foreach($request->qty as $key=> $val){
                        if($val > 0){
                            $item_detail = TyreInventory::where('id',$key)->first();                
                            $item = new WaTyrePurchaseOrderItem();
                            $item->wa_tyre_purchase_order_id = $row->id;
                            $item->wa_inventory_item_id = $key;
                            $item->quantity = $val;
                            $item->note = "";
                            $item->prev_standard_cost = $item_detail->prev_standard_cost;
                            $item->order_price = $item_detail->standard_cost;
                            $item->supplier_uom_id = $item_detail->wa_unit_of_measure_id;
                            $item->supplier_quantity = $val;
                            $item->unit_conversion = "1";
                            $item->item_no = $item_detail->stock_id_code;
                            $item->is_exclusive_vat = "No";
                            $item->unit_of_measure = $item_detail->getUnitOfMeausureDetail->id;              
                            $item_detail = TyreInventory::where('id',$key)->first();
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
                            $total_cost_with_vat = $item->total_cost+$vat_amount;
                            $roundOff = fmod($total_cost_with_vat, 1); //0.25
                            if($roundOff!=0){
                                if($roundOff > '0.50'){
                                    $roundOff = round((1-$roundOff),2);
                                }else{
                                    $roundOff = '-'.round($roundOff,2);
                                }
                            }
                            $item->round_off           =  $roundOff;
                            $item->total_cost_with_vat =  round($total_cost_with_vat);
                            //$item->total_cost_with_vat =  $item->total_cost+$vat_amount;
                            $item->save();
                        }
                    }

                    return $row;
                });
          
                if($check){
                    updateUniqueNumberSeries('TYRE PURCHASE ORDERS',$request->purchase_no);
                    Session::flash('success', 'Record added successfully.');
                    return redirect()->route($this->model.'.edit', $check->slug);
                }
                throw new \Exception("Something went wrong");
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function sendRequisitionRequest($purchase_no)
    {
      
       try
        {


            $row =  WaTyrePurchaseOrder::where('status','UNAPPROVED')->where('purchase_no',$purchase_no)->first();
            if($row)
            {
                $row->status = 'PENDING';
                $row->save();
                addTyrePurchaseOrderPermissions($row->id,$row->wa_department_id);
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
        
            $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.tyrepurchaseorders.show',compact('title','model','breadcum','row')); 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        
    }

    public function print(Request $request)
    {
      
        $slug = $request->slug;  
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
        return view('admin.tyrepurchaseorders.print',compact('title','model','breadcum','row')); 
    }

     public function exportToPdf($slug)
    {
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
        $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.tyrepurchaseorders.print', compact('title','model','breadcum','row'));
        $report_name = 'purchase_order_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
    }






    public function edit($slug)
    {
        try
        {
            
                $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
                if($row)
                {
                    $data['row'] = $row;
                    $data['title'] = 'Edit '.$this->title;
                    $data['units'] = \App\Model\WaUnitOfMeasure::get();
                    $data['chart_of_accounts'] = \App\Model\WaChartsOfAccount::orderBy('id','DESC')->get();
                    $data['assets'] = \App\Model\WaAssets::orderBy('id','DESC')->get();
                    $data['asset_category'] = \App\Model\WaAssetCategory::orderBy('id','DESC')->get();
                    $data['asset_location'] = \App\Model\WaAssetLocation::orderBy('id','DESC')->get();
                    $data['vats'] = \App\Model\TaxManager::orderBy('id','DESC')->get();
                    $data['asset_depreciation'] = \App\Model\WaAssetDepreciation::orderBy('id','DESC')->get();
                    $data['profit_loss'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','PROFIT AND LOSS')->orderBy('id','DESC')->get();
                    $data['gl'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','BALANCE SHEET')->orderBy('id','DESC')->get();
                    $data['breadcum'] = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $data['model'] =$this->model;
                    return view('admin.tyrepurchaseorders.edit')->with($data); 
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
            $row =  WaTyrePurchaseOrder::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'purchase_no' => 'required|unique:wa_tyre_purchase_orders,purchase_no,' . $row->id,
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {


			   foreach($request->qty as $key=> $val){
				if($val > 0){
	                $item_detail = TyreInventory::where('id',$key)->first();			    
	                $item = new WaTyrePurchaseOrderItem();
	                $item->wa_tyre_purchase_order_id = $row->id;
	                $item->wa_inventory_item_id = $key;
	                $item->quantity = $val;
 	                $item->prev_standard_cost = $item_detail->prev_standard_cost;
	                $item->order_price = $item_detail->standard_cost;
	                $item->supplier_uom_id = $item_detail->wa_unit_of_measure_id;
	                $item->supplier_quantity = $val;
	                $item->unit_conversion = "1";
	                $item->item_no = $item_detail->stock_id_code;
	                $item->is_exclusive_vat = "No";
	                $item->unit_of_measure = $item_detail->getUnitOfMeausureDetail->id;              
	                $item_detail = TyreInventory::where('id',$key)->first();
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
					$total_cost_with_vat = $item->total_cost+$vat_amount;
					$roundOff = fmod($total_cost_with_vat, 1); //0.25
					if($roundOff!=0){
						if($roundOff > '0.50'){
							$roundOff = round((1-$roundOff),2);
						}else{
							$roundOff = '-'.round($roundOff,2);
						}
					}
		            $item->round_off		   =  $roundOff;
		            $item->total_cost_with_vat =  round($total_cost_with_vat);
	  //	        $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
	                $item->save();
	           }
			}                


/*
                $item_detail = TyreInventory::where('id',$request->wa_inventory_item_id)->first();
                if($request->is_exclusive_vat=="Yes"){
                    $request->order_price = ($request->order_price*100)/(100+$item_detail->getTaxesOfItem->tax_value);
                }else{
                    $request->order_price =  $request->order_price;
                }
                 
               // echo $request->order_price; die;
                $item = new WaTyrePurchaseOrderItem ();
                $item->wa_purchase_order_id = $row->id;
                $item->wa_inventory_item_id = $request->wa_inventory_item_id;
                $item->quantity = $request->quantity;
                $item->note = $request->note;

                  $item->prev_standard_cost = $request->prev_standard_cost;
                $item->order_price = $request->order_price;
                $item->supplier_uom_id = $request->supplier_uom_id;
                $item->supplier_quantity = $request->supplier_quantity;
                $item->unit_conversion = $request->unit_conversion;
                 $item->item_no = $request->item_no;
                   $item->unit_of_measure = $request->unit_of_measure;
                   $item->is_exclusive_vat = $request->is_exclusive_vat;

                $item->standard_cost = $item_detail->standard_cost;
                $item->total_cost = $request->order_price*$request->supplier_quantity;


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

				$total_cost_with_vat = $item->total_cost+$vat_amount;
				$roundOff = fmod($total_cost_with_vat, 1); //0.25
				if($roundOff!=0){
					if($roundOff > '0.50'){
						$roundOff = round($roundOff,2);
					}else{
						$roundOff = '-'.round($roundOff,2);
					}
				}
	
	            $item->round_off		   =  $roundOff;
	            $item->total_cost_with_vat =  round($total_cost_with_vat);

//                $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
              
                $item->save();
*/
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
            WaTyrePurchaseOrder::whereSlug($slug)->delete();
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
      $rows = TyreInventory::where('wa_inventory_category_id',$request->selected_inventory_category)->orderBy('title','asc')->get();
      $data = '<option  value="">Please select item</option>';
      foreach($rows as $row)
      {
        $data .= '<option  value="'.$row->id.'">'.$row->title.'</option>';
      }

      return $data;

    }

    public function getItemsList(Request $request){
        
        //dd($request->selected_inventory_category);
        $rows = TyreInventory::where('wa_inventory_category_id',$request->selected_inventory_category)->orderBy('description','asc')->get();

        $view_data = view('admin.tyrepurchaseorders.items_list',compact('rows'));
        return $view_data;

    }


    public function getItemDetail(Request $request)
    {
      $rows = TyreInventory::where('id',$request->selected_item_id)->first();
      $vat_rate = 0;
      if($rows->tax_manager_id && $rows->getTaxesOfItem)
      {
          $vat_rate = $rows->getTaxesOfItem->tax_value;
      }
     

      return json_encode(['vat_rate'=>$vat_rate,'stock_id_code'=>$rows->stock_id_code,'unit_of_measure'=>$rows->wa_unit_of_measure_id?$rows->wa_unit_of_measure_id:'','standard_cost'=>$rows->standard_cost,'prev_standard_cost'=>0]);

    }
    public function deletingItemRelation($purchase_no,$id)
    {
        try
        {
            WaTyrePurchaseOrderItem::whereId($id)->delete();


            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }



   


    public function editPurchaseItem($purchase_no,$id)
    {
        try
        {
           
                $row =  WaTyrePurchaseOrder::where('purchase_no',$purchase_no)->with(['getRelatedItem','getRelatedItem.getInventoryItemDetail'
                ,'getRelatedItem.getNonStockItemDetail'
                ])
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
                    return view('admin.tyrepurchaseorders.editItem',compact('title','model','breadcum','row','id','form_url')); 
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

           // dd('here');
          
           $item_detail = TyreInventory::where('id',$request->wa_inventory_item_id)->first();
           if($request->is_exclusive_vat=="Yes"){
               $request->order_price = ($request->order_price*100)/(100+$item_detail->getTaxesOfItem->tax_value);
           }else{
               $request->order_price =  $request->order_price;
           }
            
          
            $item =  WaTyrePurchaseOrderItem::where('id',$id)->first();
            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->prev_standard_cost = $request->prev_standard_cost;
            $item->order_price = $request->order_price;
            $item->supplier_uom_id = $request->supplier_uom_id;
            $item->supplier_quantity = $request->supplier_quantity;
            $item->is_exclusive_vat = $request->is_exclusive_vat;

            $item->unit_conversion = $request->unit_conversion;
            $item->item_no = $request->item_no;
          
            $item->unit_of_measure = $request->unit_of_measure;
            $item_detail = TyreInventory::where('id',$request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item->order_price*$request->supplier_quantity;
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

			$total_cost_with_vat = $item->total_cost+$vat_amount;
			$roundOff = fmod($total_cost_with_vat, 1); //0.25
			if($roundOff!=0){
				if($roundOff > '0.50'){
					$roundOff = round($roundOff,2);
				}else{
					$roundOff = '-'.round($roundOff,2);
				}
			}

            $item->round_off		   =  $roundOff;
            $item->total_cost_with_vat =  round($total_cost_with_vat);

//	            $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.edit', $item->getTyrePurchaseOrder->slug);
            
        }
        catch(\Exception $e)
        {
          
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function maintainSupplierPurchaseList(Request $request)
    {
        try{
            $data['pmodule'] = 'maintain-supplier-purchase-list';
            $data['title'] = "Maintain Supplier Purchase List";
            $data['model'] = 'maintain-supplier-purchase-list';
            if(isset($_GET['supplier_name']) || isset($_GET['supplier_code'])){
                $sup = new \App\Model\WaSupplier();
                if($request->supplier_name)
                {
                    $sup = $sup->orWhere('name',$request->supplier_name);
                }
                if($request->supplier_code)
                {
                    $sup = $sup->orWhere('supplier_code',$request->supplier_code);
                }
                $data['suppliers'] = $sup->get();            
            }
            return view('admin.maintainSupplierPurchase.list')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->route('admin.dashboard');
        }
    }

    public function maintainSupplierPurchaseSuppliers(Request $request)
    {
        try{
            $data['pmodule'] = 'maintain-supplier-purchase-list';
            $data['title'] = "Maintain Supplier Purchase List";
            $data['model'] = 'maintain-supplier-purchase-list';
            $data['units'] = \App\Model\WaUnitOfMeasure::get();
            $data['supplier'] = \App\Model\WaSupplier::where('supplier_code',$request->supplier_code)->firstOrFail();
            $data['item_suppliers'] = \App\Model\TyreInventorySupplier::where('wa_supplier_id',$data['supplier']->id)->orderBy('id','DESC')->get();
            return view('admin.maintainSupplierPurchase.suppliers')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->route('maintainSupplierPurchaseList');
        }
    }
    
    public function maintainSupplierPurchaseSuppliersSave(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:1|max:2550000000',
            'supplier_unit_of_measure' => 'required|exists:wa_unit_of_measures,title',
            'conversion_factor' => 'nullable|string|min:1|max:255',
            'supplier_stock_description' => 'required|string|max:255|min:1',
            'lead_time_days' => 'required|numeric|digits_between:1,10|min:1',
            'preferred_supplier' => 'nullable|in:No,Yes',
            'price_effective_from' => 'required|date_format:Y-m-d|after:today',
            'supplier_stock_code' => 'nullable|string|min:1|max:255',
            'minimum_order_quantity' => 'required|numeric|digits_between:1,10|min:1',
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        $suplier = DB::transaction(function () use ($request) {
            $itemid = $request->id;
            $suplier = \App\Model\TyreInventorySupplier::findOrFail($itemid);
            $suplier->price = $request->price;
            $suplier->supplier_unit_of_measure = $request->supplier_unit_of_measure;
            $suplier->conversion_factor = $request->conversion_factor;
            $suplier->supplier_stock_description = $request->supplier_stock_description;
            $suplier->price_effective_from = $request->price_effective_from;
            $suplier->supplier_stock_code = $request->supplier_stock_code;
            $suplier->minimum_order_quantity = $request->minimum_order_quantity;
            $suplier->lead_time_days = $request->lead_time_days;
            if(isset($request->preferred_supplier)){
                $suplier->preferred_supplier = $request->preferred_supplier;
            }
            $suplier->save();
            \App\Model\TyreInventorySupplierPrices::where('wa_inventory_item_supplier_id',$suplier->id)->update(['status'=>'Old']);
            $price = new \App\Model\TyreInventorySupplierPrices;
            $price->wa_inventory_item_supplier_id = $suplier->id;
            $price->price = $suplier->price;
            $price->status = 'Current';
            $price->save();
            return  $suplier;
        });
        if($suplier)
        {   
            return response()->json([
                'result' => 1,
                'message' => 'Supplier Item updated successfully',
                'location'=>route('maintainSupplierPurchaseSuppliers'),
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    
    public function addLocationId(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $validator = Validator::make($request->all(), [
            'modal' => 'nullable|string',
            'location_id' => 'required|string|min:1|max:255',
            'location_description' => 'required|string|min:1|max:255',
            'location_parent' => 'nullable|exists:wa_asset_locations,id',
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        $location = new \App\Model\WaAssetLocation;
        $location->location_ID = $request->location_id;
        $location->location_description = $request->location_description;
        $location->wa_asset_locations_id = $request->location_parent;
        $location->save();
        if($location)
        {   
            $response['result'] = 1;
            if($request->modal){
                $response['data_id'] = $location->id;
                $response['data_value'] = $location->location_ID.' '. $location->location_description;
                $response['modal'] = $request->modal;
            }else
            {
                $response['refresh'] = true;
            }
            $response['message'] = 'location added successfully';
            return response()->json($response);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function addAssetCategory(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $validator = Validator::make($request->all(), [
            'modal' => 'nullable|string',
            'category_code' => 'required|string|min:1|max:255',
            'category_description' => 'required|string|min:1|max:255',
            'fixed_asset_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_depreciation_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_disposal_id' => 'required|exists:wa_charts_of_accounts,id',
            'balance_sheet_id' => 'required|exists:wa_charts_of_accounts,id',
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        # WaAssetCategory
        $category = new \App\Model\WaAssetCategory;
        $category->category_code = $request->category_code;
        $category->category_description = $request->category_description;
        $category->fixed_asset_id = $request->fixed_asset_id;
        $category->profit_loss_depreciation_id = $request->profit_loss_depreciation_id;
        $category->profit_loss_disposal_id = $request->profit_loss_disposal_id;
        $category->balance_sheet_id = $request->balance_sheet_id;
        $category->save();
        if($category)
        {   
            $response['result'] = 1;
            if($request->modal){
                $response['data_id'] = $category->id;
                $response['data_value'] = $category->category_code;
                $response['modal'] = $request->modal;
            }else
            {
                $response['refresh'] = true;
            }
            $response['message'] = 'category added successfully';
            return response()->json($response);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function addAsset(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $validator = Validator::make($request->all(), [
            'modal' => 'nullable|string',
            'asset_description_short' => 'required|string|min:1|max:255',
            'asset_description_long' => 'required|string|min:1|max:255',
            'wa_asset_location_id' => 'required|exists:wa_asset_locations,id',
            'wa_asset_categorie_id' => 'required|exists:wa_asset_categories,id',
            'bar_code' => 'required|string|min:1|max:255',
            'serial_number' => 'required|string|min:1|max:255',
            'wa_asset_depreciation_id' => 'required|exists:wa_asset_depreciations,id',
            'depreciation_rate' => 'required|numeric|min:1|max:255',
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        # WaAssetCategory
        $asset = new \App\Model\WaAssets;
        $asset->asset_description_short = $request->asset_description_short;
        $asset->asset_description_long = $request->asset_description_long;
        $asset->wa_asset_location_id = $request->wa_asset_location_id;
        $asset->wa_asset_categorie_id = $request->wa_asset_categorie_id;
        $asset->bar_code = $request->bar_code;
        $asset->serial_number = $request->serial_number;
        $asset->wa_asset_depreciation_id = $request->wa_asset_depreciation_id;
        $asset->depreciation_rate = $request->depreciation_rate;
        $asset->save();
        if($asset)
        {   
            $response['result'] = 1;
            if($request->modal){
                $response['data_id'] = $asset->id;
                $response['data_value'] = $asset->asset_description_short;
                $response['modal'] = $request->modal;
            }else
            {
                $response['refresh'] = true;
            }
            $response['message'] = 'asset added successfully';
            return response()->json($response);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function addNonStockItem(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $inputArray = [               
            'purchase_no' => 'required|unique:wa_tyre_purchase_orders,purchase_no',
            'item_description'=>'required|max:255',
            'item_gl'=>'required|exists:wa_charts_of_accounts,id',
            'item_asset'=>'nullable|exists:wa_assets,id',
            'item_quantity'=>'required|numeric|min:1',
            'item_price'=>'required|numeric|min:1',
            'item_unit'=>'required|exists:wa_unit_of_measures,id',
            'item_vat'=>'required|exists:tax_managers,id',
            'item_inclusive_vat'=>'required|max:255',
            'item_delivery_date'=>'required|date',
        ];
        if($request->id){
            $inputArray['purchase_no'] .= ','.$request->id;
        }
        $validator = Validator::make($request->all(), $inputArray);  
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        $save = DB::transaction(function () use ($request){            
            if(isset($request->id))
            {
                $row = WaTyrePurchaseOrder::findOrFail($request->id);

            }else
            {
                $row = new WaTyrePurchaseOrder();
                $row->purchase_no= $request->purchase_no;
                $row->restaurant_id= getLoggeduserProfile()->restaurant_id;
                $row->wa_department_id= getLoggeduserProfile()->wa_department_id;
                $row->user_id = getLoggeduserProfile()->id;
                $row->purchase_date = $request->purchase_date;
                $row->wa_supplier_id = $request->wa_supplier_id;
                $row->wa_location_and_store_id = $request->wa_location_and_store_id;
                $row->save();
                // dd($row);

            }
            if(isset($request->nonstockid)){
                $item_detail = \App\Model\WaNonStockInventoryItems::findOrFail($request->nonstockid);
            }else
            {
                $item_detail = new \App\Model\WaNonStockInventoryItems;
                $item_detail->item_description = $request->item_description;
                $item_detail->gl_code_id  = $request->item_gl ;
                $item_detail->assets_id  = $request->item_asset ;
                $item_detail->quantity_to_purchase = $request->item_quantity;
                $item_detail->price_per_item = $request->item_price;
                $item_detail->unit_id  = $request->item_unit ;
                $item_detail->vat_id  = $request->item_vat ;
                $item_detail->price_inclusice_of_vat = $request->item_inclusive_vat;
                $item_detail->delivery_date = $request->item_delivery_date;
                $item_detail->save();  
            }
            if($request->itemid){
                $item = WaTyrePurchaseOrderItem::findOrFail($request->itemid);
            }
            else
            {
                $item = new WaTyrePurchaseOrderItem();
            }
            $item->wa_tyre_purchase_order_id = $row->id;
            $item->wa_inventory_item_id = $item_detail->id;
            $item->quantity = $item_detail->quantity_to_purchase;
            $item->note = "";
            $item->prev_standard_cost = $item_detail->price_per_item;
            $item->order_price = $item_detail->price_per_item;
            $item->supplier_uom_id = $item_detail->unit_id;
            $item->supplier_quantity = $item_detail->quantity_to_purchase;
            $item->unit_conversion = "1";
            $item->item_no = $item_detail->gl_code_id ? $item_detail->gl_code->account_code : NULL;
            $item->is_exclusive_vat = "No";
            $item->unit_of_measure = $item_detail->unit_id;              
            $item->standard_cost = $item_detail->price_per_item;
            $item->total_cost = $item_detail->price_per_item*$item_detail->quantity_to_purchase;
            $vat_rate = 0;
            $vat_amount = 0;        
            if($item_detail->vat_id && $item_detail->getTaxesOfItem)
            {
                $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                if($item->total_cost > 0)
                {
                    $vat_amount = ($item_detail->getTaxesOfItem->tax_value*$item->total_cost)/100;
                }
            }        
            $item->vat_rate = $vat_rate;
            $item->vat_amount = $vat_amount;
            $total_cost_with_vat = $item->total_cost+$vat_amount;
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if($roundOff!=0){
                if($roundOff > '0.50'){
                    $roundOff = round((1-$roundOff),2);
                }else{
                    $roundOff = '-'.round($roundOff,2);
                }
            }
            $item->round_off		   =  $roundOff;
            $item->total_cost_with_vat =  round($total_cost_with_vat);
            $item->item_type = 'Non-Stock';
            $item->save();
            return $row;
        });
        if($save)
        {   
            return response()->json([
                'result' => 1,
                'location'=>route($this->model.'.edit', $save->slug),
                'message' => 'Non Stock asset added successfully',
            ]);
        }
      
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
}
