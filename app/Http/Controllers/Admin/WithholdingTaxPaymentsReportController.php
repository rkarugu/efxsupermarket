<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\WaBankAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WithholdingTaxPaymentsReportController extends Controller
{
    protected $model = 'withholding-tax-payments-report';

    protected $title = 'Withholding Tax Payments Report';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : false;
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : false;

        $query = DB::table('withholding_payment_vouchers as vouchers')
            ->select([
                'vouchers.id',
                'vouchers.number',
                'vouchers.cheque_number',
                'vouchers.memo',
                'accounts.account_name',
                'withholding_files.file_no AS withholding_file_no',
                DB::raw("DATE_FORMAT(vouchers.payment_date, '%Y-%m-%d') AS payment_date"),
                'vouchers.amount',
            ])
            ->join('wa_bank_accounts as accounts', 'accounts.id', 'vouchers.wa_bank_account_id')
            ->join('wa_withholding_files as withholding_files', 'withholding_files.id', 'vouchers.withholding_file_id')
            ->when(request()->filled('account'), function ($query) {
                $query->where('accounts.id', request()->account);
            })
            ->when($from && $to, function ($query) use ($from, $to) {
                return $query->whereBetween('vouchers.created_at', [$from, $to]);
            });

        if (request()->wantsJson()) {
            return DataTables::of($query)
                ->editColumn('amount', function ($voucher) {
                    return manageAmountFormat($voucher->amount);
                })
                ->addColumn('actions', function ($voucher) {
                    return view('admin.withholding_files_payments.partials.link', compact('voucher'));
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('vouchers.amount'));
                })
                ->toJson();
        }

        if (request()->download == 'excel') {
            $view = view(
                'admin.withholding_files_payments.exports.excel',
                [
                    'vouchers' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                ]
            );

            return Excel::download(new ExportViewToExcel($view), 'withholding_files_payments_report' . date('Ymdhis') . '.xlsx');
        }

        if (request()->download == 'pdf') {
            $pdf = Pdf::loadView(
                'admin.withholding_files_payments.exports.pdf',
                [
                    'vouchers' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                    'description' => 'WITHHOLDING TAX PAYMENT VOUCHERS REPORT'
                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download('withholding_files_payments_report' . date('Ymdhis') . '.pdf');
        }

        return view('admin.withholding_files_payments.report', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                'Accounts Payables Reports' => route('account-payables-reports.index'),
                $this->title => ''
            ],
            'accounts'   => WaBankAccount::makesPayments()->get(),
        ]);
    }
}
