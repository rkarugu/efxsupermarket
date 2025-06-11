<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Model\Order;
use App\Model\User;
use App\Model\OrderBookedTable;
use App\Model\PrintClass;
use App\Model\TableManager;
use App\Model\OrderedItem;
use App\Model\OrderOffer;
use App\Model\Setting;
use App\Model\FoodItemsPrintClassRelation;
use App\Model\EmployeeTableAssignment;
use App\Model\MpesaTransactionDetail;
use App\Model\ReceiptSummaryPayment;
use App\Model\OrderReceipt;
use App\Model\BillOrderRelation;
use App\Model\Bill;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaStockMove;
use App\Model\WaGlTran;
use App\Model\OrderReceiptRelation;
use App\Model\WalletTransaction;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

use File;
use DB;
    class OrderController extends Controller
    {   
       
        private $uploadsfolder;
        public function __construct()
        {  
          $this->uploadsfolder = asset('uploads/');   
        }


        public function getCheckoutOLD(Request $request)
        {
          //$this->getPrintclassidByItemId(20);
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'restaurant_id' => 'required',
                        'device_type' => 'required',
                        'restaurant_id' => 'required',    
                        'checkout_json' => 'required',  
                        'Total_price' => 'required',  
                        'device_id' => 'required'     
                ]);

            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages()); 
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {


            
                if($request->payment_mode == 'Loyalty Point')
                {
                  $total_amount_to_be_paying = $request->Total_price;
                  $user_have_amount = $this->getLoyaltyPointsByUserId($request->user_id);

                   if($total_amount_to_be_paying >= $user_have_amount)
                   {
                     $response_arr =   ['status'=>false,'message'=>'insufficient loyalty points'];
                     return response()->json($response_arr);
                   }
                }
             

             
            

              


                $order_default_status = 'NEW_ORDER';
                $new_order = new Order();
                $new_order->user_id = $request->user_id;
                $new_order->restaurant_id = $request->restaurant_id;
                $new_order->final_comment = isset($request->final_comment)?$request->final_comment:'';
                $new_order->order_final_price = $request->Total_price;
                $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                $new_order->order_type = isset($request->order_type)?$request->order_type:'PREPAID';



                $new_order->payment_mode = 'NA';
                if(isset($request->payment_mode) && $request->payment_mode != "" )
                {
                  $new_order->payment_mode = $request->payment_mode;
                }

                if(isset($request->compliementary_reason) && $request->compliementary_reason != "" )
                {
                  $new_order->compliementary_reason = $request->compliementary_reason;
                }

                

                if(isset($request->mpesa_request_id) && $request->mpesa_request_id != "" )
                {
                  $new_order->mpesa_request_id = $request->mpesa_request_id;
                }

                if(isset($request->transaction_id) && $request->transaction_id != "" )
                {
                  $new_order->transaction_id = $request->transaction_id;
                }

                
                $new_order->complimentry_code = isset($request->complimentry_code)?$request->complimentry_code:null;

                if(isset($request->order_default_status) && $request->order_default_status == 'PENDING')
                {
                  $new_order->status = 'PENDING';
                  $order_default_status = 'PENDING';
                }



                $new_order->save();
                
                $order_id = $new_order->id;



                //$path    = 'resources/views/checkoutjson.blade.php';
               // $bytes_written = File::put($path, $request->checkout_json);
              
               $json = json_decode($request->checkout_json);

               //book the table for related order start
               if(isset($json->table_info) && isset($json->table_info->Table_id) && isset($json->table_info->total_guests))
               {
                    $this->bookedTheTableForRelatedOrder($json->table_info->Table_id,$json->table_info->total_guests,$order_id,$order_default_status);
                    $new_order->total_guests = $json->table_info->total_guests;
                    $new_order->save();
               }


               //book the table for related order end

               //store extra charges for related order start
               if(isset($json->order_charges))
               {
                    $new_order->order_charges = json_encode($json->order_charges);
                    $new_order->save();
               }
               //store extra charges for related order end

                //store discounts for related order start
               if(isset($json->order_discounts))
               {
                    $new_order->order_discounts = json_encode($json->order_discounts);
                    $new_order->save();
               }
               //store discounts for related order end


               //store general items  start
               
                if(isset($json->Appetizerdata) && count($json->Appetizerdata)>0)
               {
                    $this->storeGeneralItemForOrder($json->Appetizerdata,$order_id, $new_order->restaurant_id,$order_default_status);
               }
               //store general item end

               //store offer items  start
               
              if(isset($json->offerdata) && count($json->offerdata)>0)
              {
                $this->storeOfferItemForOrder($json->offerdata,$order_id, $new_order->restaurant_id);
              }
               $this->manageLoyalityPoint($new_order,$order_default_status);
               //store offer item end


              if($new_order->payment_mode == 'Loyalty Point')
              {
                $this->spentLoyalityPoints($new_order,$request->Total_price);
              }

              


                
                $response_arr = ['status'=>true,'message'=>'Booking Confirmed Successfully','order_id'=>$order_id,

                'pending_order_count'=>$this->getPendingorderCount($request->user_id),
                'loyalty_points'=>$this->getLoyaltyPointsByUserId($request->user_id)
                ];

                //make receipt
               if($new_order->order_type =='PREPAID' )
               {
                $receipt_id = $this->makeReceipt([$order_id],$request->user_id);

                  //manage multiple payment records

                  $this->managepaymentSummary($receipt_id,$new_order);

                  $my_receipt = OrderReceipt::whereId($receipt_id)->first();
                  $this->managetimeForall($receipt_id,$my_receipt->created_at);

                  $response_arr['receipt_id'] = $receipt_id;

                  if($new_order->payment_mode == 'WALLET')
                  {
                    $loggeduser = User::where('id',$request->user_id)->first();
                    $walletTransaction = new WalletTransaction();
                    $walletTransaction->refrence_description = $receipt_id;
                    $walletTransaction->phone_number = $loggeduser->phone_number;
                    $walletTransaction->entry_type = 'PURCHASE';
                    $walletTransaction->amount = $request->Total_price;
                    $walletTransaction->user_id = $request->user_id;
                    $walletTransaction->transaction_type = 'DR';
                    $walletTransaction->save();
                    $this->updateWalletAmount($loggeduser->phone_number);
                  }
               }

               if($new_order->order_type=='POSTPAID')
               {
                 $table_detail  = OrderBookedTable::where('order_id',$order_id)->first();
                  $bill_id = associatePostpaidOrderByBillUsineOrderIdAndTableId($order_id,$table_detail->table_id,$request->user_id);
                   $response_arr['bill_id'] = $bill_id;
               }
              
                return response()->json($response_arr);
            }
        } 



        public function getCheckout(Request $request)
        {
          //$this->getPrintclassidByItemId(20);
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'restaurant_id' => 'required',
                        'device_type' => 'required',
                        'restaurant_id' => 'required',    
                        'checkout_json' => 'required',  
                        'Total_price' => 'required',  
                        'device_id' => 'required'     
                ]);

            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages()); 
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {

               $json = json_decode($request->checkout_json);
	           if(isset($json->Appetizerdata) && count($json->Appetizerdata)>0)
               {
		        foreach($json->Appetizerdata as $item)
		        {			   
		            $food_item_row = \App\Model\FoodItem::where('id', $item->appetizer_id)->first();
		            $less_quantity = 0;
		            if($food_item_row->check_stock_before_sale){	            
		                $recipe_row = $food_item_row->getAssociateRecipe;
		                $recipe_ingredients = isset($food_item_row->getAssociateRecipe->getAssociateIngredient) ? $food_item_row->getAssociateRecipe->getAssociateIngredient : [];
		                $qtyavailable = 0;
		                $wa_location_and_store_id = isset($recipe_row->wa_location_and_store_id) ? $recipe_row->wa_location_and_store_id : '';
		                foreach ($recipe_ingredients as $key => $recipe_ingredient_row) {
		                    $inventory_item_row = $recipe_ingredient_row->getAssociateItemDetail;
		                    $inventory_qoh = getItemAvailableQuantity($inventory_item_row->stock_id_code, $wa_location_and_store_id);
		                    $weight = $recipe_ingredient_row->weight;
		                    $weight_for_required_required_quantity = $weight * $item->quantity;
		                    $qtyavailable = $inventory_qoh;
		                    if ($weight_for_required_required_quantity > $inventory_qoh) {
		                        $less_quantity = 1;
		                        break;
		                    }
		                }
				        if($less_quantity == 1){
				             return response()->json(['status'=>false,'message'=>"The Quantity available for ".$item->title." is ".$qtyavailable." which is less than the quantity that you are ordering. Please remove this from your cart in order to continue with check out."]);
				        }
		            }
		         }
               }

            
                if($request->payment_mode == 'Loyalty Point')
                {
                  $total_amount_to_be_paying = $request->Total_price;
                  $user_have_amount = $this->getLoyaltyPointsByUserId($request->user_id);

                   if($total_amount_to_be_paying >= $user_have_amount)
                   {
                     $response_arr =   ['status'=>false,'message'=>'insufficient loyalty points'];
                     return response()->json($response_arr);
                   }
                }
             

             
            

              


                $order_default_status = 'NEW_ORDER';
                $new_order = new Order();
                $new_order->user_id = $request->user_id;
                $new_order->restaurant_id = $request->restaurant_id;
                $new_order->final_comment = isset($request->final_comment)?$request->final_comment:'';
                $new_order->order_final_price = $request->Total_price;
                $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                $new_order->order_type = isset($request->order_type)?$request->order_type:'PREPAID';



                $new_order->payment_mode = 'NA';
                if(isset($request->payment_mode) && $request->payment_mode != "" )
                {
                  $new_order->payment_mode = $request->payment_mode;
                }

                if(isset($request->compliementary_reason) && $request->compliementary_reason != "" )
                {
                  $new_order->compliementary_reason = $request->compliementary_reason;
                }

                

                if(isset($request->mpesa_request_id) && $request->mpesa_request_id != "" )
                {
                  $new_order->mpesa_request_id = $request->mpesa_request_id;
                }

                if(isset($request->transaction_id) && $request->transaction_id != "" )
                {
                  $new_order->transaction_id = $request->transaction_id;
                }

                
                $new_order->complimentry_code = isset($request->complimentry_code)?$request->complimentry_code:null;

                if(isset($request->order_default_status) && $request->order_default_status == 'PENDING')
                {
                  $new_order->status = 'PENDING';
                  $order_default_status = 'PENDING';
                }



                $new_order->save();
                
                $order_id = $new_order->id;



                //$path    = 'resources/views/checkoutjson.blade.php';
               // $bytes_written = File::put($path, $request->checkout_json);
              
               $json = json_decode($request->checkout_json);

               //book the table for related order start
               if(isset($json->table_info) && isset($json->table_info->Table_id) && isset($json->table_info->total_guests))
               {
                    $this->bookedTheTableForRelatedOrder($json->table_info->Table_id,$json->table_info->total_guests,$order_id,$order_default_status);
                    $new_order->total_guests = $json->table_info->total_guests;
                    $new_order->save();
               }


               //book the table for related order end

               //store extra charges for related order start
               if(isset($json->order_charges))
               {
                    $new_order->order_charges = json_encode($json->order_charges);
                    $new_order->save();
               }
               //store extra charges for related order end

                //store discounts for related order start
               if(isset($json->order_discounts))
               {
                    $new_order->order_discounts = json_encode($json->order_discounts);
                    $new_order->save();
               }
               //store discounts for related order end


               //store general items  start
               
	           if(isset($json->Appetizerdata) && count($json->Appetizerdata)>0)
               {
                    $this->storeGeneralItemForOrder($json->Appetizerdata,$order_id, $new_order->restaurant_id,$order_default_status);
               }
               //store general item end

               //store offer items  start
               
              if(isset($json->offerdata) && count($json->offerdata)>0)
              {
                $this->storeOfferItemForOrder($json->offerdata,$order_id, $new_order->restaurant_id);
              }
               $this->manageLoyalityPoint($new_order,$order_default_status);
               //store offer item end


              if($new_order->payment_mode == 'Loyalty Point')
              {
                $this->spentLoyalityPoints($new_order,$request->Total_price);
              }

              


                
                $response_arr = ['status'=>true,'message'=>'Booking Confirmed Successfully','order_id'=>$order_id,
                'pending_order_count'=>$this->getPendingorderCount($request->user_id),
                'loyalty_points'=>$this->getLoyaltyPointsByUserId($request->user_id),
                'error'=>false,
                'errorMessage'=>""
                ];

                //make receipt
               if($new_order->order_type =='PREPAID' )
               {
                $receipt_id = $this->makeReceipt([$order_id],$request->user_id);

                  //manage multiple payment records

                  $this->managepaymentSummary($receipt_id,$new_order);

                  $my_receipt = OrderReceipt::whereId($receipt_id)->first();
                  $this->managetimeForall($receipt_id,$my_receipt->created_at);

                  $response_arr['receipt_id'] = $receipt_id;

                  if($new_order->payment_mode == 'WALLET')
                  {
                    $loggeduser = User::where('id',$request->user_id)->first();
                    $walletTransaction = new WalletTransaction();
                    $walletTransaction->refrence_description = $receipt_id;
                    $walletTransaction->phone_number = $loggeduser->phone_number;
                    $walletTransaction->entry_type = 'PURCHASE';
                    $walletTransaction->amount = $request->Total_price;
                    $walletTransaction->user_id = $request->user_id;
                    $walletTransaction->transaction_type = 'DR';
                    $walletTransaction->save();
                    $this->updateWalletAmount($loggeduser->phone_number);
                  }
               }

               if($new_order->order_type=='POSTPAID')
               {
                   $table_detail  = OrderBookedTable::where('order_id',$order_id)->first();

                   $bill_id = associatePostpaidOrderByBillUsineOrderIdAndTableId($order_id,$table_detail->table_id,$request->user_id);

                 $loggeduser = User::where('id',$request->user_id)->first();
                 $order_detail = OrderedItem::where('order_id',$order_id)->first();
           $orderedProduct = OrderedItem::select('item_title as name','item_quantity as quantity','price as amount')->where('order_id',$order_id)->get()->toArray();
                   $response_arr['bill_id'] = $bill_id;
//           $print_class_data = PrintClass::where('id',$order_detail->print_class_id)->first();
           $print_class_data = PrintClass::get();
            
          foreach($print_class_data as $val){
          $orderedProduct = OrderedItem::select('item_title as name','item_quantity as quantity','price as amount','item_comment','condiments_json')
                    ->where('order_id',$order_id)
                    ->where('print_class_id',$val->id)
                    ->get()->toArray();
          if(count($orderedProduct) > 0){         
          $bill = [
          'order_id' => $order_id,//'EAT IN',
          'table_no' => $table_detail->getRelativeTableData->name,
          'datetime' => date('d F,Y H:i A'),//'16 Jul 20:03 PM',
          'delivery_mode' => $request->payment_mode,//'EAT IN',
          'print_class' => $val->name,//'EAT IN',
          'waiter' => [
          'name' => $loggeduser->name
          ],
          'products' => $orderedProduct
          ];
        try{ 
          
          $ip = $val->ip;//"197.232.252.20"; //$print_class_data->ip;//"192.168.1.102"; // from DB
          $port = $val->port; //$print_class_data->port; //9100; // from DB
          
          $connector = new NetworkPrintConnector($ip, $port);
          // Initialize printer object
          $printer = new Printer($connector);
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          $printer->selectPrintMode();
          $printer->feed(1); // create a line
        
          // return justifiacation to the left

          $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
          $printer->setJustification(Printer::JUSTIFY_LEFT);          
          // enter waiter details and date
          $printer->text("Order No: ".$bill['order_id']."\n");
          $printer->text("Table No: ".$bill['table_no']."\n");
          $printer->text("Date And Time: ".$bill['datetime']."\n");
          $printer->text("Print Class Name: ".$bill['print_class']."\n");
          $printer->text("Waiter Name: ".$bill['waiter']['name']."\n");
          $printer->feed(1);

          //$printer->text($bill['datetime']."\n");
          $printer->text("________________________________________________\n"); // this line is printed according to printer size

          $printer->text($bill['delivery_mode']."\n");

          
          // display product details
          $productTotalAmount = 0;
          //echo "<pre>"; print_r($bill); die;
          foreach ($bill['products'] as $key => $product) {
            
          if($product['item_comment'] && $product['item_comment'] != ''){
            $comment = '('.$product['item_comment'].')'; 
          }else{
            $comment = "";
          }           

            $condiment_arr =  json_decode($product['condiments_json']);
      
          $condiment = "";
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
                                $condiment = ucfirst($sub_items->title)."\n";
                            }
                        }
                        
                        
                    }
                }
              }

        //  echo $product['quantity']." ".$product['name']." ".$comment."\n"." ".$condiment; die;
          
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          $printer->text($product['quantity']." ".$product['name']." ".$comment."\n"." ".$condiment);
          
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $totalamount = $product['quantity']*$product['amount'];
          $printer->text(number_format($totalamount,2)."\n");
          $productTotalAmount += $totalamount;
          }
          $printer->text("________________________________________________\n");
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          
          
          
          $printer->text("2% CTL    ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount*.02,2)."\n");
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          
          $printer->text("14% VAT    ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount*.14,2)."\n");
          
          $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
          $printer->text("Total:  ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount,2)."\n");
          $printer->feed(1);
          $printer->cut();
          $printer->close();
          
          } catch (\Throwable $e) {
                    $response_arr['error'] = true;
                    $response_arr['errorMessage'] = $e->getMessage()." - ( ".$val->name." )";// ['status'=>false,'message'=>$e->getMessage()." ( ".$val->name." )"];
          }
          
          	 	$saveActivity = [];
	 	        
 		        $saveActivity['bill_id'] = $bill_id;
		        $saveActivity['user_id'] = $request->user_id;
		        $saveActivity['trans_type'] = "Posting Order - (".$val->name.")";
		        $saveActivity['table_no'] = $bill['table_no'];//implode(',', $tableIds);
		        $saveActivity['order_id'] = $bill['order_id'];
		        $saveActivity['receipt_id'] = "";//$receipt_id;
		        $saveActivity['old_bill_id'] = "";//implode(',', $rows);
		        $saveActivity['old_table_no'] = "";//implode(',', $tableIds);
		        Bill::saveActivity($saveActivity);          
          
          }
        }

               }

             
                return response()->json($response_arr);
            }
        } 


  public function getPrintClasses(Request $request){
           $validator = Validator::make($request->all(), [
                             'user_id' => 'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
        $print_class_data = PrintClass::select('id','name')->get();
        $message = "Print Classes List.";
              return response()->json(['status'=>true,'message'=>$message,'data'=>$print_class_data]);

      }
  }



    public function multiplebillreceipt(Request $request)
    {
        $title = 'Receipt';
        $bill_id = $request->bill_id;
        $print_class_id = $request->print_class_id;
        $row = Bill::where('id', $bill_id)->first();

        $orders = $row->getAssociateOrdersWithBill;

    $my_first_order = [];
    $total_amount_fo_all_bills=[];
    $counter = 1;
    $total_order_count = count($orders);
    $all_charges= [];
    $total_discount = [];
    $getGroupedOrderItems = [];
    $getGroupedOfferItems = [];

        if($total_order_count > 0){   
              $orderId = [];
              foreach($orders as $key=> $val){
            $orderId[$key] = $val->order_id;                
              }
          $order_detail = Order::whereId($orderId[0])->first();       
              $print_class_data = PrintClass::where('id',$print_class_id)->first();
          $response_arr = ['status'=>true,'message'=>'Bill Printed Successfully'];

        try{ 
          $ip = $print_class_data->ip;
          $port = $print_class_data->port;

          $connector = new NetworkPrintConnector($ip, $port);
          
          // Initialize printer object
          $printer = new Printer($connector);
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          $printer->selectPrintMode();

          $printer->feed(1); // create a line

          $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
          
          $printer->setJustification(Printer::JUSTIFY_CENTER);          
          // enter waiter details and date
          if($row->print_count>0){
          $printer->text(" DUPLICATE BILL \n");
          $printer->text("________________________________________________\n\n"); // this line is printed according to printer size
          }
          $printer->text(strtoupper($order_detail->getAssociateRestro->name)."\n");
          $printer->text(strtoupper($order_detail->getAssociateRestro->location)."\n");
          $printer->text($order_detail->getAssociateRestro->telephone." MPESA TILL ".$order_detail->getAssociateRestro->mpesa_till."\n");
          $printer->text("PIN : ".$order_detail->getAssociateRestro->pin." VAT: ".$order_detail->getAssociateRestro->vat."\n");
          $printer->text($order_detail->getAssociateRestro->website_url."\n");
          $printer->text($order_detail->getAssociateRestro->email."\n");
          
          $printer->setJustification(Printer::JUSTIFY_LEFT);          
          $printer->text("Waiter Name: ".ucfirst(getAssociateWaiteWithOrderWithBadge($order_detail))."\n");
          $printer->text('Bill Id: '.$bill_id."\n");

          $printer->setJustification(Printer::JUSTIFY_RIGHT);         
          $printer->text(date('d M\' y h:i A')."\n");
          
          
          $printer->feed(1);

          $printer->text("________________________________________________\n"); // this line is printed according to printer size

          $printer->text("EAT IN \n");

          $productTotalAmount = 0;
          foreach($orders as $key=> $order_s){

            $order_detail = Order::whereId($order_s->order_id)->first();
            $my_first_order = $order_detail;
            $total_amount_fo_all_bills[] = $order_detail->order_final_price;
            $order_discountsdata = json_decode($order_detail->order_discounts);
//              echo "<pre>"; print_r($order_detail->getAssociateItemWithOrder); die;
              foreach($order_detail->getAssociateItemWithOrder as $ordered_item){
              $getGroupedOrderItems[$ordered_item->item_title]['title'] = $ordered_item->item_title;
              $getGroupedOrderItems[$ordered_item->item_title]['amounts'][] = $ordered_item->item_quantity*$ordered_item->price;
              $getGroupedOrderItems[$ordered_item->item_title]['quantity'][] = $ordered_item->item_quantity;
              }
              
          }
              foreach($getGroupedOrderItems as $ordItem){
                
                $grandtotal = array_sum($ordItem['amounts']);
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text(array_sum($ordItem['quantity'])." ".$ordItem['title']."\n"); 
                            
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text(number_format($grandtotal,2)."\n");
                $productTotalAmount += $grandtotal;
              }

          $printer->text("________________________________________________\n");
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          
          $printer->text("2% CTL  :  ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount*.02,2)."\n");
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          
          $printer->text("16% VAT  :  ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount*.14,2)."\n");
          
          $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
          $printer->text("Total Amount To Be Paid :  ");
          $printer->setJustification(Printer::JUSTIFY_RIGHT);
          $printer->text(number_format($productTotalAmount,2)."\n");          
          $printer->text("________________________________________________\n");         
          $printer->feed(1);
          
          $printer->cut();
          $printer->close();

          Bill::where('id', $bill_id)->update(["print_count"=>$row->print_count+1]);
          } catch (\Throwable $e) {
                    $response_arr['error'] = true;
                    $response_arr['errorMessage'] = $e->getMessage();
          }
      //    }
        }
          return response()->json($response_arr);


//        return view('admin.orders.billwithoutheaderreceipt',compact('user_detail','orders','bill_id'));
    }
    

       




 

        public function getWaiterDiscount($order)
        {
          $waiter_discount = [];
          if($order->admin_discount_in_percent && $order->admin_discount_in_percent > 0)
          {
            $inner_array= [
                  'discount_title'=>'Promotion Discount',
                  'discount_value'=>$order->admin_discount_in_percent,
                  'discount_format'=>'PERCENTAGE'
                  ];
            $waiter_discount[] = $inner_array;
          }
          return $waiter_discount;
        }


        public function getdiscounts(Request $request)
        {
            $validator = Validator::make($request->all(), [
                             'restaurant_id' => 'required',
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $dicsounts = Setting::where('name','PROMOTION_DISCOUNT_PERCENTAGE')
              ->where('description','>','0')
              ->get();
              $all_discounts = [];
              if(count($dicsounts) > 0)
              {
                foreach($dicsounts as $dis )
                {
                  $inner_array= [
                  'discount_title'=>'Promotion Discount',
                  'discount_value'=>$dis->description,
                  'discount_format'=>'PERCENTAGE'
                  ];
                 $all_discounts[] = $inner_array;
                }
              }

              $response_arr = ['status'=>true,'message'=>'Available Discounts','discounts'=>$all_discounts];

              if(isset($request->user_id))
              {
                $response_arr['is_have_any_order_for_feedback'] = false;
                $my_last_order = Order::where('user_id',$request->user_id)
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->orderBy('id','desc')->first();
                if( $my_last_order && !$my_last_order->getAssociateFeedback)
                {
                  $response_arr['is_have_any_order_for_feedback'] = true;
                  $response_arr['pending_order_id_for_feedback'] = $my_last_order->id;
                  $response_arr['feedback_status'] = 'PENDING';
                }
                

              }
              return response()->json($response_arr);
             

             
            }
        }


        public function getMyOrder(Request $request)
        {
           $validator = Validator::make($request->all(), [
                             'waiter_id' => 'required',
                             'waiter_order_type_screen'=>'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $waiter_id = $request->waiter_id;
              $waiter_order_type_screen = $request->waiter_order_type_screen;
              $orders_arr = [];
              if($waiter_order_type_screen == 'PAID')
              {
                $message = 'Paid orders';
                $my_order =  Order::where('user_id',$waiter_id)
                ->where('order_type','PREPAID')
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->whereRaw('DATE(created_at) = ?',[date('Y-m-d')])
                ->orderBy('id','desc')
                ->get();
                
                foreach($my_order as $order)
                {
                  $inner_array = [
                  'order_id'=>$order->id,
                  'order_total_price'=>$order->order_final_price,
                   'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                  ];
                  $ordered_item = $order->getAssociateItemWithOrder;
                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if($item->item_delivery_status !='CANCLED')
                    {

                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['item_id'] = $item_detail->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['price'] = $item->price;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                      $inner_array['items'][$counter]['condiments'] = [];
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                             
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }
                      $counter++;
                    }
                  }
                  $orders_arr[] = $inner_array;
                }
                 
              }
              if($waiter_order_type_screen == 'UNPAID')
              {
                $message = 'Unpaid orders detail';
                $my_order =  Order::where('user_id',$waiter_id)
                ->where('order_type','POSTPAID')
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->doesnthave('getAssociateBillRelation')
                //->whereRaw('DATE(created_at) = ?',[date('Y-m-d')])
                ->orderBy('id','desc')
                ->get();
                foreach($my_order as $order)
                {
                  $inner_array = [
                  'order_id'=>$order->id,
                  'is_order_completed'=>$order->status=='COMPLETED'?true:false,
                  'is_payment_done'=>$order->order_type=='PREPAID'?true:false,
                  'table_names'=>getAssociateTableWithOrder($order),
                 // 'order_total_price'=>$order->order_final_price,
                  'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                  'discounts'=>$this->getWaiterDiscount($order),
                  'items'=>[],
                  'offer_data'=>[]
                  ];
                  $ordered_item = $order->getAssociateItemWithOrder;

                  $ordered_offers = $order->getAssociateOffersWithOrder;

                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if(!$item->order_offer_id && $item->item_delivery_status !='CANCLED')
                    {

                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['item_id'] = $item_detail->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['price'] = $item->price;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                     
                      $inner_array['items'][$counter]['item_charges'] = [];
                       $inner_array['items'][$counter]['condiments'] = [];

                      
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }

                      $item_charges_json_arr = json_decode($item->item_charges);
                      if(count($item_charges_json_arr)>0)
                      {
                        $c=0;
                        foreach($item_charges_json_arr as $ch)
                        {
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_name'] =$ch->charges_name;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_value'] =$ch->charges_value;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_format'] =$ch->charges_format;
                          $inner_array['items'][$counter]['item_charges'][$c]['charged_amount'] = isset($ch->charged_amount)? $ch->charged_amount:'0';
                          $c++;
                        }
                      }


                      $counter++;
                    }

                  }
                  $offer_data = [];
                  foreach($ordered_offers as $offers)
                  {
                    $single_offer = [
                    'offer_title'=>$offers->offer_title,
                    'offer_quantity'=>$offers->quantity,
                    'offer_price'=>$offers->price,
                    'offer_image'=>$offers->getAssociateOffersDetail->image?$this->uploadsfolder.'/menu_item_groups/thumb/'.$offers->getAssociateOffersDetail->image:$this->uploadsfolder.'/item_none.png',
                    'offer_charges'=>[],
                    'items'=>[]
                    ];
                     $offer_charges_json_arr = json_decode($offers->offer_charges);
                     if(count($offer_charges_json_arr)>0)
                     {
                        $ch = 0;
                        foreach($offer_charges_json_arr as $offer_charges)
                        {
                          $single_offer['offer_charges'][$ch]['charges_name'] = $offer_charges->charges_name;
                          $single_offer['offer_charges'][$ch]['charges_value'] = $offer_charges->charges_value;
                          $single_offer['offer_charges'][$ch]['charges_format'] = $offer_charges->charges_format;
                          $single_offer['offer_charges'][$ch]['charged_amount'] = $offer_charges->charged_amount;
                          $ch++;
                        }
                     }
                    $offer_ordered_item  = $offers->getAssociateItemwithOffers;
                    $ocounter = 0;
                    foreach($offer_ordered_item as $oitem)
                    {
                      if($oitem->item_delivery_status !='CANCLED')
                      {
                        $oitem_detail = $oitem->getAssociateFooditem;

                        $single_offer['items'][$ocounter]['title'] = $oitem_detail->name;
                        $single_offer['items'][$ocounter]['item_id'] = $oitem_detail->id;
                        //$single_offer['items'][$ocounter]['quantity'] = $oitem->item_quantity;
                        //$single_offer['items'][$ocounter]['price'] = $oitem->price;
                        $single_offer['items'][$ocounter]['comment'] = isset($oitem->item_comment)?$oitem->item_comment:'';
                        $single_offer['items'][$ocounter]['image'] = $oitem_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$oitem_detail->image:$this->uploadsfolder.'/item_none.png';
                        $single_offer['items'][$ocounter]['condiments'] = [];
                        $ocondiments_json_arr = json_decode($oitem->condiments_json);
                        if(count($ocondiments_json_arr)>0)
                        {
                          foreach($ocondiments_json_arr as $ocn)
                          {
                            if(count($ocn->sub_items)>0)
                            {
                              foreach($ocn->sub_items as $ocondiment_item)
                              {
                                $single_offer['items'][$ocounter]['condiments'][] =ucfirst($ocondiment_item->title);
                              }
                            } 
                          }
                        }
                        $ocounter++;
                      }
                    }
                    $offer_data[] = $single_offer;
                  }
                  $inner_array['offer_data'] = $offer_data;
                  $orders_arr[] = $inner_array;
                }
              } 
              return response()->json(['status'=>true,'message'=>$message,'orders'=>$orders_arr]);
            }
        }


        public function getUnpaidOrdersDetailsByOrderids(Request $request)
        {
          $validator = Validator::make($request->all(), [
                        'waiter_id' => 'required',  
                        'order_ids' => 'required',          
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $given_order_ids_arr =  explode(',',$request->order_ids);
              $my_order =  Order::whereIn('id',$given_order_ids_arr)
                ->where('order_type','POSTPAID')
                ->whereNotIn('status',['CANCLED','PENDING'])
                ->whereRaw('DATE(created_at) = ?',[date('Y-m-d')])
                ->orderBy('id','desc')
                ->get();

              if(count($my_order) == count($given_order_ids_arr) && count($given_order_ids_arr)>0)
              {
                  foreach($my_order as $order)
                {
                  $inner_array = [
                  'order_id'=>$order->id,
                 // 'order_total_price'=>$order->order_final_price,

                  'table_names'=>getAssociateTableWithOrder($order),
                  'discounts'=>$this->getWaiterDiscount($order),
                  'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                  'items'=>[],
                  'offer_data'=>[]
                  ];
                  $ordered_item = $order->getAssociateItemWithOrder;

                  $ordered_offers = $order->getAssociateOffersWithOrder;

                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if(!$item->order_offer_id && $item->item_delivery_status !='CANCLED')
                    {

                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['item_id'] = $item_detail->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['price'] = $item->price;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                     
                      $inner_array['items'][$counter]['item_charges'] = [];
                      $inner_array['items'][$counter]['condiments'] = [];
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }

                      $item_charges_json_arr = json_decode($item->item_charges);
                      if(count($item_charges_json_arr)>0)
                      {
                        $c=0;
                        foreach($item_charges_json_arr as $ch)
                        {
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_name'] =$ch->charges_name;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_value'] =$ch->charges_value;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_format'] =$ch->charges_format;
                          $inner_array['items'][$counter]['item_charges'][$c]['charged_amount'] = isset($ch->charged_amount)? $ch->charged_amount:'0';
                          $c++;
                        }
                      }


                      $counter++;
                    }

                  }
                  $offer_data = [];
                  foreach($ordered_offers as $offers)
                  {
                    $single_offer = [
                    'offer_title'=>$offers->offer_title,
                    'offer_quantity'=>$offers->quantity,
                    'offer_price'=>$offers->price,
                    'offer_image'=>$offers->getAssociateOffersDetail->image?$this->uploadsfolder.'/menu_item_groups/thumb/'.$offers->getAssociateOffersDetail->image:$this->uploadsfolder.'/item_none.png',
                    'offer_charges'=>[],
                    'items'=>[]
                    ];
                     $offer_charges_json_arr = json_decode($offers->offer_charges);
                     if(count($offer_charges_json_arr)>0)
                     {
                        $ch = 0;
                        foreach($offer_charges_json_arr as $offer_charges)
                        {
                          $single_offer['offer_charges'][$ch]['charges_name'] = $offer_charges->charges_name;
                          $single_offer['offer_charges'][$ch]['charges_value'] = $offer_charges->charges_value;
                          $single_offer['offer_charges'][$ch]['charges_format'] = $offer_charges->charges_format;
                          $single_offer['offer_charges'][$ch]['charged_amount'] = $offer_charges->charged_amount;
                          $ch++;
                        }
                     }
                    $offer_ordered_item  = $offers->getAssociateItemwithOffers;
                    $ocounter = 0;
                    foreach($offer_ordered_item as $oitem)
                    {
                      if($oitem->item_delivery_status !='CANCLED')
                      {
                        $oitem_detail = $oitem->getAssociateFooditem;

                        $single_offer['items'][$ocounter]['title'] = $oitem_detail->name;
                        $single_offer['items'][$ocounter]['item_id'] = $oitem_detail->id;
                        $single_offer['items'][$ocounter]['comment'] = isset($oitem->item_comment)?$oitem->item_comment:'';
                        $single_offer['items'][$ocounter]['image'] = $oitem_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$oitem_detail->image:$this->uploadsfolder.'/item_none.png';
                        $single_offer['items'][$ocounter]['condiments'] = [];
                        $ocondiments_json_arr = json_decode($oitem->condiments_json);
                        if(count($ocondiments_json_arr)>0)
                        {
                          foreach($ocondiments_json_arr as $ocn)
                          {
                            if(count($ocn->sub_items)>0)
                            {
                              foreach($ocn->sub_items as $ocondiment_item)
                              {
                                $single_offer['items'][$ocounter]['condiments'][] =ucfirst($ocondiment_item->title);
                              }
                            } 
                          }
                        }
                        $ocounter++;
                      }
                    }
                    $offer_data[] = $single_offer;
                  }
                  $inner_array['offer_data'] = $offer_data;
                  $orders_arr[] = $inner_array;
                }

                 return response()->json(['status'=>true,'message'=>'Orders Detail','orders'=>$orders_arr]);
              }
              else
              {
                $valid_ids = $my_order->pluck('id')->toArray();
                foreach($given_order_ids_arr as $given_order_id)
                {
                  if(!in_array($given_order_id,$valid_ids))
                  {
                    return response()->json(['status'=>false,'message'=>$given_order_id.' Invalid order id']);
                    break;
                  }
                }
               /* echo '<pre>';
                print_r($valid_ids);
                die;*/
                return response()->json(['status'=>false,'message'=>'Invalid Request']);
              }
            }
        }


        public function updateOrderUnpaidToPaidByWaiter(Request $request)
        {
          $validator = Validator::make($request->all(), [
                       
                        'payment_mode' => 'required',
                        'waiter_id' => 'required',  
                        'orders' => 'required',      
                          
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $orders = json_decode($request->orders);
              $getReceiptOrderids = [];
              $total_payed_amount = [];
              if(count($orders)>0)
              {
                foreach($orders as $row)
                {
                  $order = Order::where('id',$row->order_id)->first();
                  
                  $order->order_final_price = $row->order_final_payed_price;
                  $order->order_type = 'PREPAID';

                  $total_payed_amount[] = $row->order_final_payed_price;

                  if(isset($row->order_charges))
                  {
                   $order->order_charges = json_encode($row->order_charges); 
                  }

                  if(isset($request->mpesa_request_id))
                  {
                   $order->mpesa_request_id = $request->mpesa_request_id; 
                  }

                  if(isset($row->order_discounts) && $row->order_discounts != '')
                  {
                    $order->order_discounts = json_encode($row->order_discounts); 
                  }


                   if(isset($request->compliementary_reason))
                  {
                   $order->compliementary_reason = $request->compliementary_reason; 
                  }



                  if(isset($request->complimentry_code))
                  {
                    $order->complimentry_code = $request->complimentry_code; 
                  } 
                  $order->save();
                  $getReceiptOrderids[] = $order->id;
                }
              }

              //make receipt
              $receipt_id = $this->makeReceipt($getReceiptOrderids,$request->waiter_id);


              $billreceipt =  new ReceiptSummaryPayment();
              $billreceipt->order_receipt_id = $receipt_id;
              $billreceipt->payment_mode = $request->payment_mode;
              $billreceipt->narration = '';
              $billreceipt->amount = array_sum($total_payed_amount);
              if(isset($request->mpesa_request_id))
              {
                $billreceipt->mpesa_request_id = $request->mpesa_request_id;
              }
              $billreceipt->save();

              $my_receipt = OrderReceipt::whereId($receipt_id)->first();
              $this->managetimeForall($receipt_id,$my_receipt->created_at);

              

               return response()->json(['status'=>true,'message'=>'Order Marked As Paid','receipt_id'=>$receipt_id]);
            }

        }


        public function markItemAsDelivered(Request $request)
        {
           $validator = Validator::make($request->all(), [
                        'order_item_id' => 'required',
                        'waiter_id' => 'required',    
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $order_item = OrderedItem::where('id',$request->order_item_id)->first();
              $order_item->item_delivery_status = 'COMPLETED';
              $order_item->save();
              return response()->json(['status'=>true,'message'=>'Item mark as completed']);
            }
        }

  public function combineBill(Request $request)
    {

            $response_arr['status'] = false;
            $response_arr['error'] = true;
            $response_arr['message'] = "The Combine Bill Functionality has been temporarily disabled for update purposes";
			return response()->json($response_arr);
//         $rows = [];
//          $validator = Validator::make($request->all(), [
//                         'bill_id' => 'required',
//                         'user_id' => 'required'
//                     ]);
//         if ($validator->fails()) 
//         {
//             $error = $this->validationHandle($validator->messages()); 
//             return response()->json(['status'=>false,'message'=>$error]);
//         }
//         else
//         {
//     $rows = $request->bill_id;
// //    echo "<pre>"; print_r($rows); die;
//     $waiter_id =  $request->user_id;
//         if(count($rows)>0)
//         {
//             $lists_count = Bill::select(['order_id'])
//                 ->where('status','PENDING')
//                 ->whereIn('id',$rows)
//                 ->count();
//            //     echo count($rows)." - ".$lists_count; die;
//             if(count($rows) == $lists_count)
//             {
//                 //generatebill
                
//                 $ordersId = BillOrderRelation::whereIn('bill_id',$rows)->pluck("order_id");
                
//                 $tableId = OrderBookedTable::where('order_id',$ordersId[0])->first()->table_id;
//                 //echo $tableId; die;
//                 $new_bill = new Bill();
//                 $new_bill->user_id = $waiter_id;
//                 $new_bill->slug = rand(1111,99999).strtotime(date('Y-m-d h:i:s'));
//                 $new_bill->save();
//                 $bill_id = $new_bill->id;

//                 Bill::whereIn('id',$rows)->update(["status"=>"COMPLETED"]);

//                 foreach($ordersId as $order_id)
//                 {
//                     BillOrderRelation::updateOrCreate(
//                             ['bill_id' => $bill_id,'order_id'=>$order_id]
//                             );  
//                          OrderBookedTable::where('order_id',$order_id)->update(["table_id"=>$tableId]);

//                  }
         
//             $saveActivity = [];
            
//                 $tableIds = OrderBookedTable::whereIn('order_id',$ordersId)->pluck('table_id')->toArray();
//               //  echo "<pre>"; print_r($tableIds); die;
//             $saveActivity['bill_id'] = $bill_id;
//             $saveActivity['user_id'] = $request->user_id;
//             $saveActivity['trans_type'] = "Combining Bill (App)";
//             $saveActivity['table_no'] = $tableId;//implode(',', $tableIds);
//             $saveActivity['order_id'] = "";
//             $saveActivity['receipt_id'] = "";//$receipt_id;
//             $saveActivity['old_bill_id'] = implode(',', $rows);
//             $saveActivity['old_table_no'] = implode(',', $tableIds);
//             Bill::saveActivity($saveActivity);

                 
//             return response()->json(['status'=>true,'message'=>'Bill Commbined successfully']);
//             }
//             else
//             {
//             return response()->json(['status'=>false,'message'=>'Something went wrong']);
//             }
//         }
//         }
    }
    
    public function combineBillBackup(Request $request)
    {
        $rows = [];
         $validator = Validator::make($request->all(), [
                        'bill_id' => 'required',
                        'user_id' => 'required'
                    ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {
    $rows = $request->bill_id;
//    echo "<pre>"; print_r($rows); die;
    $waiter_id =  $request->user_id;
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
		        $saveActivity['user_id'] = $request->user_id;
		        $saveActivity['trans_type'] = "Combining Bill (App)";
		        $saveActivity['table_no'] = $tableId;//implode(',', $tableIds);
		        $saveActivity['order_id'] = "";
		        $saveActivity['receipt_id'] = "";//$receipt_id;
		        $saveActivity['old_bill_id'] = implode(',', $rows);
		        $saveActivity['old_table_no'] = implode(',', $tableIds);
		        Bill::saveActivity($saveActivity);

                 
            return response()->json(['status'=>true,'message'=>'Bill Commbined successfully']);
            }
            else
            {
            return response()->json(['status'=>false,'message'=>'Something went wrong']);
            }
        }
        }
    }
   
    public function combineBills(Request $request)
    {
	   // echo "<pre>"; print_r($request->all()); die;
        $rows = [];
         $validator = Validator::make($request->all(), [
                        'master_bill_id' => 'required',
                        'bill_ids' => 'required',
                        'user_id' => 'required'
                    ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {
		$rows = $request->bill_ids;
		$bill_id = $request->master_bill_id;
		$waiter_id =  $request->user_id;
        if(count($rows)>0)
        {
            $lists_count = Bill::select(['order_id'])
                ->where('status','PENDING')
                ->whereIn('id',$rows)
                ->count();
            if(count($rows) == $lists_count)
            {
                //generatebill

                
                $masterOrderId = BillOrderRelation::where('bill_id',$bill_id)->first();                
                $tableId = OrderBookedTable::where('order_id',$masterOrderId->order_id)->first()->table_id;

                $ordersId = BillOrderRelation::whereIn('bill_id',$rows)->pluck("order_id");

                $tableIds = OrderBookedTable::whereIn('order_id',$ordersId)->pluck('table_id')->toArray();

                Bill::whereIn('id',$rows)->update(["status"=>"COMPLETED"]);

                foreach($ordersId as $order_id)
                {
                    BillOrderRelation::where('order_id',$order_id)->update(['bill_id' => $bill_id]);  
                    
                         OrderBookedTable::where('order_id',$order_id)->update(["table_id"=>$tableId]);

                 }
         
         	 	$saveActivity = [];
	 	        
              //  echo "<pre>"; print_r($tableIds); die;
		        $saveActivity['bill_id'] = $bill_id;
		        $saveActivity['user_id'] = $request->user_id;
		        $saveActivity['trans_type'] = "Combining Bill (App)";
		        $saveActivity['table_no'] = $tableId;//implode(',', $tableIds);
		        $saveActivity['order_id'] = "";
		        $saveActivity['receipt_id'] = "";//$receipt_id;
		        $saveActivity['old_bill_id'] = implode(',', $rows);
		        $saveActivity['old_table_no'] = implode(',', $tableIds);
		        Bill::saveActivity($saveActivity);

                 
            return response()->json(['status'=>true,'message'=>'Bill Commbined successfully']);
            }
            else
            {
            return response()->json(['status'=>false,'message'=>'Something went wrong']);
            }
        }
        }
    }



        public function markOrderAsDelivered(Request $request)
        {
           $validator = Validator::make($request->all(), [
                        'order_id' => 'required',
                        'waiter_id' => 'required',    
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $order = Order::where('id',$request->order_id)->first();
              $order->status = 'COMPLETED';
              $order->save();

              if($order->getAssociateUserForOrder->role_id =='11')
              {
                $all_tables=$this->getOrderRelatedTable($order);
                $table_ids = array_column($all_tables,'id');
                TableManager::whereIn('id',$table_ids)->update(['booking_status'=>'FREE','booking_for_user_id'=>null]);
              }
              



              return response()->json(['status'=>true,'message'=>'Order mark as completed']);
              
              
            }
        }

        public function getOrderRelatedTable($order)
        {
          $my_tables = [];
          $c = 0;
          foreach($order->getAssociateTableWithOrder as $table_order)
          {
            $my_tables[$c]['id'] = $table_order->getRelativeTableData->id;
            $my_tables[$c]['name'] = $table_order->getRelativeTableData->name;
            $c++;
          }
          return $my_tables;
        }

        public function getOrderInProgressByWaiterId(Request $request)
        {
          $validator = Validator::make($request->all(), [
                       
                        'waiter_id' => 'required',

                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $all_related_orders_ids_arr = $this->getAssocitaedOrderIdForThiswaiter($request->waiter_id);
              $order_in_process_arr = [];
              if(count($all_related_orders_ids_arr)>0)
              {

                $default_status = 'NEW_ORDER';

                if(isset($request->getByStatus) && $request->getByStatus != '')
                {
                  $default_status = $request->getByStatus;
                }
                $my_order =  Order::whereIn('id',$all_related_orders_ids_arr)
                ->where('status',$default_status)
                //->whereRaw('DATE(created_at) = ?',[date('Y-m-d')])
                ->orderBy('id','desc')
                ->get();
                
                foreach($my_order as $order)
                {
                  $inner_array = [
                  'order_id'=>$order->id,
                  'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                  'is_completed'=>$order->status=='COMPLETED'?true:false,
                  'current_status'=>$order->status,
                  'discounts'=>$this->getWaiterDiscount($order),
                  'order_tables'=>$this->getOrderRelatedTable($order)
                  ];

                  $ordered_item = $order->getAssociateItemWithOrder;
                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if($item->item_delivery_status !='CANCLED')
                    {
                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['is_completed'] = $item->item_delivery_status=='COMPLETED'?true:false;

                       $inner_array['items'][$counter]['item_delivery_status'] = $item->item_delivery_status;


                       $inner_array['items'][$counter]['current_status'] = $item->item_delivery_status;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['order_item_id'] = $item->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                      $inner_array['items'][$counter]['condiments'] = [];
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                             
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }
                      $counter++;
                    }
                  }


                  $order_in_process_arr[] = $inner_array;
                  
                }

              }
             return response()->json(['status'=>true,'message'=>'Order in process listing','listing'=>$order_in_process_arr]);

            }
        }

        public function getAssocitaedOrderIdForThiswaiter($waiter_id)
        {
          $employeeyTables_arr =  EmployeeTableAssignment::where('user_id',$waiter_id)->pluck('table_manager_id')->toArray();
          if(count($employeeyTables_arr)>0)
          {
            $all_related_orders =  OrderBookedTable::whereIn('table_id',$employeeyTables_arr)->pluck('order_id')->toArray();
            if(count($all_related_orders)>0)
            {
              return $all_related_orders;
            }
            else
            {
              return [];
            }
          }
          else
          {
            return [];
          }
         
        }

        public function getMpesaRequestStatus(Request $request)
        {
          $validator = Validator::make($request->all(), [
                        'mpesa_request_id' => 'required'
                         
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
             
              $mpesa_request_id = $request->mpesa_request_id;
              $is_exist = MpesaTransactionDetail::where('mpesa_request_id',$mpesa_request_id)->first();

              if($is_exist)
              {
                $message = 'CONFIRMED';
                if($is_exist->is_done == '0')
                {
                  $message = 'CANCLED';
                }
                return response()->json(['status'=>true,'message'=>'details available','payment_status'=>$message]);
              }
              else
              {
                return response()->json(['status'=>true,'message'=>'MPESA request is pending please try again','payment_status'=>'PENDING']);
              }
            }
        }


        public function makeBill(Request $request)
        {
           $validator = Validator::make($request->all(), [
                        'waiter_id' => 'required',
                        'order_ids' => 'required',
                         
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
             
              $order_ids = explode(',',$request->order_ids);
              $lists = Order::where('order_type','POSTPAID')
              ->where('status','COMPLETED')
              ->doesnthave('getAssociateBillRelation')
              ->whereIn('id',$order_ids)
              ->get();
              $lists_count = count($lists);
              $all_order_related_tables = [];

              if(count($order_ids) == $lists_count)
              {
                //getting waiter table check
                foreach($lists as $list)
                {
                  foreach($list->getAssociateTableWithOrder as $table_info)
                  {
                    $all_order_related_tables[] = $table_info->table_id;
                  }
                }


                if(count($all_order_related_tables)>0)
                {
                  $is_not_related_to_this_waiter = EmployeeTableAssignment::whereIn('table_manager_id',$all_order_related_tables)->where('user_id','!=',$request->waiter_id)->get();
                  if(count($is_not_related_to_this_waiter)>0)
                  {
                    return response()->json(['status'=>false,'message'=>'Please try again']);
                  }
                  else
                  {
                    // all orders related to this waiter now we can make a bill
                    $new_bill = new Bill();
                    $new_bill->user_id = $request->waiter_id;
                    $new_bill->slug = rand(1111,99999).strtotime(date('Y-m-d h:i:s'));
                    $new_bill->save();
                    $bill_id = $new_bill->id;
                    foreach($order_ids as $order_id)
                    {
                        BillOrderRelation::updateOrCreate(
                                ['bill_id' => $bill_id,'order_id'=>$order_id]
                                );  
                    }

                    return response()->json(['status'=>true,'message'=>'Bill generated successfully','bill_id'=>$bill_id]);
                    // 'Bill generated successfully your bill id is: '.$bill_id

                  }


                }
                else
                {
                  return response()->json(['status'=>false,'message'=>'Please try again']);
                }



               
                
              }
              else
              {
                 return response()->json(['status'=>false,'message'=>'Please try again']);
              }
            }
        }
        public function getMyunpaidBills(Request $request)
        {
          $validator = Validator::make($request->all(), [
                        'waiter_id' => 'required'      
                ]);
            if ($validator->fails()) 
            {
              $error = $this->validationHandle($validator->messages());   
              return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              $waiter_id = $request->waiter_id;
              $lists = Bill::where('status','PENDING')->where('user_id',$waiter_id)->get();

              $bill_listing = [];
              $i=0;
              foreach($lists as $list)
              {
                $bill_listing[$i]['bill_id'] = $list->id;
                $bill_listing[$i]['bill_narration'] = $list->bill_narration;
                $bill_listing[$i]['Table_id'] = getTableByBillid($list->id);
                $bill_listing[$i]['Table_name'] = getTableNumberByBillid($list->id);

                
                $bill_listing[$i]['total_orders'] = count($list->getAssociateOrdersWithBill);
                $total_bill = [];
                foreach($list->getAssociateOrdersWithBill as $single_order)
                {
                  $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
                }
                $bill_listing[$i]['bill_amount'] = array_sum($total_bill);
                $i++;
              }
              return response()->json(['status'=>true,'message'=>'Bill listing','listing'=>$bill_listing]);
            }
        }

        public function markUnpaidBillToPaid(Request $request)
        {
          $validator = Validator::make($request->all(), [
                        'waiter_id' => 'required',
                        'bill_id'=>'required',
                        'payment_mode'=>'required', 
                        'payed_amount'=>'required',     
                ]);
          if ($validator->fails()) 
          {
            $error = $this->validationHandle($validator->messages());   
            return response()->json(['status'=>false,'message'=>$error]);
          }
          else
          {
            $waiter_id = $request->waiter_id;
             $bill_id = $request->bill_id;
            $bill = Bill::where('status','PENDING')
              ->where('user_id',$waiter_id)
              ->where('id',$bill_id)
              ->first();
            if($bill)
            {
              if($request->payment_mode == 'MPESA')
              {
                if($request->mpesa_request_id)
                {
                  $mpesa_request_id = $request->mpesa_request_id;
                  $is_payment_done = MpesaTransactionDetail::where('mpesa_request_id',$mpesa_request_id)->where('is_done','1')->first();
                  if($is_payment_done)
                  {
                    $bill->status = 'COMPLETED';
                    $bill->save();
                    $receipt = new OrderReceipt();
                    $receipt->cashier_id = $waiter_id;
                    $receipt->user_id = $waiter_id;
                    $receipt->save();
                    $receipt_id = $receipt->id;
                    foreach($bill->getAssociateOrdersWithBill as $bill_orders)
                    {
                        OrderReceiptRelation::updateOrCreate(
                                ['order_receipt_id' => $receipt_id,'order_id'=>$bill_orders->order_id]
                                );  
                        $order = Order::whereId($bill_orders->order_id)->first();
                        $order->order_type = 'PREPAID';
                        $order->mpesa_request_id = $request->mpesa_request_id;
                        $order->save();
                    }
                    $billreceipt =  new ReceiptSummaryPayment();
                    $billreceipt->order_receipt_id = $receipt_id;
                    $billreceipt->payment_mode = $request->payment_mode;
                    $billreceipt->narration = '';
                    $billreceipt->amount =$request->payed_amount;
                    $billreceipt->mpesa_request_id =$request->mpesa_request_id;
                    $billreceipt->save();
                    $my_receipt = OrderReceipt::whereId($receipt_id)->first();
                    $this->managetimeForall($receipt_id,$my_receipt->created_at);
                  }
                  else
                  {
                    return response()->json(['status'=>false,'message'=>'mpesa payment not completed yet']);
                  }
                }
                else
                {
                  return response()->json(['status'=>false,'message'=>'mpesa request id is required']);
                }
              }
              if($request->payment_mode == 'COMPLEMENTARY')
              {
                if($request->complimentry_code)
                {

                  $bill->status = 'COMPLETED';
                  $bill->save();
                  $receipt = new OrderReceipt();
                  $receipt->cashier_id = $waiter_id;
                  $receipt->user_id = $waiter_id;
                  $receipt->save();
                  $receipt_id = $receipt->id;
                  foreach($bill->getAssociateOrdersWithBill as $bill_orders)
                  {
                      OrderReceiptRelation::updateOrCreate(
                              ['order_receipt_id' => $receipt_id,'order_id'=>$bill_orders->order_id]
                              );  
                      $order = Order::whereId($bill_orders->order_id)->first();
                      $order->order_type = 'PREPAID';
                      $order->complimentry_code = $request->complimentry_code;
                      if(isset($request->compliementary_reason))
                      {
                       $order->compliementary_reason = $request->compliementary_reason;
                      }
                      $order->save();
                  }
                  $billreceipt =  new ReceiptSummaryPayment();
                  $billreceipt->order_receipt_id = $receipt_id;
                  $billreceipt->payment_mode = $request->payment_mode;
                  $billreceipt->narration = '';
                  $billreceipt->amount =$request->payed_amount;
                  $billreceipt->save();
                  $my_receipt = OrderReceipt::whereId($receipt_id)->first();
                  $this->managetimeForall($receipt_id,$my_receipt->created_at);
                  $this->updateManageStockMoves($bill_orders->order_id);
                   return response()->json(['status'=>true,'message'=>'Bill marked as paid','receipt_id'=>$receipt_id]);
                  //mark bill as paid



                }
                else
                {
                  return response()->json(['status'=>false,'message'=>'Complimentry code is required']);
                }
              }
            }
            else
            {
              return response()->json(['status'=>false,'message'=>'Invalid bill id']);
            }
          }
        }

        public function markUnpaidBillToPaidByManager(Request $request)
        {
          $validator = Validator::make($request->all(), [
                        'manager_id' => 'required',
                        'bill_id'=>'required',
                        'complimentry_code'=>'required', 
                        'compliementary_reason'=>'required',
                        'payed_amount'=>'required',     
                ]);
          if ($validator->fails()) 
          {
            $error = $this->validationHandle($validator->messages());   
            return response()->json(['status'=>false,'message'=>$error]);
          }
          else
          {
            $manager_id = $request->manager_id;
             $bill_id = $request->bill_id;
            $bill = Bill::where('status','PENDING')
              ->where('id',$bill_id)
              ->first();
            if($bill)
            {
                 if($request->complimentry_code)
                {

                  $bill->status = 'COMPLETED';
                  $bill->save();
                  $receipt = new OrderReceipt();
                  $receipt->cashier_id = $manager_id;
                  $receipt->user_id = $manager_id;
                  $receipt->save();
                  $receipt_id = $receipt->id;
                  foreach($bill->getAssociateOrdersWithBill as $bill_orders)
                  {
                      OrderReceiptRelation::updateOrCreate(
                              ['order_receipt_id' => $receipt_id,'order_id'=>$bill_orders->order_id]
                              );  
                      $order = Order::whereId($bill_orders->order_id)->first();
                      $order->order_type = 'PREPAID';
                      $order->complimentry_code = $request->complimentry_code;
                      if(isset($request->compliementary_reason))
                      {
                       $order->compliementary_reason = $request->compliementary_reason;
                      }
                      $order->save();
                  }
                  $billreceipt =  new ReceiptSummaryPayment();
                  $billreceipt->order_receipt_id = $receipt_id;
                  $billreceipt->payment_mode = $request->payment_mode;
                  $billreceipt->narration = '';
                  $billreceipt->amount =$request->payed_amount;
                  $billreceipt->save();
                  $my_receipt = OrderReceipt::whereId($receipt_id)->first();
                  $this->managetimeForall($receipt_id,$my_receipt->created_at);
                  $this->updateManageStockMoves($bill_orders->order_id);
                   return response()->json(['status'=>true,'message'=>'Bill marked as paid','receipt_id'=>$receipt_id]);
                  //mark bill as paid
                }
                else
                {
                  return response()->json(['status'=>false,'message'=>'Complimentry code is required']);
                }
             }
            else
            {
              return response()->json(['status'=>false,'message'=>'Invalid bill id']);
            }
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

                    $draccountno =  $inventory_item_row->getInventoryCategoryDetail->getIssueGlDetail->account_code;
                        $deficient_quantity = $weight - $inventory_qoh;
                        $stockMove = new WaStockMove();
                        $stockMove->user_id = $order->user_id;
                        $stockMove->ordered_item_id = $order_item_row->id;
                        $stockMove->restaurant_id = $order_item_row->restaurant_id;
                        $stockMove->wa_location_and_store_id = $wa_location_and_store_id;
                        $stockMove->wa_inventory_item_id = $inventory_item_row->id;
                        $stockMove->standard_cost = $inventory_item_row->standard_cost;
                        $stockMove->qauntity = -($order_item_row->item_quantity );
                        $stockMove->stock_id_code = $inventory_item_row->stock_id_code;
                        $stockMove->grn_type_number = $series_module->type_number;
                        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                        $stockMove->price = $inventory_item_row->standard_cost;
                        $stockMove->refrence = $order->slug;
                        $stockMove->save();

                        $dr =  new WaGlTran();
                        $dr->grn_type_number = $series_module->type_number;
                        $dr->grn_last_used_number = $series_module->last_number_used;
                        $dr->transaction_type = $intr_smodule->description;
                        $dr->transaction_no =  $grn_number;
                        $dr->trans_date = $dateTime;
                        $dr->restaurant_id = $order_item_row->restaurant_id;
                        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $dr->account = $craccountno;
                        $dr->amount = '-' . ( $inventory_item_row->standard_cost * $order_item_row->item_quantity);
                        $dr->narrative = $order->slug . '/' . $inventory_item_row->stock_id_code . '/' . $inventory_item_row->title . '/' . $inventory_item_row->standard_cost . '@' . $order_item_row->item_quantity;
                        $dr->save();



                        $dr =  new WaGlTran();
                        $dr->grn_type_number = $series_module->type_number;
                        $dr->grn_last_used_number = $series_module->last_number_used;
                        $dr->transaction_type = $intr_smodule->description;
                        $dr->transaction_no =  $grn_number;
                        $dr->trans_date = $dateTime;
                        $dr->restaurant_id = $order_item_row->restaurant_id;
                        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $dr->account = $draccountno;
                        $camount = $inventory_item_row->standard_cost * $order_item_row->item_quantity;
                        $dr->amount = $camount;
                        $dr->narrative = $order->slug . '/' . $inventory_item_row->stock_id_code . '/' . $inventory_item_row->title . '/' . $inventory_item_row->standard_cost . '@' . $order_item_row->item_quantity;
                        $dr->save();



                    
                }
            }
            }
            return;
        }
    public function checkqty(Request $request){
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required|exists:users,id',
                    'checkout_json' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {

               $json = json_decode($request->checkout_json);
	           if(isset($json->Appetizerdata) && count($json->Appetizerdata)>0)
               {
		        foreach($json->Appetizerdata as $item)
		        {			   
		            $food_item_row = \App\Model\FoodItem::where('id', $item->appetizer_id)->first();
		            $less_quantity = 0;
		            if($food_item_row->check_stock_before_sale){	            
		                $recipe_row = $food_item_row->getAssociateRecipe;
		                $recipe_ingredients = isset($food_item_row->getAssociateRecipe->getAssociateIngredient) ? $food_item_row->getAssociateRecipe->getAssociateIngredient : [];
		                $qtyavailable = 0;
		                $wa_location_and_store_id = isset($recipe_row->wa_location_and_store_id) ? $recipe_row->wa_location_and_store_id : '';
		                foreach ($recipe_ingredients as $key => $recipe_ingredient_row) {
		                    $inventory_item_row = $recipe_ingredient_row->getAssociateItemDetail;
		                    $inventory_qoh = getItemAvailableQuantity($inventory_item_row->stock_id_code, $wa_location_and_store_id);
		                    $weight = $recipe_ingredient_row->weight;
		                    $weight_for_required_required_quantity = $weight * $item->quantity;
		                    $qtyavailable = $inventory_qoh;
		                    if ($weight_for_required_required_quantity > $inventory_qoh) {
		                        $less_quantity = 1;
		                        break;
		                    }
		                }
				        if($less_quantity == 1){
				             return response()->json(['status'=>false,'message'=>"The Quantity available for ".$item->title." is ".$qtyavailable." which is less than the quantity that you are ordering. Please remove this from your cart in order to continue with check out."]);
				        }
		            }
		         }
               }
             return response()->json(['status'=>true,'message'=>'Enough quantity is available.']);
		}
	    
    }    
    public function checkEnoughInventoryItemQuanity(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required|exists:users,id',
                    'food_item_id' => 'required|exists:food_items,id',
                    'required_quantity' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {
            $food_item_row = \App\Model\FoodItem::where('id', $request->food_item_id)->first();
            $less_quantity = 0;
            $qtyavailable = 0;
                $recipe_row = $food_item_row->getAssociateRecipe;
                $recipe_ingredients = isset($food_item_row->getAssociateRecipe->getAssociateIngredient) ? $food_item_row->getAssociateRecipe->getAssociateIngredient : [];
                $wa_location_and_store_id = isset($recipe_row->wa_location_and_store_id) ? $recipe_row->wa_location_and_store_id : '';
                foreach ($recipe_ingredients as $key => $recipe_ingredient_row) {
                    $inventory_item_row = $recipe_ingredient_row->getAssociateItemDetail;
                    if($inventory_item_row){	                    
	                    $inventory_qoh = getItemAvailableQuantity($inventory_item_row->stock_id_code, $wa_location_and_store_id);
                    }else{
	                    $inventory_qoh = 0;
                    }
                    $qtyavailable = $inventory_qoh;
                    $weight = $recipe_ingredient_row->weight;
                    $weight_for_required_required_quantity = $weight * $request->required_quantity;
                    if ($weight_for_required_required_quantity > $inventory_qoh) {
                        $less_quantity = 1;
                        break;
                    }
                }
        }
        if($less_quantity == 0){
             return response()->json(['status'=>true,'qty'=>$qtyavailable,'check_stock_before_sale'=>(int)$food_item_row->check_stock_before_sale,'message'=>'Enough quantity is available.']);
        }
        else {
             return response()->json(['status'=>true,'qty'=>$qtyavailable,'check_stock_before_sale'=>(int)$food_item_row->check_stock_before_sale,'message'=>"The Quantity available for ".$food_item_row->title." is ".$qtyavailable." which is less than the quantity that you are ordering. Please remove this from your cart in order to continue with check out."]);
        }
    }


  public function getBillsWithOrderByTableId(Request $request)
  {
       $validator = Validator::make($request->all(), [
                  'table_id' => 'required',
                   
        ]);
        if ($validator->fails()) 
        {
          $error = $this->validationHandle($validator->messages());
          return response()->json(['status' => false, 'message' => $error]);
        } 
        else 
        {
          $unbilledOrderForThistable = OrderBookedTable::where('table_id',$request->table_id)
             ->whereHas('getRelativeOrderData', function ($query){ 
                $query->where('order_type','POSTPAID')
                 ->whereIn('status',['NEW_ORDER','DELIVERED','COMPLETED'])
                ->whereHas('getAssociateBillRelation',function($qq){$qq->whereHas('getAssociateBill',function($qqq){ $qqq->where('status','PENDING'); });});})->pluck('order_id')->toArray();
            $final_data = [];
            if(count($unbilledOrderForThistable)>0)
            {
               $order_ids = $unbilledOrderForThistable;
                $my_order =  Order::with('getAssociateTableWithOrder','getAssociateTableWithOrder.getRelativeTableData')->whereIn('id',$order_ids)
                ->orderBy('id','desc')
                ->get();

                foreach($my_order as $order)
                {
                  $final_data[$order->getAssociateBillRelation->bill_id]['bill_id'] = $order->getAssociateBillRelation->bill_id;
                  $final_data[$order->getAssociateBillRelation->bill_id]['table_number'] = @$order->getAssociateTableWithOrder[0]->getRelativeTableData->name;
                  $final_data[$order->getAssociateBillRelation->bill_id]['bill_narration'] = $order->getAssociateBillRelation->getAssociateBill->bill_narration;
                  $inner_array = [
                  'order_id'=>$order->id,
                  'order_total_price'=>$order->order_final_price,
                   'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                  ];
                                    $ordered_item = $order->getAssociateItemWithOrder;
                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if($item->item_delivery_status !='CANCLED')
                    {

                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['item_id'] = $item_detail->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['price'] = $item->price;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                      $inner_array['items'][$counter]['condiments'] = [];
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                             
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }

                      $item_charges_json_arr = json_decode($item->item_charges);
                      if(count($item_charges_json_arr)>0)
                      {
                        $c=0;
                        foreach($item_charges_json_arr as $ch)
                        {
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_name'] =$ch->charges_name;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_value'] =$ch->charges_value;
                          $inner_array['items'][$counter]['item_charges'][$c]['charges_format'] =$ch->charges_format;
                          $inner_array['items'][$counter]['item_charges'][$c]['charged_amount'] = isset($ch->charged_amount)? $ch->charged_amount:'0';
                          $c++;
                        }
                      }
                      $counter++;
                    }
                  }


                  $final_data[$order->getAssociateBillRelation->bill_id]['orders'][] =  $inner_array;


                 
                }

              
            }
           
            $final_data = array_values($final_data);
           

            
          return response()->json(['status'=>true,'message'=>'Table Info.','data'=>$final_data]);
        }
  }
    
    

}  
    