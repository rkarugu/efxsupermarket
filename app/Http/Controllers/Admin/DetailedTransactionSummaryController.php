<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaGlTran;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class DetailedTransactionSummaryController extends Controller
{
    protected $model = 'detailed-transaction-summary';

    protected $title = 'Detailed Transaction Summary';

    public function index()
    {
        if (!can('detailed-transaction-summary', 'general-ledger-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $startDate = Carbon::parse(request()->start_date)->startOfDay();
        $endDate = Carbon::parse(request()->end_date)->endOfDay();

        $query = WaGlTran::query()->select([
            'wa_gl_trans.trans_date',
            'wa_gl_trans.created_at',
            'wa_gl_trans.transaction_no',
            'wa_gl_trans.transaction_type',
            'wa_charts_of_accounts.account_code',
            'wa_charts_of_accounts.account_name',
            'wa_gl_trans.amount',
        ])
            ->join('wa_charts_of_accounts', 'wa_gl_trans.account', '=', 'wa_charts_of_accounts.account_code')
            ->whereBetween('wa_gl_trans.trans_date', [$startDate, $endDate]);

        if (request()->intent == 'Excel') {
            $fileName = "Detailed-Transaction-Report-$startDate-$endDate";
            $headings = ['Transaction Date', 'Posting Date', 'Transaction No', 'Transaction Type', 'Account Code', 'Account Name', 'Amount'];

            return ExcelDownloadService::download($fileName, $query->get(), $headings);
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($transaction) {
                    return $transaction->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('amount', function ($transaction) {
                    return manageAmountFormat($transaction->amount);
                })
                ->with('grand_total', function () use ($query) {
                    return manageAmountFormat($query->sum('amount'));
                })
                ->toJson();
        }

        $breadcum = ['General Ledger Reports' => '', $this->title => ''];

        return view('admin.gl_reports.detailed_transaction_summary', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $breadcum
        ]);
    }
}
