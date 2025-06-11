<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WaGlTran;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ExcelDownloadService;

class TransactionsWithoutBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', 'transaction-without-branch')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Transaction Without Branch';
        $model = 'transactions-without-branches';

        $breadcum = [
            'Transaction Without Branch' => '',
            $title => ''
        ];

        if (request()->wantsJson()) {
            $transactions = WaGlTran::with('getAccountDetail','restaurant')
                            ->where('tb_reporting_branch',NULL);
                            if (request()->filled('start_date') && request()->filled('end_date')) {
                                $transactions->whereBetween('trans_date', [request()->start_date, request()->end_date]);
                            }

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('account',function($transaction){
                    if ($transaction->getAccountDetail) {
                        return $transaction->getAccountDetail->account_code.'('.$transaction->getAccountDetail->account_name.')';
                    }
                    return '-';
                })
                ->editColumn('amount',function($transaction){
                    return manageAmountFormat($transaction->amount);
                })
                ->editColumn('trans_date',function($transaction){
                    return date('d-m-Y', strtotime($transaction->trans_date));
                })
                ->toJson();
        }

        if (request()->print == 'excel') {
            $lists = WaGlTran::with('getAccountDetail','restaurant')->where('tb_reporting_branch',NULL);
            
            if (request()->filled('start_date') && request()->filled('end_date')) {
                $lists->whereBetween('trans_date', [request()->start_date, request()->end_date]);
            }
            // $lists->groupBy('transaction_no');
            $lists = $lists->orderBy('trans_date')->get();
            
                $data = [];
                foreach ($lists as $trans) {
                    $child = [];
                    $child['Transaction Date'] = date('d-m-Y', strtotime($trans->trans_date));
                    $child['Transaction No'] = $trans->transaction_no;
                    $child['Transaction Type'] = $trans->transaction_type;
                    $child['Narrative'] = $trans->narrative;
                    $child['Branch'] = $trans->restaurant->name;
                    $child['Account'] = $trans->getAccountDetail->account_code.'('.$trans->getAccountDetail->account_name.')';
                    $child['Amount'] = manageAmountFormat($trans->amount);
                    $data[] = $child;
                }

                return ExcelDownloadService::download('Gl-Transaction-No-TB-Branch', collect($data), 
                ['TRANSACTION DATE', 'TRANSACTION NO', 'TRANSACTION TYPE','NARRATIVE','BRANCH', 'ACCOUNT', 'AMOUNT']);
            
        }

        return view('admin.transaction_without_branch.list', compact('title', 'model', 'breadcum'));
        
    }
}
