<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\WaExternalReqPermission;


use App\Model\WaExternalRequisition;
use App\Model\WaExternalRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\User;
use App\Model\WaUserSupplier;
use App\Model\WaSupplier;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ApproveExternalRequisitionController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'approve-external-requisitions';
        $this->title = 'Approve External Requisitions';
        $this->pmodule = 'approve-external-requisitions';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaExternalReqPermission::with(['getExternalPurchase'])->whereHas('getExternalPurchase')->where('user_id',getLoggeduserProfile()->id)->where('status','NEW')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            $user = getLoggeduserProfile()->id;
            $userrole = getLoggeduserProfile()->role_id;
            

            if($userrole == 1){
                $suppliers = WaSupplier::get();

            }else{
                $suppliers = WaUserSupplier::where('user_id',$user)->join('wa_suppliers','wa_user_suppliers.wa_supplier_id','=','wa_suppliers.id')->get();
            }
            return view('admin.approveexternalrequisition.index',compact('title','lists','model','breadcum','pmodule','permission','suppliers'));
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
            
                $row =  WaExternalRequisition::with(['getRelatedItem.getInventoryItemDetail.getAllFromStockMoves'])->whereSlug($slug)->first();
                if($row)
                {
                    $title = $this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.approveexternalrequisition.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaExternalRequisition::whereSlug($slug)->first();
            if($request->requisition_status == 'approve')
            {
                $permmission_detail = WaExternalReqPermission::where('wa_external_requisition_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'APPROVED','note'=>$request->authorizer_note]);
                 
                 $lattest_permmission = WaExternalReqPermission::where('wa_external_requisition_id',$row->id)->where('approve_level','>',$logged_user->external_authorization_level)->orderBy('approve_level','asc')->first();

                 if($lattest_permmission)
                {
                    $lattest_permmission->status = 'NEW';
                    $lattest_permmission->save();
                    $emp = User::where('id',$lattest_permmission->user_id)->first();

                    $ex_req =  WaExternalRequisition::where('id',$row->id)->first();
                    $ex_req->status = 'PROCESSING';
                    $ex_req->save();

                    sendMailForExternalRequisition($emp->email,$lattest_permmission->wa_external_requisition_id,$lattest_permmission->approve_level);
                    $phone = (int)$emp->phone_number;
                    $u = @$ex_req->getrelatedEmployee;
                    $message = 'You have a Branch Requistion No '.$ex_req->purchase_no.' from '.@$u->name.' of Branch: '.@$u->userRestaurent->name.' that requires your approval. Status - '.$ex_req->project_level;
                    // send_sms($phone,$message);
                    sendMessage($message, $phone);
                }
                else
                {
                    $ex_req =  WaExternalRequisition::where('id',$row->id)->first();
                    $ex_req->status = 'APPROVED';
                    $ex_req->save();
                    $u = @$ex_req->getrelatedEmployee;
                    $phone = $u->phone_number;
                    $message = 'Your External Requistion No '.$ex_req->purchase_no.' is Approved';
                    // send_sms($phone,$message);
                    sendMessage($message, $phone);
                }


            }
            else
            {
                //decline case
               WaExternalReqPermission::where('wa_external_requisition_id',$row->id)->where('user_id',$logged_user->id)->update(['status'=>'DECLINED','note'=>$request->authorizer_note]);

                WaExternalReqPermission::where('wa_external_requisition_id',$row->id)->where('approve_level','>',$logged_user->external_authorization_level)->delete();
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
            WaExternalRequisitionItem::whereId($id)->delete();
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
           
                $row =  WaExternalRequisition::where('purchase_no',$purchase_no)
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
                    return view('admin.externalrequisition.editItem',compact('title','model','breadcum','row','id','form_url')); 
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
           
          
            $item =  WaExternalRequisitionItem::where('id',$id)->first();
          
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
            return redirect()->route($this->model.'.edit', $item->getExternalPurchaseId->slug);
            
        }
        catch(\Exception $e)
        {
            dd($e);
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function externalRequisitionReport(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'external-requisitions';
        $title = 'External Requisition Report';
        $model = 'external-requisition-report';
        if(isset($permission[$pmodule.'___'.$model]) || $permission == 'superadmin')
        {
            $lists = WaExternalRequisition::with(['getrelatedEmployee','getDepartment',
                'getRelatedItem.getInventoryItemDetail',
                'getRelatedItem.unit_of_measures',
                'getRelatedAuthorizationPermissions.getExternalAuthorizerProfile'
            ])->where(function($e) use ($request){
                if($request->date_from){
                    $e->whereBetween('requisition_date',[$request->date_from,$request->date_to]);
                }
                if($request->project){
                    $e->where('project_id',$request->project);
                }
                if($request->purchase_no){
                    $e->where('purchase_no','LIKE',$request->purchase_no);
                }
            })->orderBy('id', 'desc');
            if($request->manage == 'pdf'){
                $lists = $lists->get();
            }else{
                $lists = $lists->paginate(20);
            }
            $projects = \App\Model\Projects::get();
            $breadcum = [$title=>route('externalRequisitionReport'),'Listing'=>''];
            if($request->manage == 'pdf'){
                $pdf = \PDF::loadView('admin.approveexternalrequisition.pdfRequisitionReport',compact('projects','title','lists','model','breadcum','pmodule','permission'));
                return $pdf->download('requisition-report-'.date('Y-m-d-H-i-s').'.pdf');
            }

            return view('admin.approveexternalrequisition.requisitionReport',compact('projects','title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    
}
