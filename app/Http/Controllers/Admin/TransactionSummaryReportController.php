<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaGlTran;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class TransactionSummaryReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'gl-transaction-report-summary';
        $this->title = 'Reports';
        $this->pmodule = 'general-ledger-reports';
    }

    // GL REPORTS
    public function gl_transaction_summary(Request $request)
    {
        $title = "GL Transaction Summary";
        $model = 'gl-transaction-report-summary';
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $lists = [];

        if (isset($permission['general-ledger-reports___transaction-summary']) || $permission == 'superadmin') {
            if ($request->has('manage-request')) {
                $lists = WaGlTran::with(['user'])->select([
                    DB::RAW('COALESCE(SUM(amount),0) as sum_am'),
                    DB::RAW('SUM(CASE WHEN amount > 0 THEN amount else 0 END) as sum_debit'),
                    DB::RAW('SUM(CASE WHEN amount < 0 THEN amount else 0 END) as sum_credit'),
                    'trans_date',
                    'created_at',
                    'transaction_no',
                    'transaction_type',
                    'user_id',
                    DB::RAW('COUNT(transaction_no) as count_transaction')
                ]);

                $start_date = null;
                $end_date = null;
                if ($request->has('start-date')) {
                    $start_date = $request->input('start-date');
                    $lists = $lists->where('created_at', '>=', $request->input('start-date') . ' 00:00:00');
                }
                if ($request->has('end-date')) {
                    $end_date = $request->input('end-date');
                    $lists = $lists->where('created_at', '<=', $request->input('end-date') . " 23:59:59");
                }

                $lists = $lists->groupBy('transaction_no')->orderBy('created_at')->get();

                if ($request->input('manage-request') == 'excel') {
                    $data = [];
                    foreach ($lists as $trans) {
                        $child = [];
                        $child['Transaction Date'] = date('d/M/Y', strtotime($trans->created_at));
                        $child['Transaction No'] = $trans->transaction_no;
                        $child['Transaction Type'] = $trans->transaction_type;
                        $child['User'] = @$trans->user->name;
                        $child['Transaction Count'] = $trans->count_transaction;
                        $child['Debit'] = manageAmountFormat($trans->sum_debit);
                        $child['Credit'] = manageAmountFormat($trans->sum_credit);
                        $child['Amount'] = manageAmountFormat($trans->sum_am);
                        $data[] = $child;
                    }
                    $child = [];
                    $child['Transaction Date'] = 'Total';
                    $child['Transaction No'] = "";
                    $child['Transaction Type'] = "";
                    $child['User'] = "";
                    $child['Transaction Count'] = "";
                    $child['Debit'] = manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_debit') : 0);
                    $child['Credit'] = manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_credit') : 0);
                    $child['Amount'] = manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_credit') : 0);
                    $data[] = $child;

                    return ExcelDownloadService::download('Gl-Transaction-Summary', collect($data), ['TRANSACTION DATE', 'TRANSACTION NO', 'TRANSACTION TYPE', 'USER', 'TRANSACTION COUNT', 'DEBIT', 'CREDIT', 'AMOUNT']);
                }
            }

            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.gl_reports.gl_transaction_summary', compact('title', 'lists', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
