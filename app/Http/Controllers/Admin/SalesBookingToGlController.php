<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\Payment;
use App\Model\ItemSalesWithGlCode;
use App\Model\PaymentDebit;
use App\Model\PaymentCredit;
use App\Model\WaAccountingPeriod;
use App\Model\TaxManager;
use App\Model\PostedSale;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\OrdersDiscountsForGlTran;



class SalesBookingToGlController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'sales-booking-to-gl';
        $this->title = 'Sales Booking To Gl';
        $this->pmodule = 'sales-booking-to-gl';
          ini_set('memory_limit', '4096M');
        set_time_limit(300000000); // Extends to 5 minutes.
    } 

    public function index()
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Advertisement::orderBy('display_order', 'ASC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.salesBookingToGl.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }   

    }

    public function dailySales()
    {
        
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
          /* $lists = ItemSalesWithGlCode::select(
                    'item_sales_with_gl_codes.sale_date', 'item_sales_with_gl_codes.item_title', 
                    'categories.name as category_name', 'wa_charts_of_accounts.account_code', 'wa_charts_of_accounts.account_name',
                    DB::raw("SUM(quantity) as quantity_sum"),
                    DB::raw("SUM(gross_sale) as gross_sale_sum"),
                    DB::raw("SUM(vat) as vat_sum"),
                    DB::raw("SUM(catering_levy) as catering_levy_sum"),
                    DB::raw("SUM(service_tax) as service_tax_sum"),
                    DB::raw("SUM(net_sales) as net_sales_sum")
                    )
                    ->leftJoin('categories', 'item_sales_with_gl_codes.family_group_id', '=', 'categories.id')
                    ->leftJoin('wa_charts_of_accounts', 'item_sales_with_gl_codes.gl_code_id', '=', 'wa_charts_of_accounts.id')
                    ->groupBy('sale_date', 'food_item_id')
                    ->orderBy('item_sales_with_gl_codes.id', 'DESC')
                    ->get();

                //    $lists = null;
                */

            //$arr = ItemSalesWithGlCode::where('is_posted','0')->orderBy('sale_date','desc')->get();

            $arr = ItemSalesWithGlCode::select(
                        'item_sales_with_gl_codes.*',
                        DB::raw("SUM(quantity) as quantity"),
                        DB::raw("SUM(gross_sale) as gross_sale"),
                        DB::raw("SUM(vat) as vat"),
                        DB::raw("SUM(catering_levy) as catering_levy"),
                        DB::raw("SUM(service_tax) as service_tax"),
                        DB::raw("SUM(net_sales) as net_sales")
                    )
                ->where('is_posted','0')
                ->groupBy( 'restaurant_id','gl_code_id')
                ->orderBy('sale_date','desc')
                ->get();
                    // echo "<pre>";
                    // print_r($arr);
                    // die;
            $data = [];
            foreach($arr as $array)
            {
                if(isset($array->getGlDetail))
                {
                    // echo "<pre>";
                    // print_r($array->getGlDetail);
                    // die;

                     $getGlDetail = $array->getGlDetail;
                    $key = $getGlDetail->account_code;

                   
                    $data[$array->restaurant_id.'#'.$key]['item_title'] = $array->item_title;
                    $data[$array->restaurant_id.'#'.$key]['account_code'] = $getGlDetail->account_code;

                    $data[$array->restaurant_id.'#'.$key]['account_code'] = $getGlDetail->account_code;
                    $data[$array->restaurant_id.'#'.$key]['restaurant_name'] = ($array->restaurant_id!="") ? getRestaurantNameById($array->restaurant_id) : '--';

                    $data[$array->restaurant_id.'#'.$key]['account_name'] = $getGlDetail->account_name;

                   // $data[$array->sale_date.'#'.$key]['family_group'] = ucfirst($array->getRelatedCategory->name);

                     $data[$array->restaurant_id.'#'.$key]['family_group'] = '';

                    $data[$array->restaurant_id.'#'.$key]['sale_date'] = $array->sale_date;

                    if(isset($data[$array->restaurant_id.'#'.$key]['gross_sale']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['gross_sale'] = (float)($data[$array->restaurant_id.'#'.$key]['gross_sale']+$array->gross_sale);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['gross_sale'] = (float) $array->gross_sale;
                    }

                    if(isset($data[$array->restaurant_id.'#'.$key]['quantity']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['quantity'] = (float)($data[$array->restaurant_id.'#'.$key]['quantity']+$array->quantity);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['quantity'] = (float) $array->quantity;
                    }

                    if(isset($data[$array->restaurant_id.'#'.$key]['vat']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['vat'] = (float)($data[$array->restaurant_id.'#'.$key]['vat']+$array->vat);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['vat'] = (float) $array->vat;
                    }

                     if(isset($data[$array->restaurant_id.'#'.$key]['catering_levy']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['catering_levy'] = (float)($data[$array->restaurant_id.'#'.$key]['catering_levy']+$array->catering_levy);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['catering_levy'] = (float) $array->catering_levy;
                    }

                    if(isset($data[$array->restaurant_id.'#'.$key]['service_tax']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['service_tax'] = (float)($data[$array->restaurant_id.'#'.$key]['service_tax']+$array->service_tax);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['service_tax'] = (float) $array->service_tax;
                    }

                    if(isset($data[$array->restaurant_id.'#'.$key]['net_sales']))
                    {
                        $data[$array->restaurant_id.'#'.$key]['net_sales'] = (float)($data[$array->restaurant_id.'#'.$key]['net_sales']+$array->net_sales);
                    }
                    else
                    {
                        $data[$array->restaurant_id.'#'.$key]['net_sales'] = (float) $array->net_sales;
                    }
                }   
            }
          //  dd($data);
           
            
            $breadcum = [$title=>route($model.'.daily-sales'),'Daily Sales'=>''];
            return view('admin.salesBookingToGl.index',compact('title','lists','model','breadcum','pmodule','permission','data'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }   

    }

   

    public function store(Request $request)
    {
      
    }


    public function show($id)
    {
        
    }


    public function edit($slug)
    {
    }


    public function update(Request $request, $slug)
    {
    }


    public function destroy($slug)
    {
       
    }

    
    public function postSalesToGeneralLedger() 
    {
        $saleWithGlCodeArr = ItemSalesWithGlCode::where('is_posted','0')->get();
        $paymentsArr =    Payment::where('is_posted','0')->get();
     //  echo "<pre>"; print_r($saleWithGlCodeArr); die;
        $taxArr = TaxManager::pluck('output_tax_gl_account','slug')->toArray();
        $posNumber = getCodeWithNumberSeries('POS SALES');
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
        $period_no = isset($WaAccountingPeriod->period_no) ? $WaAccountingPeriod->period_no : '';
        $series_module = WaNumerSeriesCode::where('module','POS SALES')->first();
        $order_discounts = OrdersDiscountsForGlTran::where('is_posted','0')->get();
        //managing payments into debit table

        
        foreach($paymentsArr as $paymentData) 
        {

            $entryDate = $paymentData->date;
            $gl_code_id = $paymentData->gl_account_no;
            $is_have_row_with_date_and_gl = PaymentDebit::where('date',$paymentData->date)->where('gl_code_id',$gl_code_id)->first();


                $PaymentDebit = new PaymentDebit();
                $is_have_row_with_date =  PaymentDebit::where('date',$paymentData->date)->where('gl_code_id',$gl_code_id)->first();

                if($is_have_row_with_date)
                {
                    $PaymentDebit->period = $is_have_row_with_date->period;
                    $PaymentDebit->gl_code_id = $gl_code_id;
                    $PaymentDebit->narration = $is_have_row_with_date->narration;
                    $PaymentDebit->transaction_type = $is_have_row_with_date->transaction_type;
                    $PaymentDebit->transaction_no = $is_have_row_with_date->transaction_no;
                    $PaymentDebit->gross_amount = $paymentData->amount+$is_have_row_with_date->gross_amount;
                    $PaymentDebit->date = $is_have_row_with_date->date;
                    $PaymentDebit->type = $is_have_row_with_date->type;   
                    
                }
                else
                {
                    $PaymentDebit->period = $period_no;
                    $PaymentDebit->gl_code_id = $gl_code_id;
                    $PaymentDebit->narration ='POS Sales - '.$paymentData->date;
                    $PaymentDebit->transaction_type = 'POS Sales';
                    $PaymentDebit->transaction_no = $posNumber;
                    $PaymentDebit->gross_amount = $paymentData->amount;
                    $PaymentDebit->date =$paymentData->date;
                    $PaymentDebit->type = 'ITEM';      
                }
                $PaymentDebit->save();
                 $gl_data = [
                    'grn_type_number'=>$series_module->type_number,
                    'transaction_no'=>$PaymentDebit->transaction_no,
                    'transaction_type'=>$PaymentDebit->transaction_type,
                    'period_number'=>$PaymentDebit->period,
                    'restaurant_id'=> $paymentData->restaurant_id,
                    'amount'=>$paymentData->amount,
                    'trans_date'=> $paymentData->created_at,
                    'narrative'=>$PaymentDebit->narration,
                    'account'=>$gl_code_id,
                    'grn_last_used_number'=>$series_module->last_number_used
                    
                ];
                $this->savePaymentInwaGlTrans($gl_data);
                $updatePayment = Payment::where('id',$paymentData->id)->first();
                $updatePayment->is_posted = '1';
                $updatePayment->save();


        }




        //managing credit entry
      
       $all_taxes = [];
        $all_items = [];
        foreach($saleWithGlCodeArr as $sArr) 
        {
            $isAlreadyHaveDate = PaymentCredit::where('date',$sArr->sale_date)->first();
            $is_credit_aleredy_inserted = 0;
            if($isAlreadyHaveDate) 
            {
                $posNumber = $sArr->transaction_no;
                $isHaveSameGl = PaymentCredit::where('date',$sArr->sale_date)->where('gl_code_id',$sArr->gl_code_id)->first();
                if($isHaveSameGl)
                {
                    $is_credit_aleredy_inserted = 1;
                    $row = $isHaveSameGl;
                    $row->gross_amount = $row->gross_amount+$sArr->gross_sale;
                    $row->transaction_type =  'POS Sales';
                    $row->net_sales = $row->net_sales+$sArr->net_sales;
                    $this->updatePostedSales('gross_sale',$sArr->gross_sale,$sArr->sale_date);
                }
                else
                {
                    $row = new PaymentCredit();
                    $row->gross_amount = $sArr->gross_sale;
                    $row->period = $isAlreadyHaveDate->period;
                    $row->gl_code_id =  $sArr->gl_code_id;
                    $row->transaction_type =  'POS Sales';
                    $row->narration =  $isAlreadyHaveDate->narration;
                    $row->net_sales = $sArr->net_sales;
                    $this->updatePostedSales('gross_sale',$sArr->gross_sale,$sArr->sale_date);
                }

            }
            else
            {
                $row = new PaymentCredit();
                $row->gross_amount = $sArr->gross_sale;
                $row->net_sales = $sArr->net_sales;
                $row->period = $period_no;
                $row->gl_code_id =  $sArr->gl_code_id;
                $row->narration =  'POS Sales - '.$sArr->sale_date;
                $row->transaction_type =  'POS Sales';
                $row->transaction_no =  $posNumber;
                $row->date =  $sArr->sale_date;
                $row->type = 'ITEM';  
                $this->updatePostedSales('gross_sale',$sArr->gross_sale,$sArr->sale_date);
            }
            //dd($row);
            $row->save();
            
            
            $gl_data = [
                'grn_type_number'=>$series_module->type_number,
                'transaction_no'=> $PaymentDebit->transaction_no,
                'transaction_type'=> $row->transaction_type,
                'period_number'=>$row->period,
                'restaurant_id'=> $sArr->restaurant_id,
                'trans_date'=> $sArr->created_at,
                'amount'=> -($sArr->net_sales),
                // 'amount'=> -($sArr->gross_sale),
                'account'=>getAccountDetailsFromGlCode($row->gl_code_id),
                'narrative'=>$row->narration,
                'grn_last_used_number'=>$series_module->last_number_used
            ];
            $this->savePaymentInwaGlTrans($gl_data);
           
            if($sArr->vat)
            {
                $vatGlCode = isset($taxArr['vat'])?$taxArr['vat']:null;
                if($vatGlCode)
                {
                    $isHaveVAt = PaymentCredit::where('date',$sArr->sale_date)->where('gl_code_id',$vatGlCode)->first();
                    if($isHaveVAt)
                    {
                        $vat = $isHaveVAt;
                        $isHaveVAt->gross_amount = (float)($isHaveVAt->gross_amount+$sArr->vat);
                         $isHaveVAt->net_sales = (float)($isHaveVAt->net_sales+$sArr->vat);
                        $isHaveVAt->save();
                        $this->updatePostedSales('vat',$sArr->vat,$sArr->sale_date);
                    }
                    else {
                            $vat = new PaymentCredit();
                            $vat->gross_amount = $sArr->vat;
                            $vat->net_sales = $sArr->vat;
                            $vat->period = $period_no;
                            $vat->gl_code_id =  $vatGlCode;
                            $vat->date =  $sArr->sale_date;
                            $vat->type = 'VAT'; 
                            $vat->save();
                            $this->updatePostedSales('vat',$sArr->vat,$sArr->sale_date);
                    }
                    $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_no'=> $PaymentDebit->transaction_no,
                        'transaction_type'=>$row->transaction_type,
                        'period_number'=>$row->period,
                        'restaurant_id' => $sArr->restaurant_id,
                        'trans_date'=> $sArr->created_at,
                        'amount'=> -($sArr->vat),
                        'account'=>getAccountDetailsFromGlCode($vat->gl_code_id),
                        'narrative'=>$row->narration,
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);
                    
                }

                
            }

            if($sArr->catering_levy)
            {
                $catLvyGlCode = isset($taxArr['ctl'])?$taxArr['ctl']:null;
                if($catLvyGlCode)
                {
                    $isHaveCTL = PaymentCredit::where('date',$sArr->sale_date)->where('gl_code_id',$catLvyGlCode)->first();
                    if($isHaveCTL)
                    {
                        $ctl = $isHaveCTL;
                        $isHaveCTL->gross_amount = (float)($isHaveCTL->gross_amount+$sArr->catering_levy);
                         $isHaveCTL->net_sales = (float)($isHaveCTL->net_sales+$sArr->catering_levy);
                        $isHaveCTL->save();
                        $this->updatePostedSales('catering_levy',$sArr->catering_levy,$sArr->sale_date);
                    }
                    else
                    {
                            $ctl = new PaymentCredit();
                            $ctl->gross_amount = $sArr->catering_levy;
                            $ctl->net_sales = $sArr->catering_levy;
                            $ctl->period = $period_no;
                            $ctl->gl_code_id =  $catLvyGlCode;
                            $ctl->date =  $sArr->sale_date;
                            $ctl->type = 'CTL'; 
                            $ctl->save();
                            $this->updatePostedSales('catering_levy',$sArr->catering_levy,$sArr->sale_date);
                    }
                    $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_no'=> $PaymentDebit->transaction_no,
                        'transaction_type'=>$row->transaction_type,
                        'period_number'=>$row->period,
                        'restaurant_id' => $sArr->restaurant_id,

                        'trans_date'=> $sArr->created_at,
                        'amount'=> -($sArr->catering_levy),
                        'account'=>getAccountDetailsFromGlCode($ctl->gl_code_id),
                        'narrative'=>$row->narration,
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);
                }
            }

            if($sArr->service_tax)
            {
                 $stGlCode = isset($taxArr['service-tax'])?$taxArr['service-tax']:null;
                if($stGlCode)
                {
                    $isHaveST = PaymentCredit::where('date',$sArr->sale_date)->where('gl_code_id',$stGlCode)->first();
                    if($isHaveST)
                    {
                        $st = $isHaveST;
                        $isHaveST->gross_amount = (float)($isHaveST->gross_amount+$sArr->service_tax);
                         $isHaveST->net_sales = (float)($isHaveST->net_sales+$sArr->service_tax);
                        $isHaveST->save();
                          $this->updatePostedSales('service_tax',$sArr->service_tax,$sArr->sale_date);
                    }
                    else
                    {
                            $st = new PaymentCredit();
                            $st->gross_amount = $sArr->service_tax;
                            $st->net_sales = $sArr->service_tax;
                            $st->period = $period_no;
                            $st->gl_code_id =  $stGlCode;
                            $st->date =  $sArr->sale_date;
                            $st->type = 'SERVICETAX';
                            $st->save();
                            $this->updatePostedSales('service_tax',$sArr->service_tax,$sArr->sale_date);
                    }
                    $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_no'=> $PaymentDebit->transaction_no,
                        'transaction_type'=>$row->transaction_type,
                        'period_number'=>$row->period,
                        'restaurant_id' => $sArr->restaurant_id,

                        'trans_date'=> $sArr->created_at,
                        'amount'=> -($sArr->service_tax),
                        'account'=>getAccountDetailsFromGlCode($st->gl_code_id),
                        'narrative'=>$row->narration,
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);
                }
            }
            
            $updaterow = ItemSalesWithGlCode::where('id',$sArr->id)->first();
            $updaterow->is_posted = '1';
            $updaterow->save();
        }

        foreach($order_discounts as $disData)
        {
             $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_type'=>'POS Sales',
                        'transaction_no'=>$disData->id,
                        'period_number'=>$period_no,
                        'restaurant_id' => $sArr->restaurant_id,
                        'trans_date'=> date('Y-m-d H:i:s'),
                        'amount'=> $disData->discount_amount,
                        'account'=>getAccountDetailsFromGlCode($disData->gl_code_id),
                        'narrative'=>'',
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);
             $updaterow = OrdersDiscountsForGlTran::where('id',$disData->id)->first();
            $updaterow->is_posted = '1';
            $updaterow->save();
        }
        
        Session::flash('success', 'Posted successfully');
        return redirect()->back();
    }
    

   /* public function postSalesToGeneralLedger() {
        $saleWithGlCodeArr = ItemSalesWithGlCode::where('is_posted','0')->get();
        $saleWithGlCodeArrByDate = $paymentsArrDate = [];
        foreach($saleWithGlCodeArr as $key => $row) {
            if(isset($saleWithGlCodeArrByDate[$row->sale_date.'__'.$row->getGlDetail->account_code]['sum_of_gross_sale'])){
                $saleWithGlCodeArrByDate[$row->sale_date.'__'.$row->getGlDetail->account_code]['sum_of_gross_sale'] += $row->gross_sale;
            } else{
                $saleWithGlCodeArrByDate[$row->sale_date.'__'.$row->getGlDetail->account_code]['sum_of_gross_sale'] = $row->gross_sale;
            }
            $saleWithGlCodeArrByDate[$row->sale_date.'__'.$row->getGlDetail->account_code]['items'][] = $row;
            
        }
        
        
        $paymentsArr = Payment::where('is_posted','0')->get();
        foreach($paymentsArr as $key => $row){
            if(isset($paymentsArrDate[$row->date.'__'.$row->gl_account_no]['sum_of_amount'])) {
                $paymentsArrDate[$row->date.'__'.$row->gl_account_no]['sum_of_amount'] += $row->amount;
            } else {
                $paymentsArrDate[$row->date.'__'.$row->gl_account_no]['sum_of_amount'] = $row->amount;
            }
            $paymentsArrDate[$row->date.'__'.$row->gl_account_no]['items'][] = $row;
            
        }
        

        $taxArr = TaxManager::pluck('output_tax_gl_account','slug')->toArray();
        $posNumber = getCodeWithNumberSeries('POS SALES');
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
        $period_no = isset($WaAccountingPeriod->period_no) ? $WaAccountingPeriod->period_no : '';
        $series_module = WaNumerSeriesCode::where('module','POS SALES')->first();

        foreach($saleWithGlCodeArrByDate as $sale_date_and_code => $saleWithGlCodeArrByAccountCode) {
            $sArr_first = $saleWithGlCodeArrByAccountCode['items'][0];
            list($sale_date, $code) = explode('__', $sale_date_and_code);
            if(isset($paymentsArrDate[$sale_date_and_code])){
                $payment_filter_arr = $paymentsArrDate[$sale_date_and_code];
                if($saleWithGlCodeArrByAccountCode['sum_of_gross_sale'] == $payment_filter_arr['sum_of_amount']) {
                    //Debit entry
                    $PaymentDebit = new PaymentDebit();
                    $PaymentDebit->period = $period_no;
                    $PaymentDebit->gl_code_id = $code;
                    $PaymentDebit->narration ='POS Sales - '. $sale_date;
                    $PaymentDebit->transaction_type = 'POS Sales';
                    $PaymentDebit->transaction_no = $posNumber;
                    $PaymentDebit->gross_amount = $payment_filter_arr['sum_of_amount'];
                    $PaymentDebit->date = $sale_date;
                    $PaymentDebit->type = 'ITEM';
                    $PaymentDebit->save();

                    $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_no'=>$PaymentDebit->transaction_no,
                        'transaction_type'=>$PaymentDebit->transaction_type,
                        'period_number'=>$PaymentDebit->period,
                        'amount'=>$payment_filter_arr['sum_of_amount'],
                        'trans_date'=> $sale_date,
                        'narrative'=>$PaymentDebit->narration,
                        'account'=>$code,
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);

                    $taxes = [];
                    $taxes_arr = ['vat'=>0, 'cat'=>0, 'st'=>0];
                    $item_sales_with_gl_codes_ids_arr = [];
                    $posted_sales_ids_arr = [];
                    foreach($payment_filter_arr['items'] as $p_key => $p_row) {
                        $posted_sales_ids_arr[] = $p_row->id;
                    }
                    
                    foreach($saleWithGlCodeArrByAccountCode['items'] as $s_key => $sArr) {
                        $item_sales_with_gl_codes_ids_arr[] = $sArr->id;
                        if ($sArr->vat) {
                            $taxes[] = $sArr->vat;
                            $taxes_arr['vat'] += $sArr->vat;
                        }
            
                        if ($sArr->catering_levy) {
                            $taxes[] = $sArr->catering_levy;
                            $taxes_arr['cat'] += $sArr->catering_levy;
                        }
                        
            
                        if ($sArr->service_tax) {
                            $taxes[] = $sArr->service_tax;
                            $taxes_arr['st'] += $sArr->service_tax;
                        }
                    }
                    $taxes_sum = array_sum($taxes);
                    
                    $net_sales = $saleWithGlCodeArrByAccountCode['sum_of_gross_sale'] - $taxes_sum;
                    //Credit entry
                    $PaymentCredit = new PaymentCredit();
                    $PaymentCredit->gross_amount = $saleWithGlCodeArrByAccountCode['sum_of_gross_sale'];
                    $PaymentCredit->net_sales = $net_sales;
                    $PaymentCredit->period = $period_no;
                    $PaymentCredit->gl_code_id =  $sArr_first->gl_code_id;
                    $PaymentCredit->narration =  'POS Sales - '.$sale_date;
                    $PaymentCredit->transaction_type =  'POS Sales';
                    $PaymentCredit->transaction_no =  $posNumber;
                    $PaymentCredit->date =  $sale_date;
                    $PaymentCredit->type = 'ITEM';  
                    $PaymentCredit->save();
                    
                    $gl_data = [
                        'grn_type_number'=>$series_module->type_number,
                        'transaction_no'=>$PaymentCredit->transaction_no,
                        'transaction_type'=>$PaymentCredit->transaction_type,
                        'period_number'=>$PaymentCredit->period,
                        'trans_date'=> $PaymentCredit->created_at,
                        'amount'=> -($net_sales),
                        'account'=>$code,
                        'narrative'=>$PaymentCredit->narration,
                        'grn_last_used_number'=>$series_module->last_number_used
                    ];
                    $this->savePaymentInwaGlTrans($gl_data);


                    $vatGlCode = isset($taxArr['vat'])?$taxArr['vat']:null;
                    $catLvyGlCode = isset($taxArr['ctl'])?$taxArr['ctl']:null;
                    $stGlCode = isset($taxArr['service-tax'])?$taxArr['service-tax']:null;
                    
                    if(!empty($taxes_arr['vat']) && $vatGlCode) {
                        $vat = new PaymentCredit();
                        $vat->gross_amount = $taxes_arr['vat'];
                        $vat->net_sales = $taxes_arr['vat'];
                        $vat->period = $period_no;
                        $vat->gl_code_id =  $vatGlCode;
                        $vat->date =  $sale_date;
                        $vat->type = 'VAT'; 
                        $vat->save();
                        $gl_data = [
                            'grn_type_number'=>$series_module->type_number,
                            'transaction_no'=>$PaymentCredit->transaction_no,
                            'transaction_type'=>$PaymentCredit->transaction_type,
                            'period_number'=>$PaymentCredit->period,
                            'trans_date'=> $PaymentCredit->created_at,
                            'amount'=> -($taxes_arr['vat']),
                            'account'=>getAccountDetailsFromGlCode($vat->gl_code_id),
                            'narrative'=>$PaymentCredit->narration,
                            'grn_last_used_number'=>$series_module->last_number_used
                        ];
                        $this->savePaymentInwaGlTrans($gl_data);

                    }

                    if(!empty($taxes_arr['cat']) && $catLvyGlCode){
                        $ctl = new PaymentCredit();
                        $ctl->gross_amount = $taxes_arr['cat'];
                        $ctl->net_sales = $taxes_arr['cat'];
                        $ctl->period = $period_no;
                        $ctl->gl_code_id =  $catLvyGlCode;
                        $ctl->date =  $sale_date;
                        $ctl->type = 'CTL'; 
                        $ctl->save();
                        $gl_data = [
                            'grn_type_number'=>$series_module->type_number,
                            'transaction_no'=>$PaymentCredit->transaction_no,
                            'transaction_type'=>$PaymentCredit->transaction_type,
                            'period_number'=>$PaymentCredit->period,
                            'trans_date'=> $PaymentCredit->created_at,
                            'amount'=> -($taxes_arr['cat']),
                            'account'=>getAccountDetailsFromGlCode($ctl->gl_code_id),
                            'narrative'=>$PaymentCredit->narration,
                            'grn_last_used_number'=>$series_module->last_number_used
                        ];
                        $this->savePaymentInwaGlTrans($gl_data);
                    }

                    if(!empty($taxes_arr['st']) && $stGlCode){
                        $st = new PaymentCredit();
                        $st->gross_amount = $taxes_arr['st'];
                        $st->net_sales = $taxes_arr['st'];
                        $st->period = $period_no;
                        $st->gl_code_id = $stGlCode;
                        $st->date =  $sale_date;
                        $st->type = 'CTL'; 
                        $st->save();
                        $gl_data = [
                            'grn_type_number'=>$series_module->type_number,
                            'transaction_no'=>$PaymentCredit->transaction_no,
                            'transaction_type'=>$PaymentCredit->transaction_type,
                            'period_number'=>$PaymentCredit->period,
                            'trans_date'=> $PaymentCredit->created_at,
                            'amount'=> -($taxes_arr['st']),
                            'account'=>getAccountDetailsFromGlCode($st->gl_code_id),
                            'narrative'=>$PaymentCredit->narration,
                            'grn_last_used_number'=>$series_module->last_number_used
                        ];
                        $this->savePaymentInwaGlTrans($gl_data);
                    }
                    $postedRow = new PostedSale();
                    $postedRow->sales_date =  $sale_date;
                    $postedRow->gross_sale =  $payment_filter_arr['sum_of_amount'];
                    $postedRow->vat =  $taxes_arr['vat'];
                    $postedRow->catering_levy =  $taxes_arr['cat'];
                    $postedRow->service_tax =  $taxes_arr['st'];
                    $postedRow->net_sales =  $net_sales;
                    $postedRow->save();
                    

                    ItemSalesWithGlCode::whereIn('id', $item_sales_with_gl_codes_ids_arr)->update(['is_posted' => '1']);
                    Payment::whereIn('id', $posted_sales_ids_arr)->update(['is_posted' => '1']);
                










                }
            } 
            
            

        }






        //managing payments into debit table
        foreach($paymentsArr as $paymentData) {
            $entryDate = $paymentData->date;
            $is_have_row_with_date = PaymentDebit::where('date',$paymentData->date)->first();

            $gl_code_id = $paymentData->gl_account_no;
            if($is_have_row_with_date)
            {
                $entryDate = $is_have_row_with_date->date;

            }

            $is_have_row_with_date_and_gl = PaymentDebit::where('date',$paymentData->date)->where('gl_code_id',$gl_code_id)->first();


            if($is_have_row_with_date_and_gl)
            {
                $is_have_row_with_date_and_gl->gross_amount =  (float)($is_have_row_with_date_and_gl->gross_amount+$paymentData->amount);
                $is_have_row_with_date_and_gl->save();
            }
            else
            {
                $PaymentDebit = new PaymentDebit();
                if($is_have_row_with_date)
                {
                    $PaymentDebit->period = $is_have_row_with_date->period;
                    $PaymentDebit->gl_code_id = $gl_code_id;
                    $PaymentDebit->narration = $is_have_row_with_date->narration;
                    $PaymentDebit->transaction_type = $is_have_row_with_date->transaction_type;
                    $PaymentDebit->transaction_no = $is_have_row_with_date->transaction_no;
                    $PaymentDebit->gross_amount = $paymentData->amount;
                    $PaymentDebit->date = $is_have_row_with_date->date;
                    $PaymentDebit->type = $is_have_row_with_date->type;   
                    
                }
                else
                {
                    $PaymentDebit->period = $period_no;
                    $PaymentDebit->gl_code_id = $gl_code_id;
                    $PaymentDebit->narration ='POS Sales - '.$paymentData->date;
                    $PaymentDebit->transaction_type = 'POS Sales';
                    $PaymentDebit->transaction_no = $posNumber;
                    $PaymentDebit->gross_amount = $paymentData->amount;
                    $PaymentDebit->date =$paymentData->date;
                    $PaymentDebit->type = 'ITEM';      
                }
                $PaymentDebit->save();
                
                $gl_data = [
                    'grn_type_number'=>$series_module->type_number,
                    'transaction_no'=>$PaymentDebit->transaction_no,
                    'transaction_type'=>$PaymentDebit->transaction_type,
                    'period_number'=>$PaymentDebit->period,
                    'amount'=>$paymentData->amount,
                    'trans_date'=> $paymentData->created_at,
                    'narrative'=>$PaymentDebit->narration,
                    'account'=>$gl_code_id,
                    'grn_last_used_number'=>$series_module->last_number_used
                    
                ];
                $this->savePaymentInwaGlTrans($gl_data);
            }

            $updatePayment = Payment::where('id',$paymentData->id)->first();
            $updatePayment->is_posted = '1';
            $updatePayment->save();
        }

        Session::flash('success', 'Posted successfully');
        return redirect()->back();
    }*/
    
    protected function savePaymentInwaGlTrans($data) 
    {
        $entity = WaGlTran::whereDate('trans_date',date('Y-m-d',strtotime($data['trans_date'])))
                ->where('account',$data['account'])->where('restaurant_id', $data['restaurant_id'])
                ->first();
        if($entity){
            $entity->amount = $entity->amount + $data['amount'];
            $entity->save();
        }
        else{
            $entity = new WaGlTran();
            $entity->grn_type_number = $data['grn_type_number'];
            $entity->transaction_type = $data['transaction_type'];
            $entity->transaction_no = $data['transaction_no'];
            $entity->trans_date = $data['trans_date'];
            $entity->period_number = $data['period_number'];
            $entity->account = $data['account'];
            $entity->restaurant_id = @$data['restaurant_id'];//getLoggeduserProfile()->restaurant_id;
            $entity->amount = $data['amount'];
            $entity->narrative = $data['narrative'];
            $entity->grn_last_used_number = $data['grn_last_used_number'];
            $entity->save();
        }
    }


    public function dailyPayments(){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin') {
           /* $data = Payment::select('date', 'payment_method', 'gl_account_no', DB::raw("COUNT(id) as no_of_entries"), DB::raw("SUM(amount) as amount_sum"))
                ->groupBy('date', 'payment_method')
                ->get();*/

            $arr =    Payment::where('is_posted','0')->orderBy('date','desc')->get();

            $data = [];
            foreach($arr as $array)
            {
                $data[$array->date.'#'.$array->gl_account_no]['date']  = $array->date;
                $data[$array->date.'#'.$array->gl_account_no]['payment_method']  = $array->payment_method;
                $data[$array->date . '#' . $array->gl_account_no]['restaurant_name'] = ($array->restaurant_id != "") ? getRestaurantNameById($array->restaurant_id) : '--';

                $data[$array->date.'#'.$array->gl_account_no]['gl_account_no']  = $array->gl_account_no;


                if(isset($data[$array->date.'#'.$array->gl_account_no]['no_of_entry']))
                {
                    $data[$array->date.'#'.$array->gl_account_no]['no_of_entry'] = $data[$array->date.'#'.$array->gl_account_no]['no_of_entry']+1;
                }
                else
                {
                    $data[$array->date.'#'.$array->gl_account_no]['no_of_entry']  = 1;
                }

                if(isset($data[$array->date.'#'.$array->gl_account_no]['amount']))
                {
                     $data[$array->date.'#'.$array->gl_account_no]['amount']  = (float)($array->amount+$data[$array->date.'#'.$array->gl_account_no]['amount']);
                }
                else
                {
                    $data[$array->date.'#'.$array->gl_account_no]['amount']  = (float)$array->amount;
                }
                  

                  
            }


            $breadcum = [
                $title=>route($model.'.daily-payment'), ' Daily Payment Methods '=>''
            ];
            return view('admin.salesBookingToGl.daily_payments',compact('title','data','model','breadcum','pmodule','permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function postedSales()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin') {
            $data = PostedSale::orderBy('sales_date','DESC')
                ->get();
            $breadcum = [
                $title=>route($model.'.posted-sales'), 'Posted Sales'=>''
            ];
            //dd($data);
            return view('admin.salesBookingToGl.posted_sales',compact('title','data','model','breadcum','pmodule','permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function updatePostedSales($field_name,$amount,$date)
    {
        $postedRow = PostedSale::where('sales_date',$date)->first();

        if($postedRow)
        {
            $postedRow->$field_name =  (float)($postedRow->$field_name+$amount);
        }
        else
        {
            $postedRow = new PostedSale();
            $postedRow->$field_name =  (float)$amount;
            $postedRow->sales_date =  $date;

        }
        $postedRow->save();
        $updateRow = PostedSale::where('sales_date',$date)->first();


        $gross_sale = $updateRow->gross_sale;
        $tax =  ($updateRow->vat+ $updateRow->catering_levy+ $updateRow->service_tax);
        $updateRow->net_sales = (float)($gross_sale-$tax);
        $updateRow->save();


    }

    
}
