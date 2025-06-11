<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\WaBankAccount;
use App\Model\WaSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class BankPaymentsReportController extends Controller
{
    protected $model = 'bank-payments-report';

    protected $title = 'Bank Payments Report';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $payments = DB::table('payment_vouchers as vouchers')
            ->select([
                'vouchers.id',
                'vouchers.number',
                DB::raw("DATE_FORMAT(vouchers.created_at, '%Y-%m-%d') AS created_at"),
                'suppliers.name AS supplier_name',
                'accounts.id AS account_id',
                'accounts.account_name',
                'payment_modes.mode AS payment_mode',
                'bank_files.file_no AS bank_file_no',
                DB::raw("DATE_FORMAT(bank_files.created_at, '%Y-%m-%d') AS bank_file_date"),
                'vouchers.amount',
            ])
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'vouchers.wa_supplier_id')
            ->join('wa_bank_accounts as accounts', 'accounts.id', 'vouchers.wa_bank_account_id')
            ->join('wa_payment_modes as payment_modes', 'payment_modes.id', 'vouchers.wa_payment_mode_id')
            ->join('wa_bank_file_items as bank_file_items', 'bank_file_items.payment_voucher_id', 'vouchers.id')
            ->join('wa_bank_files as bank_files', 'bank_files.id', 'bank_file_items.wa_bank_file_id')
            ->when(request()->filled('supplier'), function ($query) {
                $query->where('suppliers.id', request()->supplier);
            })
            ->whereBetween('vouchers.created_at', [$from, $to])
            ->get();

        $accounts = WaBankAccount::makesPayments()->get();

        if (request()->download == 'excel') {
            $view = view(
                'admin.bank_files.exports.excel',
                [
                    'payments' => $payments,
                    'accounts' => $accounts,
                    "supplier" => request()->filled('supplier') ? WaSupplier::find(request()->supplier) : '',
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                    'status' => request()->filled('status') ? Str::upper(request()->status) : false
                ]
            );

            return Excel::download(new ExportViewToExcel($view), 'bank_payments_report' . date('Ymdhis') . '.xlsx');
        }

        if (request()->download == 'pdf') {
            $pdf = Pdf::loadView(
                'admin.bank_files.exports.pdf',
                [
                    'payments' => $payments,
                    'accounts' => $accounts,
                    "supplier" => request()->filled('supplier') ? WaSupplier::find(request()->supplier) : '',
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                    'description' => request()->filled('status') ? Str::upper(request()->status) . ' BANK PAYMENTS REPORT' : 'BANK PAYMENTS REPORT'
                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download('bank_payments_report' . date('Ymdhis') . '.pdf');
        }

        return view('admin.bank_files.report', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                'Accounts Payables Reports' => route('account-payables-reports.index'),
                $this->title => ''
            ],
            'suppliers' => WaSupplier::get(),
            'payments' => $payments,
            'accounts' => $accounts
        ]);
    }
}
