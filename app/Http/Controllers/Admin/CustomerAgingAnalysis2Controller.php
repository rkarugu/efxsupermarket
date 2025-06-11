<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\User;
use App\Model\WaGlTran;

use App\Model\WaCustomer;
use App\Model\WaDebtorTran;

use Session;
use Excel;
use PDF;

class CustomerAgingAnalysis2Controller extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'customer-aging-analysis-2';
        $this->title = 'Customer Aging Analysis';
        $this->pmodule = 'customer-aging-analysis-2';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    } 

   
    public function index(Request $request)
    {
        //$this->managetimeForallCron();
       // dd('here');
       $title = $this->title;
       $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
       
        if(isset($permission['sales-and-receivables-reports___customer-aging-analysis-2']) || $permission == 'superadmin')
        {

            if($request->print){
                $lists = DB::table('wa_customers')->select([
                    '*',
                    DB::RAW("(select (
                        CASE WHEN sum(wa_debtor_trans.amount) > 0
                            THEN sum(wa_debtor_trans.amount)
                        WHEN sum(wa_debtor_trans.amount) < 0
                            THEN sum(wa_debtor_trans.amount)
                        ELSE
                            0
                        END
                    ) as aggregate from `wa_debtor_trans` where wa_debtor_trans.wa_customer_id = wa_customers.id AND DATE(trans_date) <= '".$request->date."' ) as total_amount_f")
                ])->orderBy('wa_customers.customer_name','ASC');
                if($request->type=='zero'){
                    $lists = $lists->having('total_amount_f',0);
                }
                if($request->type=='more'){
                    $lists = $lists->having('total_amount_f','!=',0);
                }
                $lists = $lists->orderBy('customer_name','asc')->get();
                $pdf = \PDF::loadView('admin.customer_aging_analysis_2.pdf', compact('lists'));
                $report_name = 'customer_aging_analysis_2_'.date('Y_m_d_H_i_A');
                return $pdf->download($report_name.'.pdf');
            }
	        $detail = [];
	        $customer =  WaCustomer::get()->toArray();  
	        $restroList = $this->getRestaurantList();
	
	
	        $breadcum = ['Accounts Receivables' => '', 'Sales & Receivables'=>'', 'Report' => '', $title => ''];
	        return view('admin.customer_aging_analysis_2.sheet',compact('title', 'customer', 'restroList','lists','model','breadcum','detail'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
       
          
    }

    // public function getTrailBalanceByGroup($array)
    // {
    //     $final_array = [];
    //     foreach($array as $arr)
    //     {
    //         if(!isset($final_array[$arr['account_group']][$arr['gl_account']]))
    //         {
    //             $final_array[$arr['account_group']][$arr['gl_account']]['gl_account'] = $arr['gl_account'];
    //             $final_array[$arr['account_group']][$arr['gl_account']]['gl_account_name'] = $arr['gl_account_name'];
    //             $final_array[$arr['account_group']][$arr['gl_account']]['openingBalanceAmount'] = $arr['openingBalanceAmount'];
    //              $final_array[$arr['account_group']][$arr['gl_account']]['periodDebit'] = $arr['periodDebit'];
    //               $final_array[$arr['account_group']][$arr['gl_account']]['periodCredit'] = $arr['periodCredit'];
    //                $final_array[$arr['account_group']][$arr['gl_account']]['periodBalance'] = $arr['periodBalance'];
    //                 $final_array[$arr['account_group']][$arr['gl_account']]['closingBalance'] = $arr['closingBalance'];
    //                  $final_array[$arr['account_group']][$arr['gl_account']]['account_group'] = $arr['account_group'];
    //         }
           
    //     }
    //     return $final_array;
    // }



    

  

 
 

    // public function exportdata($filetype,$mixed_array,$request)
    // {
    //     $export_array = [];

    //      $COMPANY_NAME = getAllSettings()['COMPANY_NAME'];
    //     $export_array[] = [$COMPANY_NAME];
    //     $file_name = 'test';
    //     $export_array[] = array('Trial Balance');//heading;
    //     $date_arr = array('','','','Printed On:'.date('d/m/Y h:i A'));
    //     if ($request->has('start-date'))
    //     {
    //        $date_arr[0] = 'Period From : '.date('d/m/Y',strtotime($request->input('start-date')));  
    //     }
    //     if ($request->has('end-date'))
    //     {
    //        $date_arr[0] = $date_arr[0] != ''?$date_arr[0].'  - To : '.date('d/m/Y',strtotime($request->input('end-date'))):' To :'.date('d/m/Y',strtotime($request->input('end-date')));  
    //     }

    //     $export_array[] = $date_arr;
    //     $export_array[] = [];
     
    //     $export_array[] = array('Account Code','Account Name','Period Debits','Period Credits');



        
       
    //     $file_name = 'trial_balance_report';
    //     $counter = 1;
    //     $openingBalanceAmount = [];
    //     $periodDebit = [];
    //     $periodCredit = [];
    //     $periodBalance = [];
    //     $closingBalance = [];
    
    //     foreach($mixed_array as $account_name=>$itemArray)
    //     {
    //         $subopeningBalanceAmount = [];
    //         $subperiodDebit = [];
    //         $subperiodCredit = [];
    //         $subperiodBalance = [];
    //         $subclosingBalance = [];
    //        // $export_array[] =[$account_name]; 
    //         foreach($itemArray as $itemData)
    //         {
               
    //             $export_array[] = [
    //                         $itemData['gl_account'],
    //                         $itemData['gl_account_name'],

    //                         // manageAmountFormat($itemData['openingBalanceAmount']),
    //                         (manageAmountFormat(abs($itemData['periodDebit']))=="0.00") ? '-': manageAmountFormat(abs($itemData['periodDebit'])),
    //                         (manageAmountFormat(abs($itemData['periodCredit']))=="0.00") ? '-': manageAmountFormat(abs($itemData['periodCredit'])),
    //                         // manageAmountFormat($itemData['periodBalance']),
    //                         // manageAmountFormat($itemData['closingBalance'])
    //                     ]; 
                       
    //             $openingBalanceAmount[]= $itemData['openingBalanceAmount'];
    //             $periodDebit[] = $itemData['periodDebit'];
    //             $periodCredit[] =$itemData['periodCredit'];
    //             $periodBalance[] = $itemData['periodBalance'];
    //             $closingBalance[]= $itemData['closingBalance'];
    //             $subopeningBalanceAmount[]= $itemData['openingBalanceAmount'];
    //             $subperiodDebit[] = $itemData['periodDebit'];
    //             $subperiodCredit[] =$itemData['periodCredit'];
    //             $subperiodBalance[] = $itemData['periodBalance'];
    //             $subclosingBalance[]= $itemData['closingBalance'];
    //             $counter++; 
    //         }

    //         // $export_array[] = [
    //         //                 '',
    //         //                 'Sub Total',
    //         //                 manageAmountFormat(array_sum($subopeningBalanceAmount)),
    //         //                 manageAmountFormat(array_sum($subperiodDebit)),
    //         //                 manageAmountFormat(array_sum($subperiodCredit)),
    //         //                 manageAmountFormat(array_sum($subperiodBalance)),
    //         //                 manageAmountFormat(array_sum($subclosingBalance))
    //         //             ]; 



         
    //     } 
    //      $export_array[] = array();
    //      $export_array[] = [
    //                         '',
    //                         'Total',
    //                         // manageAmountFormat(array_sum($openingBalanceAmount)),
    //                         manageAmountFormat(abs(array_sum($periodDebit))),
    //                         manageAmountFormat(abs(array_sum($periodCredit))),
    //                         // manageAmountFormat(array_sum($periodBalance)),
    //                         // manageAmountFormat(array_sum($closingBalance))
    //                     ]; 

       
      
    //     $this->downloadExcelFile($export_array,$filetype,$file_name);

    // }

    // public function downloadExcelFile($data,$type,$file_name)
    // {
      
    //     // refrence url http://www.maatwebsite.nl/laravel-excel/docs/blade
    //     //http://www.easylaravelbook.com/blog/2016/04/19/exporting-laravel-data-to-an-excel-spreadsheet/
    //     return Excel::create($file_name, function($excel) use ($data) {
    //             $from = "A1"; // or any value
    //             $to = "G5"; // or any value
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {

              
           



    //             $sheet->fromArray($data);
    //         })
    //        // ->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true )

    //         ;
    //     })->download($type);



    // }


    // public function downloadPDF($filetype,$mixed_array,$request)
    // {
    //     $heading =   'Trial Balance';//heading;
    //     $printed_time = 'Printed On:'.date('d/m/Y h:i A');
       

    //     $period_from = '';
    //     $period_to = '';
       

       
           
    //     if ($request->has('start-date'))
    //     {
    //        $period_from = 'Period From : '.date('d/m/Y',strtotime($request->input('start-date')));  
           
          
    //     }
    //     if ($request->has('end-date'))
    //     {
    //         $period_to = '  - To : '.date('d/m/Y',strtotime($request->input('end-date')));
    //     }

    //     $COMPANY_NAME = getAllSettings()['COMPANY_NAME'];

    //   $pdf = PDF::loadView('admin.trailbalance.reportinpdf', compact('filetype','mixed_array','request','heading','period_from','period_to','printed_time','COMPANY_NAME'));
    //   return $pdf->download('trial_balance.pdf');

      
    // }

 
    public function vatReport(Request $request)
    {     
        $title = 'Vat Report';
        $model = 'total-vat-report';
        $pmodule = 'vat-report';
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission['sales-and-receivables-reports___vat-report']) && $permission != 'superadmin'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $allTrans = [];
         if($request->manage){
             $pos = " AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN '".$request->from."' AND '".$request->to."')";
             $sales = " AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN '".$request->from."' AND '".$request->to."')";
             $getUpUsers = \App\Model\User::where(['upload_data'=>1])->pluck('id')->toArray();
             $ids = implode(',',$getUpUsers);
            if($request->type == 'true'){
                $pos .= " AND exists (SELECT * from wa_pos_cash_sales 
                where wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id 
                AND wa_pos_cash_sales.user_id IN (". $ids ."))";
                $sales .= " AND exists (SELECT * from wa_inventory_location_transfers where wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id AND wa_inventory_location_transfers.user_id IN (". $ids ."))";
            }
            if($request->type == 'false'){
                $pos .= " AND exists (SELECT * from wa_pos_cash_sales 
                where wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id 
                AND wa_pos_cash_sales.user_id NOT IN (". $ids ."))";
                $sales .= " AND exists (SELECT * from wa_inventory_location_transfers where wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id AND wa_inventory_location_transfers.user_id NOT IN (". $ids ."))";
            }
            $allTrans = \App\Model\TaxManager::select([
                '*',
                DB::RAW('(SELECT SUM(selling_price * qty) FROM `wa_pos_cash_sales_items` 
                WHERE wa_pos_cash_sales_items.tax_manager_id = tax_managers.id '.$pos.') as pos_total'),
                DB::RAW('(SELECT SUM(
                    (selling_price * qty) - (((selling_price * qty)*100 ) / (vat_percentage+100))
                    ) FROM `wa_pos_cash_sales_items` WHERE wa_pos_cash_sales_items.tax_manager_id = tax_managers.id '.$pos.') as pos_vat_total'),

                DB::RAW('(SELECT SUM(selling_price * quantity) FROM 
                    `wa_inventory_location_transfer_items` WHERE wa_inventory_location_transfer_items.tax_manager_id = tax_managers.id '.$sales.') 
                    as sales_total'),
                DB::RAW('(SELECT SUM(
                    (selling_price * quantity) - (((selling_price * quantity)*100 ) / (vat_rate+100))
                ) FROM 
                `wa_inventory_location_transfer_items` WHERE wa_inventory_location_transfer_items.tax_manager_id = tax_managers.id '.$sales.') 
                as sales_vat_total')
            ])->get();  
            if($request->manage == 'pdf' || $request->manage == 'print'){
                $allTrans = $allTrans->map(function($item){
                    $item->saleso = ($item->pos_vat_total+$item->sales_vat_total);
                    $item->toto = ($item->pos_total+$item->sales_total);
                    $item->posto = ($item->toto)-($item->saleso);
                    return $item;
                });
                if($request->manage == 'pdf'){
                    $pdf = \PDF::loadView('admin.customer_aging_analysis_2.vatReportpdf',compact('allTrans'))->setPaper('a4','landscape');
                    return $pdf->download('vat-Report-'.$request->from.'-'.$request->to.'-'.time().'.pdf');
                }
                return view('admin.customer_aging_analysis_2.vatReportpdf',compact('allTrans'));
            } 
            $allTrans = $allTrans->map(function($item){
                $item->salesot = ($item->pos_vat_total+$item->sales_vat_total);
                $item->totot = ($item->pos_total+$item->sales_total);
                $item->postot = ($item->totot)-($item->salesot);
                $item->saleso = manageAmountFormat($item->salesot);
                $item->toto = manageAmountFormat($item->totot);
                $item->posto = manageAmountFormat(($item->totot)-($item->salesot));
                return $item;
            });
            return response()->json(['data'=>$allTrans,
            'posto'=>manageAmountFormat($allTrans->sum('postot')),
            'saleso'=>manageAmountFormat($allTrans->sum('salesot')),
            'toto'=>manageAmountFormat($allTrans->sum('totot'))]);
        } 
        return view('admin.customer_aging_analysis_2.vatReport',compact('title','model','pmodule','permission','allTrans'));
    }
    // INSERT INTO `tax_managers` (`id`, `title`, `slug`, `tax_value`, `tax_format`, `input_tax_gl_account`, `output_tax_gl_account`, `status`, `created_at`, `updated_at`) VALUES ('0', 'VAT 8%', 'ctl', '8.00', 'PERCENTAGE', '8', '8', '1', '2020-03-29 11:59:46', '2021-05-15 11:59:02');
    
}
