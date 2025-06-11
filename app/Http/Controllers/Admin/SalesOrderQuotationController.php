<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\WaSalesOrderQuotation;
use App\Model\WaCustomer;
use App\Model\WaSalesOrderQuotationItem;
use App\Model\WaChartsOfAccount;
use App\Model\WaSalesInvoice;
use App\Model\WaSalesInvoiceItem;




class SalesOrderQuotationController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'proforma-invoice';
        $this->title = 'Proforma Invoice';
        $this->pmodule = 'proforma-invoice';

       

        
    } 

  
    
    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            
            if($permission != 'superadmin')
            {
                if(isset($permission[$pmodule.'___view-all']))
                {
                    $lists = WaSalesOrderQuotation::orderBy('id', 'desc')->get();
                }
                else
                {
                    $lists = WaSalesOrderQuotation::where('creater_id', getLoggeduserProfile()->id);
                    $lists = $lists->orderBy('id', 'desc')->get();
                }
                
                
            }
            else
            {
                $lists = WaSalesOrderQuotation::orderBy('id', 'desc')->get();
            }
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.saleorderquotation.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function getCustomerDetail(Request $request)
    {
         $rows = WaCustomer::where('id',$request->customer_id)->first();


     

      return json_encode(['customer_name'=>$rows->customer_name,'customer_code'=>$rows->customer_code?$rows->customer_code:'','address'=>$rows->address,'telephone'=>$rows->telephone]);
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
                return view('admin.saleorderquotation.create',compact('title','model','breadcum'));
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
        try
        {
           

            $row = new WaSalesOrderQuotation();
            $row->sales_order_number= $request->sales_order_number;
            $row->wa_customer_id= $request->selected_customer_id;
            $row->creater_id = getLoggeduserProfile()->id;
            $row->order_date = $request->selected_order_date;
            $row->request_or_delivery = $request->selected_request_or_delivery;
            $row->status = $request->selected_status;
            $row->save();
            updateUniqueNumberSeries('SALES_ORDER',$request->sales_order_number);

          
           
            $item = new WaSalesOrderQuotationItem ();
            $item->wa_sales_order_quotation_id = $row->id;

            $item->item_type = $request->selected_item_type;
          
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->item_no = $request->item_no;
              if($request->selected_item_type == 'item')
              {
                    $item_detail = WaInventoryItem::where('id',$request->wa_inventory_item_id)->first();
                    $item->item_name = $item_detail->title;
              }
              else
              {
                $item_detail = WaChartsOfAccount::where('id',$request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->account_name;
              }

                
                $item->standard_cost = $request->standard_cost;
                $item->unit_price = $request->unit_price;
                $item->actual_unit_price = $request->unit_price;
                $item->unit_of_measure_id = $request->unit_of_measure;
                $item->total_cost = $request->quantity*$request->unit_price;
                $item->vat_rate = $request->vat_rate;
                $item->vat_amount = 0;
                $item->service_charge_amount = 0;
                $item->catering_levy_amount = 0;
                $totalTaxation_percent = 100;
                if($request->vat_rate>'0')
                {

                    $totalTaxation_percent = $totalTaxation_percent+$request->vat_rate;
                }
                if($request->service_charge_rate>'0')
                {

                    $totalTaxation_percent = $totalTaxation_percent+$request->service_charge_rate;
                }
                 if($request->catering_levy_rate>'0')
                {

                    $totalTaxation_percent = $totalTaxation_percent+$request->catering_levy_rate;
                }

                $base_value = $item->total_cost;


                if($totalTaxation_percent>100)
                {
                    $base_value = ($item->total_cost*100)/$totalTaxation_percent;
                }
               
                


                if($request->vat_rate>'0')
                {
                    $item->vat_amount =  ($request->vat_rate*$base_value)/100;
                }

                $item->service_charge_rate = $request->service_charge_rate;
               
                if($request->service_charge_rate>'0')
                {
                    $item->service_charge_amount =  ($request->service_charge_rate*$base_value)/100;
                }                
                $item->catering_levy_rate = $request->catering_levy_rate;
              
                if($request->catering_levy_rate>'0')
                {
                    $item->catering_levy_amount =  ($request->catering_levy_rate*$base_value)/100;
                }
                $item->total_cost_with_vat = $item->total_cost;
                $item->save();
       


           
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.edit', $row->slug);
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

            $row =  WaPurchaseOrder::where('status','UNAPPROVED')->where('purchase_no',$purchase_no)->first();
            if($row)
            {
                $row->status = 'PENDING';
                $row->save();
                addPurchaseOrderPermissions($row->id,$row->wa_department_id);
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
        
            $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
            if($row)
            {
                $title = 'View '.$this->title;
                $breadcum = [$this->title=>route($this->model.'.index'),'Show'=>''];
                $model =$this->model;
                return view('admin.saleorderquotation.show',compact('title','model','breadcum','row')); 
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
        $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
        return view('admin.saleorderquotation.print',compact('title','model','breadcum','row')); 
    }

     public function exportToPdf($slug)
    {
      
        $title = 'Add '.$this->title;
        $model = $this->model;
        $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
          $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.saleorderquotation.print', compact('title','model','breadcum','row'));
        $report_name = 'sales_order_quotation_'.date('Y_m_d_H_i_A');
        return $pdf->download($report_name.'.pdf');
    }






    public function edit($slug)
    {
        try
        {
            
                $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.saleorderquotation.edit',compact('title','model','breadcum','row')); 
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



    public function process(Request $request, $slug)
    {
        

        try
        {
            $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
            foreach($row->getRelatedItem as $item)
            {
                $key = 'discount_percent_'.$item->id;
                if(isset($request->$key))
                {
                    $discount_percent = $request->$key;
                    if($discount_percent>0)
                    {

                        $itemRow = WaSalesOrderQuotationItem::where('id',$item->id)->first();

                        $unit_price =  $itemRow->unit_price;
                        $discount_amount = ($discount_percent*$unit_price)/100;
                        $new_unit_price = $unit_price-$discount_amount;
                        $itemRow->unit_price = $new_unit_price;

                        $itemRow->total_cost =  $itemRow->unit_price*$itemRow->quantity;

                        $total_cost_with_vat = $itemRow->total_cost;

                        if($itemRow->vat_rate > 0)
                        {
                            $vat_amount =  ($itemRow->vat_rate*$total_cost_with_vat)/100;
                            //echo $vat_amount;
                            $itemRow->vat_amount =  $vat_amount;
                            $total_cost_with_vat = $total_cost_with_vat+$vat_amount;
                        }
                        $itemRow->discount_percent = $discount_percent;
                        $itemRow->discount_amount = $discount_amount*$itemRow->quantity;

                        $itemRow->total_cost_with_vat = $total_cost_with_vat;

                        
                        $itemRow->save();







                    }
                    
                }
            }
            $row->order_creating_status = 'completed';
            $row->save();
              Session::flash('success', 'Processed successfully.');
           return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

   
     public function addMore(Request $request, $slug)
     {
        

        try
        {
            $row =  WaSalesOrderQuotation::whereSlug($slug)->first();
            $item = new WaSalesOrderQuotationItem ();
            $item->wa_sales_order_quotation_id = $row->id;

            $item->item_type = $request->selected_item_type;

            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->item_no = $request->item_no;
            if($request->selected_item_type == 'item')
            {
                $item_detail = WaInventoryItem::where('id',$request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->title;
            }
            else
            {
                $item_detail = WaChartsOfAccount::where('id',$request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->account_name;
            }


            $item->standard_cost = $request->standard_cost;
            $item->unit_price = $request->unit_price;
            $item->actual_unit_price = $request->unit_price;
            $item->unit_of_measure_id = $request->unit_of_measure;
            $item->total_cost = $request->quantity*$request->unit_price;
             $item->vat_rate = $request->vat_rate;
            $item->vat_amount = 0;
            $item->service_charge_amount = 0;
            $item->catering_levy_amount = 0;
            $totalTaxation_percent = 100;
            if($request->vat_rate>'0')
            {

                $totalTaxation_percent = $totalTaxation_percent+$request->vat_rate;
            }
            if($request->service_charge_rate>'0')
            {

                $totalTaxation_percent = $totalTaxation_percent+$request->service_charge_rate;
            }
             if($request->catering_levy_rate>'0')
            {

                $totalTaxation_percent = $totalTaxation_percent+$request->catering_levy_rate;
            }

            $base_value = $item->total_cost;


            if($totalTaxation_percent>100)
            {
                $base_value = ($item->total_cost*100)/$totalTaxation_percent;
            }
            // echo $totalTaxation_percent;die;

            


            if($request->vat_rate>'0')
            {
                $item->vat_amount =  ($request->vat_rate*$base_value)/100;
            }

            $item->service_charge_rate = $request->service_charge_rate;
           
            if($request->service_charge_rate>'0')
            {
                $item->service_charge_amount =  ($request->service_charge_rate*$base_value)/100;
            }                
            $item->catering_levy_rate = $request->catering_levy_rate;
          
            if($request->catering_levy_rate>'0')
            {
                $item->catering_levy_amount =  ($request->catering_levy_rate*$base_value)/100;
            }
            $item->total_cost_with_vat = $item->total_cost;
            $item->save();
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.edit', $row->slug);
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }


    public function update(Request $request, $slug)
    {
      
       
    }


    public function destroy($slug)
    {
        try
        {
            WaSalesOrderQuotation::whereSlug($slug)->delete();
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
        if($request->selected_item_type == 'item')
        {
            $rows = WaInventoryItem::orderBy('title','asc')->get();
            $data = '<option  value="">Please select item</option>';
              foreach($rows as $row)
              {
                $data .= '<option  value="'.$row->id.'">'.$row->title.'</option>';
              }

            return $data;
        }
        else
        {
            $rows =  WaChartsOfAccount::whereIn('wa_account_group_id',[19,20])->orderBy('account_name','asc')->get();
             $data = '<option  value="">Please select item</option>';
              foreach($rows as $row)
              {
                $data .= '<option  value="'.$row->id.'">'.$row->account_name.'('.$row->account_code.')'.'</option>';
              }

            return $data;
        }
     

    }




    public function getItemDetail(Request $request)
    {

        if($request->item_type == 'item')
        {
            $rows = WaInventoryItem::where('id',$request->selected_item_id)->first();
            return json_encode(['stock_id_code'=>$rows->stock_id_code,'unit_of_measure'=>$rows->wa_unit_of_measure_id?$rows->wa_unit_of_measure_id:'','standard_cost'=>$rows->standard_cost,'prev_standard_cost'=>$rows->prev_standard_cost]);  
        }
        else
        {
            $rows =  WaChartsOfAccount::where('id',$request->selected_item_id)->first();
            return json_encode(['stock_id_code'=>$rows->account_code,'unit_of_measure'=>'','standard_cost'=>0,'prev_standard_cost'=>0]);  
        }
    

    }
    public function deletingItemRelation($purchase_no,$id)
    {
        try
        {
            WaPurchaseOrderItem::whereId($id)->delete();


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
           
                $row =  WaPurchaseOrder::where('purchase_no',$purchase_no)
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
                    return view('admin.purchaseorders.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
          
          
            $item =  WaPurchaseOrderItem::where('id',$id)->first();
            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->prev_standard_cost = $request->prev_standard_cost;
            $item->order_price = $request->order_price;
            $item->supplier_uom_id = $request->supplier_uom_id;
            $item->supplier_quantity = $request->supplier_quantity;
            $item->unit_conversion = $request->unit_conversion;
            $item->item_no = $request->item_no;
          
            $item->unit_of_measure = $request->unit_of_measure;
            $item_detail = WaInventoryItem::where('id',$request->wa_inventory_item_id)->first();
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
            $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.edit', $item->getPurchaseOrder->slug);
            
        }
        catch(\Exception $e)
        {
          
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function transferToSalesInvoice($slug)
    {
       $proformaInvoice =  WaSalesOrderQuotation::whereSlug($slug)->first();
       $proformaInvoiceItems = $proformaInvoice->getRelatedItem;
        $sales_invoice_number = getCodeWithNumberSeries('SALES_INVOICE');
        //$order_date = date('Y-m-d');
        $row = new WaSalesInvoice();
        $row->sales_invoice_number= $sales_invoice_number;
        $row->wa_customer_id= $proformaInvoice->wa_customer_id;
        $row->creater_id = $proformaInvoice->creater_id;
        $row->order_date = $proformaInvoice->order_date;
        $row->request_or_delivery = $proformaInvoice->request_or_delivery;
        $row->status = $proformaInvoice->status;
        $row->save();
        foreach($proformaInvoiceItems as $pIItem)
        {
            $item = new WaSalesInvoiceItem();
            $item->wa_sales_invoice_id = $row->id;
            $item->item_type = $pIItem->item_type;
            $item->quantity = $pIItem->quantity;
            $item->note = $pIItem->note;
            $item->item_no = $pIItem->item_no;
            $item->item_name = $pIItem->item_name;
            $item->standard_cost = $pIItem->standard_cost;
            $item->unit_price = $pIItem->unit_price;
            $item->actual_unit_price = $pIItem->unit_price;
            $item->unit_of_measure_id = $pIItem->unit_of_measure_id ;
            $item->total_cost = $pIItem->total_cost;
            $item->vat_rate = $pIItem->vat_rate;
            $item->vat_amount = $pIItem->vat_amount;
            $item->service_charge_rate = $pIItem->service_charge_rate;
            $item->service_charge_amount = $pIItem->service_charge_amount;
            $item->catering_levy_amount = $pIItem->catering_levy_amount;
            $item->catering_levy_rate = $pIItem->catering_levy_rate;
            $item->total_cost_with_vat = $pIItem->total_cost_with_vat;

              $item->discount_percent = $pIItem->discount_percent;
            $item->discount_amount = $pIItem->discount_amount;
            $item->save();
        }
        updateUniqueNumberSeries('SALES_INVOICE',$sales_invoice_number);
        $proformaInvoice->status = 'close';
        $proformaInvoice->save();
        Session::flash('success', 'Proforma invoice transfered successfully into sales invoice with invoice number '.$sales_invoice_number);
        return redirect()->back()->withInput();

       
    }


    

    

    

    
}
