<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\User;
use App\Model\WaGlTran;

use App\Interfaces\Finance\GeneralLedgerInterface;
use App\Interfaces\Finance\ChartOfAccountsInterface;

use App\Exports\Finance\TrialBalanceReportExport;
use App\Exports\Finance\TrialBalanceAccountExport;
use App\Exports\Finance\TrialBalanceAccountGroupedTransactionExport;
use App\Exports\TrialBalanceExport;
use App\Model\Restaurant;
use App\Model\WaChartsOfAccount;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Session;
use Excel;
use PDF;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class TrailBalanceController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    private GeneralLedgerInterface $generalLedger;
    private ChartOfAccountsInterface $chartOfAccounts;
    public $querystring;

    public function __construct(
        GeneralLedgerInterface   $generalLedger,
        ChartOfAccountsInterface $chartOfAccounts
    )
    {
        $this->model = 'trial-balances';
        $this->title = 'Trial Balance';
        $this->pmodule = 'trial-balances';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
        $this->generalLedger = $generalLedger;
        $this->chartOfAccounts = $chartOfAccounts;
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        $start_date = $request->input('start-date') ? $request->input('start-date') : Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end_date = $request->input('end-date') ? $request->input('end-date') : Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        $detail = [];
        $openingBalanceDate = date('Y-m-d', strtotime('-1 day', strtotime($request->input('start-date'))));
        $all_item = WaGlTran::with(['getAccountDetail.getRelatedGroup'])->select(['account',
            DB::RAW("COALESCE(sum(amount),0) as sm")
        ]);

        if ($request->restaurant) {
            $all_item->whereIn('tb_reporting_branch', $request->restaurant);
        }

        $all_item = $all_item->whereDate('trans_date', '>=', $start_date);
        $all_item = $all_item->whereDate('trans_date', '<=', $end_date);
        $all_item = $all_item->whereHas('getAccountDetail')->groupBy('account')->orderBy('id', 'desc')->get();

        if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf')) {


            if ($request->input('manage-request') == 'xls') {
                $export_array = [];
                $periodDebit = [];
                $periodCredit = [];

                foreach ($all_item as $account_name => $itemArray) {
                    $debit = 0;
                    $credit = 0;
                    if ($itemArray['sm'] > 0) {
                        $debit = $itemArray['sm'];
                    } else {
                        $credit = $itemArray['sm'];
                    }
                    $export_array[] = [
                        $itemArray->account,
                        $itemArray->getAccountDetail->account_name,
                        manageAmountFormat($debit),
                        manageAmountFormat($credit),
                    ];
                    $periodDebit[] = $debit;
                    $periodCredit[] = $credit;
                }

                $totalDebit = manageAmountFormat(abs(array_sum($periodDebit)));
                $totalCredit = manageAmountFormat(abs(array_sum($periodCredit)));
                $data = $export_array;
                $data[] = [
                    '',
                    'TOTAL',
                    $totalDebit,
                    $totalCredit,
                ];

                $branch = 'All';
                if ($request->restaurant) {
                    $branchQuery = Restaurant::whereIn('id', $request->restaurant)->get()->pluck('name');

                    $branch = '';
                    foreach ($branchQuery as $index => $element) {
                        $branch .= $element;
                        if ($index < count($branchQuery) - 1) {
                            $branch .= ', ';
                        }
                    }
                }
                $info = [
                    'company_name' => getAllSettings()['COMPANY_NAME'],
                    'start_date' => request()->filled('start-date') ? request()->input('start-date') : '-',
                    'end_date' => request()->filled('end-date') ? request()->input('end-date') : '-',
                    'branch' => $branch,
                ];
                // return ExcelDownloadService::download('Trial-Balance', collect($data), ['ACCOUNT CODE', 'ACCOUNT NAME','PERIOD DEBITS','PERIOD CREDITS']);
                $export = new TrialBalanceReportExport(collect($data), $info);
                return Excel::download($export, "Trial-Balance.xlsx");
            }

            if ($request->input('manage-request') == 'pdf') {
                return $this->downloadPDF('pdf', $all_item, $request);
            }
        }
// dd($all_item->where('sm', '>', 0)->sum('sm'),$all_item->where('sm', '<', 0)->sum('sm'));
        $restroList = $this->getRestaurantList();
        $breadcum = [$title => '', 'Sheet' => ''];
        return view('admin.trailbalance.sheet', compact('title', 'restroList', 'model', 'breadcum', 'detail', 'all_item'));
    }

    public function getTrailBalanceByGroup($array)
    {
        $final_array = [];
        foreach ($array as $arr) {
            if (!isset($final_array[$arr['account_group']][$arr['gl_account']])) {
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

    public function accountPayablesDetails(Request $request)
    {
        $all_item = WaGlTran::select(DB::RAW("SUM(amount) as total_sum"), "supplier_account_number", 'wa_suppliers.name as sup_name', 'wa_customers.customer_name as cus_name')
            ->where(function ($e) use ($request) {
                if ($request->gl_account) {
                    $e->where('account', $request->gl_account);
                }
            })
            ->where('tb_reporting_branch', $request->restaurant)
            ->whereDate('trans_date', '>=', $request->from)
            ->where('supplier_account_number', '!=', null)
            ->whereDate('trans_date', '<=', $request->to)
            ->leftJoin('wa_suppliers', function ($e) {
                $e->on('wa_suppliers.supplier_code', '=', 'supplier_account_number');
            })
            ->leftJoin('wa_customers', function ($e) {
                $e->on('wa_customers.customer_code', '=', 'supplier_account_number');
            })
            ->groupBy('supplier_account_number')->get();
        $table = "<table class='table table-hover'>";
        $table .= "<tr>";
        $table .= "<th> Supplier/Customer Account Number </th>";
        $table .= "<th> Supplier/Customer Name </th>";
        $table .= "<th style='text-align:right'> Total </th>";
        $table .= "</tr>";
        if (count($all_item) == 0) {
            $table .= "<tr>";
            $table .= "<td style='text-align:center' colspan='3'> No Record Found </td>";
            $table .= "</tr>";
        }
        foreach ($all_item as $key => $value) {
            $table .= "<tr>";
            $table .= "<td> " . $value->supplier_account_number . " </td>";
            $table .= "<td> " . $value->sup_name . $value->cus_name . " </td>";
            $table .= "<td style='text-align:right'> " . manageAmountFormat($value->total_sum) . " </td>";
            $table .= "</tr>";
        }
        $table .= "<tr>";
        $table .= "<td> Total </td>";
        $table .= "<td>  </td>";
        $table .= "<td style='text-align:right'> " . manageAmountFormat($all_item->sum('total_sum')) . " </td>";
        $table .= "</tr>";
        $table .= "</table>";
        return $table;
    }


    public function exportdata($filetype, $mixed_array, $request)
    {
        $export_array = [];

        foreach ($mixed_array as $account_name => $itemArray) {
            $debit = 0;
            $credit = 0;
            if ($itemArray['sm'] > 0) {
                $debit = $itemArray['sm'];
            } else {
                $credit = $itemArray['sm'];
            }
            $export_array[] = [
                $itemArray->account,
                $itemArray->getAccountDetail->account_name,
                manageAmountFormat($debit),
                manageAmountFormat($credit),
            ];
            $periodDebit[] = $debit;
            $periodCredit[] = $credit;
        }

        $totalDebit = manageAmountFormat(abs(array_sum($periodDebit)));
        $totalCredit = manageAmountFormat(abs(array_sum($periodCredit)));
        $data = $export_array;
        $data[] = [
            '',
            'TOTAL',
            $totalDebit,
            $totalCredit,
        ];
        return ExcelDownloadService::download('approval-transactions', collect($data), ['TRANS DATE', 'ROUTE', 'BRANCH', 'CHANNEL']);
    }

    public function downloadExcelFile($data, $type, $file_name)
    {

        // refrence url http://www.maatwebsite.nl/laravel-excel/docs/blade
        //http://www.easylaravelbook.com/blog/2016/04/19/exporting-laravel-data-to-an-excel-spreadsheet/
        return Excel::create($file_name, function ($excel) use ($data) {
            $from = "A1"; // or any value
            $to = "G5"; // or any value
            $excel->sheet('mySheet', function ($sheet) use ($data) {


                $sheet->fromArray($data);
            })// ->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true )

            ;
        })->download($type);


    }


    public function downloadPDF($filetype, $mixed_array, $request)
    {
        $heading = 'Trial Balance';//heading;
        $printed_time = 'Printed On:' . date('d/m/Y h:i A');


        $period_from = '';
        $period_to = '';


        if ($request->has('start-date')) {
            $period_from = 'Period From : ' . date('d/m/Y', strtotime($request->input('start-date')));
        }
        if ($request->has('end-date')) {
            $period_to = '  - To : ' . date('d/m/Y', strtotime($request->input('end-date')));
        }
        $branch = 'All';
        if ($request->restaurant) {
            $branchQuery = Restaurant::whereIn('id', $request->restaurant)->get()->pluck('name');

            $branch = '';
            foreach ($branchQuery as $index => $element) {
                $branch .= $element;
                if ($index < count($branchQuery) - 1) {
                    $branch .= ', ';
                }
            }
        }

        $COMPANY_NAME = getAllSettings()['COMPANY_NAME'];

        $file_name = 'trial_balance_report';
        $periodDebit = [];
        $periodCredit = [];
        $export_array = [];
        foreach ($mixed_array as $account_name => $itemArray) {
            $debit = 0;
            $credit = 0;
            if ($itemArray['sm'] > 0) {
                $debit = $itemArray['sm'];
            } else {
                $credit = $itemArray['sm'];
            }
            $export_array[] = [
                'account' => $itemArray->account,
                'name' => $itemArray->getAccountDetail->account_name,
                'debit' => $debit,
                'credit' => $credit,
            ];
            $periodDebit[] = $debit;
            $periodCredit[] = $credit;
        }

        $totalDebit = manageAmountFormat(abs(array_sum($periodDebit)));
        $totalCredit = manageAmountFormat(abs(array_sum($periodCredit)));
        $data = $export_array;

        $pdf = PDF::loadView('admin.trailbalance.reportinpdf', compact('filetype', 'data', 'request', 'heading', 'period_from', 'period_to', 'printed_time', 'branch', 'COMPANY_NAME', 'totalDebit', 'totalCredit'));
        // return $pdf->stream();
        return $pdf->download('trial_balance.pdf');
    }

    public function account_data(Request $request, $account)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        $accountInfo = WaChartsOfAccount::with('getRelatedGroup', 'getSubAccountSection')->where('account_code', $account)->get()->first();// $this->chartOfAccounts->getByAccount($account);dd($account);
        $data = WaGlTran::with('branch')
            ->where('account', $account)
            ->when(request('branch'), function ($query, $branch) {
                return $query->where('tb_reporting_branch', $branch);
            });
        if (request()->filled('start-date') && request()->filled('end-date')) {
            $data->whereBetween('trans_date', [request()->input('start-date') . ' 00:00:00', request()->input('end-date') . ' 23:59:59']);
        }

        $transactionTypes = DB::table('wa_gl_trans')->distinct('transaction_type')->where('account', $account)->pluck('transaction_type')->toArray();

        if (request()->filled('transaction_type')) {
            $data->where('transaction_type', '=', request()->transaction_type);
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($date) {
                    return date('Y-m-d H:i:s', strtotime($date->trans_date));
                })
                ->addColumn('debit', function ($item) {
                    return $item->amount > 0 ? manageAmountFormat($item->amount) : '';
                })
                ->addColumn('credit', function ($item) {
                    return $item->amount < 0 ? manageAmountFormat($item->amount) : '';
                })
                ->with('debitCreditTotal', function () use ($data) {
                    $debit = 0;
                    $credit = 0;
                    foreach ($data->get() as $item) {
                        if ($item->amount > 0) {
                            $debit = $debit + $item->amount;
                        } else {
                            $credit = $credit + $item->amount;
                        }
                    }
                    return ['debit' => manageAmountFormat($debit), 'credit' => manageAmountFormat($credit)];
                })
                ->editColumn('branch.name', function ($data) {
                    $branch = '-';
                    if ($data->branch) {
                        $branch = $data->branch->name;
                    }
                    return $branch;
                })
                ->toJson();

        }
        $debit = 0;
        $credit = 0;

        $branches = Restaurant::get();
        $restroList = $this->getRestaurantList();
        $breadcum = [$title => '', 'Account Data' => ''];

        return view('admin.trailbalance.account-details', compact('title', 'restroList', 'model', 'breadcum', 'debit', 'credit', 'accountInfo', 'branches', 'transactionTypes'));
    }

    public function account_data_search(Request $request, $account)
    {

        return $this->generalLedger->getTrialBalanceAccountDataPaginate($account);//dd($data[0]);         
    }

    public function export_data($account)
    {
        $accountInfo = $this->chartOfAccounts->getByAccount($account);
        $lists = $this->generalLedger->getTrialBalanceAccountData($account);//dd($data[0]);

        $data = [];
        $debit = 0;
        $credit = 0;
        foreach ($lists as $list) {
            $payload = [
                'date' => date('d-m-Y', strtotime(@$list->created_at)),
                'branch' => ucfirst(@$list->branch_name),
                'narrative' => $list->narrative,
                'reference' => $list->reference,
                'transaction_type' => $list->transaction_type,
                'transaction_no' => $list->transaction_no,
                'debit' => $list->amount > 0 ? manageAmountFormat($list->amount) : '',
                'credit' => $list->amount < 0 ? manageAmountFormat($list->amount) : '',
            ];
            $data[] = $payload;
            if ($list->amount > 0) {
                $debit = $debit + $list->amount;
            } else {
                $credit = $credit + $list->amount;
            }
        }
        $data[] = [
            'date' => '',
            'branch' => '',
            'narrative' => '',
            'reference' => '',
            'transaction_type' => '',
            'transaction_no' => 'Total',
            'debit' => $debit,
            'credit' => $credit,
        ];
        $data[] = [
            'date' => '',
            'branch' => '',
            'narrative' => '',
            'reference' => '',
            'transaction_type' => '',
            'transaction_no' => '',
            'debit' => 'Total',
            'credit' => $debit + $credit,
        ];

        $branch = 'All';
        if (request()->filled('branch')) {
            $branchQuery = DB::table('restaurants')->where('id', request()->branch)->first();
            $branch = $branchQuery->name;
        }

        $transactionType = 'All';
        if (request()->filled('transaction_type')) {
            $NumSeries = DB::table('wa_numer_series_codes')->where('code', request()->input('transaction_type'))->first();

            if ($NumSeries) {
                $title = $NumSeries ? $NumSeries->description : '';

                $title = str_replace('_', ' ', $title);
                $title = str_replace('-', ' ', $title);
                $transactionType = $title;
            }
        }

        $info = [
            'company_name' => getAllSettings()['COMPANY_NAME'],
            'name' => $accountInfo->account_name,
            'code' => $accountInfo->account_code,
            'group' => $accountInfo->getRelatedGroup->group_name,
            'section' => $accountInfo->getSubAccountSection->getAccountSection->section_name,
            'sub_section' => $accountInfo->getSubAccountSection->section_name,
            'start_date' => request()->filled('start-date') ? request()->input('start-date') : '-',
            'end_date' => request()->filled('end-date') ? request()->input('end-date') : '-',
            'branch' => $branch,
            'transaction_type' => $transactionType,
        ];
        $export = new TrialBalanceAccountExport(collect($data), $info);
        return Excel::download($export, $accountInfo->slug . "-" . $accountInfo->account_code . "-trial_balance_account.xlsx");

    }

    public function export_group_transaction($account)
    {
        $accountInfo = $this->chartOfAccounts->getByAccount($account);
        $lists = $this->generalLedger->getTrialBalanceAccountDataGroupTransaction($account);
        if ($lists->status() == 200) {

            $data = [];
            $total = 0;

            foreach (json_decode($lists->content()) as $list) {
                $date = isset($list->date) ? date('d-m-Y', strtotime($list->date)) : null;

                $payload = [
                    'transaction_no' => $list->transaction_no,
                    'date' => $date,
                    'transaction_type' => $list->transaction_type,
                    'amount' => manageAmountFormat($list->total_amount),
                ];
                $data[] = $payload;

                $total += $list->total_amount;
            }
            $data[] = [
                'transaction_no' => '',
                'date' => '',
                'transaction_type' => 'Total',
                'amount' => manageAmountFormat($total),
            ];

            $branch = 'All';
            if (request()->filled('branch')) {
                $branchQuery = DB::table('restaurants')->where('id', request()->branch)->first();
                $branch = $branchQuery->name;
            }

            $transactionType = 'All';
            if (request()->filled('transaction_type')) {
                $NumSeries = DB::table('wa_numer_series_codes')->where('code', request()->input('transaction_type'))->first();

                if ($NumSeries) {
                    $title = $NumSeries ? $NumSeries->description : '';

                    $title = str_replace('_', ' ', $title);
                    $title = str_replace('-', ' ', $title);
                    $transactionType = $title;
                }
            }

            $info = [
                'company_name' => getAllSettings()['COMPANY_NAME'],
                'name' => $accountInfo->account_name,
                'code' => $accountInfo->account_code,
                'group' => $accountInfo->getRelatedGroup->group_name,
                'section' => $accountInfo->getSubAccountSection->getAccountSection->section_name,
                'sub_section' => $accountInfo->getSubAccountSection->section_name,
                'start_date' => request()->filled('start-date') ? request()->input('start-date') : '-',
                'end_date' => request()->filled('end-date') ? request()->input('end-date') : '-',
                'branch' => $branch,
                'transaction_type' => $transactionType,
            ];

            $export = new TrialBalanceAccountGroupedTransactionExport(collect($data), $info);
            return Excel::download($export, $accountInfo->slug . "-" . $accountInfo->account_code . "-trial_balance_account_grouped.xlsx");
        } else {
            request()->session()->flash('status', $lists->content());
        }
    }


}
