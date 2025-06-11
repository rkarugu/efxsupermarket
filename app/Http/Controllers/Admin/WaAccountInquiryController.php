<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaChartsOfAccount;
use App\Model\WaGlTran;
use App\Model\Restaurant;
use Excel;
use App\Exports\WaAccountInquiryExport;
use App\Exports\WaAccountInquiryGroupedExport;
use App\Models\GlAccountUpdateHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class WaAccountInquiryController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'account-inquiry';
        $this->title = 'Account Inquiry';
        $this->pmodule = 'account-inquiry';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }

    public function index(Request $request)
    {
        // $data['chart_accounts'] = WaChartsOfAccount::get();
        $accountid = null;
        if (request()->has('account') && request()->filled('account')) {
            $accountid = request()->account;
        } else {
            $accountid = null;
        }
        $accountdata = WaChartsOfAccount::where('id', $accountid)->first();
        // dd($accountdata);
        // dd($accountdata);
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['permission'] =  $this->mypermissionsforAModule();
        return view('admin.waaccountinquiry.index', compact('accountdata'))->with($data);
    }
    public function search(Request $request)
    {
        
        $record = WaGlTran::with('getAccountDetail')
        // ->orderBy('id', 'desc')
        ->orderBy('created_at')
        ->with('restaurant');
        if ($request->account) {
            $account = WaChartsOfAccount::where('id', $request->account)->first();
        }

        if ($request->branch) {
            $record = $record->where('restaurant_id', $request->branch);
        }

        $openingBalance = $record->clone();
        if (isset($account) && !empty($account)) {
            $record = $record->where('account', $account->account_code);
            $openingBalance = $openingBalance->where('account', $account->account_code);            
        }

        if ($request->get('start-date') && $request->get('end-date')) {
            $date1 = $request->get('start-date') . ' 00:00:00';
            $date2 = $request->get('end-date') . ' 23:59:59';
            $openingBalance = $openingBalance->where('created_at', '<', $date1);
            $record = $record->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
            $data['openingBalance'] = $openingBalance->sum('amount');
        }else{
            $data['openingBalance'] = 0;
        }

        


        if ($request->manage && $request->manage == 'export-grouped-transaction') {
            $data = $record->select(
                'created_at as date',
                'transaction_no',
                'transaction_type',
                DB::RAW("COALESCE(sum(amount),0) as total_amount"),
            )
                ->groupBy('transaction_no')->get();
            $records = [];
            $total = 0;
            foreach ($data as $list) {
                $payload = [
                    'transaction_no' => $list->transaction_no,
                    'transaction_type' => $list->transaction_type,
                    'trans_date' => getDateFormatted($list->date),
                    'amount' => manageAmountFormat($list->total_amount),
                ];
                $records[] = $payload;
                $total += $list->total_amount;
            }
            $records[] = [
                'transaction_no' => '',
                'transaction_type' => '',
                'date' => 'Total',
                'amount' => manageAmountFormat($total),
            ];
            $export = new WaAccountInquiryGroupedExport(collect($records));
            return Excel::download($export, 'account-inquiry-grouped' . date('Y-m-d-H-i-s') . ".xlsx");
        }

        $data['record'] = $record->get();
        
        $negativeAMount =  WaGlTran::where('amount', '<=', '0');
        if ($request->get('start-date') && $request->get('end-date')) {
            $date1 = $request->get('start-date') . ' 00:00:00';
            $date2 = $request->get('end-date') . ' 23:59:59';
            $negativeAMount = $negativeAMount->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
        }
        if ($request->branch) {
            $negativeAMount = $negativeAMount->where('restaurant_id', $request->branch);
        }
        if (isset($account) && !empty($account)) {
            $negativeAMount = $negativeAMount->where('account', $account->account_code);
        }
        $data['negativeAMount'] = $negativeAMount->sum('amount');

        $positiveAMount =  WaGlTran::where('amount', '>=', '0');
        if ($request->get('start-date') && $request->get('end-date')) {
            $date1 = $request->get('start-date') . ' 00:00:00';
            $date2 = $request->get('end-date') . ' 23:59:59';
            $positiveAMount = $positiveAMount->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
        }
        if (isset($account) && !empty($account)) {
            $positiveAMount = $positiveAMount->where('account', $account->account_code);
        }
        if ($request->branch) {
            $positiveAMount = $positiveAMount->where('restaurant_id', $request->branch);
        }
        $data['positiveAMount'] = $positiveAMount->sum('amount');
        $data['branch'] = (isset($request->branch)) ? Restaurant::where('id', $request->branch)->first() : '';
        $data['accounts'] = (isset($account) && !empty($account)) ? $account : '';
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['permission'] =  $this->mypermissionsforAModule();

        $records = [];
        $account_codes =  getChartOfAccountsList();
        $runningBalance = $data['openingBalance'];
        $records[] = [
            'branch'  => '',
            'account_code' =>  '',
            'account' => '',
            'transaction_no' => '',
            'trans_date' => '',
            'post_date' => '',
            'narrative' => '',
            'tag' => '',
            'debit' => '',
            'credit' => '',
            'running_balance' => $runningBalance
        ];
        foreach ($data['record'] as $list) {
            if ($list->transaction_type == "Sales Invoice" && $list->amount > 0) {
                $accountno = explode(':', $list->narrative);
                $narrative = (count($accountno) > 1) ? $accountno[0] : '---';
            } elseif ($list->transaction_type == 'Receipt') { 
                $narrative = $list->narrative;
            }
            else {
                $accountno = explode('/', $list->narrative);
                $narrative = (count($accountno) > 1) ? $accountno[1] : '---';
            }
            
            $runningBalance += $list->amount;
                                    
            $payload = [
                'branch'  => (isset($list->restaurant->name)) ? $list->restaurant->name : '----',
                'account_code' => isset($account_codes[$list->account]) ? $account_codes[$list->account] : '',
                'account' => $list->account,
                'transaction_no' => $list->transaction_no,
                'trans_date' => getDateFormatted($list->trans_date),
                'post_date' => getDateFormatted($list->created_at),
                'narrative' => $narrative,
                'tag' => (isset($list->restaurant->branch_code)) ? $list->restaurant->branch_code : '----',
                'debit' => $list->amount >= '0' ? $list->amount : '',
                'credit' => $list->amount <= '0' ? $list->amount : '',
                'running_balance' => $runningBalance,                
            ];
            $records[] = $payload;
        }
        $records[] = [
            'branch'  => '',
            'account_code' =>  '',
            'account' => '',
            'transaction_no' => '',
            'trans_date' => '',
            'post_date' => '',
            'narrative' => '',
            'tag' => 'Total',
            'debit' => $data['positiveAMount'],
            'credit' => $data['negativeAMount'],
            'running_balance' => $runningBalance
        ];

        if ($request->manage && $request->manage == 'export') {
            $export = new WaAccountInquiryExport(collect($records));
            return Excel::download($export, 'account-inquiry-' . date('Y-m-d-H-i-s') . ".xlsx");
        }

        return view('admin.waaccountinquiry.search')->with($data);
    }

    public function details(Request $request, $transaction = '')
    {
        if ($transaction == '') {
            return redirect()->route('admin.account-inquiry.index');
        }
        $record = WaGlTran::with('getAccountDetail')->orderBy('id', 'desc')->with('restaurant')->where('transaction_no', $transaction);
        $data['record'] = $record->get();

        $negativeAMount =  WaGlTran::where('amount', '<=', '0')->where('transaction_no', $transaction);
        $data['negativeAMount'] = $negativeAMount->sum('amount');

        $positiveAMount =  WaGlTran::where('amount', '>=', '0')->where('transaction_no', $transaction);
        $data['positiveAMount'] = $positiveAMount->sum('amount');


        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['permission'] =  $this->mypermissionsforAModule();
        $data['transaction'] = $transaction;
        return view('admin.waaccountinquiry.details')->with($data);
    }

    public function edit($transaction)
    {
        if (!can('edit-account-transaction', $this->model)) {
            return returnAccessDeniedPage();
        }

        if ($transaction == '') {
            return redirect()->route('admin.account-inquiry.index');
        }
        $record = WaGlTran::with('getAccountDetail')->orderBy('id', 'desc')->with('restaurant')->where('transaction_no', $transaction);
        $data['record'] = $record->get();

        $negativeAMount =  WaGlTran::where('amount', '<=', '0')->where('transaction_no', $transaction);
        $data['negativeAMount'] = $negativeAMount->sum('amount');

        $positiveAMount =  WaGlTran::where('amount', '>=', '0')->where('transaction_no', $transaction);
        $data['positiveAMount'] = $positiveAMount->sum('amount');


        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['permission'] =  $this->mypermissionsforAModule();
        $data['transaction'] = $transaction;
        $data['accounts'] = WaChartsOfAccount::orderBy('account_name')->get();
        // dd($data['record']);
        return view('admin.waaccountinquiry.edit')->with($data);
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'gl'=>'required',
                'account'=>'required'
            ]);
            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
    
            $check = DB::transaction(function () use ($request){
                $gl = WaGlTran::find($request->gl);
                if ($gl->account !=$request->account) {
                    
                    GlAccountUpdateHistory::create([
                        'gl_trans_id' => $gl->id,
                        'new_account' => $request->account,
                        'old_account' => $gl->account,
                        'created_by' => Auth::user()->id,
                    ]);
                    $gl->account= $request->account;
                    $gl->save();
                    
                }
                return true;
            });
            if($check){
                $account_codes =  getChartOfAccountsList();
                return response()->json([
                    'result'=>1,
                    'message'=>'Gl Account Updated Successfully.',
                    'id' => $request->gl,
                    'account'=>$account_codes[$request->account] .'('.$request->account.')'
                    ], 200);  
            }
            return response()->json(['result'=>-1,'message'=>'Something went wrong'], 500); 
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update_report()
    {
        if (!can('gl-account-update-report', 'general-ledger-reports')) {
            return returnAccessDeniedPage();
        }
        $title = $this->title;
        $model = $this->model;
        $reports = GlAccountUpdateHistory::orderBy('created_at','desc')->get();

        return view('admin.waaccountinquiry.update_report',compact('title','model','reports'));
    }
}
