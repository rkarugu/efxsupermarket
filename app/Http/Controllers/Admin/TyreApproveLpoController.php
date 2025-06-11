<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\WaTyrePurchaseOrderPermission;


use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\User;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class TyreApproveLpoController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'tyre-approve-lpo';
        $this->title = 'Approve LPO';
        $this->pmodule = 'tyre-approve-lpo';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            //$lists = WaTyrePurchaseOrderPermission::where('user_id',getLoggeduserProfile()->id)->where('status','NEW')->orderBy('id', 'desc')->get();
            $lists = WaTyrePurchaseOrderPermission::where('status','NEW')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyreapprovelpo.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
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


    public function show($id)
    {
        
    }


    public function edit($slug)
    {
        try
        {
            
                $row =  WaPurchaseOrder::whereSlug($slug)->first();
                if($row)
                {
                    $title = $this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.tyreapprovelpo.edit',compact('title','model','breadcum','row')); 
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
            $logged_user = User::where('id',getLoggeduserProfile()->id)->first();
            $row = WaPurchaseOrder::whereSlug($slug)->first();
            $row->wa_supplier_id = $request->wa_supplier_id;
            $row->wa_location_and_store_id = $request->wa_location_and_store_id;
            $row->save();
            if($request->requisition_status == 'approve')
            {
                $permmission_detail = WaTyrePurchaseOrderPermission::where('wa_purchase_order_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'APPROVED','note'=>$request->authorizer_note]);
                 
                 $lattest_permmission = WaTyrePurchaseOrderPermission::where('wa_purchase_order_id',$row->id)->where('approve_level','>',$logged_user->purchase_order_authorization_level)->orderBy('approve_level','asc')->first();

                 if($lattest_permmission)
                {
                    $lattest_permmission->status = 'NEW';
                    $lattest_permmission->save();
                    $emp = User::select('email')->where('id',$lattest_permmission->user_id)->first();

                    $ex_req =  WaPurchaseOrder::where('id',$row->id)->first();
                    $ex_req->status = 'PROCESSING';
                    $ex_req->save();

                    sendMailForPurchaseOrder($emp->email,$lattest_permmission->wa_purchase_order_id,$lattest_permmission->approve_level);
                }
                else
                {

                    $ex_req =  WaPurchaseOrder::where('id',$row->id)->first();
                    $ex_req->status = 'APPROVED';
                    $ex_req->save();
                }


            }
            else
            {
                //decline case
               WaTyrePurchaseOrderPermission::where('wa_purchase_order_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'DECLINED','note'=>$request->authorizer_note]);

                WaTyrePurchaseOrderPermission::where('wa_purchase_order_id',$row->id)->where('approve_level','>',$logged_user->purchase_order_authorization_level)->delete();
                $row->status = 'DECLINED';
                $row->save();


                
            }



            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
            dd($e);
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        
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
                    return view('admin.tyreapprovelpo.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
            if($request->tax_value)
            {
                $vat_rate = $request->tax_value;
                if($item->total_cost > 0)
                {
                    $vat_amount = ($request->tax_value*$item->total_cost)/100;
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
            dd($e);
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    
}
