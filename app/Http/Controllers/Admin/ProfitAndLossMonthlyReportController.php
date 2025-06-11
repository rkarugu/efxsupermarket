<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaAccountSection;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ProfitAndLossMonthlyReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'profit-and-loss';
        $this->title = 'Reports';
        $this->pmodule = 'general-ledger-reports';
    }
    public function monthlyProfitSummary(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $myData = [];
        $title = '';
        $lists=[];
        $restroList = $this->getRestaurantList();
        $monthRange=0;
        $selectedMonthArr=[];
        if (isset($permission['general-ledger-reports___p-l-monthly-report']) || $permission == 'superadmin') {
            if($request->has('manage-request')){
                $start_date = null;
                $end_date = null;
                if ($request->has('start-date')){
                    $start_date=$request->input('start-date');
                }
                if ($request->has('end-date')) {
                    $end_date=$request->input('end-date');
                }

                $selectedMonthArr=getMonthsBetweenDates($start_date,$end_date);
                $monthRange=getMonthRangeBetweenDate($start_date,$end_date);
                $lists = WaAccountSection::with('getWaAccountGroup', "getWaAccountGroup.getChartAccountMontly")
                    ->whereIn('section_name', ['INCOME', 'COST OF SALES', 'OVERHEADS'])
                    ->orderBy('section_number', 'ASC')
                    ->get();

                
                if($monthRange > 12){
                    Session::flash('warning', "You can't select more than 12 months.");
                }  

                if($request->input('manage-request') == 'export'){
                    $pdf = Pdf::loadView('admin.gl_reports.monthly_profit_and_loss_pdf', compact('title', 'lists', 'model', 'breadcum', 'selectedMonthArr', 'monthRange','restroList'));
                    return $pdf->download('Monthly-Project-Summary.pdf');
                }
                if($request->input('manage-request') == 'excel'){
                    $data = [];
                    $a = [];
                    $b = [];
                    $c = [];
                    $d = [];
                    foreach($lists as $key => $val)
                    {
                        if(count($val->getWaAccountGroup) > 0){
                            $totalcost = [];
                            $grandtotalcost = 0;
                            foreach($val->getWaAccountGroup as $key => $groupacount){
                                $dataChartAccount = $groupacount->getChartAccountMontly;
                                if(count($dataChartAccount)>0){
                                    $child = [];
                                    $child['Main Category'] = $val->section_name;
                                    $child['Sub-Category'] = $groupacount->group_name;
                                    $child['Account Name'] = "";
                                    $child['Account ID'] = "";
                                    if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                                        foreach($selectedMonthArr['m'] as $key => $month){
                                            $year=$selectedMonthArr['y'][$key]; 
                                            $child[date('F',strtotime(date($year.'-'.$month.'-01')))] = "";
                                        }
                                    }
                                    $child['Grand Total'] = "";
                                    $data[] = $child;
                                    foreach($dataChartAccount as $key => $value){
                                        $child = [];
                                        $child['Main Category'] = '';
                                        $child['Sub-Category'] = '';
                                        $child['Account Name'] = $value->account_name;
                                        $child['Account ID'] = $value->account_code;
                                        if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                                            foreach($selectedMonthArr['m'] as $key => $month){
                                                $year=$selectedMonthArr['y'][$key]; 
                                                $am = "amount_".$month.'_'.$year;
                                                $totalcost[$year][$month][] = abs($value->$am);
                                                $child[date('F',strtotime(date($year.'-'.$month.'-01')))] = abs($value->$am);
                                            }
                                        }
                                        $child['Grand Total'] = abs($value->amount_total);
                                        $grandtotalcost += abs($value->amount_total);
                                        $data[] = $child;
                                    }
                                }
                            }
                            $child = [];
                            $child['Main Category'] = "";
                            $child['Sub-Category'] = "Total";
                            $child['Account Name'] = "";
                            $child['Account ID'] = "";
                            if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                                foreach($selectedMonthArr['m'] as $key => $month){
                                    $year=$selectedMonthArr['y'][$key]; 
                                    if($val->section_name=="INCOME"){
                                        $a[$year][$month] = array_sum(@$totalcost[$year][$month] ?? []);
                                    }
                                    if($val->section_name=="COST OF SALES"){
                                        $b[$year][$month] = array_sum(@$totalcost[$year][$month] ?? []);
                                    }
                                    if($val->section_name=="OVERHEADS"){
                                        $d[$year][$month] = array_sum(@$totalcost[$year][$month] ?? []);
                                    }
                                    $c[$year][$month] = (@$a[$year][$month] ?? 0)-(@$b[$year][$month] ?? 0);
				                    $e[$year][$month] = (@$c[$year][$month] ?? 0)-(@$d[$year][$month] ?? 0);
                                    $child[date('F',strtotime(date($year.'-'.$month.'-01')))] = abs(array_sum(@$totalcost[$year][$month] ?? []));
                                }
                            }
                            $child['Grand Total'] = $grandtotalcost;
                            $data[] = $child;
                            if($val->section_name=="COST OF SALES"){
                                $gtSUm = 0;
                                $child = [];
                                $child['Main Category'] = "GROSS PROFIT ";
                                $child['Sub-Category'] = "";
                                $child['Account Name'] = "";
                                $child['Account ID'] = "";
                                if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                                    foreach($selectedMonthArr['m'] as $key => $month){
                                        $year=$selectedMonthArr['y'][$key]; 
                                        $gtSUm += (@$c[$year][$month] ?? 0);
                                        $child[date('F',strtotime(date($year.'-'.$month.'-01')))] = (@$c[$year][$month] ?? 0);
                                    }
                                }
                                $child['Grand Total'] = $gtSUm;
                                $data[] = $child;
                            }
                            if($val->section_name=="OVERHEADS"){
                                $gtSUm = 0;
                                $child = [];
                                $child['Main Category'] = "NET PROFIT ";
                                $child['Sub-Category'] = "";
                                $child['Account Name'] = "";
                                $child['Account ID'] = "";
                                if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                                    foreach($selectedMonthArr['m'] as $key => $month){
                                        $year=$selectedMonthArr['y'][$key]; 
                                        $gtSUm += (@$e[$year][$month] ?? 0);
                                        $child[date('F',strtotime(date($year.'-'.$month.'-01')))] = (@$e[$year][$month] ?? 0);
                                    }
                                }
                                $child['Grand Total'] = $gtSUm;
                                $data[] = $child;
                            }
                        }
                    }
                    // return Excel::create('Monthly-Profit-Loss-Summary', function($excel) use ($data) {
                    //     $from = "A1"; // or any value
                    //     $to = "G5"; // or any value
                    //         $excel->sheet('mySheet', function($sheet) use ($data)
                    //         {
                    //             $sheet->fromArray($data);
                    //         });
                    //     })->download('xls');
                    return ExcelDownloadService::download('Monthly-Profit-Loss-Summary', collect($data), []);
                }

            } 
            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.gl_reports.profit_and_loss_monthly_report', compact('title', 'lists', 'model', 'breadcum','selectedMonthArr','monthRange','restroList'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
