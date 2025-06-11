<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Model\Order;
use App\Model\OrderedItem;
use App\Model\WaCashSales;
use App\Model\WaCashSalesItem;
use App\Model\OrderOffer;
use App\Model\FoodItem;
use App\Model\Category;
use App\Model\User;
use App\Model\CategoryRelation;
use App\Model\ItemCategoryRelation;
use App\Model\Restaurant;
use App\Model\PaymentMethod;
use App\Model\Condiment;
use App\Model\ReceiptSummaryPayment;
use App\Model\OrderReceipt;
use App\Model\WalletTransaction;

use Session;
use Excel;
use PDF;

class ReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'reports';
        $this->title = 'Reports';
        $this->pmodule = 'reports';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    } 

    public function getdiscountsReports(Request $request)
    {
        
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-discounts-reports']) || $permission == 'superadmin')
        {
            $detail = [];
            $Complementary = [];
            $Admin_discounts_arr = [];
            $Customer_discounts_arr=[];
            $complemanetary_records = ReceiptSummaryPayment::select('amount')->where('payment_mode','COMPLEMENTARY');
            $Customer_discounts_records  = Order::select('order_discounts','user_id','id','billing_time')->where('status','COMPLETED')
            ->where('order_type','PREPAID')->where('order_discounts','!=',NULL);
            if ($request->has('start-date'))
            {
                $complemanetary_records = $complemanetary_records->where('created_at','>=',$request->input('start-date'));
                $Customer_discounts_records = $Customer_discounts_records->where('billing_time','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $complemanetary_records = $complemanetary_records->where('created_at','<=',$request->input('end-date'));
                $Customer_discounts_records = $Customer_discounts_records->where('billing_time','<=',$request->input('end-date'));
            }
            $Complementary = $complemanetary_records->pluck('amount')->toArray();
            $detail['Complementary']['no_of_transactions'] = count($Complementary);
            $detail['Complementary']['total_amount'] = array_sum($Complementary);
            $Customer_discounts_records = $Customer_discounts_records->get();
            foreach($Customer_discounts_records as $discounts)
            {
                $order_discounts_arr = json_decode($discounts->order_discounts);
                foreach($order_discounts_arr as $order_discounts)
                {
                    if(isset($order_discounts->discount_amount) && $order_discounts->discount_amount != "")
                    {
                        if($discounts->getAssociateUserForOrder->role_id == '11')
                        {
                            $Customer_discounts_arr[] = $order_discounts->discount_amount;
                        }

                        if($discounts->getAssociateUserForOrder->role_id == '4')
                        {
                            $Admin_discounts_arr[] = $order_discounts->discount_amount;
                        } 
                    }
                } 
            }

        $detail['Customer_discounts']['no_of_transactions'] = count($Customer_discounts_arr);
        $detail['Customer_discounts']['total_amount'] = array_sum($Customer_discounts_arr);


        $detail['Admin_discounts']['no_of_transactions'] = count($Admin_discounts_arr);
        $detail['Admin_discounts']['total_amount'] = array_sum($Admin_discounts_arr);

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'DISCOUNTREPORTS','Discounts Reports'); 
            }
            if($request->input('manage-request') == 'pdf')
            {
                return $this->downloadPDF('xls',$detail,$request,'DISCOUNTREPORTS','Discounts Reports'); 
            }
        }

        $breadcum = [$title=>'','Discounts Reports'=>''];
        return view('admin.reports.discounts_reports',compact('title','model','breadcum','detail'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    public function paymentSalesSummary(Request $request)
    {
        //$this->managetimeForallCron();
       $title = $this->title;
       $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___payment-sales-summary']) || $permission == 'superadmin')
        {

        $detail = [];

       $restro = $this->getRestaurantList();
      


        $all_item = ReceiptSummaryPayment::orderBy('id','desc');

          if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
           
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
          
        }
        //dd($all_item->toSql());
        $all_item = $all_item->get();


        foreach($all_item as $item)
        {
            $can_come = true;

            if ($request->has('restaurant'))
            {
                $restaurant_id = $request->input('restaurant');
                $can_come = $this->isReportexistInRestro($item->order_receipt_id,$restaurant_id);
            }

           if($can_come == true)
           {

                 if(strtoupper($item->payment_mode) =='MPESA')
                 {
                    $item->payment_mode = 'MPESA TILL';
                    if(isset($item->mpesa_request_id))
                    {
                        $item->payment_mode = 'MPESA APP'; 
                    }
                 }

                $detail[$item->payment_mode]['payment_mode'] =  $item->payment_mode; 

                if(strtoupper($item->payment_mode) =='COMPLEMENTARY')
                {
                    $item->amount = 0;
                }


                if(isset($detail[$item->payment_mode]['number_of_transaction']))
                {
                    $detail[$item->payment_mode]['number_of_transaction'] = $detail[$item->payment_mode]['number_of_transaction']+1;
                } 
                else
                {
                     $detail[$item->payment_mode]['number_of_transaction'] =1;
                }

                 if(isset($detail[$item->payment_mode]['amount']))
                {
                    $detail[$item->payment_mode]['amount'] = $detail[$item->payment_mode]['amount']+$item->amount;
                } 
                else
                {
                     $detail[$item->payment_mode]['amount'] =$item->amount;
                }
            }
        }

        $detail = array_values($detail);
        $detail = json_decode(json_encode($detail));

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                 $this->exportdata('xls',$detail,$request,'PAYMENTSALESUMMARY','Payments/Sales Summary Report'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'PAYMENTSALESUMMARY','Payments/Sales Summary Report'); 
            }
           
        }
       
        $breadcum = [$title=>'','Payment Sales Summary'=>''];
        return view('admin.reports.paymentSalesSummary',compact('title','lists','model','breadcum','detail','restro'));
        }
          else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function percentageProfitReport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___percentage-profit-report']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id','created_at','billing_time'])
            ->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });
            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {

                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
               
            }
            $data  = $all_item->get();
           $detail = [];
           $charges_names = [];
           foreach($data as $row)
           {      
                $total_charges= [];
                $key = $row->food_item_id;
                $detail[$key]['title'] = $row->getAssociateFooditem->name;

                 $detail[$key]['item_price'] = $row->getAssociateFooditem->price;
                  $detail[$key]['cost'] = $row->getAssociateFooditem->recipe_cost;



                $detail[$key]['family_group_name'] = $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name;

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                    $row->price = 0;
                    $get_charge = false;

                }

               
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

              


               
            
           }

       
            sort($detail);
      

     

           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
                    $this->exportdata('xls',$detail,$request,'PERCENTAGEPROFITREPORT','Percentage Profit Report'); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'PERCENTAGEPROFITREPORT','Percentage Profit Report'); 
                }
            }

            // dd($detail);
            $breadcum = [$title=>'','Percentage Profit Report'=>''];
            return view('admin.reports.precentageProfitReport',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    public function menuItemGeneralSales(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id','created_at','billing_time'])
            ->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });
            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {

                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
               
            }
            $data  = $all_item->get();
           $detail = [];
           $charges_names = [];
           foreach($data as $row)
           {      
                $total_charges= [];
                $key = $row->food_item_id;
                $detail[$key]['title'] = $row->getAssociateFooditem->name;
                $detail[$key]['recipe_name'] = isset($row->getAssociateFooditem->getAssociateRecipe->title) ? $row->getAssociateFooditem->getAssociateRecipe->title : "-";
                $detail[$key]['family_group_name'] = $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name;

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                    $row->price = 0;
                    $get_charge = false;

                }

               
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

                if(isset($detail[$key]['gross_sale']))
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                }
                else
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                }
                $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        {
                            $total_charges[] = $ch->charged_amount;

                            $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                            if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                            {
                                 $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                            }
                            else
                            {
                                $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                            }
                           
                        }
                       
                    }

                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = array_sum($total_charges);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }


                $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
            
           }

       
            sort($detail);
      

     

           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
                    $this->exportdata('xls',$detail,$request,'MENUITEMGENERALSALES','Menu Item - General Sales Report',$charges_names); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'MENUITEMGENERALSALES','Menu Item - General Sales Report',$charges_names); 
                }
            }

            // dd($detail);
            $breadcum = [$title=>'','Menu Item General Sales'=>''];
            return view('admin.reports.menuItemGeneralSales',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function familygroupmenuItemGeneralSales(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id','created_at','billing_time'])
            ->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });
            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {

                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
               
            }
            $data  = $all_item->get();
           $detail = [];
           $charges_names = [];
           foreach($data as $row)
           {      
                $total_charges= [];
                $key = $row->food_item_id;
                $detail[$key]['title'] = $row->getAssociateFooditem->name;
                $detail[$key]['price'] = $row->getAssociateFooditem->price;
                $detail[$key]['recipe_name'] = isset($row->getAssociateFooditem->getAssociateRecipe->title) ? $row->getAssociateFooditem->getAssociateRecipe->title : "-";
                $detail[$key]['family_group_name'] = $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name;

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                    $row->price = 0;
                    $get_charge = false;

                }

               
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

                if(isset($detail[$key]['gross_sale']))
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                }
                else
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                }
                $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        {
                            $total_charges[] = $ch->charged_amount;

                            $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                            if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                            {
                                 $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                            }
                            else
                            {
                                $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                            }
                           
                        }
                       
                    }

                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = array_sum($total_charges);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }


                $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
            
           }

       
            sort($detail);
      
//          echo "<pre>"; print_r(); die;
            $finalArr = [];
            $rec = json_decode(json_encode($detail));
            $tatalgross = 0;
            foreach($rec as $key=> $val){
                $finalArr[$val->family_group_name][$key]['title'] = $val->title;    
                $finalArr[$val->family_group_name][$key]['price'] = $val->price;    
                $finalArr[$val->family_group_name][$key]['recipe_name'] = $val->recipe_name;    
                $finalArr[$val->family_group_name][$key]['family_group_name'] = $val->family_group_name;    
                $finalArr[$val->family_group_name][$key]['item_total_quantity'] = $val->item_total_quantity;    
                $finalArr[$val->family_group_name][$key]['gross_sale'] = $val->gross_sale;  
                $finalArr[$val->family_group_name][$key]['total_charges'] = $val->total_charges;    
                $finalArr[$val->family_group_name][$key]['net_sale'] = $val->net_sale;  
            }   
            foreach($finalArr as $key=> $vals){
                $grossTotal = 0;
                $qtyTotal = 0;
             foreach($vals as $keys=> $val){
                $grossTotal += $val['gross_sale'];
                $qtyTotal   += $val['item_total_quantity']; 
             }              
                $finalArr[$key]['gross_sales_total'] = $grossTotal;
                $finalArr[$key]['total_qty_total'] = $qtyTotal;
            }
        //  echo "<pre>"; print_r($finalArr); die;
            $detail = $finalArr;
           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
//                    $this->exportdata('xls',$detail,$request,'FAMILYGROUPMENUITEMGENERALSALES','Family Group Menu Item - General Sales Report',$charges_names); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'FAMILYGROUPMENUITEMGENERALSALES','Family Group Menu Item - General Sales Report',$charges_names); 
                }
            }

            // dd($detail);
            $breadcum = [$title=>'','Menu Item General Sales'=>''];
            return view('admin.reports.familygroupmenuItemGeneralSales',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }
    

    public function waitermenuItemGeneralSales(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales']) || $permission == 'superadmin')
        {
            $waiter = $request->input('waiter');
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id','created_at','billing_time'])
            ->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem', function ($query) use($waiter){ 
                $query->where('order_type','PREPAID');
                if ($waiter)
                {
                    $query->where('user_id',$waiter);
                }
            });
            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {

                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
               
            }
            $data  = $all_item->get();
           $detail = [];
           $charges_names = [];
           foreach($data as $row)
           {      
                $total_charges= [];
                $key = $row->food_item_id;
                $detail[$key]['title'] = $row->getAssociateFooditem->name;
                $detail[$key]['recipe_name'] = isset($row->getAssociateFooditem->getAssociateRecipe->title) ? $row->getAssociateFooditem->getAssociateRecipe->title : "-";
                $detail[$key]['family_group_name'] = $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name;

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                    $row->price = 0;
                    $get_charge = false;

                }

               
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

                if(isset($detail[$key]['gross_sale']))
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                }
                else
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                }
                $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        {
                            $total_charges[] = $ch->charged_amount;

                            $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                            if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                            {
                                 $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                            }
                            else
                            {
                                $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                            }
                           
                        }
                       
                    }

                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = array_sum($total_charges);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }


                $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
            
           }

       
            sort($detail);
      

     

           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
                    $this->exportdata('xls',$detail,$request,'MENUITEMGENERALSALES','Waiter Menu Item - General Sales Report',$charges_names); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'MENUITEMGENERALSALES','Waiter Menu Item - General Sales Report',$charges_names); 
                }
            }

            // dd($detail);
            $breadcum = [$title=>'','Waiter Menu Item General Sales'=>''];
            return view('admin.reports.waitermenuItemGeneralSales',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function menuItemSalesDepartment(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales']) || $permission == 'superadmin')
        {
            $print_class_id = $request->input('print_class_id');
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id','created_at','billing_time'])
            ->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });
            if ($print_class_id)
            {
                $all_item = $all_item->where('print_class_id',$print_class_id);
            }

            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {

                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
               
            }
            $data  = $all_item->get();
           $detail = [];
           $charges_names = [];
           foreach($data as $row)
           {      
                $total_charges= [];
                $key = $row->food_item_id;
                $detail[$key]['title'] = $row->getAssociateFooditem->name;
                $detail[$key]['recipe_name'] = isset($row->getAssociateFooditem->getAssociateRecipe->title) ? $row->getAssociateFooditem->getAssociateRecipe->title : "-";
                $detail[$key]['family_group_name'] = $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name;

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                    $row->price = 0;
                    $get_charge = false;

                }

               
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

                if(isset($detail[$key]['gross_sale']))
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                }
                else
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                }
                $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        {
                            $total_charges[] = $ch->charged_amount;

                            $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                            if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                            {
                                 $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                            }
                            else
                            {
                                $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                            }
                           
                        }
                       
                    }

                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = array_sum($total_charges);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }


                $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
            
           }

       
            sort($detail);
      

     

           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
                    $this->exportdata('xls',$detail,$request,'MENUITEMGENERALSALES','Waiter Menu Item - General Sales Report',$charges_names); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'MENUITEMGENERALSALES','Waiter Menu Item - General Sales Report',$charges_names); 
                }
            }

            // dd($detail);
            $breadcum = [$title=>'','Waiter Menu Item General Sales'=>''];
            return view('admin.reports.menuItemSalesPerDepartment',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }



    public function condimentSalesReportWithPlu(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___condiment-sales-report-with-plu']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
            $all_item =  OrderedItem::select(['order_id','condiments_json','item_quantity'])->where('item_delivery_status','COMPLETED')->where('condiments_json','!=',null)->where('condiments_json','!=','[]');


            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
            }

            if ($request->has('restaurant'))
            {
                $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
            }

            if(!$request->has('start-date') && !$request->has('end-date'))
            {
               //$all_item = $all_item->whereDate('created_at','>=',date('Y-m-d'));
            }




            $data  = $all_item->orderBy('order_id','desc')->get();
           
            $detail = [];
            $pluNumberList = $this->getPluList();
              
            foreach($data as $row)
            {
                $condiments_json = json_decode($row->condiments_json);
              
                
                    foreach($condiments_json as $sub_items)
                    {
                        if(isset($sub_items->sub_items) && count($sub_items->sub_items)>0)
                        {
                            foreach($sub_items->sub_items as $condiment)
                            {
                               
                                $condiment_detail = Condiment::where('id',$condiment->id)->where('plu_number','!=',null)->first();
                                if($condiment_detail && isset($pluNumberList[$condiment_detail->plu_number]))
                                {
                                    $key = $condiment->id;
                                    $detail[$key]['title'] = $condiment->title;
                                    $detail[$key]['plu_number'] =$condiment_detail->plu_number;
                                    $detail[$key]['plu_name'] = $pluNumberList[$condiment_detail->plu_number];
                                    
                                    if(isset($detail[$key]['item_total_quantity']))
                                    {
                                        $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                                    }
                                    else
                                    {
                                        $detail[$key]['item_total_quantity'] = $row->item_quantity;
                                    }
                                }

                            }
                        }
                    }
          
            
            }

            sort($detail);
           if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
            {
                if($request->input('manage-request') == 'xls')
                {
                    $this->exportdata('xls',$detail,$request,'CONDIMENTSALESREPORTWITHPLU','Condiment Sales With Plu Report'); 
                }
                if($request->input('manage-request') == 'pdf')
                {
                    return $this->downloadPDF('xls',$detail,$request,'CONDIMENTSALESREPORTWITHPLU','Condiment Sales With Plu Report'); 
                }
            }

          // dd($detail);

            $breadcum = [$title=>'','Condiment Sales With Plu Report'=>''];
            return view('admin.reports.condimentSalesReportWithPlu',compact('title','lists','model','breadcum','detail','restro'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
      

    }

    public function menuItemGeneralSalesWithPlu(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales-with-plu']) || $permission == 'superadmin')
        {
       $restro = $this->getRestaurantList();
        
       $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

        if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
           // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
           //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
        }

        if ($request->has('restaurant'))
        {
            $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
           //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
        }

       $data  = $all_item->get();

       $detail = [];
        $charges_names = [];
        $pluNumberList = $this->getPluList();
       foreach($data as $row)
       {
           
            if($row->getAssociateFooditem->plu_number && isset($pluNumberList[$row->getAssociateFooditem->plu_number])){
            $total_charges= [];
            $key = $row->food_item_id;
            $detail[$key]['title'] = $row->getAssociateFooditem->name;
            $detail[$key]['plu_number'] = $row->getAssociateFooditem->plu_number;
            $detail[$key]['plu_name'] = $pluNumberList[$row->getAssociateFooditem->plu_number];


            $get_charge = true;
            
            if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
            {
                $row->price = 0;
                $get_charge = false;
               
            }
            
            if(isset($detail[$key]['item_total_quantity']))
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
            }
            else
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity;
            }

            if(isset($detail[$key]['gross_sale']))
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
            }
            else
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
            }
            $charges_arr = json_decode($row->item_charges);

            if(count($charges_arr)>0  && $get_charge == true)
            {

                foreach($charges_arr as $ch)
                {
                    if(isset($ch->charged_amount))
                    {
                        $total_charges[] = $ch->charged_amount;

                        $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                        if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                        {
                             $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                        }
                        else
                        {
                            $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                        }
                       
                    }
                }

                if(isset($detail[$key]['total_charges']))
                {
                        $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                }
                else
                {
                    $detail[$key]['total_charges'] = array_sum($total_charges);
                }
            }

            if(!isset($detail[$key]['total_charges']))
            {
                $detail[$key]['total_charges'] = '0';
            }
            $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
        }
        
       }
       
       sort($detail);

       if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'MENUITEMGENERALSALESWITHPLU','Menu Item - General Sales With Plu Report',$charges_names); 
            }
            if($request->input('manage-request') == 'pdf')
            {
                return $this->downloadPDF('xls',$detail,$request,'MENUITEMGENERALSALESWITHPLU','Menu Item - General Sales With Plu Report',$charges_names); 
            }
        }

      // dd($detail);

        $breadcum = [$title=>'','Menu Item General Sales With Plu'=>''];
        return view('admin.reports.menuItemGeneralSalesWithPlu',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
     else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function menuItemGeneralSalesWithoutPlu(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-general-sales-with-plu']) || $permission == 'superadmin')
        {
       $restro = $this->getRestaurantList();

       $itemwithoutplu = FoodItem::where('plu_number',null)->pluck('id')->toarray();
       if(count($itemwithoutplu)<1)
       {
        $itemwithoutplu = [0];
       }
         
       $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])

              ->whereIn('food_item_id',$itemwithoutplu)
              ->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

        if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
           // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
           //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
        }

        if ($request->has('restaurant'))
        {
            $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
           //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
        }

       $data  = $all_item->get();

       $detail = [];
        $charges_names = [];
        
       foreach($data as $row)
       {
           
            if(!$row->getAssociateFooditem->plu_number){
            $total_charges= [];
            $key = $row->food_item_id;
            $detail[$key]['title'] = $row->getAssociateFooditem->name;
           


            $get_charge = true;
            
            if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
            {
                $row->price = 0;
                $get_charge = false;
               
            }
            
            if(isset($detail[$key]['item_total_quantity']))
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
            }
            else
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity;
            }

            if(isset($detail[$key]['gross_sale']))
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
            }
            else
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
            }
            $charges_arr = json_decode($row->item_charges);

            if(count($charges_arr)>0  && $get_charge == true)
            {

                foreach($charges_arr as $ch)
                {
                    if(isset($ch->charged_amount))
                    {
                        $total_charges[] = $ch->charged_amount;

                        $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                        if(isset($detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]))
                        {
                             $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount;
                        }
                        else
                        {
                            $detail[$key][str_replace(' ','_',strtolower($ch->charges_name))] = $ch->charged_amount;
                        }
                       
                    }
                }

                if(isset($detail[$key]['total_charges']))
                {
                        $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                }
                else
                {
                    $detail[$key]['total_charges'] = array_sum($total_charges);
                }
            }

            if(!isset($detail[$key]['total_charges']))
            {
                $detail[$key]['total_charges'] = '0';
            }
            $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
        }
        
       }
       
       sort($detail);

       if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'MENUITEMGENERALSALESWITHOUTPLU','Menu Item - General Sales Without Plu Report',$charges_names); 
            }
            if($request->input('manage-request') == 'pdf')
            {
                return $this->downloadPDF('xls',$detail,$request,'MENUITEMGENERALSALESWITHOUTPLU','Menu Item - General Sales Without Plu Report',$charges_names); 
            }
        }

      // dd($detail);

        $breadcum = [$title=>'','Menu Item General Sales Without Plu'=>''];
        return view('admin.reports.menuItemGeneralSalesWithoutPlu',compact('title','lists','model','breadcum','detail','restro','charges_names'));
        }
     else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }



    public function familyGroupSales(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___family-group-sales']) || $permission == 'superadmin')
        {

       $restro = $this->getRestaurantList();
        
       $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

        if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
           // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
           //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
        }

        if ($request->has('restaurant'))
        {
            $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
           //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
        }

       $data  = $all_item->get();

       $detail = [];
       foreach($data as $row)
       {
            $total_charges= [];
            $food_item = FoodItem::select('id')->where('id',$row->food_item_id)->first();
            $familyGroup = $food_item->getItemCategoryRelation->getRelativecategoryDetail;
            $key  = $familyGroup->id;
            $detail[$key]['title'] = $familyGroup->name;


            $get_charge = true;
            if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
            {
                $row->price = 0;
                $get_charge = false;
               
            }
            
            if(isset($detail[$key]['item_total_quantity']))
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
            }
            else
            {
                $detail[$key]['item_total_quantity'] = $row->item_quantity;
            }

            if(isset($detail[$key]['gross_sale']))
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
            }
            else
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
            }
            $charges_arr = json_decode($row->item_charges);

            if(count($charges_arr)>0 && $get_charge == true)
            {

                foreach($charges_arr as $ch)
                {
                    if(isset($ch->charged_amount))
                    $total_charges[] = $ch->charged_amount;
                }

                if(isset($detail[$key]['total_charges']))
                {
                        $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                }
                else
                {
                    $detail[$key]['total_charges'] = array_sum($total_charges);
                }
            }

            if(!isset($detail[$key]['total_charges']))
            {
                $detail[$key]['total_charges'] = '0';
            }


            $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
        
       }
       
       sort($detail);


       if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {

            if($request->input('manage-request') == 'xls')
            {
                 $this->exportdata('xls',$detail,$request,'FAMILYGROUPSALES','Family Group Sales Detail Report'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'FAMILYGROUPSALES','Family Group Sales Detail Report'); 
            }

            
        }



        $breadcum = [$title=>'','Family Group Sales'=>''];
        return view('admin.reports.familyGroupSales',compact('title','lists','model','breadcum','detail','restro'));
         }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    public function familyGroupSalesWithGl(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___family-group-sales-with-gl']) || $permission == 'superadmin')
        {

       $restro = $this->getRestaurantList();
        
       $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

        if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
           // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
           //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
        }

        if ($request->has('restaurant'))
        {
            $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
           //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
        }

       $data  = $all_item->get();

       $detail = [];
       $getGLDetail = $this->getGLDetail();
      // die;
       foreach($data as $row)
       {
           
            $food_item = FoodItem::select('id')->where('id',$row->food_item_id)->first();
            $familyGroup = $food_item->getItemCategoryRelation->getRelativecategoryDetail;

            if($familyGroup->gl_account_no && isset($getGLDetail[$familyGroup->gl_account_no]))
            {
                $total_charges= [];
                $key  = $familyGroup->id;
                $detail[$key]['title'] = $familyGroup->name;
                $detail[$key]['gl_code'] = $familyGroup->gl_account_no;
                $detail[$key]['gl_name'] =$getGLDetail[$familyGroup->gl_account_no];

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                $row->price = 0;
                $get_charge = false;

                }
                
                if(isset($detail[$key]['item_total_quantity']))
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                }
                else
                {
                    $detail[$key]['item_total_quantity'] = $row->item_quantity;
                }

                if(isset($detail[$key]['gross_sale']))
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                }
                else
                {
                    $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                }
                $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        $total_charges[] = $ch->charged_amount;
                    }
                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = array_sum($total_charges);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }
                $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];

            }

        
       }
       
       sort($detail);


       if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {

            if($request->input('manage-request') == 'xls')
            {
                 $this->exportdata('xls',$detail,$request,'FAMILYGROUPSALESWITHGL','Family Group Sales With Gl Detail Report'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'FAMILYGROUPSALESWITHGL','Family Group Sales With Gl Detail Report'); 
            }

            
        }



        $breadcum = [$title=>'','Family Group Sales With Gl'=>''];
        return view('admin.reports.familyGroupSalesWithGl',compact('title','lists','model','breadcum','detail','restro'));
         }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    public function menuItemGroupSales(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___menu-item-group-sales']) || $permission == 'superadmin')
        {

        $restro = $this->getRestaurantList();
        $menuItmGroup = Category::select(['id','name','is_have_another_layout'])->where('level',2)->get();
        $detail = [];
        foreach($menuItmGroup as $group)
        {
            
            $my_child = $group->getManyRelativeChilds;
            $item_ids = [];
            $key = $group->id;
            if($group->is_have_another_layout=='1')
            {
                $subchild = $my_child->pluck('category_id')->toArray();
                if(count($subchild)>0)
                {
                    $third_child = CategoryRelation::whereIn('parent_id',$subchild)->pluck('category_id');
                    $items= [];
                    if(count($third_child)>0)
                    {
                        $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$third_child)->pluck('item_id')->toArray();
                        if(count($item_ids_arr)>0)
                        {
                            $item_ids = $item_ids_arr;
                        } 
                    }

                }
            }
            else
            {
                $second_child = $my_child->pluck('category_id')->toArray();
                if(count($second_child)>0)
                {
                    $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$second_child)->pluck('item_id')->toArray();
                    if(count($item_ids_arr)>0)
                    {
                        $item_ids = $item_ids_arr;
                        //dd($item_ids);
                    }
                }

                
            }

            
            $detail[$key]['title'] = ucfirst($group->name);
            $detail[$key]['item_total_quantity'] = 0;
            $detail[$key]['gross_sale'] = 0;
            $detail[$key]['total_charges'] = 0;
            $detail[$key]['net_sale'] = 0;
                  
            if(count($item_ids)>0)
            {
                $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->whereIn('food_item_id',$item_ids)->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

                if ($request->has('start-date'))
                {
                    $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
                   // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
                }
                if ($request->has('end-date'))
                {
                    $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
                   //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
                }

                if ($request->has('restaurant'))
                {
                    $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
                   //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
                }
                $data  = $all_item->get();
                if(count($data)>0)
                {
                          
                   foreach($data as $row)
                   {
                        $total_charges= [];

                        $get_charge = true;
                        if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                        {
                            $row->price = 0;
                            $get_charge = false;

                        }
                       
                       
                        
                        if(isset($detail[$key]['item_total_quantity']))
                        {
                            $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                        }
                        else
                        {
                            $detail[$key]['item_total_quantity'] = $row->item_quantity;
                        }

                        if(isset($detail[$key]['gross_sale']))
                        {
                            $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                        }
                        else
                        {
                            $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                        }
                        $charges_arr = json_decode($row->item_charges);

                        if(count($charges_arr)>0 && $get_charge == true)
                        {

                            foreach($charges_arr as $ch)
                            {
                                if(isset($ch->charged_amount))
                                $total_charges[] = $ch->charged_amount;
                            }

                            if(isset($detail[$key]['total_charges']))
                            {
                                    $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                            }
                            else
                            {
                                $detail[$key]['total_charges'] = array_sum($total_charges);
                            }
                        }

                        if(!isset($detail[$key]['total_charges']))
                        {
                            $detail[$key]['total_charges'] = '0';
                        }
                        $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
                   }
                }
            }
        }
        sort($detail);
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {

            if($request->input('manage-request') == 'xls')
            {
                 $this->exportdata('xls',$detail,$request,'MENUITEMGROUPSALES','Menu Item Group Sales Report'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'MENUITEMGROUPSALES','Menu Item Group Sales Report'); 
            }
        }
        $breadcum = [$title=>'','Menu Item Group Sales'=>''];
        return view('admin.reports.menuItemGroupSales',compact('title','lists','model','breadcum','detail','restro'));
         }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    public function majorGroupSales(Request $request)
    {
        $title = $this->title;
        $model = $this->model;

         $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___major-group-sales']) || $permission == 'superadmin')
        {
        $restro = $this->getRestaurantList();
        $major_group = Category::select('id','name')->whereIn('id',['1','5','6'])->get();
        $detail = [];
        $major_group_item_ids = [];
        $major_group_item_ids_styring_arr = [];
        foreach($major_group as $mjg)
        {
            //echo '<pre>';
            $key = $mjg->id;
            $detail[$key]['title'] = ucfirst($mjg->name);
            $detail[$key]['item_total_quantity'] = 0;
            $detail[$key]['gross_sale'] = 0;
            $detail[$key]['total_charges'] = 0;
            $detail[$key]['net_sale'] = 0;
            $my_first_child = $mjg->getManyRelativeChilds->pluck('category_id')->toArray();
            if(count($my_first_child)>0)
            {
                $my_first_child_details = Category::select('id','name')->whereIn('id',$my_first_child)->get();
                foreach($my_first_child_details as $my_first_child_detail_arr)
                {
                    $my_scond_child_arr = $my_first_child_detail_arr->getManyRelativeChilds->pluck('category_id')->toArray();
                    if(count($my_scond_child_arr)>0)
                    {
                        if($key !='6')
                        {
                            //leave offers
                            $menuItmGroup = Category::select(['id','name','is_have_another_layout'])->whereIn('id',$my_scond_child_arr)->get(); 
                            foreach($menuItmGroup as $group)
                            {
                                $my_child = $group->getManyRelativeChilds;
                                if($group->is_have_another_layout=='1')
                                {
                                    $subchild = $my_child->pluck('category_id')->toArray();
                                    if(count($subchild)>0)
                                    {
                                        $third_child = CategoryRelation::whereIn('parent_id',$subchild)->pluck('category_id');
                                        $items= [];
                                        if(count($third_child)>0)
                                        {
                                            $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$third_child)->pluck('item_id')->toArray();
                                            if(count($item_ids_arr)>0)
                                            {
                                                $major_group_item_ids[$key][] = implode(',',$item_ids_arr);
                                            } 
                                        }
                                    }
                                }
                                else
                                {
                                    $second_child = $my_child->pluck('category_id')->toArray();
                                    if(count($second_child)>0)
                                    {
                                        $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$second_child)->pluck('item_id')->toArray();
                                        if(count($item_ids_arr)>0)
                                        {
                                            $major_group_item_ids[$key][] =  implode(',',$item_ids_arr); 
                                        }
                                    } 
                                }

                            } 
                        }
                        else
                        {
                            // for offers
                            $major_group_item_ids[$key][] = implode(',',$my_scond_child_arr);
                        }
                    }
                }
            } 
            $major_group_item_ids_styring_arr[$key] = isset($major_group_item_ids[$key])?implode(',',$major_group_item_ids[$key]):'';
        }


        foreach($major_group_item_ids_styring_arr as $main_group_key =>$main_group_item_string)
        {
            if($main_group_key=='6')
            {
                $offer_ids_arr = explode(',',$main_group_item_string);
                if(count($offer_ids_arr)>0)
                {
                    //print_r($offer_ids_arr);
                     $all_offer =  OrderOffer::select(['offer_id','price','quantity','offer_charges'])->whereIn('offer_id',$offer_ids_arr);

                    if ($request->has('start-date'))
                    {
                        $all_offer = $all_offer->where('created_at','>=',$request->input('start-date'));
                       // $conditions = $conditions." and date(created_at) >= '".$request->input('start-date')."'";
                    }
                    if ($request->has('end-date'))
                    {
                        $all_offer = $all_offer->where('created_at','<=',$request->input('end-date'));
                       //$conditions = $conditions." and date(created_at) <= '".$request->input('end-date')."'";
                    }

                    if ($request->has('restaurant'))
                    {
                        $all_offer = $all_offer->where('restaurant_id',$request->input('restaurant'));
                       //$conditions = $conditions." and restaurant_id = ".$request->input('restaurant');
                    }
                    $data  = $all_offer->get();
                    if(count($data)>0)
                    {
                              
                       foreach($data as $row)
                       {
                            $total_charges= [];
                            if(isset($detail[$main_group_key]['item_total_quantity']))
                            {
                                $detail[$main_group_key]['item_total_quantity'] = $row->quantity+$detail[$main_group_key]['item_total_quantity'];
                            }
                            else
                            {
                                $detail[$main_group_key]['item_total_quantity'] = $row->quantity;
                            }

                            if(isset($detail[$main_group_key]['gross_sale']))
                            {
                                $detail[$main_group_key]['gross_sale'] = ($row->quantity*$row->price)+$detail[$main_group_key]['gross_sale'];
                            }
                            else
                            {
                                $detail[$main_group_key]['gross_sale'] = ($row->quantity*$row->price);
                            }
                            $charges_arr = json_decode($row->offer_charges);

                            if(count($charges_arr)>0)
                            {

                                foreach($charges_arr as $ch)
                                {
                                    if(isset($ch->charged_amount))
                                    $total_charges[] = $ch->charged_amount;
                                }

                                if(isset($detail[$main_group_key]['total_charges']))
                                {
                                        $detail[$main_group_key]['total_charges'] = array_sum($total_charges)+$detail[$main_group_key]['total_charges'];
                                }
                                else
                                {
                                    $detail[$main_group_key]['total_charges'] = array_sum($total_charges);
                                }
                            }

                            if(!isset($detail[$main_group_key]['total_charges']))
                            {
                                $detail[$main_group_key]['total_charges'] = '0';
                            }
                            $detail[$main_group_key]['net_sale'] = $detail[$main_group_key]['gross_sale']- $detail[$main_group_key]['total_charges'];
                        }
                    }
                }
            }
            else
            {
                $item_ids = explode(',',$main_group_item_string);
                 if(count($item_ids)>0)
                {
                    $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])
                    ->whereNotIn('item_delivery_status',['CANCLED','PENDING'])

                    ->whereIn('food_item_id',$item_ids)->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });

                    if ($request->has('start-date'))
                    {
                        $all_item = $all_item->where('billing_time','>=',$request->input('start-date'));
                       
                    }
                    if ($request->has('end-date'))
                    {
                        $all_item = $all_item->where('billing_time','<=',$request->input('end-date'));
                      
                    }

                    if ($request->has('restaurant'))
                    {
                        $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
                    }
                    $data  = $all_item->get();
                    if(count($data)>0)
                    {
                              
                       foreach($data as $row)
                       {
                            $total_charges= [];

                            $get_charge = true;
                            if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                            {
                                $row->price = 0;
                                $get_charge = false;

                            }
                            if(isset($detail[$main_group_key]['item_total_quantity']))
                            {
                                $detail[$main_group_key]['item_total_quantity'] = $row->item_quantity+$detail[$main_group_key]['item_total_quantity'];
                            }
                            else
                            {
                                $detail[$main_group_key]['item_total_quantity'] = $row->item_quantity;
                            }

                            if(isset($detail[$main_group_key]['gross_sale']))
                            {
                                $detail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$main_group_key]['gross_sale'];
                            }
                            else
                            {
                                $detail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price);
                            }
                            $charges_arr = json_decode($row->item_charges);

                            if(count($charges_arr)>0 && $get_charge == true)
                            {

                                foreach($charges_arr as $ch)
                                {
                                    if(isset($ch->charged_amount))
                                    $total_charges[] = $ch->charged_amount;
                                }

                                if(isset($detail[$main_group_key]['total_charges']))
                                {
                                        $detail[$main_group_key]['total_charges'] = array_sum($total_charges)+$detail[$main_group_key]['total_charges'];
                                }
                                else
                                {
                                    $detail[$main_group_key]['total_charges'] = array_sum($total_charges);
                                }
                            }

                            if(!isset($detail[$main_group_key]['total_charges']))
                            {
                                $detail[$main_group_key]['total_charges'] = '0';
                            }
                            $detail[$main_group_key]['net_sale'] = $detail[$main_group_key]['gross_sale']- $detail[$main_group_key]['total_charges'];
                        }
                    }
                }
            }
        }



        

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                 $this->exportdata('xls',$detail,$request,'MAJORGROUPSALES','Major Group Sales  Report'); 
            }
            else
            {
                return $this->downloadPDF('xls',$detail,$request,'MAJORGROUPSALES','Major Group Sales  Report'); 
            }
           
        }



        $breadcum = [$title=>'','Major Group Sales'=>''];
        return view('admin.reports.majorGroupSales',compact('title','lists','model','breadcum','detail','restro'));
          }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }



    public function waiterWithFamilyGroupSales(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___waiter-with-family-groups']) || $permission == 'superadmin')
        {
       $restro = $this->getRestaurantList();
       $waiterList = User::where('role_id','4')->pluck('name','id');
       
       $detail = [];


       if(isset($request->user_id))
       {
            $getWaiterRelated_order = Order::select(['id'])->where('user_id',$request->user_id)->where('status','!=','CANCLED')->pluck('id')->toArray();
            if(count($getWaiterRelated_order)>0)
            {
                $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])
                ->whereNotIn('item_delivery_status',['CANCLED','PENDING'])
                ->whereIn('order_id',$getWaiterRelated_order)
                ->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                $query->where('order_type','PREPAID');
            });
                if ($request->has('start-date'))
                {
                    $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
                }
                if ($request->has('end-date'))
                {
                    $all_item = $all_item->where('created_at','<=',$request->input('end-date'));

                }

                if ($request->has('restaurant'))
                {
                    $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));

                }
                $data  = $all_item->get();

                foreach($data as $row)
                {
                    $total_charges= [];

                    $get_charge = true;
                    if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                    {
                        $row->price = 0;
                        $get_charge = false;

                    }
                    $food_item = FoodItem::select('id')->where('id',$row->food_item_id)->first();
                    $familyGroup = $food_item->getItemCategoryRelation->getRelativecategoryDetail;
                    $key  = $familyGroup->id;
                    $detail[$key]['title'] = $familyGroup->name;
                    
                    if(isset($detail[$key]['item_total_quantity']))
                    {
                        $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                    }
                    else
                    {
                        $detail[$key]['item_total_quantity'] = $row->item_quantity;
                    }

                    if(isset($detail[$key]['gross_sale']))
                    {
                        $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                    }
                    else
                    {
                        $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                    }
                    $charges_arr = json_decode($row->item_charges);

                    if(count($charges_arr)>0 && $get_charge == true)
                    {

                        foreach($charges_arr as $ch)
                        {
                            if(isset($ch->charged_amount))
                            $total_charges[] = $ch->charged_amount;
                        }

                        if(isset($detail[$key]['total_charges']))
                        {
                                $detail[$key]['total_charges'] = array_sum($total_charges)+$detail[$key]['total_charges'];
                        }
                        else
                        {
                            $detail[$key]['total_charges'] = array_sum($total_charges);
                        }
                    }

                    if(!isset($detail[$key]['total_charges']))
                    {
                        $detail[$key]['total_charges'] = '0';
                    }


                    $detail[$key]['net_sale'] = $detail[$key]['gross_sale']- $detail[$key]['total_charges'];
                
               }
            }
       }
       sort($detail);
        

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                  $this->exportdata('xls',$detail,$request,'WAITERWITHFAMILYGROUPSALEREPORT','Waiter Summary Sales Report (By Family Group)'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'WAITERWITHFAMILYGROUPSALEREPORT','Waiter Summary Sales Report (By Family Group)'); 
            }
           
        }
       $breadcum = [$title=>'','Waiter Family Group Sales'=>''];
        return view('admin.reports.waiterwithfamilyGroupSales',compact('title','lists','model','breadcum','detail','restro','waiterList'));

         }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function exportdata($filetype,$mixed_array,$request,$case,$report_name,$charges_names=null)
    {
        $export_array = [];
        $file_name = 'test';
        $export_array[] = array($report_name);//heading;
        if ($request->has('restaurant'))
        {
            $restro_detail = Restaurant::select(['name'])->whereId($request->input('restaurant'))->first();
            $export_array[] = array(strtoupper($restro_detail->name));//restro;
        }

        if ($request->has('user_id'))
        {
            $user_detail = User::select(['name'])->whereId($request->input('user_id'))->first();
            if($case == 'CASHIERREPORT')
            {
              $export_array[] = array('Cashier: '.strtoupper($user_detail->name));//restro;
            }
            else
            {
                $export_array[] = array('Waiter: '.strtoupper($user_detail->name));//restro;
            }
            
        }
       

        $date_arr = array('','','','Printed On:'.date('d/m/Y h:i A'));
        
        if ($request->has('start-date'))
        {
           $date_arr[0] = 'Period From : '.date('d/m/Y h:i A',strtotime($request->input('start-date')));  
           
          
        }
        if ($request->has('end-date'))
        {
           $date_arr[0] = $date_arr[0] != ''?$date_arr[0].'  - To : '.date('d/m/Y h:i A',strtotime($request->input('end-date'))):' To :'.date('d/m/Y h:i A',strtotime($request->input('end-date')));  
           
           
        }

        $export_array[] = $date_arr;
        $export_array[] = [];
       $counter = 1;

       if($case == 'CASHIERREPORT')
       {
            $export_array[] = array('SN','Payment Name','Amount');
            $total_qty = [];
            $total_amount = [];
            $file_name = 'discount_reports';
            foreach($mixed_array as $row)
            {
                $export_array[] = [
                $counter,$row['payment_mode'],manageAmountFormat($row['amount'])
                ]; 
                 
                $total_amount[] = $row['amount'];
                $counter++;
            } 
            $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_amount));//restro;
             
       }

        if($case=='PERCENTAGEPROFITREPORT')
       {

            $export_array[] = array('Item','Family Group','Sales Price','Cost','Sales QTY','Total Sales','Total Cost','Profit','Margin(%)');
            $final_sale = [];
            $final_cost = [];
            $final_profit = [];
            $file_name = 'percentage_profit_report';
            foreach($mixed_array as $row)
            {
                $total_sale = $row['item_total_quantity']*$row['item_price'];
                $total_cost = $row['item_total_quantity']*$row['cost'];
                $profit = $total_sale-$total_cost;
                $profit_percentage = 0;
                if($total_cost>0)
                {
                    $profit_percentage = ($profit/$total_cost)*100;
                }
                $final_sale[] = $total_sale;
                $final_cost[] = $total_cost;
                $final_profit[] = $profit;

                $export_array[] = [
                    $row['title'],$row['family_group_name'],manageAmountFormat($row['item_price']),manageAmountFormat($row['cost']),manageAmountFormat($row['item_total_quantity']),manageAmountFormat($total_sale),manageAmountFormat($total_cost),manageAmountFormat($profit),manageAmountFormat($profit_percentage)

                ];



               
            } 
             $fianl_margin = 0;
                                     if(array_sum($final_cost)>0)
                                    {
                                         $fianl_margin = (array_sum($final_profit)/array_sum($final_cost))*100;
                                    }
            $export_array[] = array();
            $export_array[] = array('','','Total','','',manageAmountFormat(array_sum($final_sale)),manageAmountFormat(array_sum($final_cost)),manageAmountFormat(array_sum($final_profit)),manageAmountFormat($fianl_margin));//restro;
       }


       if($case=='CASHIERDETAILEDREPORT')
       {
            $export_array[] = array('Cashier','Waiter Name','Pay Method','Sales Receipt');
            $total_amount = [];
            $file_name = 'cashier_detailed_reports';
            foreach($mixed_array as $data)
            {
                $export_array[] = [
                ucfirst($data['cashier_name'])
                ]; 
                foreach($data['payments'] as $payment)
                {
                    $total_amount[] = $payment['amount'];
                    $export_array[] = [
                      '',  ucfirst($payment['waiter_name']),$payment['payment_mode'],manageAmountFormat($payment['amount'])]; 
                }  
            } 
            $export_array[] = array();
            $export_array[] = array('','','Grand Total',manageAmountFormat(array_sum($total_amount)));//restro;
       }

        if($case=='DETAILEDPAYMENTMETHODREPORT')
       {
            $export_array[] = array('Receipt ID','Datetime','Waiter Name','Cashier Name','Number of Orders','Payment Method','Amount','Narration');
            $total_amount = [];
            $file_name = 'detailed_payment_methods_report';
             $export_array[] = array();
            foreach($mixed_array as $data)
            {

                $rendered = [];
                $sub_total = [];

                
                foreach($data['payments'] as $pData)
                {
                    $is_render = false;
                    if(!in_array($data['receipt_id'], $rendered))
                    {
                        $rendered[] = $data['receipt_id'];
                        $is_render = true;
                    }
                    $sub_total[] = $pData['amount'];
                    $total_amount[] = $pData['amount'];
                     $export_array[] = array(

                       $is_render==true?$data['receipt_id']:'',
                       $is_render==true?$data['created_at']:'',
                       $is_render==true?ucfirst($data['waiter_name']):'',
                       $is_render==true?$data['cashier_name']:'',
                       $is_render==true?$data['no_of_orders']:'',
                       $pData['payment_mode'],
                       manageAmountFormat($pData['amount']),
                      $pData['narration']

                        );
                }  
                $export_array[] = array('','','','','','Sub Total',manageAmountFormat(array_sum($sub_total)),'');
            } 
            $export_array[] = array();

             $export_array[] = array('','','','','','Grand Total',manageAmountFormat(array_sum($total_amount)),'');
          
       }

       

        if($case=='WAITERSUMMARYREPORT')
       {
            $export_array[] = array('EMPLOYEE NAME','TOTAL SALES');
            $total_amount = [];
            $file_name = 'waiter_summary_reports';
            foreach($mixed_array as $data)
            {
                $export_array[] = [
                ucfirst($data['waiter_Name']),manageAmountFormat(array_sum($data['amount']))
                ]; 
                $total_amount[] = array_sum($data['amount']);
                
            } 
            $export_array[] = array();
            $export_array[] = array('Grand Total',manageAmountFormat(array_sum($total_amount)));//restro;
       }
       

       if($case == 'DISCOUNTREPORTS')
       {
            $export_array[] = array('SN','Discount Name','No of Tranx','Discount Amount');
            $total_qty = [];
            $total_amount = [];
            $file_name = 'discount_reports';
            foreach($mixed_array as $discount_name=>$row)
            {
                $export_array[] = [
                $counter,str_replace('_',' ',$discount_name),$row['no_of_transactions'],manageAmountFormat($row['total_amount'])
                ]; 
                 $total_qty[] = $row['no_of_transactions'];
                $total_amount[] = $row['total_amount'];
                $counter++;
            } 
            $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));//restro;
             
       }
        if($case=='PAYMENTSALESUMMARY')
        {
            $export_array[] = array('SN','TITLE','No of Tranx','Total Payments');
            $total_qty = [];
            $total_amount = [];
            $file_name = 'paymentsalesummary';
            foreach($mixed_array as $array)
            {
                $export_array[] = [
                $counter,$array->payment_mode,$array->number_of_transaction,manageAmountFormat($array->amount)
                ]; 

                $total_qty[] = $array->number_of_transaction;
                $total_amount[] = $array->amount;
                $counter++;
            } 
             $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));//restro;
        }
        if($case == 'CONDIMENTSALESREPORTWITHPLU')
        {
             $export_array[] = array('SN','Item','Sales QTY','Plu no','Plu Name');
            $total_qty = [];
           
            $file_name = 'condimentreportwithplu';
            foreach($mixed_array as $array)
            {
                $export_array[] = [
                $counter,$array['title'],$array['item_total_quantity'],$array['plu_number'],$array['plu_name']
                ]; 

                $total_qty[] = $array['item_total_quantity'];
                
                $counter++;
            } 
             $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_qty),'','');//restro;
        }
        if($case == 'FAMILYGROUPSALES' || $case =='MENUITEMGROUPSALES'|| $case == 'MAJORGROUPSALES' || $case =='WAITERWITHFAMILYGROUPSALEREPORT' )
        {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Sales QTY','Gross Sales','Taxes','Net Sales % Of Ttl');
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
            foreach($mixed_array as $array)
            {
                $export_array[] = [
                $counter,
                $array['title'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale']),manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges'])
                ]; 
                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
             $export_array[] = array();

            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)),manageAmountFormat(array_sum($total_disc)),manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc)
            ));
        }

         if($case == 'FAMILYGROUPSALESWITHGL')
         {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Sales QTY','Gross Sales','Taxes','Net Sales % Of Ttl','GL Code','GL Name');
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
            foreach($mixed_array as $array)
            {
                $export_array[] = [
                $counter,
                $array['title'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale']),manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges']),$array['gl_code'],$array['gl_name']
                ]; 
                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
             $export_array[] = array();

            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)),manageAmountFormat(array_sum($total_disc)),manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc)
            ));
         }

         if($case == 'MENUITEMGENERALSALES')
        {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Recipe','Family Group','Sales QTY','Gross Sales');

             //count($export_array);   
            if($charges_names)
            {
                foreach($charges_names as $chargename)
                {
                    $export_array[count($export_array)-1][] = str_replace('_',' ',strtoupper($chargename));
                }
            }
            $export_array[count($export_array)-1][] = 'Taxes';
            $export_array[count($export_array)-1] [] = 'Net Sales % Of Ttl';
            //,'Taxes','Net Sales % Of Ttl');
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
            $final_totla_charge= [];
            foreach($mixed_array as $array)
            {
               // $my_inner_report = [];
                $my_inner_report = [
                $counter,
                $array['title'],
                $array['recipe_name'],
                $array['family_group_name'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale'])];

               foreach($charges_names as $charges_name_detail)
               {
                    if(isset($array[$charges_name_detail]))
                    {
                        $my_inner_report[] =   manageAmountFormat($array[$charges_name_detail]);
                        $final_totla_charge[$charges_name_detail][] = $array[$charges_name_detail]; 
                    }
                    else
                    {
                        $my_inner_report[] =     '0.00';
                    }
               }


                $my_inner_report[] = manageAmountFormat($array['total_charges']);
                $my_inner_report[] = manageAmountFormat($array['gross_sale']-$array['total_charges']);

                //,manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges'])
                //]; 
                $export_array[] = $my_inner_report;


                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
             $export_array[] = array();

            $export_array[] = array('','','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));


           foreach($charges_names as $charges_name_detail)
           {
            if(isset($final_totla_charge[$charges_name_detail]))
                                            {
                                               $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
                                            }
                                            else
                                            {
                                               $export_array[count($export_array)-1][] = '0.00';
                                            }
           }

            $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($total_disc));

             $export_array[count($export_array)-1][] = manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc));
            
            
        }



         if($case == 'MENUITEMDEMO')
        {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Group Name','Sales QTY','Gross Sales');

             //count($export_array);   
            if($charges_names)
            {
                foreach($charges_names as $chargename)
                {
                    $export_array[count($export_array)-1][] = str_replace('_',' ',strtoupper($chargename));
                }
            }
            $export_array[count($export_array)-1][] = 'Taxes';
            $export_array[count($export_array)-1] [] = 'Net Sales';
            //,'Taxes','Net Sales % Of Ttl');
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
            $final_totla_charge= [];
            foreach($mixed_array as $array)
            {
               // $my_inner_report = [];
                $my_inner_report = [
                $counter,
                $array['title'],
                $array['family_group_name'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale'])];

               foreach($charges_names as $charges_name_detail)
               {
                    if(isset($array[$charges_name_detail]))
                    {
                        $my_inner_report[] =   manageAmountFormat($array[$charges_name_detail]);
                        $final_totla_charge[$charges_name_detail][] = $array[$charges_name_detail]; 
                    }
                    else
                    {
                        $my_inner_report[] =     '0.00';
                    }
               }


                $my_inner_report[] = manageAmountFormat($array['total_charges']);
                $my_inner_report[] = manageAmountFormat($array['gross_sale']-$array['total_charges']);

                //,manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges'])
                //]; 
                $export_array[] = $my_inner_report;


                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
             $export_array[] = array();

            $export_array[] = array('','','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));


           foreach($charges_names as $charges_name_detail)
           {
            if(isset($final_totla_charge[$charges_name_detail]))
                                            {
                                               $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
                                            }
                                            else
                                            {
                                               $export_array[count($export_array)-1][] = '0.00';
                                            }
           }

            $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($total_disc));

             $export_array[count($export_array)-1][] = manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc));
            
            
        }
        


        if($case == 'MENUITEMGENERALSALESWITHOUTPLU')
        {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Sales QTY','Gross Sales');
            if($charges_names)
            {
                foreach($charges_names as $chargename)
                {
                    $export_array[count($export_array)-1][] = str_replace('_',' ',strtoupper($chargename));
                }
            }
            $export_array[count($export_array)-1][] = 'Taxes';
            $export_array[count($export_array)-1] [] = 'Net Sales % Of Ttl';
            
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
             $final_totla_charge= [];
            foreach($mixed_array as $array)
            {
                $my_inner_report = [
                $counter,
                $array['title'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale'])];

                /*,manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges']),$array['plu_number'],$array['plu_name']
                ]; */
                if($charges_names)
                {
                    foreach($charges_names as $charges_name_detail)
                    {
                        if(isset($array[$charges_name_detail]))
                        {
                            $my_inner_report[] =   manageAmountFormat($array[$charges_name_detail]);
                            $final_totla_charge[$charges_name_detail][] = $array[$charges_name_detail]; 
                        }
                        else
                        {
                            $my_inner_report[] =     '0.00';
                        }
                    }
                }
                $my_inner_report[] = manageAmountFormat($array['total_charges']);
                $my_inner_report[] = manageAmountFormat($array['gross_sale']-$array['total_charges']);
               

                 $export_array[] = $my_inner_report;
                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
            $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));
            if($charges_names)
            {
                foreach($charges_names as $charges_name_detail)
                {
                    if(isset($final_totla_charge[$charges_name_detail]))
                    {
                        $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
                    }
                    else
                    {
                        $export_array[count($export_array)-1][] = '0.00';
                    }
                } 
            }

            $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($total_disc));

             $export_array[count($export_array)-1][] = manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc));

            /*$export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)),manageAmountFormat(array_sum($total_disc)),manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc)
            ));*/
        }

        if($case == 'MENUITEMGENERALSALESWITHPLU')
        {
            $file_name = strtolower($case);
            $export_array[] = array('SN','Item','Sales QTY','Gross Sales');
            if($charges_names)
            {
                foreach($charges_names as $chargename)
                {
                    $export_array[count($export_array)-1][] = str_replace('_',' ',strtoupper($chargename));
                }
            }
            $export_array[count($export_array)-1][] = 'Taxes';
            $export_array[count($export_array)-1] [] = 'Net Sales % Of Ttl';
             $export_array[count($export_array)-1] [] = 'Plu No';
             $export_array[count($export_array)-1] [] = 'Plu Name';
            $total_qty = [];
            $total_amount = [];
            $total_disc = [];
             $final_totla_charge= [];
            foreach($mixed_array as $array)
            {
                $my_inner_report = [
                $counter,
                $array['title'],
                $array['item_total_quantity'],manageAmountFormat($array['gross_sale'])];

                /*,manageAmountFormat($array['total_charges']),manageAmountFormat($array['gross_sale']-$array['total_charges']),$array['plu_number'],$array['plu_name']
                ]; */
                if($charges_names)
                {
                    foreach($charges_names as $charges_name_detail)
                    {
                        if(isset($array[$charges_name_detail]))
                        {
                            $my_inner_report[] =   manageAmountFormat($array[$charges_name_detail]);
                            $final_totla_charge[$charges_name_detail][] = $array[$charges_name_detail]; 
                        }
                        else
                        {
                            $my_inner_report[] =     '0.00';
                        }
                    }
                }
                $my_inner_report[] = manageAmountFormat($array['total_charges']);
                $my_inner_report[] = manageAmountFormat($array['gross_sale']-$array['total_charges']);
                $my_inner_report[] = $array['plu_number'];
                 $my_inner_report[] = $array['plu_name'];

                 $export_array[] = $my_inner_report;
                $total_qty[] = $array['item_total_quantity'];
                $total_amount[] = $array['gross_sale'];
                $total_disc[] = $array['total_charges']; 
                $counter++;
            } 
            $export_array[] = array();
            $export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)));
            if($charges_names)
            {
                foreach($charges_names as $charges_name_detail)
                {
                    if(isset($final_totla_charge[$charges_name_detail]))
                    {
                        $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
                    }
                    else
                    {
                        $export_array[count($export_array)-1][] = '0.00';
                    }
                } 
            }

            $export_array[count($export_array)-1][] = manageAmountFormat(array_sum($total_disc));

             $export_array[count($export_array)-1][] = manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc));

            /*$export_array[] = array('','Grand Total',array_sum($total_qty),manageAmountFormat(array_sum($total_amount)),manageAmountFormat(array_sum($total_disc)),manageAmountFormat(
                array_sum($total_amount)-array_sum($total_disc)
            ));*/
        }



        
        $this->downloadExcelFile($export_array,$filetype,$file_name);

    }

    public function downloadExcelFile($data,$type,$file_name)
    {
        // refrence url http://www.maatwebsite.nl/laravel-excel/docs/blade
        //http://www.easylaravelbook.com/blog/2016/04/19/exporting-laravel-data-to-an-excel-spreadsheet/
        return Excel::create($file_name, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);



    }


    public function downloadPDF($filetype,$mixed_array,$request,$case,$report_name,$charges_names=null)
    {
        $heading =   $report_name;//heading;
        $restro_name = '';
        $waiter_name = '';
        $printed_time = 'Printed On:'.date('d/m/Y h:i A');
        $taxes_name = $charges_names;

        $period_from = '';
        $period_to = '';
        if ($request->has('restaurant'))
        {
            $restro_detail = Restaurant::select(['name'])->whereId($request->input('restaurant'))->first();
            $restro_name = strtoupper($restro_detail->name);//restro;
        }

        if ($request->has('user_id'))
        {
            $user_detail = User::select(['name'])->whereId($request->input('user_id'))->first();
            $waiter_name = 'Waiter: '.strtoupper($user_detail->name);//restro;
            if($case == 'CASHIERREPORT')
            {
               $waiter_name = 'Cashier: '.strtoupper($user_detail->name);//restro; 
            }
        }
           
        if ($request->has('start-date'))
        {
           $period_from = 'Period From : '.date('d/m/Y h:i A',strtotime($request->input('start-date')));  
           
          
        }
        if ($request->has('end-date'))
        {
            $period_to = '  - To : '.date('d/m/Y h:i A',strtotime($request->input('end-date')));
        }

      $pdf = PDF::loadView('admin.reports.reportinpdf', compact('filetype','mixed_array','request','case','report_name','restro_name','waiter_name','printed_time','period_from','period_to','heading','taxes_name'));
      return $pdf->download($report_name.'.pdf');

      
    }

    public function isReportexistInRestro($receipt_id,$restro_id)
    {
       
        $returning_data = false;
         $my_query =  "select ord.restaurant_id from order_receipts as ordr join order_receipt_relations as orr on orr.order_receipt_id=ordr.id join orders as ord on ord.id = orr.order_id where ordr.id = ".$receipt_id." limit 1 ";
        $data = DB::select( DB::raw($my_query) );
        if(count($data)>0)
        {
            

           if($data[0]->restaurant_id == $restro_id)
           {
            $returning_data = true;
           }
        }
        return $returning_data;
    }


    public function cashierReport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-cashier-reports']) || $permission == 'superadmin')
        {
            
            $cashierList=[];
            $all_cashier_list = OrderReceipt::select('cashier_id')->where('cashier_id','!=',null)->pluck('cashier_id')->toArray();
            if(count($all_cashier_list)>0)
            {
                $cashierList = User::whereIn('id',$all_cashier_list)->pluck('name','id');
            }
       $detail = [];
       if(isset($request->user_id))
       {
            $all_cash_receipt = OrderReceipt::where('cashier_id',$request->user_id);
            if ($request->has('start-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','<=',$request->input('end-date'));
            }
            $all_cash_receipt  = $all_cash_receipt->get();
            foreach ($all_cash_receipt as  $cash_receipt) {
                foreach($cash_receipt->getAssociatePaymentsWithReceipt as $transactions)
                {
                    $detail[$transactions->payment_mode]['payment_mode'] = $transactions->payment_mode;

                    if(strtoupper($transactions->payment_mode) =='COMPLEMENTARY')
                    {
                        $transactions->amount = 0;
                    }

                    if(isset($detail[$transactions->payment_mode]['amount']))
                    {
                        $detail[$transactions->payment_mode]['amount'] = $detail[$transactions->payment_mode]['amount']+$transactions->amount;
                    }
                    else
                    {
                        $detail[$transactions->payment_mode]['amount'] = $transactions->amount; 
                    }   
                }  
            }  
       }
       sort($detail);
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
            else
            {
                return $this->downloadPDF('pdf',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
        }
            $breadcum = [$title=>'','Cashier Report'=>''];
            return view('admin.reports.cashierReports',compact('title','model','breadcum','detail','cashierList'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function getdiscountsReportsWithOrders(Request $request)
    {


         $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-discounts-reports-with-orders']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
           $discounts_records  = Order::where('status','COMPLETED')
                ->where('order_type','PREPAID')->where('order_discounts','!=',NULL);
            if ($request->has('start-date'))
            {
               
                $discounts_records = $discounts_records->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            { 
                $discounts_records = $discounts_records->where('created_at','<=',$request->input('end-date'));
            }
            if ($request->has('restaurant'))
            {
                $discounts_records = $discounts_records->where('restaurant_id',$request->input('restaurant'));
            }

            $discounts_records = $discounts_records->orderBy('id','desc');
            $data  = $discounts_records->get();
        
            if ($request->has('manage-request') &&  $request->input('manage-request') == 'pdf' )
            {
                $pdf = PDF::loadView('admin.reports.getordersdiscountreportsinpdf', compact('data','request'));
                return $pdf->download('discountsreportswithorders.pdf');
            }

            $breadcum = [$title=>'','Discount report with orders'=>''];
            return view('admin.reports.discountreportwithorders',compact('title','data','model','breadcum','restro'));
           
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

        


    }

    public function getCancelledOrdersReports(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-void-orders-reports']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
            $cancled_records  = Order::where('status','CANCLED')->where('order_final_price','>','0');
            if ($request->has('start-date'))
            {
               
                $cancled_records = $cancled_records->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            { 
                $cancled_records = $cancled_records->where('created_at','<=',$request->input('end-date'));
            }
            if ($request->has('restaurant'))
            {
                $cancled_records = $cancled_records->where('restaurant_id',$request->input('restaurant'));
            }

            $cancled_records = $cancled_records->orderBy('id','desc');

            $data  = $cancled_records->get();

            //dd($data);
        
            if ($request->has('manage-request') &&  $request->input('manage-request') == 'pdf' )
            {
                $pdf = PDF::loadView('admin.reports.cancledordersinpdf', compact('data','request'));
                return $pdf->download('voidordersreport.pdf');
            }

            $breadcum = [$title=>'','Void orders reports'=>''];
            return view('admin.reports.cancledorders',compact('title','data','model','breadcum','restro'));
           
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

    }

    public function getcomplementaryReportsWithOrders(Request $request) {
      //  echo "sf"; die;
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-complementary-reports-with-orders']) || $permission == 'superadmin') {
            $restro = $this->getRestaurantList();
           $complementary_records  = Order::where('order_type','PREPAID')->whereNotNull('complimentry_code');
            //echo $request->input('start-date'); die;
            if ($request->has('start-date')){
               $complementary_records = $complementary_records->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date')) { 
                $complementary_records = $complementary_records->where('created_at','<=',$request->input('end-date'));
            }
            if ($request->has('restaurant')) {
                $complementary_records = $complementary_records->where('restaurant_id',$request->input('restaurant'));
            }

            $complementary_records = $complementary_records->orderBy('id','desc');

            $data  = $complementary_records->get();
            //print_r($data); die;
            
            if ($request->has('manage-request') &&  $request->input('manage-request') == 'pdf' )
            {
                $pdf = PDF::loadView('admin.reports.getorderscomplementaryreportsinpdf', compact('data','request'));
                return $pdf->download('complementaryreportswithorders.pdf');
            }

            $breadcum = [$title=>'','Complementary report with orders'=>''];
            return view('admin.reports.complementaryreportwithorders',compact('title','data','model','breadcum','restro'));
           
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

        


    }


    public function getcomplementaryReportsWithOrdersOld(Request $request)
    {


         $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-complementary-reports-with-orders']) || $permission == 'superadmin')
        {
            $restro = $this->getRestaurantList();
           $complementary_records  = Order::where('status','COMPLETED')
                ->where('order_type','PREPAID')->where('complimentry_code','!=',NULL);
            if ($request->has('start-date'))
            {
               
                $complementary_records = $complementary_records->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            { 
                $complementary_records = $complementary_records->where('created_at','<=',$request->input('end-date'));
            }
            if ($request->has('restaurant'))
            {
                $complementary_records = $complementary_records->where('restaurant_id',$request->input('restaurant'));
            }

            $complementary_records = $complementary_records->orderBy('id','desc');

            $data  = $complementary_records->get();

            //dd($data);
        
            if ($request->has('manage-request') &&  $request->input('manage-request') == 'pdf' )
            {
                $pdf = PDF::loadView('admin.reports.getorderscomplementaryreportsinpdf', compact('data','request'));
                return $pdf->download('complementaryreportswithorders.pdf');
            }

            $breadcum = [$title=>'','Complementary report with orders'=>''];
            return view('admin.reports.complementaryreportwithorders',compact('title','data','model','breadcum','restro'));
           
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

        


    }


    public function userWalletSummary(Request $request)
    {
       $title = $this->title;
       $model = $this->model;
       $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___user-wallet-summary']) || $permission == 'superadmin')
        {
       $restro = $this->getRestaurantList();
       $userList = WalletTransaction::pluck('phone_number','phone_number');
       
       $detail = [];


        $rows = WalletTransaction::where('entry_type','!=','');

        if ($request->has('start-date'))
        {
           
            $rows = $rows->where('created_at','>=',$request->input('start-date'));
        }
        if ($request->has('end-date'))
        { 
            $rows = $rows->where('created_at','<=',$request->input('end-date'));
        }
        if ($request->has('phone_number'))
        {
            $rows = $rows->where('phone_number',$request->input('phone_number'));
        }


       $rows = $rows->get();
       $i=0;


       foreach($rows as $row)
       {
            $detail[$i]['phone_number'] = $row->phone_number;
            $detail[$i]['created_date'] =date('Y-m-d',strtotime( $row->created_at));
            $narration = '';
            $entry_type = 'PURCHASE';

            if($row->transaction_type == 'CR')
            {
                $entry_type_str = explode(' ',$row->entry_type);
                $entry_type =$entry_type_str[1].' '.$entry_type_str[2];
                $narration = $entry_type_str[0];
            }

            $detail[$i]['entry_type'] = $entry_type;
            $detail[$i]['amount'] = $row->amount;
            $detail[$i]['narration'] = $narration;
            $detail[$i]['remark'] = $row->refrence_description;
             $detail[$i]['transaction_type'] = $row->transaction_type;

            
            
            
            

            $i++;
       }
       sort($detail);
       $breadcum = [$title=>'','Wallet Ledger Entries'=>''];
        return view('admin.reports.userWalletSummary',compact('title','lists','model','breadcum','detail','restro','userList'));

         }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function paymentSalesSummaryData(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___payment-sales-summary-data']) || $permission == 'superadmin') {
            $category_of_complimentary = \Config::get('params.category_of_complimentary');
            $restro = $this->getRestaurantList();

            //getting data for payment method summary start
            $paymentDataSummary = [];
            $paymentSummary = ReceiptSummaryPayment::orderBy('id','desc');
            if ($request->has('start-date')) {
                $paymentSummary = $paymentSummary->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date')) {
                $paymentSummary = $paymentSummary->where('created_at','<=',$request->input('end-date'));
            }
            $paymentSummary = $paymentSummary->get();
            foreach($paymentSummary as $paymentDetail) {
                $can_come = true;
                if ($request->has('restaurant'))
                {
                    $restaurant_id = $request->input('restaurant');
                    $can_come = $this->isReportexistInRestro($paymentDetail->order_receipt_id,$restaurant_id);
                }
                if($can_come == true)
                {
                    if(strtoupper($paymentDetail->payment_mode) =='MPESA')
                    {
                        $paymentDetail->payment_mode = 'MPESA TILL';
                        if(isset($paymentDetail->mpesa_request_id))
                        {
                            $paymentDetail->payment_mode = 'MPESA APP'; 
                        }
                    }
                    $paymentDataSummary[$paymentDetail->payment_mode]['payment_mode'] =  $paymentDetail->payment_mode;
                    if(strtoupper($paymentDetail->payment_mode) =='COMPLEMENTARY') {
                        $paymentDetail->amount = 0;
                    }
                    if(isset($paymentDataSummary[$paymentDetail->payment_mode]['number_of_transaction'])) {
                        $paymentDataSummary[$paymentDetail->payment_mode]['number_of_transaction'] = $paymentDataSummary[$paymentDetail->payment_mode]['number_of_transaction']+1;
                    } 
                    else
                    {
                        $paymentDataSummary[$paymentDetail->payment_mode]['number_of_transaction'] =1;
                    }
                    if(isset($paymentDataSummary[$paymentDetail->payment_mode]['amount']))
                    {
                        $paymentDataSummary[$paymentDetail->payment_mode]['amount'] = $paymentDataSummary[$paymentDetail->payment_mode]['amount']+$paymentDetail->amount;
                    } 
                    else
                    {
                        $paymentDataSummary[$paymentDetail->payment_mode]['amount'] =$paymentDetail->amount;
                    }
                }
            }
            $paymentDataSummary = array_values($paymentDataSummary);
            //getting data for payment method summary end

            //getting discount summary reports start
            $Complementary = [];
            $Admin_discounts_arr = [];
            $Customer_discounts_arr=[];
            $discount_record_arr = [];
            $complemanetary_records = ReceiptSummaryPayment::select('orders.id AS order_id', 'receipt_summary_payments.id','receipt_summary_payments.amount', 'receipt_summary_payments.amount', 'receipt_summary_payments.category_of_complimentary')->leftJoin('order_receipt_relations', 'order_receipt_relations.order_receipt_id', '=', 'receipt_summary_payments.order_receipt_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_receipt_relations.order_id');
                    
            $Customer_discounts_records  = Order::select('order_discounts','user_id','id','billing_time')
            ->where('order_type','PREPAID')->where('order_discounts','!=',NULL);
            if ($request->has('start-date'))
            {
                $complemanetary_records = $complemanetary_records->where('orders.created_at','>=',$request->input('start-date'));
                $Customer_discounts_records = $Customer_discounts_records->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date')) {
                $complemanetary_records = $complemanetary_records->where('orders.created_at','<=',$request->input('end-date'));
                $Customer_discounts_records = $Customer_discounts_records->where('created_at','<=',$request->input('end-date'));
            }
            
            if ($request->has('restaurant')) {
                $restaurant_id = $request->input('restaurant');
                $complemanetary_records = $complemanetary_records->where('orders.restaurant_id', $restaurant_id);
            }
            $Complementary = $complemanetary_records->where('receipt_summary_payments.payment_mode','COMPLEMENTARY')
                ->where('orders.order_type','PREPAID')
                ->groupBy('receipt_summary_payments.id')
                ->get();
            
            
            $comp_arr = [
                1=>['no_of_transactions'=>0, 'total_amount'=>0],
                2=>['no_of_transactions'=>0, 'total_amount'=>0],
                3=>['no_of_transactions'=>0, 'total_amount'=>0]
            ];
            
            foreach($Complementary as $key => $comp_row){
                if(!empty($comp_row->category_of_complimentary) && isset($comp_arr[$comp_row->category_of_complimentary])) {
                    $comp_arr[$comp_row->category_of_complimentary]['no_of_transactions'] += 1;
                    $comp_arr[$comp_row->category_of_complimentary]['total_amount'] += $comp_row->amount;
                }
            }

            foreach($comp_arr as $comp_id=> $comp_row){
                $comp_name = $category_of_complimentary[$comp_id];
                $discount_record_arr[$comp_name] = $comp_row;
            }
      //  echo "<pre>"; print_r($comp_arr); die;
            $Complementary = $complemanetary_records->pluck('amount')->toArray();
             $discount_record_arr['Complementary']['no_of_transactions'] = count($Complementary);
             $discount_record_arr['Complementary']['total_amount'] = array_sum($Complementary);
            $Customer_discounts_records = $Customer_discounts_records->get();
            foreach($Customer_discounts_records as $discounts)
            {
                $order_discounts_arr = json_decode($discounts->order_discounts);
                foreach($order_discounts_arr as $order_discounts)
                {
                    if(isset($order_discounts->discount_amount) && $order_discounts->discount_amount != "")
                    {
                        if($discounts->getAssociateUserForOrder->role_id == '11')
                        {
                            $Customer_discounts_arr[] = $order_discounts->discount_amount;
                        }

                        if($discounts->getAssociateUserForOrder->role_id == '4')
                        {
                            $Admin_discounts_arr[] = $order_discounts->discount_amount;
                        } 
                    }
                } 
            }
        $discount_record_arr['Customer_discounts']['no_of_transactions'] = count($Customer_discounts_arr);
        $discount_record_arr['Customer_discounts']['total_amount'] = array_sum($Customer_discounts_arr);
        $discount_record_arr['Admin_discounts']['no_of_transactions'] = count($Admin_discounts_arr);
        $discount_record_arr['Admin_discounts']['total_amount'] = array_sum($Admin_discounts_arr);
        //getting discount summary reports end

        //major group sales summary start

        
        
        
        $major_group = Category::select('id','name')->whereIn('id',['5','6'])->get();
        
        //print_r($major_group); die;
        $mjGroupDetail = [];
        $major_group_item_ids = [];
        $major_group_item_ids_styring_arr = [];
        foreach($major_group as $mjg)
        {
            $key = $mjg->id;
            $mjGroupDetail[$key]['title'] = ucfirst($mjg->name);
            $mjGroupDetail[$key]['item_total_quantity'] = 0;
            $mjGroupDetail[$key]['gross_sale'] = 0;
            $mjGroupDetail[$key]['total_charges'] = 0;
            $mjGroupDetail[$key]['net_sale'] = 0;
            $my_first_child = $mjg->getManyRelativeChilds->pluck('category_id')->toArray(); // sub major group
            if(count($my_first_child)>0) {
                $my_first_child_details = Category::select('id','name')->whereIn('id',$my_first_child)->get(); // sub majaor details
                foreach($my_first_child_details as $my_first_child_detail_arr)
                {
                    $my_scond_child_arr = $my_first_child_detail_arr->getManyRelativeChilds->pluck('category_id')->toArray(); // menu item group
                    if(count($my_scond_child_arr)>0)
                    {
                        if($key =='1') {
                            
                        } else if($key == '6'){
                            $major_group_item_ids[$key][] = implode(',',$my_scond_child_arr);
                        }
                        else {
                            $menuItmGroup = Category::select(['id','name','is_have_another_layout'])->whereIn('id',$my_scond_child_arr)->get();  
                            foreach($menuItmGroup as $group) {
                                $my_child = $group->getManyRelativeChilds; // family group
                                
                                if($group->is_have_another_layout=='1')
                                {
                                    $subchild = $my_child->pluck('category_id')->toArray(); // family group ids
                                    if(count($subchild)>0)
                                    {
                                        $third_child = CategoryRelation::whereIn('parent_id',$subchild)->pluck('category_id'); // sub family groups
                                        $items= [];
                                        if(count($third_child)>0)
                                        {
                                            $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$third_child)->pluck('item_id')->toArray();
                                            if(count($item_ids_arr)>0) {
                                                $major_group_item_ids[$key][] = implode(',',$item_ids_arr);
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $second_child = $my_child->pluck('category_id')->toArray();
                                    if(count($second_child)>0)
                                    {
                                        $item_ids_arr = ItemCategoryRelation::whereIn('category_id',$second_child)->pluck('item_id')->toArray();
                                        if(count($item_ids_arr)>0)
                                        {
                                            $major_group_item_ids[$key][] =  implode(',',$item_ids_arr); 
                                        }
                                    } 
                                }

                            } 
                        }
                        
                    }
                }
            } 
            $major_group_item_ids_styring_arr[$key] = isset($major_group_item_ids[$key])?implode(',',$major_group_item_ids[$key]):'';
        }
        foreach($major_group_item_ids_styring_arr as $main_group_key =>$main_group_item_string)
        {
            
            $item_ids = explode(',',$main_group_item_string);
            if(count($item_ids)>0)
            {
                $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])
                ->whereNotIn('item_delivery_status',['CANCLED','PENDING'])

                ->whereIn('food_item_id',$item_ids)->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                    $query->where('order_type','PREPAID');
                 });
                if ($request->has('start-date'))
                {
                    $all_item = $all_item->where('created_at','>=',$request->input('start-date'));

                }
                if ($request->has('end-date'))
                {
                    $all_item = $all_item->where('created_at','<=',$request->input('end-date'));

                }

                if ($request->has('restaurant'))
                {
                    $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
                }
                $data  = $all_item->get();
                
                if(count($data)>0)
                {

                   foreach($data as $row)
                   {
                       
                        $total_charges= [];
                        $get_charge = true;
                        if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                        {
                            $row->price = 0;
                            $get_charge = false;

                        }
                        if(isset($mjGroupDetail[$main_group_key]['item_total_quantity']))
                        {
                            $mjGroupDetail[$main_group_key]['item_total_quantity'] = $row->item_quantity+$mjGroupDetail[$main_group_key]['item_total_quantity'];
                        }
                        else
                        {
                            $mjGroupDetail[$main_group_key]['item_total_quantity'] = $row->item_quantity;
                        }

                        if(isset($mjGroupDetail[$main_group_key]['gross_sale']))
                        {
                            $mjGroupDetail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price)+$mjGroupDetail[$main_group_key]['gross_sale'];
                        }
                        else
                        {
                            $mjGroupDetail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price);
                        }
                        $charges_arr = json_decode($row->item_charges);
                        if(count($charges_arr)>0 && $get_charge == true)
                        {
                            foreach($charges_arr as $ch)
                            {
                                if(isset($ch->charged_amount))
                                $total_charges[] = $ch->charged_amount;
                            }

                            if(isset($mjGroupDetail[$main_group_key]['total_charges']))
                            {
                                $mjGroupDetail[$main_group_key]['total_charges'] = array_sum($total_charges)+$mjGroupDetail[$main_group_key]['total_charges'];
                            }
                            else
                            {
                                $mjGroupDetail[$main_group_key]['total_charges'] = array_sum($total_charges);
                            }
                        }
                        if(!isset($mjGroupDetail[$main_group_key]['total_charges']))
                        {
                            $mjGroupDetail[$main_group_key]['total_charges'] = '0';
                        }
                        $mjGroupDetail[$main_group_key]['net_sale'] = $mjGroupDetail[$main_group_key]['gross_sale']- $mjGroupDetail[$main_group_key]['total_charges'];
                    }
                }
            }
            
        }
        
        
        
        //major group sales summary end

        
        
        
        $mjg = Category::select('id','name')->where('id','1')->first();
        
        $menu_group_detail = [];
        $menu_item_ids = [];
        $menu_group_item_ids_styring_arr = [];
        $key = @$mjg->id;
        
        
        if(isset($mjg)){
        $my_first_child = $mjg->getManyRelativeChilds->pluck('category_id')->toArray(); // sub major group
        }else{
            $my_first_child = [];
        }
        if (count($my_first_child) > 0) {
            $my_first_child_details = Category::select('id', 'name')->whereIn('id', $my_first_child)->get(); // sub majaor details
            foreach ($my_first_child_details as $my_first_child_detail_arr) {
                $my_scond_child_arr = $my_first_child_detail_arr->getManyRelativeChilds->pluck('category_id')->toArray(); // menu item group
                if (count($my_scond_child_arr) > 0) {
                    
                    $menuItmGroup = Category::select(['id', 'name', 'is_have_another_layout'])->whereIn('id', $my_scond_child_arr)->get(); // menu group
                    foreach ($menuItmGroup as $group) {
                        $menu_group_id = $group->id;
                        $menu_group_detail[$menu_group_id]['title'] = ucfirst($group->name);
                        $menu_group_detail[$menu_group_id]['item_total_quantity'] = 0;
                        $menu_group_detail[$menu_group_id]['gross_sale'] = 0;
                        $menu_group_detail[$menu_group_id]['total_charges'] = 0;
                        $menu_group_detail[$menu_group_id]['net_sale'] = 0;
                        
                        $my_child = $group->getManyRelativeChilds; // family group

                        if ($group->is_have_another_layout == '1') {
                            $subchild = $my_child->pluck('category_id')->toArray(); // family group ids
                            if (count($subchild) > 0) {
                                $third_child = CategoryRelation::whereIn('parent_id', $subchild)->pluck('category_id'); // sub family groups
                                $items = [];
                                if (count($third_child) > 0) {
                                    $item_ids_arr = ItemCategoryRelation::whereIn('category_id', $third_child)->pluck('item_id')->toArray();
                                    if (count($item_ids_arr) > 0) {
                                        $menu_item_ids[$menu_group_id][] = implode(',', $item_ids_arr);
                                    }
                                }
                            }
                        } else {
                            $second_child = $my_child->pluck('category_id')->toArray();
                            if (count($second_child) > 0) {
                                $item_ids_arr = ItemCategoryRelation::whereIn('category_id', $second_child)->pluck('item_id')->toArray();
                                if (count($item_ids_arr) > 0) {
                                    $menu_item_ids[$menu_group_id][] = implode(',', $item_ids_arr);
                                }
                            }
                        }
                        $menu_group_item_ids_styring_arr[$menu_group_id] = isset($menu_item_ids[$menu_group_id]) ? implode(',', $menu_item_ids[$menu_group_id]) : '';
                    }
                    
                }
            }
        }
        

        
        
        foreach($menu_group_item_ids_styring_arr as $main_group_key =>$main_group_item_string) {
           
                $item_ids = explode(',',$main_group_item_string);
                if(count($item_ids)>0)
                {
                    $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','order_id'])
                    ->whereNotIn('item_delivery_status',['CANCLED','PENDING'])

                    ->whereIn('food_item_id',$item_ids)->where('order_offer_id',null)->whereHas('getrelatedOrderForItem', function ($query){ 
                        $query->where('order_type','PREPAID');
                     });
                    if ($request->has('start-date'))
                    {
                        $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
                       
                    }
                    if ($request->has('end-date'))
                    {
                        $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
                      
                    }

                    if ($request->has('restaurant'))
                    {
                        $all_item = $all_item->where('restaurant_id',$request->input('restaurant'));
                    }
                    $data  = $all_item->get();
                    if(count($data)>0)
                    {
                              
                       foreach($data as $row)
                       {
                            $total_charges= [];
                            $get_charge = true;
                            if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                            {
                                $row->price = 0;
                                $get_charge = false;

                            }
                            if(isset($menu_group_detail[$main_group_key]['item_total_quantity']))
                            {
                                $menu_group_detail[$main_group_key]['item_total_quantity'] = $row->item_quantity+$menu_group_detail[$main_group_key]['item_total_quantity'];
                            }
                            else
                            {
                                $menu_group_detail[$main_group_key]['item_total_quantity'] = $row->item_quantity;
                            }

                            if(isset($menu_group_detail[$main_group_key]['gross_sale']))
                            {
                                $menu_group_detail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price)+$menu_group_detail[$main_group_key]['gross_sale'];
                            }
                            else
                            {
                                $menu_group_detail[$main_group_key]['gross_sale'] = ($row->item_quantity*$row->price);
                            }
                            $charges_arr = json_decode($row->item_charges);

                            if(count($charges_arr)>0 && $get_charge == true)
                            {
                                foreach($charges_arr as $ch)
                                {
                                    if(isset($ch->charged_amount))
                                    $total_charges[] = $ch->charged_amount;
                                }

                                if(isset($menu_group_detail[$main_group_key]['total_charges']))
                                {
                                    $menu_group_detail[$main_group_key]['total_charges'] = array_sum($total_charges)+$menu_group_detail[$main_group_key]['total_charges'];
                                }
                                else
                                {
                                    $menu_group_detail[$main_group_key]['total_charges'] = array_sum($total_charges);
                                }
                            }
                            if(!isset($menu_group_detail[$main_group_key]['total_charges']))
                            {
                                $menu_group_detail[$main_group_key]['total_charges'] = '0';
                            }
                            $menu_group_detail[$main_group_key]['net_sale'] = $menu_group_detail[$main_group_key]['gross_sale']- $menu_group_detail[$main_group_key]['total_charges'];
                        }
                    }
                }
            
        }
        
        //print_r($menu_group_detail); die;












            //get cancled order summary start

        $cancled_records  = Order::where('status','CANCLED')->where('order_final_price','>','0');
        if ($request->has('start-date'))
        {
           
            $cancled_records = $cancled_records->where('created_at','>=',$request->input('start-date'));
        }
        if ($request->has('end-date'))
        { 
            $cancled_records = $cancled_records->where('created_at','<=',$request->input('end-date'));
        }
        if ($request->has('restaurant'))
        {
            $cancled_records = $cancled_records->where('restaurant_id',$request->input('restaurant'));
        }

        $cancled_records = $cancled_records->orderBy('id','desc');
        $cancledOrdertSummary['count'] = $cancled_records->count();
        $cancledOrdertSummary['amount'] = $cancled_records->sum('order_final_price');

    
        
        $mjGroupDetail = array_merge($mjGroupDetail, $menu_group_detail);
        

        //get cancle order summary end

        if ($request->has('manage-request') &&  $request->input('manage-request') == 'pdf' ) {

            if ($request->has('start-date'))
            {
               
                $start_date = $request->input('start-date');
            }
            if ($request->has('end-date'))
            { 
                $end_date =$request->input('end-date');
            }

            if ($request->has('restaurant'))
            { 
                $restro_id =$request->input('restaurant');
                $restro_name = @$restro[$restro_id];
            }
           // echo "<pre>"; print_r($discount_record_arr); die;

            $pdf = PDF::loadView('admin.reports.paymentSalesSummaryDataPDF', compact('title','lists','model','breadcum','restro','paymentDataSummary','discount_record_arr','mjGroupDetail','cancledOrdertSummary','start_date','end_date','restro_name'))
            ->setPaper('a4', 'landscape');
            return $pdf->download('salessummarydata.pdf');
        }
            $breadcum = [$title=>'','Payment Sales Summary Data'=>''];
            return view('admin.reports.paymentSalesSummaryData',compact('title','lists','model','breadcum','restro'));

           // return view('admin.reports.paymentSalesSummaryDataPDF',compact('title','lists','model','breadcum','restro','paymentDataSummary','discount_record_arr','mjGroupDetail','cancledOrdertSummary'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }



    public function waiterSummaryReports(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___waiter-summary-reports']) || $permission == 'superadmin')
        {
            $detail = [];
            $all_cash_receipt = OrderReceipt::where('user_id','>',0);
            if ($request->has('start-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','<=',$request->input('end-date'));
            }
            $all_cash_receipt  = $all_cash_receipt->orderBy('user_id','asc')->get();
            foreach ($all_cash_receipt as  $cash_receipt) 
            {
                 if(!isset($detail[$cash_receipt->getAssociateUserForReceipt->id]))
                   {
                        $detail[$cash_receipt->getAssociateUserForReceipt->id]['waiter_Name']=$cash_receipt->getAssociateUserForReceipt->name;
                        $detail[$cash_receipt->getAssociateUserForReceipt->id]['waiter_id']=$cash_receipt->getAssociateUserForReceipt->id;
                   }

                   $detail[$cash_receipt->getAssociateUserForReceipt->id]['amount'][] = $cash_receipt->getAssociatePaymentsWithReceipt?$cash_receipt->getAssociatePaymentsWithReceipt->sum('amount'):0;
            }  
       sort($detail);
      // dd($detail);
       
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'WAITERSUMMARYREPORT','Waiter Summary Report'); 
            }
            else
            {
                //return $this->downloadPDF('pdf',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
        }
            $breadcum = [$title=>'','Waiter Summary Report'=>''];
            return view('admin.reports.waiterSummaryReports',compact('title','model','breadcum','detail','cashierList'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }
       

    public function waiterSummarymenuitemReports(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___waiter-summary-reports']) || $permission == 'superadmin')
        {
            $detail = [];
            $all_cash_receipt = OrderReceipt::where('user_id','>',0);
            if ($request->has('start-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','<=',$request->input('end-date'));
            }
            $all_cash_receipt  = $all_cash_receipt->orderBy('user_id','asc')->get();
            foreach ($all_cash_receipt as  $cash_receipt) 
            {
                 if(!isset($detail[$cash_receipt->getAssociateUserForReceipt->id]))
                   {
                        $detail[$cash_receipt->getAssociateUserForReceipt->id]['waiter_Name']=$cash_receipt->getAssociateUserForReceipt->name;
                        $detail[$cash_receipt->getAssociateUserForReceipt->id]['waiter_id']=$cash_receipt->getAssociateUserForReceipt->id;
                        $waiter = $cash_receipt->user_id;
                        $orderitemcount = OrderedItem::where('order_offer_id',null);
                        if($request->has('menu_item')){
                            $orderitemcount->where('food_item_id',$request->menu_item);
                        }                       
                        $orderitemcount->whereHas('getrelatedOrderForItem', function ($query) use($waiter){ 
                            $query->where('order_type','PREPAID');
                            if ($waiter)
                            {
                                $query->where('user_id',$waiter);
                            }
                        });
                       $orderitemcount = $orderitemcount->count();
                        
//                      echo $orderitemcount;
                        $detail[$cash_receipt->getAssociateUserForReceipt->id]['item_count'] = $orderitemcount;

                   }    

                   $detail[$cash_receipt->getAssociateUserForReceipt->id]['amount'][] = $cash_receipt->getAssociatePaymentsWithReceipt?$cash_receipt->getAssociatePaymentsWithReceipt->sum('amount'):0;
            }  
  //          die("ok");
       sort($detail);
    //   echo "<pre>"; print_r($detail); die;
       
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'WAITERSUMMARYREPORT','Waiter Summary Report'); 
            }
            else
            {
                //return $this->downloadPDF('pdf',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
        }
            $breadcum = [$title=>'','Waiter Summary Per Menu Item Report'=>''];
            return view('admin.reports.waiterSummarymenuitemReports',compact('title','model','breadcum','detail','cashierList'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }


    public function detailedPaymentMethodReports(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $paymentmethods = PaymentMethod::pluck('title','title')->toArray();

        //echo "<pre>"; print_r($paymentmethods); die;
        if(isset($permission[$pmodule.'___detailed-payment-methods-reports']) || $permission == 'superadmin')
        {
            $detail = [];
            $all_cash_receipt = OrderReceipt::where('user_id','>',0);
            if ($request->has('start-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','<=',$request->input('end-date'));
            }
            // if ($request->restaurant) {
            //  $all_cash_receipt = $all_cash_receipt->whereHas('getAssociatePaymentsWithReceipt', function ($q) use($request) {
            //     $q->where('restaurant_id', $request->restaurant);
            // });
            // }
            // if ($request->payment_method) {
            //     $all_cash_receipt =  $all_cash_receipt->whereHas('getAssociatePaymentsWithReceipt', function ($q) use($request) {
            //         $q->where('payment_mode', $request->payment_method);
            //     });
            // }

            $all_cash_receipt  = $all_cash_receipt->orderBy('id','desc')->get();
            foreach ($all_cash_receipt as  $cash_receipt) 
            {
               $detail[$cash_receipt->id]['receipt_id']=$cash_receipt->id;
               $detail[$cash_receipt->id]['created_at']=date('Y-m-d H:i',strtotime($cash_receipt->created_at));
                $detail[$cash_receipt->id]['waiter_name']=$cash_receipt->getAssociateUserForReceipt->name;
                 $detail[$cash_receipt->id]['cashier_name']=$cash_receipt->getAssociateCashierDetail->name;
                  $detail[$cash_receipt->id]['no_of_orders']=$cash_receipt->getAssociateOrdersWithReceipt->count();
                  foreach($cash_receipt->getAssociatePaymentsWithReceipt as $transactions)
                {
                   
                      
                 
                    $detail[$cash_receipt->id]['payments'][]=['payment_mode'=>$transactions->payment_mode,'amount'=>$transactions->amount,'narration'=>$transactions->narration];
                }  
            }  
       
      
       
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'DETAILEDPAYMENTMETHODREPORT','Detailed Payment Methods Report'); 
            }
            else
            {
                //return $this->downloadPDF('pdf',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
        }
            $restroList = $this->getRestaurantList();

            $breadcum = [$title=>'','Detailed Payment Method'=>''];
            return view('admin.reports.detailedPaymentMethodReports',compact('title','paymentmethods', 'restroList','model','breadcum','detail','cashierList'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }



    public function cashierDetailedReport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$pmodule.'___get-cashier-detailed-reports']) || $permission == 'superadmin')
        {
            $detail = [];
            $all_cash_receipt = OrderReceipt::where('cashier_id','>',0);
            if ($request->has('start-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_cash_receipt = $all_cash_receipt->where('created_at','<=',$request->input('end-date'));
            }
            $all_cash_receipt  = $all_cash_receipt->orderBy('cashier_id','asc')->get();
            foreach ($all_cash_receipt as  $cash_receipt) 
            {
                foreach($cash_receipt->getAssociatePaymentsWithReceipt as $transactions)
                {
                   if(!isset($detail[$cash_receipt->getAssociateCashierDetail->id]))
                   {
                        $detail[$cash_receipt->getAssociateCashierDetail->id]['cashier_name']=$cash_receipt->getAssociateCashierDetail->name;
                        $detail[$cash_receipt->getAssociateCashierDetail->id]['cashier_id']=$cash_receipt->getAssociateCashierDetail->id;
                   }
                    $detail[$cash_receipt->getAssociateCashierDetail->id]['payments'][]=['payment_mode'=>$transactions->payment_mode,'amount'=>$transactions->amount,'narration'=>$transactions->narration,'waiter_name'=>$cash_receipt->getAssociateUserForReceipt->name];
                }  
            }  
       sort($detail);
       
        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                $this->exportdata('xls',$detail,$request,'CASHIERDETAILEDREPORT','Cashier Detailed Report'); 
            }
            else
            {
               // return $this->downloadPDF('pdf',$detail,$request,'CASHIERREPORT','Cashier Report'); 
            }
        }
            $breadcum = [$title=>'','Cashier Detailed Report'=>''];
            return view('admin.reports.cashierDetailedReports',compact('title','model','breadcum','detail','cashierList'));

        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }

    
    

    
}
