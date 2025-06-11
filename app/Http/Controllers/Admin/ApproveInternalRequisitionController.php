<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\WaInternalReqPermission;


use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\User;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ApproveInternalRequisitionController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'authorise-requisitions';
        $this->title = 'Authorise Requisitions';
        $this->pmodule = 'authorise-requisitions';
    } 

    public function index()
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
           
            $lists = WaInternalReqPermission::where('user_id',getLoggeduserProfile()->id)->where('status','NEW')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.approveinternalrequisition.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            
                $row =  WaInternalRequisition::whereSlug($slug)->first();
                if($row)
                {
                    $title = $this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.approveinternalrequisition.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaInternalRequisition::whereSlug($slug)->first();
            if($request->requisition_status == 'approve')
            {
                $permmission_detail = WaInternalReqPermission::where('wa_internal_requisition_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'APPROVED','note'=>$request->authorizer_note]);
                 
                 $lattest_permmission = WaInternalReqPermission::where('wa_internal_requisition_id',$row->id)->where('approve_level','>',$logged_user->authorization_level)->orderBy('approve_level','asc')->first();

                 if($lattest_permmission)
                {
                    $lattest_permmission->status = 'NEW';
                    $lattest_permmission->save();
                    $emp = User::select('email')->where('id',$lattest_permmission->user_id)->first();

                    $ex_req =  WaInternalRequisition::where('id',$row->id)->first();
                    $ex_req->status = 'PROCESSING';
                    $ex_req->save();

                    sendMailForInternalRequisition($emp->email,$lattest_permmission->wa_internal_requisition_id,$lattest_permmission->approve_level);
                }
                else
                {
                    $ex_req =  WaInternalRequisition::where('id',$row->id)->first();
                    $ex_req->status = 'APPROVED';
                    $ex_req->save();
                }


            }
            else
            {
                //decline case
               WaInternalReqPermission::where('wa_internal_requisition_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'DECLINED','note'=>$request->authorizer_note]);

                WaInternalReqPermission::where('wa_internal_requisition_id',$row->id)->where('approve_level','>',$logged_user->authorization_level)->delete();
                $row->status = 'DECLINED';
                $row->save();
            }
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
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
        
    }

       
    public function deletingItemRelation($purchase_no,$id)
    {
        try
        {
            WaInternalRequisitionItem::whereId($id)->delete();
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
           
                $row =  WaInternalRequisition::where('requisition_no',$purchase_no)
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
                    return view('admin.approveinternalrequisition.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
           
          
            $item =  WaInternalRequisitionItem::where('id',$id)->first();
          
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


    
}
