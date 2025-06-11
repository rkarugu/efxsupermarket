<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Withholding\ProcessWithholding;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaBankAccount;
use App\Model\WaBanktran;
use App\Model\WaChartsOfAccount;
use App\Model\WaGlTran;
use App\Model\WaSuppTran;
use App\Models\WaWithholdingFile;
use App\Models\WaWithholdingFileItem;
use App\Models\WithholdingPaymentVoucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class WithholdingTaxPaymentController extends Controller
{
    protected $model = 'withholding-tax-payments';

    protected $title = 'Withholding Tax Payments';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : false;
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : false;

        $query = WithholdingPaymentVoucher::query()
            ->from('withholding_payment_vouchers as vouchers')
            ->select([
                'vouchers.id',
                'vouchers.number',
                'vouchers.cheque_number',
                'vouchers.memo',
                'vouchers.status',
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
            return DataTables::eloquent($query)
                ->editColumn('amount', function ($voucher) {
                    return manageAmountFormat($voucher->amount);
                })
                ->editColumn('status', function ($voucher) {
                    return $voucher->status ? 'Yes' : 'No';
                })
                ->addColumn('actions', function ($voucher) {
                    return view('admin.withholding_files_payments.actions', compact('voucher'));
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('vouchers.amount'));
                })
                ->toJson();
        }

        return view('admin.withholding_files_payments.index', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                'Accounts Payables' => '',
                $this->title => ''
            ],
            'accounts'   => WaBankAccount::makesPayments()->get(),
        ]);
    }

    public function create()
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $file = WaWithholdingFile::findOrFail(request()->file);

        return view('admin.withholding_files_payments.create', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => route('withholding-files.index'),
                'create' => ''
            ],
            'file' => $file,
            'accounts' => WaChartsOfAccount::where('account_name', 'LIKE', '%withholding%')->get(),
            'branches' => Restaurant::get(),
            'banks' => WaBankAccount::makesPayments()->get()
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'bank' => 'required',
            'date' => 'required',
            'cheque' => 'required',
            'memo' => 'required',
            'file' => 'required',
        ]);

        if (WithholdingPaymentVoucher::where('withholding_file_id', $request->file)->exists()) {
            return redirect()->back()->withErrors('Payment already exists for the file');
        }

        DB::beginTransaction();

        try {
            $withholdingPaymentVoucher = WithholdingPaymentVoucher::create([
                'number' => getCodeWithNumberSeries('WITHHOLDING_TAX_PAYMENT_VOUCHERS'),
                'withholding_file_id' => $request->file,
                'withholding_account_id' => $request->withholding_account,
                'wa_bank_account_id' => $request->bank,
                'cheque_number' => $request->cheque,
                'memo' => $request->memo,
                'payment_date' => $request->date,
                'amount' => WaWithholdingFile::findOrFail($request->file)->amount,
                'restaurant_id' => $request->branch,
                'prepared_by' => auth()->user()->id,
            ]);

            updateUniqueNumberSeries('WITHHOLDING_TAX_PAYMENT_VOUCHERS',  $withholdingPaymentVoucher->number);

            DB::commit();

            if ($request->input('action') == 'print') {
                return redirect()->route('withholding-tax-payments.print', $withholdingPaymentVoucher->id);
            }

            Session::flash('success', 'Payment voucher created successfully');

            return redirect()->route('withholding-files.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Session::flash('error', $th->getMessage());

            return redirect()->route('withholding-tax-payments.create', ['file' => $request->file]);
        }
    }

    public function edit(WithholdingPaymentVoucher $voucher)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.withholding_files_payments.edit', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => route('withholding-files.index'),
                'edit' => ''
            ],
            'voucher' => $voucher,
            'accounts' => WaChartsOfAccount::where('account_name', 'LIKE', '%withholding%')->get(),
            'branches' => Restaurant::get(),
            'banks' => WaBankAccount::makesPayments()->get()
        ]);
    }

    public function update(Request $request, WithholdingPaymentVoucher $voucher)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'bank' => 'required',
            'date' => 'required',
            'cheque' => 'required',
            'memo' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $voucher->update([
                'withholding_account_id' => $request->withholding_account,
                'wa_bank_account_id' => $request->bank,
                'cheque_number' => $request->cheque,
                'memo' => $request->memo,
                'payment_date' => $request->date,
                'restaurant_id' => $request->branch
            ]);

            DB::commit();

            if ($request->input('action') == 'print') {
                return redirect()->route('withholding-tax-payments.print', $voucher->id);
            }

            Session::flash('success', 'Payment voucher update successfully');

            return redirect()->route('withholding-tax-payments.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Session::flash('error', $th->getMessage());

            return redirect()->route('withholding-tax-payments.edit', $voucher);
        }
    }

    public function destroy(WithholdingPaymentVoucher $voucher)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        WaSuppTran::where('document_no', $voucher->number)->delete();

        WaBanktran::where('document_no', $voucher->number)->delete();

        WaGlTran::where('transaction_no', $voucher->number)->delete();

        // Older vouchers
        WaSuppTran::where('document_no', $voucher->withholdingFile->file_no)->delete();

        WaBanktran::where('document_no', $voucher->withholdingFile->file_no)->delete();

        WaGlTran::where('transaction_no', $voucher->withholdingFile->file_no)->delete();

        $voucher->delete();

        Session::flash('success', 'Payment voucher deleted successfully');

        return redirect()->route('withholding-tax-payments.index');
    }

    public function print($voucherId)
    {
        $voucher = WithholdingPaymentVoucher::query()
            ->with([
                'withholdingFile',
            ])
            ->findOrFail($voucherId);

        $pdf = Pdf::loadView('admin.withholding_files_payments.print', compact('voucher'));

        return $pdf->stream('withholding_payment_voucher_' . date('Y-m-d-H-i-s') . '.pdf');
    }

    public function approve(WithholdingPaymentVoucher $voucher)
    {
        if (!can('approve', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        DB::beginTransaction();

        try {
            app(ProcessWithholding::class)->process($voucher);

            $voucher->update([
                'status' => $voucher::APPROVED,
            ]);

            DB::commit();

            Session::flash('success', 'Payment voucher approved successfully');

            return redirect()->route('withholding-tax-payments.index');
        } catch (\Throwable $th) {

            DB::rollBack();

            Session::flash('error',  $th->getMessage());

            return redirect()->route('withholding-tax-payments.index');
        }
    }
}
