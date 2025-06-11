<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Order;
use DB;
use Session;
use App\Model\User;
use App\Model\OrderReceiptRelation;
use App\Model\OrderReceipt;
use App\Model\OrderBookedTable;
use App\Model\EmployeeTableAssignment;
use App\Model\Bill;
use App\Model\BillOrderRelation;
use App\Model\PaymentMethod;
use App\Model\ReceiptSummaryPayment;
use App\Model\OrderedItem;
use App\Model\TableManager;
use App\Model\OrderOffer;
use App\Model\ItemCategoryRelation;
use App\Model\WaChartsOfAccount;
use App\Model\ItemSalesWithGlCode;
use App\Model\Payment;
use App\Model\WaAccountingPeriod;
use App\Model\PaymentCredit;
use App\Model\PaymentDebit;
use App\Model\TaxManager;
use App\Model\WaGlTran;
use App\Model\OrdersDiscountsForGlTran;
use App\Model\WaSoldButUnbookedItem;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;



class OrderController extends Controller
{

    protected $model;
  
    public function __construct()
    {
        $this->model = 'orders'; 
        //manageOrdersDiscountsForGlTrans([11,15,18,19,21,54]);
    } 


    public function prepaidUnCompletedOrders()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'prepaid-orders';

       
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'Prepaid Orders';
            $model = $this->model;
            $lists = Order::whereNotIn('status',['COMPLETED','PENDING'])->where('order_type','PREPAID')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route('admin.prepaidUnCompletedOrders'),'Listing'=>''];
            return view('admin.orders.index',compact('title','lists','model','breadcum','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'new-orders';
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'New Orders';
            $model = $this->model;
            $lists = Order::where('status','NEW_ORDER')->orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.orders.index',compact('title','lists','model','breadcum','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }

    public function completeOrders()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'complete-orders';
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $logged_user_info = getLoggeduserProfile();
            $title = 'Complete Orders';
            $model = $this->model;
            $lists = Order::where('status','COMPLETED')->where('order_type','PREPAID')->orderBy('id', 'desc')->paginate(8000);
            $breadcum = [$title=>route('admin.completed.orders'),'Listing'=>''];
            return view('admin.orders.completed',compact('title','lists','model','breadcum','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }        
    }


    public function datatablesGetCompletedOrders(Request $request)
    {

    	$columns = array( 
						0 =>'id', 
						1 =>'created_at',
						2 =>'table_no',
						3 =>'total_guests',
						4 =>'item_description',
						5 =>'condiments',
						6 =>'waiter_name',
						7 =>'restro_name',
						8 =>'order_final_price',
						9=> 'status',
						10=> 'order_by',
                    );

        $permission =  $this->mypermissionsforAModule();
        $logged_user_info = getLoggeduserProfile();
            
        $totalData = Order::where('status','COMPLETED')->where('order_type','PREPAID');
        if($permission != 'superadmin')
        {
            $totalData =    $totalData->where('restaurant_id', $logged_user_info->restaurant_id);
        }
        $totalData = $totalData->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');



        if(empty($request->input('search.value')))
        {            
			$posts = Order::where('status','COMPLETED')->where('order_type','PREPAID');
				if(isset($request->order_id)){
					$posts->where('id',$request->order_id);
				}
				$posts->offset($start)
				->limit($limit)
				->orderBy($order,$dir);

            if($permission != 'superadmin')
            {
                $posts =    $posts->where('restaurant_id', $logged_user_info->restaurant_id);
            }
			$posts = $posts->get();
        }
        else
        {
        	$search = $request->input('search.value'); 
			$posts = Order::where('status','COMPLETED')->where('order_type','PREPAID')
						->where(function($query) use ($search){
                    	$query->where('id','LIKE',"%{$search}%")
                    	->orWhere('created_at','LIKE',"%{$search}%")
						->orWhereHas('getAssociateRestro',function ($sql_query) use($search) {  
						$sql_query->where('name', 'LIKE',"%{$search}%");
						})
                   		->orWhere('order_final_price','LIKE',"%{$search}%");
               			})
					->offset($start)
					->limit($limit)
					->orderBy($order,$dir);
            if($permission != 'superadmin')
            {
                $posts =    $posts->where('restaurant_id', $logged_user_info->restaurant_id);
            }

			$posts = $posts->get();
			$totalFiltered = Order::where('status','COMPLETED')
								->where('order_type','PREPAID')
					        	->where(function($query) use ($search){
					                    	$query->where('id','LIKE',"%{$search}%")
					                    	->orWhere('created_at','LIKE',"%{$search}%")
											->orWhereHas('getAssociateRestro',function ($sql_query) use($search) {  
												$sql_query->where('name', 'LIKE',"%{$search}%");
												})
					                   		->orWhere('order_final_price','LIKE',"%{$search}%");
					               			});  
                                 if($permission != 'superadmin')
            {
                $totalFiltered =    $totalFiltered->where('restaurant_id', $logged_user_info->restaurant_id);
            }

		        			$totalFiltered = 	$totalFiltered->count();
		    
        }


         $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $list)
            {
            	
            	$nestedData['id'] = $list->id;
                $nestedData['created_at'] = date('Y-m-d H:i',strtotime($list->created_at));
                $nestedData['table_no'] = getAssociateTableWithOrder($list);
                $nestedData['total_guests'] = $list->total_guests;
				$item_desc_array = [];
				$condiments = [];
				foreach($list->getAssociateItemWithOrder as $ordered_item)
				{
					$condiment_arr =  json_decode($ordered_item->condiments_json);
					$item_desc = 'Item: '.$ordered_item->item_title;
					if($ordered_item->item_comment && $ordered_item->item_comment !="")
					{
						$item_desc .= '('.$ordered_item->item_comment.')';
					}
					$item_desc .= '<br>Qty: '.$ordered_item->item_quantity;
					$item_desc_array[] = $item_desc;
					if($condiment_arr && count($condiment_arr)>0)
                    {

                        foreach($condiment_arr as $condiment_data)
                        {
                            if($condiment_data->sub_items && count($condiment_data->sub_items)>0)
                            {
                                foreach($condiment_data->sub_items as $sub_items)
                                {
                                    if($sub_items->title)
                                    {
                                        $condiments[] = ucfirst($sub_items->title);
                                    }
                                }
                            }
                        }
                      }
				}

				$nestedData['item_description'] = '<b>'.implode(' ,<br>',$item_desc_array).'</b>';

				$nestedData['condiments'] = '<b>'.implode(' ,',$condiments).'</b>';
				$nestedData['waiter_name'] =  getAssociateWaiteWithOrder($list);
				$nestedData['restro_name'] =  ucfirst($list->getAssociateRestro->name);
				$nestedData['order_final_price'] =  manageAmountFormat($list->order_final_price);
				$nestedData['status'] =  $list->status;
				$nestedData['order_by'] =  '';
				if($list->getAssociateUserForOrder->role_id == '11')
				{
					$nestedData['order_by'] =  '<span>Customer Name:'. ucfirst($list->getAssociateUserForOrder->name).'</span><span>Customer No:'.$list->getAssociateUserForOrder->phone_number.'</span>';
				}
				else
				{
					$nestedData['order_by'] =  '<span>Waiter Name:'. ucfirst($list->getAssociateUserForOrder->name).'</span>';
				}
                $data[] = $nestedData;
            }
        }

      $json_data = array(
                "draw"            => intval($request->input('draw')),  
                "recordsTotal"    => intval($totalData),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $data   
                );
        echo json_encode($json_data); 
    }

    public function myreceipt()
    {
        $print_type = 'D';
        $user_id = 1;
        $order_id = 126;
        $user_detail = User::whereId($user_id)->first();
        $order_detail = Order::whereId($order_id)->first();
        return view('admin.orders.receipt',compact('user_detail','order_detail','print_type'));
    }

    public function receipt(Request $request)
    {
        $title = 'Receipt';
        $user_id = $request->user_id;
        $print_type=$request->print_type;
        $order_id= $request->order_id;
        $receipt = printBill($user_id,$order_id,$print_type,'A');
        return $receipt;
    }

   
     public function postpadOrders()
    {

        $pmodule = 'open-orders';
        
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
             $logged_user_info = getLoggeduserProfile();
            $title = 'Orders';
            $model = $this->model;
            $lists = Order::where('order_type','POSTPAID')
                ->whereNotIn('status',['CANCLED','PENDING']);
               // ->doesnthave('getAssociateBillRelation');
            if($permission != 'superadmin')
            {
                $lists =    $lists->where('restaurant_id', $logged_user_info->restaurant_id);
            }

             $lists =    $lists->orderBy('id', 'desc')->get();
             //dd($lists);
            $breadcum = ['Postpaid Orders'=>'','Open Orders'=>''];
            return view('admin.orders.postpad',compact('title','lists','model','breadcum','permission','pmodule','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
       
    }


    public function getOrderUnderReceiptsForAll()
    {

        $pmodule = 'closed-orders-payment';
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            
            $title = 'Orders';
            $model = $this->model;
            $breadcum = ['Closed Orders'=>'','Closed Orders Payments'=>''];
            return view('admin.orders.order_receipts_payments',compact('title','model','breadcum','permission','pmodule','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
       
    }

    public function datatablesForgetClosedOrderPaypanets(Request $request)
    {
    	$pmodule = 'closed-orders-payment';
        $permission =  $this->mypermissionsforAModule();
    	$columns = array( 
                            0 =>'id', 
                           
                            1=> 'waiter_name',
                            2 =>'created_at',
                            3=> 'number_of_orders',
                            4=> 'total_amount',
                            5=> 'action',
                        );
        $totalData = OrderReceipt::count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = OrderReceipt::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 
            $receipts_ids = getAllRelatedReceiptsBywaiterName($search);
            $my_array= (object)['search'=>$search];
            $posts =  OrderReceipt::where('id','LIKE',"%{$search}%")
            			->orWhere('created_at', 'LIKE',"%{$search}%")
            			->orWhereIn('id', $receipts_ids)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
            $totalFiltered = OrderReceipt::where('id','LIKE',"%{$search}%")
            			->orWhere('created_at', 'LIKE',"%{$search}%")
            			->orWhereIn('id', $receipts_ids)
                        ->count();
        }
        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $list)
            {
                $total_bill = [];
                foreach($list->getAssociateOrdersWithReceipt as $single_order)
                {
                    $total_bill[] = $single_order->getAssociateOrderForReceipt->order_final_price;
                }
                $nestedData['id'] = $list->id;
                $nestedData['created_at'] = date('Y-m-d H:i',strtotime($list->created_at));
                $nestedData['waiter_name'] = getwaiterNameForReceipt($list->id);
                
                $nestedData['number_of_orders'] = count($list->getAssociateOrdersWithReceipt);
                $nestedData['total_amount'] = manageAmountFormat(array_sum($total_bill));
                $nestedData['action'] = '<span>
                                                <a title="View Payment summary" data-href="'.route('admin.get.payment.summary', $list->id).'" onclick="uploadOfferletter('. $list->id.' );"  data-toggle="modal" data-target="#application-approval" data-dismiss="modal"><i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
                                                </a>
                                                </span>';

                 if(isset($permission[$pmodule.'___reprint']) || $permission == 'superadmin')
                 {
                 	$nestedData['action'] =$nestedData['action'].'

                 	<span>
                                                <a title="Print Receipt" href="javascript:void(0)" onclick="printBill('.$list->id.')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;" id = "print_receipt_id_'.$list->id .'"></i>
                                                </a>
                                                </span>
                 	';
                 }
               
                $data[] = $nestedData;
            }
        }
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        echo json_encode($json_data); 
    }

    public function getpaymentsummaryByreceiptId($receipt_id)
    {
       $row =  OrderReceipt::whereId($receipt_id)->first();
        return view('admin.orders.payment_summary',compact('receipt_id','row')); 
    }


    public function getOrderUnderReceipts()
    {
       // echo 'here';die;
         if(Session::has('PRINTRECEIPT'))
        {

           $receipt_id = Session::get('PRINTRECEIPT');
           Session::forget('PRINTRECEIPT');

        }
        $pmodule = 'closed-orders';
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
           
            $title = 'Orders';
            $model = $this->model;
            $lists = OrderReceipt::orderBy('id', 'desc')
                    //->whereHas('getAssociateUserForReceipt', function ($query){ $query->where('role_id','4');})
                    ->get();
            //dd($lists);
            $breadcum = ['Postpaid Orders'=>'','Closed Orders'=>''];
            return view('admin.orders.order_receipts',compact('title','model','breadcum','permission','pmodule','logged_user_info','receipt_id'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
       
    }

    public function datatablesGetClosedOrders(Request $request)
    {

        $columns = array( 
                            0 =>'id', 
                            1 =>'created_at',
                            2=> 'waiter_name',
                            3=> 'cashier_name',
                            4=> 'number_of_orders',
                            5=> 'total_amount',
                            6=> 'action',
                        );
        $totalData = OrderReceipt::where('is_printed','0')
        ->whereDate('created_at','>=',date('Y-m-d')) 
        ->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = OrderReceipt::where('is_printed','0')
                        ->whereDate('created_at','>=',date('Y-m-d'))    
                        ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 
            $waiter_name_filter = function ($query) use($search) {  
                        $query->where('name', 'LIKE',"%{$search}%")->where('role_id','4');
                    };
            $cashier_name_filter = function ($query) use($search) {  
                        $query->where('name', 'LIKE',"%{$search}%");
                    };

                    $my_array= (object)['search'=>$search,'waiter_name_filter'=>$waiter_name_filter,'cashier_name_filter'=>$cashier_name_filter];
            $posts =  OrderReceipt::
                        where('is_printed','0')
                        ->whereDate('created_at','>=',date('Y-m-d')) 
                    ->where(function($query) use ($my_array){

                           $query->where('id','LIKE',"%{$my_array->search}%");
                            $query->orWhere('created_at', 'LIKE',"%{$my_array->search}%");
                           $query->orwhereHas('getAssociateUserForReceipt', $my_array->waiter_name_filter);
                           $query->orwhereHas('getAssociateCashierDetail', $my_array->cashier_name_filter);
                            })

           
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();



            $totalFiltered = OrderReceipt::where('is_printed','0')
                    ->whereDate('created_at','>=',date('Y-m-d')) 
                    ->where(function($query) use ($my_array){

                           $query->where('id','LIKE',"%{$my_array->search}%");
                            $query->orWhere('created_at', 'LIKE',"%{$my_array->search}%");
                           $query->orwhereHas('getAssociateUserForReceipt', $my_array->waiter_name_filter);
                           $query->orwhereHas('getAssociateCashierDetail', $my_array->cashier_name_filter);
                            })
                             ->count();
        }
        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $list)
            {
                $total_bill = [];
                foreach($list->getAssociateOrdersWithReceipt as $single_order)
                {
                    $total_bill[] = $single_order->getAssociateOrderForReceipt->order_final_price;
                }
                $nestedData['id'] = $list->id;
                $nestedData['created_at'] = date('Y-m-d H:i',strtotime($list->created_at));
                $nestedData['waiter_name'] = $list->getAssociateUserForReceipt->role_id==4?ucfirst($list->getAssociateUserForReceipt->name):'';
                $nestedData['cashier_name'] = $list->getAssociateCashierDetail?ucfirst($list->getAssociateCashierDetail->name):'-';
                $nestedData['number_of_orders'] = count($list->getAssociateOrdersWithReceipt);
                $nestedData['total_amount'] = manageAmountFormat(array_sum($total_bill));
                $nestedData['action'] = '';
                if($list->is_printed == '0')
                {
                    $action = '<span><a title="Print Receipt" href="javascript:void(0)" onclick="printBill(' .$list->id.')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;" id = "print_receipt_id_'.$list->id.'"></i>
                                            </a>
                                            </span>';
                    $nestedData['action'] = $action;
                }
                $data[] = $nestedData;
            }
        }
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        echo json_encode($json_data); 
    }

     public function multiplereceipt(Request $request)
    {
        $title = 'Receipt';
        $receipt_id = $request->receipt_id;
        $row = OrderReceipt::where('id', $receipt_id)->first();

        if(isset($request->printing_scrrent_type) && $request->printing_scrrent_type == 'closedorder')
        {
            $row->is_printed='1';
            $row->save();
        }
        $logged_user_info = getLoggeduserProfile();
        $user_detail = User::whereId($logged_user_info->id)->first();
        $orders = $row->getAssociateOrdersWithReceipt;

        $payment_methods =  $row->getAssociatePaymentsWithReceipt;

       // dd($payment_methods);
        return view('admin.orders.billwithoutheaderreceipt',compact('user_detail','orders','receipt_id','payment_methods'));
    }




    public function multiplebillreceipt(Request $request)
    {
        $title = 'Receipt';
        $bill_id = $request->bill_id;
        $row = Bill::where('id', $bill_id)->first();
		Bill::where('id', $bill_id)->update(["print_count"=>$row->print_count+1]);
        $logged_user_info = getLoggeduserProfile();
        $user_detail = User::whereId($logged_user_info->id)->first();
        $orders = $row->getAssociateOrdersWithBill;
        return view('admin.orders.billwithoutheaderreceipt',compact('user_detail','orders','bill_id'));
    }







     public function simplemultiplebillreceipt(Request $request)
    {
        $title = 'Receipt';
        $bill_id = $request->bill_id;
        $row = Bill::where('id', $bill_id)->first();
        $logged_user_info = getLoggeduserProfile();
        $user_detail = User::whereId($logged_user_info->id)->first();
        $orders = $row->getAssociateOrdersWithBill;
        return view('admin.orders.simplebillwithoutheaderreceipt',compact('user_detail','orders','bill_id'));
    }

    


    public function editPostpadOrders($slug)
    {
        try 
        {
            $pmodule = 'open-orders';
            $permission =  $this->mypermissionsforAModule();
            $title = 'Orders';
            $model = $this->model;
            if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row = Order::whereSlug($slug)->where('order_type','POSTPAID')
                 //->doesnthave('getAssociateBillRelation')
                ->first();
                if($row)
                {
                    $breadcum = [$title=>route($model.'.index'),'Postpad'=>route('admin.postpad.orders'),manageOrderidWithPad($row->id)=>''];
                return view('admin.orders.editpostpad',compact('title','row','model','breadcum'));   
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
        catch (\Exception $e) 
        {
            $msg = $e->getMessage();
            Session::flash('danger',$msg);
            return redirect()->back();
        }  
    } 

    public function updatePostpadOrders(Request $request,$slug)
    {
        try 
        {

            $row = Order::whereSlug($slug)
            ->where('order_type','POSTPAID')
            //->doesnthave('getAssociateBillRelation')
            ->first();
            if($row)
            {

                $logged_user_info = getLoggeduserProfile();
       

                $row->admin_discount_in_percent = $request->admin_discount_in_percent;
                $row->discount_reason = isset($request->discount_reason)?$request->discount_reason:null;


                $total_price = [];
                foreach($row->getAssociateItemWithOrder as $item)
                {
                    if($item->item_delivery_status !='CANCLED' && !$item->order_offer_id)
                    {
                        $total_price[] = $item->price*$item->item_quantity;
                    }
                }

                foreach($row->getAssociateOffersWithOrder as $offer)
                {
                    $total_price[] = $offer->price*$offer->quantity;
                }

                $sum_of_total_price = array_sum($total_price);

                if($request->admin_discount_in_percent>0)
                {
                    if($sum_of_total_price>0)
                    {
                        $percent = ($request->admin_discount_in_percent*$sum_of_total_price)/100;
                        $discount_percent = [[
                        'discount_title'=>'Promotion Discount',
                        'discount_value'=>$request->admin_discount_in_percent,
                        'discount_format'=>'PERCENTAGE',
                        'discount_amount'=>round($percent,2)
                        ]];
                        $row->order_discounts =  json_encode($discount_percent);
                        $row->order_final_price =  round($sum_of_total_price-$percent,2);
                    }

                    $row->discounting_user_id = $logged_user_info->id;
                }

                 if($request->admin_discount_in_percent==0)
                 {
                    $row->order_discounts =  null;
                    $row->order_final_price =  round($sum_of_total_price,2);
                    $row->discounting_user_id = null;
                     $row->discount_reason = null;
                 }


               
                $row->save();
             Session::flash('success','Record updated successfully');
                return redirect()->route('admin.postpad.orders'); 
            }
             else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            } 
           
        } 
        catch (\Exception $e) 
        {
            $msg = $e->getMessage();
            Session::flash('danger',$msg);
            return redirect()->back();
        }
    } 


    public function getGenerateBills(Request $request)
    {
        $pmodule = 'generate-bills';
        $permission =  $this->mypermissionsforAModule();
        $waiter_id = '';
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
             $logged_user_info = getLoggeduserProfile();
             $title = 'Orders';
/*
            $model = $this->model;
            $lists = Order::where('order_type','POSTPAID')
                ->where('status','COMPLETED')
                ->doesnthave('getAssociateBillRelation'); 
            if($permission != 'superadmin')
            {
                $lists =    $lists->where('restaurant_id', $logged_user_info->restaurant_id);
            }
            $all_orders = $lists->pluck('id')->toArray();  
*/

            $lists = Bill::with('getAssociateOrdersWithBill','getAssociateOrdersWithBill.getAssociateOrderForBill.getAssociateItemWithOrder')->where('status','PENDING'); 
            $all_users = $lists->pluck('user_id')->toArray();  
//            $waiter_info = $this->getwaitersdetailsbyorderidarray($all_orders); 
            $waiter_info = $this->getwaitersdetailsbyuseridarray($all_users); 

            if ($request->has('waiter-id'))
            {
	               $waiter_id = $request->input('waiter-id');
	            $lists = $lists->where('user_id',$waiter_id);
                $lists =  $lists->orderBy('id', 'desc')->get();
            }
            else
            {
               $lists = null; 
            }

            //   echo "<pre>"; print_r($lists); die;
           
            
           
            $breadcum = ['Bills'=>'','Generate Bills'=>''];
            return view('admin.orders.generatebills',compact('title','lists','model','breadcum','permission','pmodule','logged_user_info','waiter_info','waiter_id'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
       
    }

    public function postGenerateBills(Request $request,$waiter_id)
    {
        $rows = [];
        $orders = array_except($request->all(), ['_token']);
      //  echo "<pre>"; print_r($orders); die;
        foreach($orders as $key=>$data)
        {
            $detail = explode('___',$key);
            if(count($detail) ==  2 )
            {
                $rows[] = $detail[1];
            }
        }
//       echo "<pre>"; print_r($rows); die;

        if(count($rows)>0)
        {
            $lists_count = Bill::select(['order_id'])
                ->where('status','PENDING')
                ->whereIn('id',$rows)
                ->count();
           //     echo count($rows)." - ".$lists_count; die;
            if(count($rows) == $lists_count)
            {
                //generatebill
                
                $ordersId = BillOrderRelation::whereIn('bill_id',$rows)->pluck("order_id");
                
                $tableId = OrderBookedTable::where('order_id',$ordersId[0])->first()->table_id;

                //echo $tableId; die;
                $new_bill = new Bill();
                $new_bill->user_id = $waiter_id;
                $new_bill->slug = rand(1111,99999).strtotime(date('Y-m-d h:i:s'));
                $new_bill->save();
                $bill_id = $new_bill->id;

                Bill::whereIn('id',$rows)->update(["status"=>"COMPLETED"]);

                foreach($ordersId as $order_id)
                {
                    BillOrderRelation::updateOrCreate(
                            ['bill_id' => $bill_id,'order_id'=>$order_id]
                            );  
                         OrderBookedTable::where('order_id',$order_id)->update(["table_id"=>$tableId]);

                 }
	 	        $saveActivity = [];
	 	        
                $tableIds = OrderBookedTable::whereIn('order_id',$ordersId)->pluck('table_id')->toArray();
              //  echo "<pre>"; print_r($tableIds); die;
		        $saveActivity['bill_id'] = $bill_id;
		        $saveActivity['user_id'] = 0;
		        $saveActivity['trans_type'] = "Combining Bill";
		        $saveActivity['table_no'] = $tableId;//implode(',', $tableIds);
		        $saveActivity['order_id'] = "";
		        $saveActivity['receipt_id'] = "";//$receipt_id;
		        $saveActivity['old_bill_id'] = implode(',', $rows);
		        $saveActivity['old_table_no'] = implode(',', $tableIds);
		        Bill::saveActivity($saveActivity);

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
               /* print_r($rows);
                die;*/
       
    }


    public function postGenerateBillsOLD(Request $request,$slug)
    {
/*
        if((isset($request->ordered_item_id) && count($request->ordered_item_id)>0) || (isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0))
        {
*/
         //   $fromBill = Bill::where('slug',$request->from_bill_slug)->where('status','PENDING')->first();
            $toBill = Bill::where('slug',$request->to_bill_slug)->where('status','PENDING')->first();
            if($toBill)
            {
               
                if(count($toBill->getAssociateOrdersWithBill)>0)
                {
                    $firstOrderForToBill = $toBill->getAssociateOrdersWithBill->first();
                    $firstOrderDetail = $firstOrderForToBill->getAssociateOrderForBill;
                    $table_id = $firstOrderDetail->getAssociateTableWithOrder->first()->table_id;
                    $table_assignment = EmployeeTableAssignment::where('table_manager_id',$table_id)->first();
                    if($table_assignment)
                    {
                        $user_id = $table_assignment->user_id;
                        $new_order = new Order();
                        $new_order->user_id = $user_id;
                        $new_order->restaurant_id = $firstOrderDetail->restaurant_id;
                        $new_order->final_comment = '';
                        $new_order->order_final_price = '0';
                        $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                        $new_order->order_type = 'POSTPAID';
                        $new_order->status = 'COMPLETED';
                        $new_order->total_guests = 1;
                        $new_order->order_charges = null;
                        $new_order->payment_mode = 'NA';
                        $new_order->save();
                        $new_order_id = $new_order->id;
                        $inserting_array = [];
                        $inner_array = [
                        'order_id'=>$new_order_id,
                        'table_id'=>$table_id
                        ];
                        $inserting_array[] = $inner_array;
                        OrderBookedTable::insert($inserting_array);

                        $bill_relation = new BillOrderRelation();
                        $bill_relation->bill_id = $toBill->id;
                        $bill_relation->order_id = $new_order_id;
                        $bill_relation->save();



                        if(isset($request->ordered_item_id) && count($request->ordered_item_id)>0)
                        {
                            //transfer orders item
                            $all_item_ids =   array_keys($request->ordered_item_id);

                            $allOrdersForUpdate= [$new_order_id];
                            foreach($all_item_ids as $ordered_item_id)
                            {
                                $original = 'original_qty_'.$ordered_item_id;
                                $selected = 'selected_qty_'.$ordered_item_id;
                                $relatedOrderItem =  OrderedItem::where('id',$ordered_item_id)->first();
                                $relatedOrderId = $relatedOrderItem->order_id;
                                $allOrdersForUpdate[] = $relatedOrderId;
                                if($request->$original == $request->$selected )
                                {
                                     OrderedItem::where('id',$ordered_item_id)->update(['order_id'=>$new_order_id]);
                                }
                                else
                                {
                                    $oldOrderedItem = OrderedItem::where('id',$ordered_item_id)->first();
                                    //create new ordereditem
                                    $leftQuantity = $request->$original-$request->$selected;
                                    $newOrderItem = new OrderedItem();
                                    $newOrderItem->order_id = $new_order_id;
                                    $newOrderItem->food_item_id = $oldOrderedItem->food_item_id;
                                    $newOrderItem->order_offer_id = $oldOrderedItem->order_offer_id;
                                    $newOrderItem->restaurant_id = $oldOrderedItem->restaurant_id;
                                    $newOrderItem->price = $oldOrderedItem->price;
                                    $newOrderItem->item_title = $oldOrderedItem->item_title;
                                    $newOrderItem->item_comment = $oldOrderedItem->item_comment;
                                    $newOrderItem->item_quantity = $request->$selected;
                                    $newOrderItem->condiments_json = $oldOrderedItem->condiments_json;
                                    $newOrderItem->print_class_id = $oldOrderedItem->print_class_id;
                                    $newOrderItem->item_delivery_status = $oldOrderedItem->item_delivery_status;
                                    $newOrderItem->created_at = $oldOrderedItem->created_at;
                                    $newOrderItem->updated_at = $oldOrderedItem->updated_at;
                                    $newOrderItem->billing_time = $oldOrderedItem->billing_time;
                                    $oldOrderedItem_item_charges = json_decode($oldOrderedItem->item_charges);
                                    if(is_array($oldOrderedItem_item_charges) && count($oldOrderedItem_item_charges)>0)
                                    {
                                        foreach($oldOrderedItem_item_charges as $charges)
                                        {
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                            if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                                            {
                                                $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$new_amount;

                                                $newCgareAmount = $spillted_chared_amount*$leftQuantity;
                                                $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$newCgareAmount;
                                            }
                                            else
                                            {
                                                $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_amount;

                                                $newCgareAmount = $spillted_chared_amount*$leftQuantity;

                                                $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $newCgareAmount;
                                            }
                                        }
                                        $newOrderItem->item_charges =  json_encode(array_values($charges_arr));
                                        $oldOrderedItem->item_charges =  json_encode(array_values($new_charges_arr));
                                    }
                                    $oldOrderedItem->item_quantity = $leftQuantity;
                                    $oldOrderedItem->save();
                                    $newOrderItem->save();
                                }
                            }
                            $this->updateOrderStatsAfterTransfer($allOrdersForUpdate);
                           // $this->manageFromBill($fromBill->id);
                            Session::flash('success', 'Bill transferred successfully');
                            return redirect()->route('admin.master-bills');  
                        }
                    }
                    else
                    {
                        Session::flash('warning', 'Table not found');
                        return redirect()->back();  
                    }
                    
                }
                else
                {
                    Session::flash('warning', 'Do not have any order');
                    return redirect()->back();
                }
    
            }
            else
            {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
           
/*
        }
        else
        {
            Session::flash('warning', 'Please select item');
            return redirect()->back(); 
        }
*/
    }


    public function cancleBill($bill_slug)
    {
        try
        {
            $bill = Bill::where('status','PENDING')->where('slug',$bill_slug)->get(); 
            if($bill)
            {
                Bill::whereSlug($bill_slug)->delete();
                Session::flash('success','Bill delete successfully');
                return redirect()->back();
            }
            else
            {
                $msg = 'Invalid request';
                Session::flash('danger',$msg);
                return redirect()->back();
            }
        }
        catch (\Exception $e) 
        {
            $msg = 'Invalid request';
            Session::flash('danger',$msg);
            return redirect()->back();
        } 
    }


    public function getMasterBills(Request $request)
    {

        $pmodule = 'master-bills';
        $permission =  $this->mypermissionsforAModule();
       
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            
             $title = 'Orders';
            $model = $this->model;
            $lists = Bill::with(['getAssociateOrdersWithBill.getAssociateOrderForBill'])->where('status','PENDING'); 
            $lists =  $lists->orderBy('id', 'desc')->get();
           // echo "<pre>"; print_r($lists); die;
            $logged_user_info = getLoggeduserProfile();
            $breadcum = ['Bills'=>'','Master Bills'=>''];
            return view('admin.orders.masterBills',compact('title','lists','model','breadcum','permission','pmodule','lists','logged_user_info'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function getMasterBillsOrders($billid)
    {

        $pmodule = 'master-bill-orders';
        $permission =  $this->mypermissionsforAModule();
       // if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $title = 'Orders';
            $model = $this->model;
            $orderids = BillOrderRelation::where('bill_id',$billid)->pluck('order_id')->toArray();
            $lists = OrderedItem::whereIn('order_id', $orderids);
            $lists =  $lists->orderBy('id', 'desc')->get();
          //  echo "<pre>"; print_r($lists); die;
            $logged_user_info = getLoggeduserProfile();
            $breadcum = ['Master Bills' => route('admin.master-bills'), 'Master Bill Orders' => ''];
            return view('admin.orders.masterBillOrders', compact('title', 'lists', 'model', 'breadcum', 'permission', 'pmodule', 'lists', 'logged_user_info'));
    //    } else {
      //      Session::flash('warning', 'Invalid Request');
     //       return redirect()->back();
     //  }
    }


      public function voidItemsFromBill($slug)
    {
        $fromBill = Bill::where('slug',$slug)->where('status','PENDING')->first();
         $pmodule = 'master-bills';

       
        if($fromBill)
        {
             $title = 'Orders';
            $model = $this->model;
         
              $breadcum = ['Master Bills'=>route('admin.master-bills'),'Make void items from bill'=>''];
            return view('admin.orders.voiditemsfrombill',compact('title','lists','model','breadcum','permission','pmodule','lists','logged_user_info','fromBill'));


        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function postvoidItemsFromBill(Request $request, $slug)
    {
	    echo "<pre>"; print_r($request->all()); die;
        if((isset($request->ordered_item_id) && count($request->ordered_item_id)>0) || (isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0))
        {
            $fromBill = Bill::where('slug',$slug)->where('status','PENDING')->first();
            if($fromBill)
            {
                $firstOrderForToBill = $fromBill->getAssociateOrdersWithBill->first();
                $firstOrderDetail = $firstOrderForToBill->getAssociateOrderForBill;
                $table_id = $firstOrderDetail->getAssociateTableWithOrder->first()->table_id;
                $table_assignment = EmployeeTableAssignment::where('table_manager_id',$table_id)->first();
                if($table_assignment)
                {
                    $user_id = $table_assignment->user_id;
                    $new_order = new Order();
                    $new_order->user_id = $user_id;
                    $new_order->restaurant_id = $firstOrderDetail->restaurant_id;
                    $new_order->final_comment = '';
                    $new_order->order_final_price = '0';
                    $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                    $new_order->order_type = 'POSTPAID';
                    $new_order->status = 'CANCLED';
                    $new_order->total_guests = 1;
                    $new_order->order_charges = null;
                    $new_order->payment_mode = 'NA';
                    $new_order->order_cancle_reason  = $request->void_reason;
                    $new_order->order_canceled_by_user = getLoggeduserProfile()->id;
                    $new_order->save();
                    $new_order_id = $new_order->id;
                    $inserting_array = [];
                    $inner_array = [
                    'order_id'=>$new_order_id,
                    'table_id'=>$table_id
                    ];
                    $inserting_array[] = $inner_array;
                    OrderBookedTable::insert($inserting_array);

                    if(isset($request->ordered_item_id) && count($request->ordered_item_id)>0)
                    {
                        //transfer orders item
                        $all_item_ids =   array_keys($request->ordered_item_id);
                        $allOrdersForUpdate= [$new_order_id];
                        foreach($all_item_ids as $ordered_item_id)
                        {
                            $original = 'original_qty_'.$ordered_item_id;
                            $selected = 'selected_qty_'.$ordered_item_id;
                            $relatedOrderItem =  OrderedItem::where('id',$ordered_item_id)->first();
                            $relatedOrderId = $relatedOrderItem->order_id;
                            $allOrdersForUpdate[] = $relatedOrderId;
                            if($request->$original == $request->$selected )
                            {
                                 OrderedItem::where('id',$ordered_item_id)->update(['order_id'=>$new_order_id]);
                            }
                            else
                            {
                                $oldOrderedItem = OrderedItem::where('id',$ordered_item_id)->first();
                                //create new ordereditem
                                $leftQuantity = $request->$original-$request->$selected;
                                $newOrderItem = new OrderedItem();
                                $newOrderItem->order_id = $new_order_id;
                                $newOrderItem->food_item_id = $oldOrderedItem->food_item_id;
                                $newOrderItem->order_offer_id = $oldOrderedItem->order_offer_id;
                                $newOrderItem->restaurant_id = $oldOrderedItem->restaurant_id;
                                $newOrderItem->price = $oldOrderedItem->price;
                                $newOrderItem->item_title = $oldOrderedItem->item_title;
                                $newOrderItem->item_comment = $oldOrderedItem->item_comment;
                                $newOrderItem->item_quantity = $request->$selected;
                                $newOrderItem->condiments_json = $oldOrderedItem->condiments_json;
                                $newOrderItem->print_class_id = $oldOrderedItem->print_class_id;
                                $newOrderItem->item_delivery_status = $oldOrderedItem->item_delivery_status;
                                $newOrderItem->created_at = $oldOrderedItem->created_at;
                                $newOrderItem->updated_at = $oldOrderedItem->updated_at;
                                $newOrderItem->billing_time = $oldOrderedItem->billing_time;
                                $oldOrderedItem_item_charges = json_decode($oldOrderedItem->item_charges);
                                if(is_array($oldOrderedItem_item_charges) && count($oldOrderedItem_item_charges)>0)
                                {
                                    foreach($oldOrderedItem_item_charges as $charges)
                                    {
                                        $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                        $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                        $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                        $new_charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                        $new_charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                        $new_charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                        if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                                        {
                                            $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                            $new_amount = $spillted_chared_amount*$request->$selected;
                                            $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$new_amount;

                                            $newCgareAmount = $spillted_chared_amount*$leftQuantity;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$newCgareAmount;
                                        }
                                        else
                                        {
                                            $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                            $new_amount = $spillted_chared_amount*$request->$selected;
                                            $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_amount;

                                            $newCgareAmount = $spillted_chared_amount*$leftQuantity;

                                            $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $newCgareAmount;
                                        }
                                    }
                                        $newOrderItem->item_charges =  json_encode(array_values($charges_arr));
                                        $oldOrderedItem->item_charges =  json_encode(array_values($new_charges_arr));
                                }
                                    $oldOrderedItem->item_quantity = $leftQuantity;
                                    $oldOrderedItem->save();
                                    $newOrderItem->save();
                            }
                        }
                        $this->updateOrderStatsAfterTransfer($allOrdersForUpdate);
                        $this->manageFromBill($fromBill->id);
                        Session::flash('success', 'Item moved to void successfully');
                        return redirect()->route('admin.master-bills');  
                    }
                }
                else
                {
                    Session::flash('warning', 'Table not found');
                    return redirect()->back();  
                }
            }
            else
            {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }

        }
        else
        {
            Session::flash('warning', 'Please select item');
            return redirect()->back(); 
        }
    }

    public function getTransferBillToOrder($slug)
    {
        $bill = Bill::where('slug',$slug)->where('status','PENDING')->first();
         $title = 'Orders';
            $model = $this->model;
        if($bill)
        {
            $all_left_bill = Bill::where('status','PENDING')->where('id','!=',$bill->id)->pluck('id','id')->toArray();
            $breadcum = ['Master Bills'=>route('admin.master-bills'),'Transfer Bill To Order'=>''];

            return view('admin.orders.transferbilltoorder',compact('title','lists','model','breadcum','permission','pmodule','lists','logged_user_info','bill','all_left_bill'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getTransferBillToOrderRequest(Request $request,$slug)
    {
        $fromBill = Bill::where('slug',$slug)->where('status','PENDING')->first();
        $toBill = Bill::where('id',$request->input('to_bill_id'))->where('status','PENDING')->first();

       
        if($fromBill && $toBill)
        {
             $title = 'Orders';
            $model = $this->model;
          //  dd($fromBill->getAssociateOrdersWithBill);
              $breadcum = ['Master Bills'=>route('admin.master-bills'),'Transfer Bill To order'=>''];
            return view('admin.orders.transferbillfrombill',compact('title','lists','model','breadcum','permission','pmodule','lists','logged_user_info','fromBill','toBill'));


        }
        else
        {
             Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function postTransferBillToOrderRequest(Request $request,$slug)
    {
	 //   echo "<pre>"; print_r($request->all()); die;
        if((isset($request->ordered_item_id) && count($request->ordered_item_id)>0) || (isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0))
        {
            $fromBill = Bill::where('slug',$request->from_bill_slug)->where('status','PENDING')->first();
            $toBill = Bill::where('slug',$request->to_bill_slug)->where('status','PENDING')->first();
            if($fromBill && $toBill)
            {
               
                if(count($toBill->getAssociateOrdersWithBill)>0)
                {
                    $firstOrderForToBill = $toBill->getAssociateOrdersWithBill->first();
                    $firstOrderDetail = $firstOrderForToBill->getAssociateOrderForBill;
                    $table_id = $firstOrderDetail->getAssociateTableWithOrder->first()->table_id;
                    $table_assignment = EmployeeTableAssignment::where('table_manager_id',$table_id)->first();
                    if($table_assignment)
                    {
                        $user_id = $table_assignment->user_id;
                        $new_order = new Order();
                        $new_order->user_id = $user_id;
                        $new_order->restaurant_id = $firstOrderDetail->restaurant_id;
                        $new_order->final_comment = '';
                        $new_order->order_final_price = '0';
                        $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                        $new_order->order_type = 'POSTPAID';
                        $new_order->status = 'COMPLETED';
                        $new_order->total_guests = 1;
                        $new_order->order_charges = null;
                        $new_order->payment_mode = 'NA';
                        $new_order->save();
                        $new_order_id = $new_order->id;
                        $inserting_array = [];
                        $inner_array = [
                        'order_id'=>$new_order_id,
                        'table_id'=>$table_id
                        ];
                        $inserting_array[] = $inner_array;
                        OrderBookedTable::insert($inserting_array);

                        $bill_relation = new BillOrderRelation();
                        $bill_relation->bill_id = $toBill->id;
                        $bill_relation->order_id = $new_order_id;
                        $bill_relation->save();



                        if(isset($request->ordered_item_id) && count($request->ordered_item_id)>0)
                        {
                            //transfer orders item
                            $all_item_ids =   array_keys($request->ordered_item_id);

                            $allOrdersForUpdate= [$new_order_id];
                            foreach($all_item_ids as $ordered_item_id)
                            {
                                $original = 'original_qty_'.$ordered_item_id;
                                $selected = 'selected_qty_'.$ordered_item_id;
                                $relatedOrderItem =  OrderedItem::where('id',$ordered_item_id)->first();
                                $relatedOrderId = $relatedOrderItem->order_id;
                                $allOrdersForUpdate[] = $relatedOrderId;
                                if($request->$original == $request->$selected )
                                {
                                     OrderedItem::where('id',$ordered_item_id)->update(['order_id'=>$new_order_id]);
                                }
                                else
                                {
                                    $oldOrderedItem = OrderedItem::where('id',$ordered_item_id)->first();
                                    //create new ordereditem
                                    $leftQuantity = $request->$original-$request->$selected;
                                    $newOrderItem = new OrderedItem();
                                    $newOrderItem->order_id = $new_order_id;
                                    $newOrderItem->food_item_id = $oldOrderedItem->food_item_id;
                                    $newOrderItem->order_offer_id = $oldOrderedItem->order_offer_id;
                                    $newOrderItem->restaurant_id = $oldOrderedItem->restaurant_id;
                                    $newOrderItem->price = $oldOrderedItem->price;
                                    $newOrderItem->item_title = $oldOrderedItem->item_title;
                                    $newOrderItem->item_comment = $oldOrderedItem->item_comment;
                                    $newOrderItem->item_quantity = $request->$selected;
                                    $newOrderItem->condiments_json = $oldOrderedItem->condiments_json;
                                    $newOrderItem->print_class_id = $oldOrderedItem->print_class_id;
                                    $newOrderItem->item_delivery_status = $oldOrderedItem->item_delivery_status;
                                    $newOrderItem->created_at = $oldOrderedItem->created_at;
                                    $newOrderItem->updated_at = $oldOrderedItem->updated_at;
                                    $newOrderItem->billing_time = $oldOrderedItem->billing_time;
                                    $oldOrderedItem_item_charges = json_decode($oldOrderedItem->item_charges);
                                    if(is_array($oldOrderedItem_item_charges) && count($oldOrderedItem_item_charges)>0)
                                    {
                                        foreach($oldOrderedItem_item_charges as $charges)
                                        {
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;
                                            if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                                            {
                                                $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$new_amount;

                                                $newCgareAmount = $spillted_chared_amount*$leftQuantity;
                                                $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$newCgareAmount;
                                            }
                                            else
                                            {
                                                $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_amount;

                                                $newCgareAmount = $spillted_chared_amount*$leftQuantity;

                                                $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $newCgareAmount;
                                            }
                                        }
                                        $newOrderItem->item_charges =  json_encode(array_values($charges_arr));
                                        $oldOrderedItem->item_charges =  json_encode(array_values($new_charges_arr));
                                    }
                                    $oldOrderedItem->item_quantity = $leftQuantity;
                                    $oldOrderedItem->save();
                                    $newOrderItem->save();
                                }
                            }
                            $this->updateOrderStatsAfterTransfer($allOrdersForUpdate);
                            $this->manageFromBill($fromBill->id);
                            Session::flash('success', 'Bill transferred successfully');
                            return redirect()->route('admin.master-bills');  
                        }
                    }
                    else
                    {
                        Session::flash('warning', 'Table not found');
                        return redirect()->back();  
                    }
                    
                }
                else
                {
                    Session::flash('warning', 'Do not have any order');
                    return redirect()->back();
                }
    
            }
            else
            {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
           
        }
        else
        {
            Session::flash('warning', 'Please select item');
            return redirect()->back(); 
        }
    }


    public function manageFromBill($bill_id)
    {
        $toBill = Bill::where('id',$bill_id)->first();
        if(isset($toBill->getAssociateOrdersWithBill) && count($toBill->getAssociateOrdersWithBill)>0)
        {
           $related_orders =  $toBill->getAssociateOrdersWithBill;

           $can_delete_bill = 'yes';
           foreach($related_orders as $orderRelation)
           {
            $order =  $orderRelation->getAssociateOrderForBill;
            $orderedItems = OrderedItem::where('order_id',$order->id)->get();
           // dd($orderedItems);
            if(count($orderedItems))
            {
                $can_delete_bill = 'no';
            }
            else
            {
                $update_order = Order::where('id',$order->id)->first();
                $update_order->order_charges =  null;
                $update_order->order_final_price = 0;
                $update_order->status = 'CANCLED';
                BillOrderRelation::where('order_id',$order->id)->delete();
            }
            
           }
           if($can_delete_bill == 'yes')
           {
            Bill::where('id',$toBill->id)->delete();
           }
           

           
        }
    }


    public function getMarkBillCashReceipt(Request $request,$bill_slug)
    {
        $pmodule = 'master-bills';
        $permission =  $this->mypermissionsforAModule();
       
        if(isset($permission[$pmodule.'___close']) || $permission == 'superadmin')
        {
            
             $title = 'Orders';
            $model = $this->model;
            $bill = Bill::where('status','PENDING')->where('slug',$bill_slug)->first(); 
            if($bill)
            {
                $breadcum = ['Bills'=>'','Master Bills'=>route('admin.master-bills'),'Cash Receipt For Bill'.$bill->id=>''];

                $payment_mode= PaymentMethod::pluck('title','id')->toArray();

                //$payment_mode = ['MPESA'=>'MPESA','CASH'=>'CASH','CHEQUE'=>'CHEQUE','CARD'=>'CARD'];
                return view('admin.orders.cashreceipt',compact('title','bill','model','breadcum','permission','pmodule','payment_mode'));

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

    public function postMarkBillCashReceipt(Request $request,$bill_slug) {

       // dd('here');

       
        $bill = Bill::where('status','PENDING')->where('slug',$bill_slug)->first(); 
        if($bill) {
            //$this->saveDebitAndCreditPayment($bill->id); die('com');
            $payment_mode= PaymentMethod::pluck('title','id')->toArray();
            $logged_user_info = getLoggeduserProfile();
           
            $bill->status = 'COMPLETED';
            $bill->save();
            $receipt = new OrderReceipt();
            $receipt->user_id = $bill->user_id;
            $receipt->cashier_id = $logged_user_info->id;
            
            $receipt->save();
            
             $receipt_id = $receipt->id;
             $orderIds = [];
             $tableIds = [];
             foreach($bill->getAssociateOrdersWithBill as $k=> $bill_orders)
            {
                OrderReceiptRelation::updateOrCreate(
                        ['order_receipt_id' => $receipt_id,'order_id'=>$bill_orders->order_id]
                        );  
                $order = Order::whereId($bill_orders->order_id)->first();

                $orderIds[$k] = $order->id;
                $tableIds[$k] = $order->getAssociateTableWithOrder[0]->table_id;
                
                $order->order_type = 'PREPAID';
                $order->status = 'COMPLETED';
                $order->save();
                OrderedItem::where('order_id',$bill_orders->order_id)->update(['item_delivery_status'=>'COMPLETED']);
				$this->updateManageStockMoves($bill_orders->order_id);
            }

            //billing info
            foreach($request->billing_info as $keys =>$billing)
            {
                if($billing['amount']>0)
                {
                   $billreceipt =  new ReceiptSummaryPayment();
                   $billreceipt->order_receipt_id = $receipt_id;
                   $billreceipt->payment_mode = $payment_mode[$keys];
                   $billreceipt->restaurant_id = ($bill->getAssociateUserForBill->restaurant_id) ? $bill->getAssociateUserForBill->restaurant_id : '';
                   $billreceipt->narration = $billing['narration'];
                   $billreceipt->amount = (float)$billing['amount'];
                   $billreceipt->save();
                }
                
            }

	        $saveActivity = [];
	        $saveActivity['bill_id'] = $bill->id;
	        $saveActivity['user_id'] = 0;
	        $saveActivity['trans_type'] = "Closing Bill";
	        $saveActivity['table_no'] = implode(',', $tableIds);
	        $saveActivity['order_id'] = implode(',', $orderIds);
	        $saveActivity['receipt_id'] = $receipt_id;
	        $saveActivity['old_bill_id'] = "";
	        $saveActivity['old_table_no'] = "";
	        Bill::saveActivity($saveActivity);


            $my_receipt = OrderReceipt::whereId($receipt_id)->first();
            $this->managetimeForall($receipt_id,$my_receipt->created_at);
            $this->manageItemSalesWithFamilyGroup($bill->id);
            $this->savePaymentData($receipt_id);
          //  $this->saveDebitAndCreditPayment($bill->id);


            $print_receipt_flag = $request->print_receipt_flag;

            if($print_receipt_flag=='yes')
            {
                Session::put('PRINTRECEIPT', $receipt->id);
                return redirect()->route('admin.order-receipts');
            }
            
            Session::flash('success', 'Bill updated successfully');
            return redirect()->route('admin.master-bills');
        }
        else {
            Session::flash('warning', 'Invalid request');
            return redirect()->back();
        }
    }
    

        protected function updateManageStockMoves($order_id){
            $order = Order::with('getAssociateItemWithOrder.getAssociateFooditem')
                ->where('id', $order_id)
                ->first();
            
            $order_items = $order->getAssociateItemWithOrder;
            foreach($order_items as $order_item_key => $order_item_row){
                if($order_item_row->getAssociateFooditem->getAssociateRecipe)
                {
                $recipe_row = $order_item_row->getAssociateFooditem->getAssociateRecipe;
                $wa_location_and_store_id = $recipe_row->wa_location_and_store_id;
                $recipe_ingredients = $order_item_row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient;
                foreach($recipe_ingredients as $key => $recipe_ingredient_row) {
                    $series_module = WaNumerSeriesCode::where('module','GRN')->first();
                    $intr_smodule = WaNumerSeriesCode::where('module', 'INGREDIENT_BOOKING')->first();
                    $dateTime = date('Y-m-d H:i:s');
                    $grn_number = getCodeWithNumberSeriesBillClose('INGREDIENT_BOOKING');
                    $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
                    $inventory_item_row = $recipe_ingredient_row->getAssociateItemDetail;
                    $inventory_qoh = getItemAvailableQuantity($inventory_item_row->stock_id_code, $wa_location_and_store_id);
                    $weight = $recipe_ingredient_row->weight;
                    $craccountno = $inventory_item_row->getInventoryCategoryDetail->getStockGlDetail->account_code;

                    $draccountno = $inventory_item_row->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                    
                        $deficient_quantity = $weight - $inventory_qoh;
                        $stockMove = new WaStockMove();
                        $stockMove->user_id = $order->user_id;
                        $stockMove->ordered_item_id = $order_item_row->id;
                        $stockMove->restaurant_id = $order_item_row->restaurant_id;
                        $stockMove->wa_location_and_store_id = $wa_location_and_store_id;
                        $stockMove->wa_inventory_item_id = $inventory_item_row->id;
                        $stockMove->standard_cost = $inventory_item_row->standard_cost;
                        $stockMove->document_no = $order_id;
                        $stockMove->qauntity = -($weight * $order_item_row->item_quantity);
                        $stockMove->stock_id_code = $inventory_item_row->stock_id_code;
                        $stockMove->grn_type_number = $series_module->type_number;
                        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                        $stockMove->price = $recipe_ingredient_row->cost;
                        $stockMove->refrence = $order->slug;
                        $stockMove->save();

                        // $dr =  new WaGlTran();
                        // $dr->grn_type_number = $series_module->type_number;
                        // $dr->grn_last_used_number = $series_module->last_number_used;
                        // $dr->transaction_type = $intr_smodule->description;
                        // $dr->transaction_no =  $grn_number;
                        // $dr->trans_date = $dateTime;
                        // $dr->restaurant_id = $order_item_row->restaurant_id;
                        // $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        // $dr->account = $craccountno;
                        // $dr->reference = $order_id;
                        // $dr->amount = '-' . ( $recipe_ingredient_row->cost * $order_item_row->item_quantity);
                        // $dr->narrative = $order->slug . '/' . $inventory_item_row->stock_id_code . '/' . $inventory_item_row->title . '/' . $recipe_ingredient_row->cost . '@' . $order_item_row->item_quantity;
                        // $dr->save();



                        // $dr =  new WaGlTran();
                        // $dr->grn_type_number = $series_module->type_number;
                        // $dr->grn_last_used_number = $series_module->last_number_used;
                        // $dr->transaction_type = $intr_smodule->description;
                        // $dr->transaction_no =  $grn_number;
                        // $dr->trans_date = $dateTime;
                        // $dr->restaurant_id = $order_item_row->restaurant_id;
                        // $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        // $dr->account = $draccountno;
                        // $dr->reference = $order_id;
                        // $camount = $recipe_ingredient_row->cost * $order_item_row->item_quantity;
                        // $dr->amount = $camount;
                        // $dr->narrative = $order->slug . '/' . $inventory_item_row->stock_id_code . '/' . $inventory_item_row->title . '/' . $recipe_ingredient_row->cost . '@' . $order_item_row->item_quantity;
                        // $dr->save();



                    
                }
            }
            }
            return;
        }
    
    
    public function savePaymentData($receipt_id){
        $receipt_summary_details = ReceiptSummaryPayment::where('order_receipt_id',$receipt_id)->get();
        if(count($receipt_summary_details) >0){

            foreach($receipt_summary_details as $paySummary)
            {
                $payment_method_row = PaymentMethod::where('title', $paySummary->payment_mode)->first();
                $gl_account_no = isset($payment_method_row->paymentGlAccount->account_code) ? $payment_method_row->paymentGlAccount->account_code : '';
                $entity = new Payment();
                $entity->payment_method = $paySummary->payment_mode;
                $entity->gl_account_no = $gl_account_no;
                $entity->restaurant_id = $paySummary->restaurant_id;
                $entity->amount = $paySummary->amount;
                $entity->date = $this->checkAndGetDate(date('Y-m-d H:i:s'));
                $entity->save();
            }



           
        }
        return;
    }

    public function saveDebitAndCreditPayment($bill_id){
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
        $period_no = isset($WaAccountingPeriod->period_no) ? $WaAccountingPeriod->period_no : '';
        $orders_ids = BillOrderRelation::where('bill_id', $bill_id)->pluck('order_id')->toArray();
        
        $order_items = OrderedItem::whereIn('order_id',$orders_ids)->where('item_delivery_status', 'COMPLETED')->get();
        
        $gl_account_arr = WaChartsOfAccount::whereIn('account_name', ['VAT', 'Service tax'])->pluck('account_code', 'account_name')->toArray();
        $item_sales_arr = [];
        $date = $this->checkAndGetDate(date('Y-m-d H:i:s'));
        
        $date_series_module = [];
        foreach($order_items as $item) {
            $item_charges = json_decode($item->item_charges);
            $category_relation =  ItemCategoryRelation::select('category_id', 'item_id')->where('item_id',$item->food_item_id)->first();
            $item_sales_arr[$item->food_item_id]['family_group_id'] = $category_relation->category_id;
            $item_sales_arr[$item->food_item_id]['gl_code_id'] = null;
            $gross_item_price = $item_price = $item->price;
            if($category_relation->getRelativecategoryDetail->gl_account_no) {
                $gl_data =  WaChartsOfAccount::select('id')->where('account_code',$category_relation->getRelativecategoryDetail->gl_account_no)->first();
            }
            $order_item_date = date('Y-m-d', strtotime($item->created_at));
            if(!isset($date_series_module[$order_item_date])){
                $series_module = getNumberSeriesRow('POS SALES');
                $date_series_module[$order_item_date] = $series_module->last_number_used;
            }
            if(!empty($item_charges)) {
                foreach($item_charges as $charges) {
                    
                    $slug_gl_slug_arr = [
                        'Service tax'=> 'service-tax',
                        'VAT'=> 'vat',
                        'CTL'=>'ctl'
                    ];
                    
                    $type_arr = [
                        'Service tax'=> 'SERVICETAX',
                        'VAT'=> 'VAT',
                        'CTL'=>'CTL'
                    ];
                    $gl_slug = isset($slug_gl_slug_arr[$charges->charges_name]) ? $slug_gl_slug_arr[$charges->charges_name] : ''; 
                    $gl_account = NULL;
                    if(!empty($gl_slug)){
                        $gl_account = TaxManager::where('slug', $gl_slug)->pluck('output_tax_gl_account')->first();
                    }
                    $item_price -= $charges->charged_amount;
                    $params = [
                        'order_id' => $item->order_id,
                        'order_item_id' => $item->id,
                        'period' => $period_no,
                        'gl_code_id' => $gl_account,
                        'narration' => "POS Sales – $order_item_date",
                        'transaction_type' => 'POS Sales',
                        'transaction_no' => $series_module->code.'-'.$date_series_module[$order_item_date],
                        'gross_amount' => -($charges->charged_amount),
                        'amount' => -($charges->charged_amount),
                        'type'=>  isset($type_arr[$charges->charges_name]) ? $type_arr[$charges->charges_name] : '',
                        'date'=>$order_item_date
                    ];
                    PaymentCredit::savePaymentCredit($params);
                    $params['gross_amount'] = $charges->charged_amount;
                    $params['amount'] = $charges->charged_amount;
                    PaymentDebit::savePaymentDebit($params);
                }
            }
            
            $params = [
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'period' => $period_no,
                'gl_code_id' => isset($gl_data->id) ? $gl_data->id : '',
                'narration' => "POS Sales – $date",
                'transaction_type' => 'POS Sales',
                'transaction_no' => $series_module->code.'-'.$date_series_module[$order_item_date],
                'gross_amount' => -($gross_item_price),
                'amount' => -($item_price),
                'type'=> 'ITEM',
                'date'=>$order_item_date
            ];
            PaymentCredit::savePaymentCredit($params);
            $params['gross_amount'] = $gross_item_price;
            $params['amount'] = $item_price;
            PaymentDebit::savePaymentDebit($params);
        }
    }
    
    
    public function manageItemSalesWithFamilyGroup($bill_id) {
        $orders_ids = BillOrderRelation::where('bill_id',$bill_id)->pluck('order_id')->toArray();
        $order_items = OrderedItem::whereIn('order_id',$orders_ids)->where('item_delivery_status','COMPLETED')->get();

        $item_sales_arr = [];

        foreach($order_items as $item)
        {

            $item_charges = json_decode($item->item_charges);

            if(!isset($item_sales_arr[$item->food_item_id]['item_title']))
            {
                $item_sales_arr[$item->food_item_id]['item_title'] = $item->item_title;
            }

             if(!isset($item_sales_arr[$item->food_item_id]['food_item_id']))
            {
                $item_sales_arr[$item->food_item_id]['food_item_id'] = $item->food_item_id;
            }

            if(!isset($item_sales_arr[$item->food_item_id]['family_group_id']))
            {
                $category_relation =  ItemCategoryRelation::select('category_id','item_id')->where('item_id',$item->food_item_id)->first();
                $item_sales_arr[$item->food_item_id]['family_group_id'] = $category_relation->category_id;
                $item_sales_arr[$item->food_item_id]['gl_code_id'] = null;
                //get gl code id
                if($category_relation->getRelativecategoryDetail->gl_account_no)
                {
                    $gl_data =  WaChartsOfAccount::select('id')->where('account_code',$category_relation->getRelativecategoryDetail->gl_account_no)->first();
                    $item_sales_arr[$item->food_item_id]['gl_code_id'] = $gl_data->id;
                }
            }



            //manage quantity
            if(!isset($item_sales_arr[$item->food_item_id]['quantity']))
            {
                $item_sales_arr[$item->food_item_id]['quantity'] = $item->item_quantity;
                $item_sales_arr[$item->food_item_id]['gross_sale'] = $item->item_quantity*$item->price;

            }
            else
            {
                $item_sales_arr[$item->food_item_id]['quantity'] = $item->item_quantity+$item_sales_arr[$item->food_item_id]['quantity'];
                $gross_sale = $item->item_quantity*$item->price;
                $item_sales_arr[$item->food_item_id]['gross_sale'] = $item_sales_arr[$item->food_item_id]['gross_sale']+$gross_sale;
            }

            $item_sales_arr[$item->food_item_id]['restaurant_id'] = $item->restaurant_id;

            if(is_array($item_charges) && count($item_charges)>0)
            {
                foreach($item_charges as $charges)
                {
                    switch ($charges->charges_name) 
                    {
                        case "VAT":
                                if(isset($item_sales_arr[$item->food_item_id]['vat']))
                                {
                                    $item_sales_arr[$item->food_item_id]['vat'] = (float)($item_sales_arr[$item->food_item_id]['vat']+$charges->charged_amount);
                                }
                                else
                                {
                                     $item_sales_arr[$item->food_item_id]['vat'] = (float) $charges->charged_amount;
                                }                           
                            break;
                        case "Service tax":
                                if(isset($item_sales_arr[$item->food_item_id]['service_tax']))
                                {
                                    $item_sales_arr[$item->food_item_id]['service_tax'] = (float)($item_sales_arr[$item->food_item_id]['service_tax']+$charges->charged_amount);
                                }
                                else
                                {
                                    $item_sales_arr[$item->food_item_id]['service_tax'] = (float) $charges->charged_amount;
                                }
                            break;
                        case "CTL":
                             if(isset($item_sales_arr[$item->food_item_id]['catering_levy']))
                                {
                                    $item_sales_arr[$item->food_item_id]['catering_levy'] = (float)($item_sales_arr[$item->food_item_id]['catering_levy']+$charges->charged_amount);
                                }
                                else
                                {
                                    $item_sales_arr[$item->food_item_id]['catering_levy'] = (float) $charges->charged_amount;
                                }
                            break;
                        default:     
                    }
                }
            }
        }
        $restaurantId = Bill::where('id', $bill_id)->first();
        foreach($item_sales_arr as $food_item_id => $data)
        {
            $newSale = new ItemSalesWithGlCode();
            $newSale->food_item_id = $data['food_item_id'];
            $newSale->item_title = $data['item_title'];
            $newSale->family_group_id = $data['family_group_id'];
            $newSale->gl_code_id = $data['gl_code_id'];
            $newSale->quantity = $data['quantity'];
            $newSale->restaurant_id = $restaurantId->getAssociateUserForBill->restaurant_id;
            $newSale->gross_sale = $data['gross_sale'];
            $newSale->vat = isset($data['vat'])?$data['vat']:'0';
            $newSale->service_tax = isset($data['service_tax'])?$data['service_tax']:'0';
            $newSale->catering_levy = isset($data['catering_levy'])?$data['catering_levy']:'0';
            $all_taxes = $newSale->vat+$newSale->service_tax+$newSale->catering_levy;
            $newSale->net_sales = (float)($data['gross_sale']-$all_taxes);
            $newSale->sale_date =  $this->checkAndGetDate(date('Y-m-d H:i:s'));
            $newSale->save();
        }

        manageOrdersDiscountsForGlTrans($orders_ids);

    }


    public function checkAndGetDate($date)
    {
        $date = strtotime($date);
        $start = strtotime(date('Y-m-d H:i:s',strtotime(date('Y-m-d').' 00:00:00')));
        $end = strtotime(date('Y-m-d H:i:s',strtotime(date('Y-m-d').' 05:59:59')));
        $date_return = date('Y-m-d');
        if($date>= $start &&  $date <= $end)
        {
            $date_return =  date('Y-m-d',strtotime('-1 day',$date));
        }
       return $date_return;     
    }





    public function getwaitersdetailsbyorderidarray($order_id_arr)
    {
        $waiter_info = [];
        if(count($order_id_arr)>0)
        {
            $related_tables_arr = OrderBookedTable::select(['table_id'])->whereIn('order_id',$order_id_arr)->pluck('table_id')->toArray();
            if(count($related_tables_arr)>0)
            {
                $waiter_arr = EmployeeTableAssignment::select('user_id')->whereIn('table_manager_id',$related_tables_arr)->pluck('user_id')->toArray();
                if(count($waiter_arr)>>0)
                {
                    $waiter_info = User::select(['id','name'])->whereIn('id',$waiter_arr)->pluck('name','id')->toArray();
                }
            }
        }
        return $waiter_info;
    }

    public function getwaitersdetailsbyuseridarray($waiter_arr)
    {
        $waiter_info = [];
        $waiter_info = User::select(['id','name'])->whereIn('id',$waiter_arr)->pluck('name','id')->toArray();
        return $waiter_info;
    }


    public function postCancleOrderRequest($slug)
    {
        $order = Order::where('order_type','POSTPAID')
                ->where('slug',$slug)
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->doesnthave('getAssociateBillRelation')
                ->orderBy('id', 'desc')->first();
       if($order)
       {
            $logged_user_info = getLoggeduserProfile();
            $order->status = 'CANCLED';
            $order->order_canceled_by_user = $logged_user_info->id;
            $order->save();
            //Order::whereSlug($slug)->delete(); 
            Session::flash('success', 'Order is deleted successfully');
            return redirect()->back();
       }
       else
       {
            Session::flash('warning', 'Invalid request');
            return redirect()->back();
       }

    }


    public function getTransferOrder($order_slug)
    {
        $title = 'Orders';
        $model = $this->model;



        $pmodule = 'open-orders';
            $permission =  $this->mypermissionsforAModule();
           
        if(isset($permission[$pmodule.'___transfer']) || $permission == 'superadmin')
        {





        $from_order = Order::select('id','user_id','restaurant_id')->where('slug',$order_slug)->where('order_type','POSTPAID')->where('status','COMPLETED')->first();
        if($from_order)
        {
             $from_order_id = $from_order->id;
           
            $restaurant_id =  $from_order->restaurant_id;
            $all_tables =null;
            $to_orders = Order::select('id','slug')->where('slug','!=',$order_slug)
            ->where('order_type','POSTPAID')
            ->where('restaurant_id',$restaurant_id)
            ->where('status','COMPLETED')->get();

             $to_order_ids_arr = $to_orders->pluck('id','slug')->toArray();


           
            


            //all restro assigned tables listing
            $all_assigned_table =  EmployeeTableAssignment::pluck('table_manager_id')->toArray();
            if(count($all_assigned_table)>0)
            {
                $all_tables = TableManager::where('status','1')->where('restaurant_id',$restaurant_id)->whereIn('id',$all_assigned_table)->pluck('name','id')->toArray();

                
            }

            $breadcum = ['Open Orders'=>route('admin.postpad.orders'),'Transfer Order'=>'',$from_order->id=>''];
  
            return view('admin.orders.transferorder',compact('title','all_tables','model','breadcum','from_order_id','to_order_ids_arr','order_slug'));

        }
        else
        {
            Session::flash('warning', 'Please try again');
            return redirect()->back();
        }
         }
          else
        {
            Session::flash('warning', 'Invalid request');
            return redirect()->back();
        }
    }

    public function makeRequestTransferOrder(Request $request,$order_slug)
    {
        $title = 'Order';
        $model = $this->model;
        $from_order_slug = $order_slug;
        $from_order = Order::select('id','user_id','restaurant_id','slug')->where('slug',$from_order_slug)->where('order_type','POSTPAID')->where('status','COMPLETED')->first();
        if($from_order)
        {
            if ($request->has('to_order_id'))
            {
                $to_order_slug = $request->input('to_order_id');
               // echo $to_order_slug;die;
                $to_order = Order::select('id','user_id','restaurant_id','slug')->where('slug',$to_order_slug)->where('order_type','POSTPAID')->where('status','COMPLETED')->first();
                if($to_order)
                {
                    //start swepping for order to order
                    $breadcum = ['Open Orders'=>route('admin.postpad.orders'),'Transfer Order'=>'',$from_order->id=>'',$to_order->id=>''];
                    return view('admin.orders.transferorderfromordertoorder',compact('title','model','breadcum','from_order','to_order'));



                }
                else
                {
                    Session::flash('warning', 'Please choose another order');
                    return redirect()->back();
                }
            }
            if ($request->has('to_table_id'))
            {
                $to_table_id = $request->input('to_table_id');
                $to_table = TableManager::where('status','1')->where('id',$to_table_id)->first();
                if($to_table)
                {
                    //start swepping for order to table
                    $breadcum = ['Open Orders'=>route('admin.postpad.orders'),'Transfer Order'=>'',$from_order->id=>'',$to_table->name=>''];

                    return view('admin.orders.transferorderfromordertotable',compact('title','model','breadcum','from_order','to_table'));
                }
                else
                {
                    Session::flash('warning', 'Please choose another table');
                    return redirect()->back();
                }
            }
        }
        else
        {
            Session::flash('warning', 'Order not found');
            return redirect()->route('admin.postpad.orders');
        }     
    }

    public function approveTransferOrderRequest(Request $request,$from_order_slug)
    {
       //Session::flash('warning', 'this functionality not enabled yet');
        //return redirect()->back(); 
        if(isset($request->request_type) &&  $request->request_type != "")
        {
            $from_order = Order::where('slug',$from_order_slug)->where('order_type','POSTPAID')->where('status','COMPLETED')->first();
            if($from_order)
            {
                if((isset($request->ordered_item_id) && count($request->ordered_item_id)>0) || (isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0))
                {
                    if($request->request_type=="order_to_order")
                    {
                        $to_order_slug = $request->to_order_slug;
                        $to_order = Order::select('id','user_id','restaurant_id','slug')->where('slug',$to_order_slug)->where('order_type','POSTPAID')->where('status','COMPLETED')->first();
                        if($to_order)
                        {
                            //transfer order to order start
                           if(isset($request->ordered_item_id) && count($request->ordered_item_id)>0)
                           {
                                //transfer orders item
                              $all_item_ids =   array_keys($request->ordered_item_id);
                              foreach($all_item_ids as $ordered_item_id)
                              {
                                $original = 'original_qty_'.$ordered_item_id;
                                $selected = 'selected_qty_'.$ordered_item_id;
                                if($request->$original == $request->$selected )
                                {
                                    OrderedItem::where('id',$ordered_item_id)->update(['order_id'=>$to_order->id]);
                                }
                                else
                                {
                                    $oldOrderedItem = OrderedItem::where('id',$ordered_item_id)->first();
                                    //create new ordereditem
                                    $leftQuantity = $request->$original-$request->$selected;
                                    $newOrderItem = new OrderedItem();
                                    $newOrderItem->order_id = $to_order->id;
                                    $newOrderItem->food_item_id = $oldOrderedItem->food_item_id;
                                    $newOrderItem->order_offer_id = $oldOrderedItem->order_offer_id;
                                    $newOrderItem->restaurant_id = $oldOrderedItem->restaurant_id;
                                    $newOrderItem->price = $oldOrderedItem->price;
                                    $newOrderItem->item_title = $oldOrderedItem->item_title;
                                    $newOrderItem->item_comment = $oldOrderedItem->item_comment;
                                    $newOrderItem->item_quantity = $request->$selected;
                                    $newOrderItem->condiments_json = $oldOrderedItem->condiments_json;
                                    $newOrderItem->print_class_id = $oldOrderedItem->print_class_id;
                                    $newOrderItem->item_delivery_status = $oldOrderedItem->item_delivery_status;
                                    $newOrderItem->created_at = $oldOrderedItem->created_at;
                                    $newOrderItem->updated_at = $oldOrderedItem->updated_at;
                                    $newOrderItem->billing_time = $oldOrderedItem->billing_time;
                                    $oldOrderedItem_item_charges = json_decode($oldOrderedItem->item_charges);
                                    if(is_array($oldOrderedItem_item_charges) && count($oldOrderedItem_item_charges)>0)
                                    {
                                        foreach($oldOrderedItem_item_charges as $charges)
                                        {
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;


                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                            $new_charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;

                                            if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                                            {
                                                $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                 $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$new_amount;

                                                 $newCgareAmount = $spillted_chared_amount*$leftQuantity;
                                                 $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$newCgareAmount;
                                            }
                                            else
                                            {
                                                 $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                $new_amount = $spillted_chared_amount*$request->$selected;
                                                 $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_amount;

                                                 $newCgareAmount = $spillted_chared_amount*$leftQuantity;

                                                 $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $newCgareAmount;
                                            }
                                        }
                                        $newOrderItem->item_charges =  json_encode(array_values($charges_arr));
                                        $oldOrderedItem->item_charges =  json_encode(array_values($new_charges_arr));
                                    }
                                    $oldOrderedItem->item_quantity = $leftQuantity;
                                    $oldOrderedItem->save();
                                    $newOrderItem->save();
                                }
                              }

                              //OrderedItem::whereIn('id',$all_item_ids)->update(['order_id'=>$to_order->id]);
                            
                           }
                            if(isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0)
                           {
                                //transfer orders offer

                                $all_offer_ids =   array_keys($request->ordered_offer_id);

                                OrderOffer::whereIn('id',$all_offer_ids)->update(['order_id'=>$to_order->id]);

                                

                           }
                            $this->updateOrderStatsAfterTransfer([$to_order->id,$from_order->id]);
                            Session::flash('success', 'Order transfered successfully');
                            return redirect()->route('admin.postpad.orders');  
                        }
                        else
                        {
                            Session::flash('warning', 'Order not found');
                            return redirect()->route('admin.postpad.orders');
                        }

                    }
                     else if($request->request_type=="order_to_table")
                    {   
                        $to_table_slug = $request->to_table_slug;
                        $to_table = TableManager::where('slug',$to_table_slug)->first();
                        $table_assignment = EmployeeTableAssignment::where('table_manager_id',$to_table->id)->first();
                        if($table_assignment)
                        {
                            $user_id = $table_assignment->user_id;
                            $new_order = new Order();
                            $new_order->user_id = $user_id;
                            $new_order->restaurant_id = $from_order->restaurant_id;
                            $new_order->final_comment = '';
                            $new_order->order_final_price = '0';
                            $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                            $new_order->order_type = 'POSTPAID';
                            $new_order->status = 'COMPLETED';
                            $new_order->total_guests = 1;
                            $new_order->order_charges = null;
                            $new_order->payment_mode = 'NA';
                            $new_order->save();
                            $new_order_id = $new_order->id;
                            $inserting_array = [];
                            $inner_array = [
                                'order_id'=>$new_order_id,
                                'table_id'=>$to_table->id
                            ];
                            $inserting_array[] = $inner_array;
                            OrderBookedTable::insert($inserting_array);


                            if(isset($request->ordered_item_id) && count($request->ordered_item_id)>0)
                               {
                                    //transfer orders item
                                  $all_item_ids =   array_keys($request->ordered_item_id);

                                  foreach($all_item_ids as $ordered_item_id)
                                  {
                                    $original = 'original_qty_'.$ordered_item_id;
                                    $selected = 'selected_qty_'.$ordered_item_id;
                                    if($request->$original == $request->$selected )
                                    {
                                         OrderedItem::where('id',$ordered_item_id)->update(['order_id'=>$new_order_id]);
                                    }
                                    else
                                    {
                                        $oldOrderedItem = OrderedItem::where('id',$ordered_item_id)->first();
                                        //create new ordereditem
                                          $leftQuantity = $request->$original-$request->$selected;
                                        $newOrderItem = new OrderedItem();
                                        $newOrderItem->order_id = $new_order_id;
                                        $newOrderItem->food_item_id = $oldOrderedItem->food_item_id;
                                        $newOrderItem->order_offer_id = $oldOrderedItem->order_offer_id;
                                        $newOrderItem->restaurant_id = $oldOrderedItem->restaurant_id;
                                        $newOrderItem->price = $oldOrderedItem->price;
                                        $newOrderItem->item_title = $oldOrderedItem->item_title;
                                        $newOrderItem->item_comment = $oldOrderedItem->item_comment;
                                        $newOrderItem->item_quantity = $request->$selected;
                                        $newOrderItem->condiments_json = $oldOrderedItem->condiments_json;
                                        $newOrderItem->print_class_id = $oldOrderedItem->print_class_id;
                                        $newOrderItem->item_delivery_status = $oldOrderedItem->item_delivery_status;
                                        $newOrderItem->created_at = $oldOrderedItem->created_at;
                                        $newOrderItem->updated_at = $oldOrderedItem->updated_at;
                                        $newOrderItem->billing_time = $oldOrderedItem->billing_time;
                                        $oldOrderedItem_item_charges = json_decode($oldOrderedItem->item_charges);
                                        if(is_array($oldOrderedItem_item_charges) && count($oldOrderedItem_item_charges)>0)
                                        {
                                            foreach($oldOrderedItem_item_charges as $charges)
                                            {
                                                $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                                $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                                $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;


                                                $new_charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                                                $new_charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                                                $new_charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;

                                                if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                                                {
                                                    $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                    $new_amount = $spillted_chared_amount*$request->$selected;
                                                     $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$new_amount;

                                                     $newCgareAmount = $spillted_chared_amount*$leftQuantity;
                                                     $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$newCgareAmount;
                                                }
                                                else
                                                {
                                                     $spillted_chared_amount = $charges->charged_amount/$request->$original;
                                                    $new_amount = $spillted_chared_amount*$request->$selected;
                                                     $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $new_amount;

                                                     $newCgareAmount = $spillted_chared_amount*$leftQuantity;

                                                     $new_charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $newCgareAmount;
                                                }
                                            }
                                            $newOrderItem->item_charges =  json_encode(array_values($charges_arr));
                                            $oldOrderedItem->item_charges =  json_encode(array_values($new_charges_arr));
                                        }
                                        $oldOrderedItem->item_quantity = $leftQuantity;
                                        $oldOrderedItem->save();
                                        $newOrderItem->save();
                                        //updateold ordered item
                                    }
                                }    
                            }
                            if(isset($request->ordered_offer_id) && count($request->ordered_offer_id)>0)
                           {
                                //transfer orders offer
                                $all_offer_ids =   array_keys($request->ordered_offer_id);
                                OrderOffer::whereIn('id',$all_offer_ids)->update(['order_id'=>$new_order_id]);
                           }
                            $this->updateOrderStatsAfterTransfer([$from_order->id,$new_order_id]);
                            Session::flash('success', 'Order transfered successfully');
                            return redirect()->route('admin.postpad.orders'); 
                        }
                        else
                        {
                            Session::flash('warning', 'Table not found');
                            return redirect()->route('admin.postpad.orders');
                        }

                    }
                    else
                    {
                        Session::flash('warning', 'Invalid Request');
                        return redirect()->back(); 
                    } 

                }
                else
                {
                    Session::flash('warning', 'Please select item');
                    return redirect()->back(); 
                }
           
            }
            else
            {
                Session::flash('warning', 'Order not found');
                return redirect()->route('admin.postpad.orders');
            }
            
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back(); 
        }

        
    }

   public function updateOrderStatsAfterTransfer($order_ids_arr)
    {
       // $order_ids_arr= [1255];
        foreach($order_ids_arr as $order_id)
        {
            $order = Order::whereId($order_id)->first();
            $ordered_item =  $order->getAssociateItemWithOrder;
            $ordered_offer =  $order->getAssociateOffersWithOrder;
            $total_amount_arr = [];
            $charges_arr = [];

            if(count($ordered_item)>0 || count($ordered_offer)>0 )
            {
            foreach($ordered_item as $item)
            {
                if($item->item_delivery_status !='CANCLED' && !$item->order_offer_id)
                {
                    $total_amount_arr[] = $item->item_quantity*$item->price;
                    //manage charges start
                    $item_charges = json_decode($item->item_charges);
                    if(is_array($item_charges) && count($item_charges)>0)
                    {
                        foreach($item_charges as $charges)
                        {
                            $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                            $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                            $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;

                            if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                            {
                                 $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$charges->charged_amount;
                            }
                            else
                            {
                                 $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges->charged_amount;
                            }
                        }
                    }
                }
                //manage charges end
            }

            foreach($ordered_offer as $offer)
            {
                $total_amount_arr[] = $offer->quantity*$offer->price;


                  //manage charges start
                $offer_charges = json_decode($offer->offer_charges);
                if(is_array($offer_charges) && count($offer_charges)>0)
                {
                    foreach($offer_charges as $charges)
                    {
                        $charges_arr[strtoupper($charges->charges_name)]['charges_name'] = $charges->charges_name;
                        $charges_arr[strtoupper($charges->charges_name)]['charges_value'] = $charges->charges_value;
                        $charges_arr[strtoupper($charges->charges_name)]['charges_format'] = $charges->charges_format;

                        if(isset($charges_arr[strtoupper($charges->charges_name)]['charged_amount']))
                        {
                             $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges_arr[strtoupper($charges->charges_name)]['charged_amount']+$charges->charged_amount;
                        }
                        else
                        {
                             $charges_arr[strtoupper($charges->charges_name)]['charged_amount'] = $charges->charged_amount;
                        }
                    }
                }
                //manage charges end
            }
            $order->order_charges =  json_encode(array_values($charges_arr));
            $sum_of_total_price = array_sum($total_amount_arr);
            if($order->admin_discount_in_percent >0)
            {
                if($sum_of_total_price>0)
                {
                    $percent = ($order->admin_discount_in_percent*$sum_of_total_price)/100;
                    $discount_percent = [[
                    'discount_title'=>'Promotion Discount',
                    'discount_value'=>$order->admin_discount_in_percent,
                    'discount_format'=>'PERCENTAGE',
                    'discount_amount'=>round($percent,2)
                    ]];
                    $order->order_discounts =  json_encode($discount_percent);
                    $order->order_final_price =  round($sum_of_total_price-$percent,2);
                }
            }
            else
            {
                $order->order_final_price =  $sum_of_total_price;
            }
        }
        else
        {
            $order->order_charges =  null;
            $order->order_final_price = 0;
            $order->status = 'CANCLED';
        }
        $order->save();
            

            

            
        }
       
        
    }




    public function getRequestForaddDiscountAtBill($bill_slug)
    {

        $pmodule = 'master-bills';
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___discount']) || $permission == 'superadmin')
        {
            
            $title = 'Orders';
            $model = $this->model;
              $logged_user_info = getLoggeduserProfile();
            $bill = Bill::where('status','PENDING')->where('slug',$bill_slug)->first();
            //echo "<pre>"; print_r($bill); die;
            if($bill)
            {
                $breadcum = ['Bills'=>route('admin.master-bills'),'Master Bills'=>route('admin.master-bills'),'Add Discount For Bill '.$bill->id=>''];
                return view('admin.orders.discountforbill',compact('title','bill','model','breadcum','permission','pmodule','payment_mode','logged_user_info'));
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

    public function setRequestForaddDiscountAtBill(Request $request,$bill_slug)
    {
        try 
        {
            $bill = Bill::where('status','PENDING')->where('slug',$bill_slug)->first();
//            echo "Dfsf"; die;
            if($bill)
            {
                $logged_user_info = getLoggeduserProfile();
                if(count($bill->getAssociateOrdersWithBill)>0)
                {
                    
                    foreach ($bill->getAssociateOrdersWithBill as $order) 
                    {
                        $row = Order::where('id',$order->order_id)->first();
                        $row->admin_discount_in_percent = $request->admin_discount_in_percent;
                        $row->discount_reason = isset($request->discount_reason)?$request->discount_reason:null;

                        $total_price = [];
                        foreach($row->getAssociateItemWithOrder as $item)
                        {
                            if($item->item_delivery_status !='CANCLED' && !$item->order_offer_id)
                            {
                                $total_price[] = $item->price*$item->item_quantity;
                            }
                        }
                        foreach($row->getAssociateOffersWithOrder as $offer)
                        {
                            $total_price[] = $offer->price*$offer->quantity;
                        }
                        $sum_of_total_price = array_sum($total_price);

                        if($request->admin_discount_in_percent>0 || $request->admin_discount_in_amount>0)
                        {
                            if($sum_of_total_price>0)
                            {
 
                                $percent = ($request->admin_discount_in_percent*$sum_of_total_price)/100;
                                $discount_percent = [[
                                'discount_title'=>'Promotion Discount',
                                'discount_value'=>$request->admin_discount_in_percent,
                                'discount_format'=>'PERCENTAGE',
                                'discount_amount'=>round($percent,2)
                                ]];                                
                         
                                $row->order_discounts =  json_encode($discount_percent);
                                $row->order_final_price =  round($sum_of_total_price-$percent,2);
                        
                            }

                            $row->discounting_user_id = $logged_user_info->id;
                        }

                        if($request->admin_discount_in_percent==0 && $request->admin_discount_in_amount==0)
                        {
                            $row->order_discounts =  null;
                            $row->order_final_price =  round($sum_of_total_price,2);
                            $row->discounting_user_id = null;
                             $row->discount_reason = null;
                        }
                        $row->save();
                    }
                    Session::flash('success','Bill updated successfully');
                    return redirect()->route('admin.master-bills');
                }
            }
            else
            {
                Session::flash('warning', 'Invalid request');
                return redirect()->route('admin.master-bills');
            }
        }
        catch (\Exception $e) 
        {
            $msg = 'Invalid request';
            Session::flash('danger',$msg);
            return redirect()->back();
        }
    }

    public function postpadOrdersDeleteWithReason(Request $request){
        $slug = $request->order_slug;
        $order = Order::where('order_type','POSTPAID')
                ->where('slug',$slug)
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->doesnthave('getAssociateBillRelation')
                ->orderBy('id', 'desc')->first();
       if($order)
       {
            $logged_user_info = getLoggeduserProfile();
            $order->status = 'CANCLED';
            $order->order_canceled_by_user = $logged_user_info->id;
            $order->order_cancle_reason = $request->order_cancle_reason;
            $order->save();
            //Order::whereSlug($slug)->delete(); 
            Session::flash('success', 'Order is deleted successfully');
            return redirect()->back();
       }
       else
       {
            Session::flash('warning', 'Invalid request');
            return redirect()->back();
       }

    }

}
