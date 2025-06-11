<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DeliveryOrder;
use DB;
use Session;
use App\Model\User;
use App\Model\DeliveryOrderSaleRepRelation;

use App\Model\DeliveryOrderBill;
use App\Model\BillDeliveryOrderRelation;

use App\Model\PaymentMethod;

use App\Model\DeliveryReceiptSummaryPayment;
use App\Model\DeliveryOrderReceiptRelation;
use App\Model\DeliveryOrderReceipt;





class DeliveryOrderController extends Controller
{

    protected $model;
  
    public function __construct()
    {
        $this->model = 'delivery-orders'; 
    } 



    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'new-delivery-orders';
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'New Delivery Orders';
            $model = $this->model;
            $lists = DeliveryOrder::where('status','AWAITING_CONFIRMATION_BY_ADMIN')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('admin.delivery-orders.index'),'Listing'=>''];
            return view('admin.deliveryorders.index',compact('title','lists','model','breadcum','logged_user_info','permission','pmodule'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }


    public function openDeliveryOrders()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';
        if(isset($permission[$pmodule.'___open-orders']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'Open Delivery Orders';
            $model = $this->model;
            $lists = DeliveryOrder::whereIn('status',['CONFIRMED','PAID'])->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('admin.delivery-orders.open-orders'),'Listing'=>''];
            return view('admin.deliveryorders.open',compact('title','lists','model','breadcum','logged_user_info','permission','pmodule'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }     
    }

    public function getmasterBillsDeliveryOrders()
    {


        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';

         $tmodule = 'open-delivery-orders-master-bills';
        if(isset($permission[$pmodule.'___master-bills']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'Open Delivery Orders';
            $model = $this->model;
            $lists = DeliveryOrderBill::whereIn('status',['PENDING'])->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('admin.delivery-orders.open-orders.masterbills'),'Master Bills'=>''];
            return view('admin.deliveryorders.masterBills',compact('title','lists','model','breadcum','logged_user_info','permission','pmodule','tmodule'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }     
    }

    public function generateBillsDeliveryOrders()
    {


        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';

         $tmodule = 'open-delivery-orders-generate-bills';
        if(isset($permission[$pmodule.'___generate-bills']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'Open Delivery Orders';
            $model = $this->model;
            $lists = DeliveryOrder::whereIn('status',['CONFIRMED'])->orderBy('id', 'desc')->doesnthave('getAssociateBillRelation')->get();
            $breadcum = [$title=>route('admin.delivery-orders.open-orders.generateBills'),'Generate Bills'=>''];
            return view('admin.deliveryorders.generateBills',compact('title','lists','model','breadcum','logged_user_info','permission','pmodule','tmodule'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }     
    }

    public function deleteBills($slug)
    {


        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';       
        if(isset($permission[$pmodule.'___delete_bill']) || $permission == 'superadmin')
        {
           $bill =  DeliveryOrderBill::where('slug',$slug)->where('status','PENDING')->first();
           if($bill)
           {
             DeliveryOrderBill::where('slug',$slug)->delete();
             Session::flash('success', 'Bill deleted successfully');
             return redirect()->back();
           }
           else
           {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
           }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }     
    }

    public function getMarkBillCashReceipt(Request $request,$bill_slug)
    {
        $pmodule = 'open-delivery-orders';

        $permission =  $this->mypermissionsforAModule();
       
        if(isset($permission[$pmodule.'___close-bill']) || $permission == 'superadmin')
        {
            
             $title = 'Close Bill';
            $model = $this->model;
            $bill = DeliveryOrderBill::where('status','PENDING')->where('slug',$bill_slug)->first(); 
            if($bill)
            {
                $breadcum = ['Open Delivery Orders'=>route('admin.delivery-orders.open-orders.masterbills'),'Close Bill'=>route('admin.delivery-orders.open-orders.masterbills'),'Bill Id:'.$bill->id.''=>''];

                $payment_mode= PaymentMethod::pluck('title','id')->toArray();

                
                return view('admin.deliveryorders.cashreceipt',compact('title','bill','model','breadcum','permission','pmodule','payment_mode'));

            }
            else
            {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
            
        }
        else
        {
        Session::flash('warning', 'Invalid request');
        return redirect()->back();
        }
    }


    public function postMarkBillCashReceipt(Request $request,$bill_slug)
    {
        $bill = DeliveryOrderBill::where('status','PENDING')->where('slug',$bill_slug)->first(); 

       // dd($bill->getAssociateOrdersWithBill);
       
        if($bill)
        {
             $payment_mode= PaymentMethod::pluck('title','id')->toArray();
            $logged_user_info = getLoggeduserProfile();
           
            $bill->status = 'COMPLETED';
            $bill->save();
            $receipt = new DeliveryOrderReceipt();
            $receipt->user_id = $bill->user_id;
            $receipt->cashier_id = $logged_user_info->id;
            $receipt->save();
            
             $receipt_id = $receipt->id;
             foreach($bill->getAssociateOrdersWithBill as $bill_orders)
            {
               
                DeliveryOrderReceiptRelation::updateOrCreate(
                        ['delivery_order_receipt_id' => $receipt_id,'delivery_order_id'=>$bill_orders->delivery_order_id]
                        ); 
                $order = DeliveryOrder::whereId($bill_orders->delivery_order_id)->first();
                $order->status = 'PAID';
                $order->save();
               
            }

            //billing info
            foreach($request->billing_info as $keys =>$billing)
            {
                if($billing['amount']>0)
                {
                   $billreceipt =  new DeliveryReceiptSummaryPayment();
                   $billreceipt->delivery_order_receipt_id = $receipt_id;
                   $billreceipt->payment_mode = $payment_mode[$keys];
                   $billreceipt->narration = $billing['narration'];
                   $billreceipt->amount = (float)$billing['amount'];
                   $billreceipt->save();
                }
                
            }

            $my_receipt = DeliveryOrderReceipt::whereId($receipt_id)->first();


            $this->managetimeForallDelivery($receipt_id,$my_receipt->created_at);
            
             Session::flash('success', 'Bill updated successfully');
            return redirect()->route('admin.delivery-orders.open-orders.masterbills');


        }
        else
        {
            Session::flash('warning', 'Invalid request');
            return redirect()->back();
        }
    }





    public function postgenerateBillsDeliveryOrders(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';

       
        if(isset($permission[$pmodule.'___generate-bills']) || $permission == 'superadmin')
        {
           $rows = [];
            foreach($request->input() as $key=>$data)
            {
                $detail = explode('___',$key);
                if(count($detail) ==  2 )
                {
                    $rows[] = $detail[1];
                }
            }

            if(count($rows)>0)
            {
                $lists_count = DeliveryOrder::select(['id'])
                ->where('status','CONFIRMED')
                ->whereIn('id',$rows)
                ->count();
                if(count($rows) == $lists_count)
                {
                   //valid rows can make a bill now

                    $new_bill = new DeliveryOrderBill();
                    $new_bill->user_id = getLoggeduserProfile()->id;
                    $new_bill->slug = rand(1111,99999).strtotime(date('Y-m-d h:i:s'));
                    $new_bill->save();
                    $bill_id = $new_bill->id;
                    foreach($rows as $order_id)
                    {
                        BillDeliveryOrderRelation::updateOrCreate(
                                ['delivery_order_bill_id' => $bill_id,'delivery_order_id'=>$order_id]
                                );  
                    }
                    Session::flash('success', 'Bill generated successfully your bill id is: '.$bill_id);
                    return redirect()->back()->withInput();


                }
                else
                {
                    Session::flash('warning', 'Please try again');
                    return redirect()->back()->withInput();
                }
            }
            else
            {
                Session::flash('warning', 'Please select at least on order');
                return redirect()->back()->withInput();
            }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }     
    }


     public function cancleOrder($slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'new-delivery-orders';
        if(isset($permission[$pmodule.'___cancle']) || $permission == 'superadmin')
        {
           $row = DeliveryOrder::where('status','AWAITING_CONFIRMATION_BY_ADMIN')->where('slug',$slug)->first();
           if($row)
           {
                $row->status = 'CANCELLED_BY_ADMIN';
                $row->save();
                Session::flash('success', 'Order Canceled successfully');
                return redirect()->back();
           }
           else
           {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
           }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }

     public function getAssignOrder($slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';
        if(isset($permission[$pmodule.'___assign']) || $permission == 'superadmin')
        {
           $row = DeliveryOrder::where('status','PAID')->where('slug',$slug)->first();
           if($row)
           {
          
                $title = 'Open Delivery Orders';
                $breadcum = [$title=>route('admin.delivery-orders.index'),'Detail'=>'',$row->id=>''];
                return view('admin.deliveryorders.deliveryorderdetail',compact('title','row','breadcum'));
           }
           else
           {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
           }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }


    public function postAssignOrder(Request $request,$slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'open-delivery-orders';
        if(isset($permission[$pmodule.'___assign']) || $permission == 'superadmin')
        {
           $row = DeliveryOrder::where('status','PAID')->where('slug',$slug)->first();
           if($row)
           {
                $is_exist =  DeliveryOrderSaleRepRelation::where('delivery_order_id',$row->id)->first();
                if(!$is_exist)
                {
                    $is_exist = new DeliveryOrderSaleRepRelation();

                }
                $is_exist->delivery_order_id = $row->id;
                $is_exist->representative_id = $request->representative_id;
                $is_exist->save();
                Session::flash('success', 'Sales Representative Assigned Successfully.');
                return redirect()->route('admin.delivery-orders.open-orders');
           }
           else
           {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
           }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }


    public function confirmOrder($slug)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'new-delivery-orders';
        if(isset($permission[$pmodule.'___confirm']) || $permission == 'superadmin')
        {
           $row = DeliveryOrder::where('status','AWAITING_CONFIRMATION_BY_ADMIN')->where('slug',$slug)->first();
           if($row)
           {
                $row->status = 'CONFIRMED';
                $row->order_confirm_time = date('Y-m-d H:i:s');
                $row->save();
                Session::flash('success', 'Order Confirmed successfully');
                return redirect()->back();
           }
           else
           {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
           }
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }

   

   
   
   


   

   

    
}
