<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaGlTran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DetailedTrialBalanceController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'trial-balances';
        $this->title = 'Trial Balance';
        $this->pmodule = 'trial-balances';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }

    public function index(Request $request)
    {
        $title = "Detailed Trial Balance";
        $model = 'trial-balances';
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();

        $start_date = $request->input('start-date') ? $request->input('start-date') : Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end_date = $request->input('end-date') ? $request->input('end-date') : Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        $detail = [];
        $openingBalanceDate = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));

        $all_item = WaGlTran::with(['getAccountDetail.getRelatedGroup'])->select('account');
        if ($request->has('manage-request') && $request->input('manage-request') == 'new-filter' && $request->restaurant) {
            $all_item->where('tb_reporting_branch', $request->restaurant);
        }

        $all_item = $all_item->whereDate('created_at','>=',$start_date);
        $all_item = $all_item->whereDate('created_at','<=',$end_date);
        $all_item = $all_item->whereHas('getAccountDetail')->get();

        $wagl = WaGlTran::whereIn('account',array_unique($all_item->pluck('account')->toArray()))->where('created_at','<=',$openingBalanceDate.' 23:59:59')->get();
        $wagl2= WaGlTran::whereIn('account',array_unique($all_item->pluck('account')->toArray()))->whereBetween('created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59'])->get();

        $gl_sum = [];
        $gl_sum_2 = [];

        foreach ($wagl as $key => $value) {
            $gl_sum[$value->account][] = $value->amount;
        }

        foreach ($wagl2 as $key => $value) {
            $gl_sum_2[$value->account]['greater'][] = $value->amount >= 0 ? $value->amount : 0;
            $gl_sum_2[$value->account]['less'][] = $value->amount < 0 ? $value->amount: 0;
        }

        $e = 0;
        $ew = 0;

        foreach($all_item as $item)
        {
            if(!isset($detail[$item->account]))
            {
                $detail[$item->account]['account_group'] = $item->getAccountDetail->getRelatedGroup->group_name;
                $detail[$item->account]['account_group_id'] = $item->getAccountDetail->getRelatedGroup->id;
                $detail[$item->account]['gl_account'] = $item->account;
                $detail[$item->account]['gl_account_name'] = $item->getAccountDetail->account_name;
                $detail[$item->account]['openingBalanceAmount'] =  array_sum(@$gl_sum[$item->account] ?? [0]);
                $detail[$item->account]['periodCredit'] =  0;
                $detail[$item->account]['periodDebit'] =  0;
                //Problem with those sum, needs to be optimized
                $debit = array_sum(@$gl_sum_2[$item->account]['greater'] ?? [0]);
                $credit = array_sum(@$gl_sum_2[$item->account]['less'] ?? [0]);

                $combined = $debit + $credit;

                if($combined > 0){
                    $detail[$item->account]['periodDebit'] =  $combined;
                }elseif($combined < 0)
                {
                    $detail[$item->account]['periodCredit'] =  $combined;
                }

                $detail[$item->account]['periodBalance'] = $detail[$item->account]['periodDebit'] - abs($detail[$item->account]['periodCredit']);
                $detail[$item->account]['closingBalance'] =  $detail[$item->account]['openingBalanceAmount'] -  $detail[$item->account]['periodBalance'];
                // }
                if($combined == 0){
                    unset($detail[$item->account]);
                }

                $ew++;
            }
            $e++;
        }

        sort($detail);

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf' ))
        {
            if($request->input('manage-request') == 'xls')
            {
                // dd('xl');
                $detail = $this->detailedgetTrailBalanceByGroup($detail);
                $this->detailedexportdata('xls',$detail,$request);
            }
            if($request->input('manage-request') == 'pdf')
            {
                $detail = $this->detailedgetTrailBalanceByGroup($detail);
                return $this->detaileddownloadPDF('pdf',$detail,$request);
                // dd('pdf');
            }
        }

        $restroList = $this->getRestaurantList();

        $detail = $this->detailedgetTrailBalanceByGroup($detail);
        $breadcum = [$title=>'','Sheet'=>''];
        return view('admin.trailbalance.detailed',compact('title', 'restroList','model','breadcum','detail'));
    }

    public function detailedgetTrailBalanceByGroup($array)
    {
        $final_array = [];
        foreach($array as $arr)
        {
            if(!isset($final_array[$arr['account_group']][$arr['gl_account']]))
            {
                $final_array[$arr['account_group']][$arr['gl_account']]['gl_account'] = $arr['gl_account'];
                $final_array[$arr['account_group']][$arr['gl_account']]['gl_account_name'] = $arr['gl_account_name'];
                $final_array[$arr['account_group']][$arr['gl_account']]['openingBalanceAmount'] = $arr['openingBalanceAmount'];
                $final_array[$arr['account_group']][$arr['gl_account']]['periodDebit'] = $arr['periodDebit'];
                $final_array[$arr['account_group']][$arr['gl_account']]['periodCredit'] = $arr['periodCredit'];
                $final_array[$arr['account_group']][$arr['gl_account']]['periodBalance'] = $arr['periodBalance'];
                $final_array[$arr['account_group']][$arr['gl_account']]['closingBalance'] = $arr['closingBalance'];
                $final_array[$arr['account_group']][$arr['gl_account']]['account_group'] = $arr['account_group'];
            }

        }
        return $final_array;
    }

    public function detaileddownloadPDF($filetype,$mixed_array,$request)
    {
        $heading =   'Trial Balance';//heading;
        $printed_time = 'Printed On:'.date('d/m/Y h:i A');
        $period_from = '';
        $period_to = '';
        if ($request->has('start-date'))
        {
            $period_from = 'Period From : '.date('d/m/Y',strtotime($request->input('start-date')));
        }
        if ($request->has('end-date'))
        {
            $period_to = '  - To : '.date('d/m/Y',strtotime($request->input('end-date')));
        }
        $COMPANY_NAME = getAllSettings()['COMPANY_NAME'];
        $pdf = PDF::loadView('admin.trailbalance.detailedreportinpdf', compact('filetype','mixed_array','request','heading','period_from','period_to','printed_time','COMPANY_NAME'));
        return $pdf->download('trial_balance.pdf');
    }

    public function detailedexportdata($filetype,$mixed_array,$request)
    {
        $export_array = [];

        $COMPANY_NAME = getAllSettings()['COMPANY_NAME'];
        $export_array[] = [$COMPANY_NAME];
        $file_name = 'test';
        $export_array[] = array('Trial Balance');//heading;
        $date_arr = array('','','','Printed On:'.date('d/m/Y h:i A'));
        if ($request->has('start-date'))
        {
            $date_arr[0] = 'Period From : '.date('d/m/Y',strtotime($request->input('start-date')));
        }
        if ($request->has('end-date'))
        {
            $date_arr[0] = $date_arr[0] != ''?$date_arr[0].'  - To : '.date('d/m/Y',strtotime($request->input('end-date'))):' To :'.date('d/m/Y',strtotime($request->input('end-date')));
        }

        $export_array[] = $date_arr;
        $export_array[] = [];

        $export_array[] = array('Account Code','Account Name','Opening Balance','Period Debits','Period Credits','Period Balance','Closing Balance');





        $file_name = 'trial_balance_report';
        $counter = 1;
        $openingBalanceAmount = [];
        $periodDebit = [];
        $periodCredit = [];
        $periodBalance = [];
        $closingBalance = [];

        foreach($mixed_array as $account_name=>$itemArray)
        {
            $export_array[] = [
                $account_name,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL
            ];
            $subopeningBalanceAmount = [];
            $subperiodDebit = [];
            $subperiodCredit = [];
            $subperiodBalance = [];
            $subclosingBalance = [];
            // $export_array[] =[$account_name];
            foreach($itemArray as $itemData)
            {
                $export_array[] = [
                    $itemData['gl_account'],
                    $itemData['gl_account_name'],
                    (manageAmountFormat(abs($itemData['openingBalanceAmount']))=="0.00") ? '-': manageAmountFormat(abs($itemData['openingBalanceAmount'])),
                    (manageAmountFormat(abs($itemData['periodDebit']))=="0.00") ? '-': manageAmountFormat(abs($itemData['periodDebit'])),
                    (manageAmountFormat(abs($itemData['periodCredit']))=="0.00") ? '-': manageAmountFormat(abs($itemData['periodCredit'])),
                    (manageAmountFormat(abs($itemData['periodBalance']))=="0.00") ? '-': manageAmountFormat(abs($itemData['periodBalance'])),
                    (manageAmountFormat(abs($itemData['closingBalance']))=="0.00") ? '-': manageAmountFormat(abs($itemData['closingBalance'])),
                ];

                $openingBalanceAmount[]= $itemData['openingBalanceAmount'];
                $periodDebit[] = $itemData['periodDebit'];
                $periodCredit[] =$itemData['periodCredit'];
                $periodBalance[] = $itemData['periodBalance'];
                $closingBalance[]= $itemData['closingBalance'];
                $subopeningBalanceAmount[]= $itemData['openingBalanceAmount'];
                $subperiodDebit[] = $itemData['periodDebit'];
                $subperiodCredit[] =$itemData['periodCredit'];
                $subperiodBalance[] = $itemData['periodBalance'];
                $subclosingBalance[]= $itemData['closingBalance'];
                $counter++;
            }
            $export_array[] = [
                '',
                'Sub Total',
                manageAmountFormat(array_sum($subopeningBalanceAmount)),
                manageAmountFormat(array_sum($subperiodDebit)),
                manageAmountFormat(array_sum($subperiodCredit)),
                manageAmountFormat(array_sum($subperiodBalance)),
                manageAmountFormat(array_sum($subclosingBalance))
            ];




        }
        $export_array[] = array();
        $export_array[] = [
            '',
            'Total',
            manageAmountFormat(array_sum($openingBalanceAmount)),
            manageAmountFormat(abs(array_sum($periodDebit))),
            manageAmountFormat(abs(array_sum($periodCredit))),
            manageAmountFormat(array_sum($periodBalance)),
            manageAmountFormat(array_sum($closingBalance))
        ];


        $this->detaileddownloadExcelFile($export_array,$filetype,$file_name);

    }
}
