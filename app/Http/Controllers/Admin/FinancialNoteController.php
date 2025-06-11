<?php

namespace App\Http\Controllers\Admin;

use App\FinancialNote;
use App\FinancialNoteItem;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\TaxManager;
use App\Model\WaAccountingPeriod;
use App\Model\WaChartsOfAccount;
use App\Model\WaCompanyPreference;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Models\UserGeneralLedgerAccount;
use App\WaSupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class FinancialNoteController extends Controller
{
    protected $model = "credit-debit-notes";

    protected $title = "Credit/Debit Notes";

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : false;
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : false;

        if (request()->wantsJson()) {
            $query = FinancialNote::query()
                ->select([
                    'financial_notes.*',
                    'trans.suppreference',
                    'voucher_items.payment_voucher_id',
                    'vouchers.number as voucher_no',
                ])
                ->with('supplier', 'location', 'items.account')
                ->leftJoin('wa_supp_trans as trans', 'trans.id', 'financial_notes.wa_supp_tran_id')
                ->leftJoin('payment_voucher_items as voucher_items', function ($join) {
                    $join->on('voucher_items.payable_id', '=', 'trans.id')
                        ->where('voucher_items.payable_type', '=', 'invoice');
                })
                ->leftJoin('payment_vouchers as vouchers', function ($join) {
                    $join->on('vouchers.id', '=', 'voucher_items.payment_voucher_id');
                })
                ->when(request()->filled('supplier'), function ($query) {
                    return $query->where('supplier_id', request()->supplier);
                })->when(request()->filled('allocation'), function ($query) {
                    if (request()->allocation == 'allocated') {
                        return $query->whereNotNull('wa_supp_tran_id');
                    }
                    $query->whereNull('wa_supp_tran_id');
                })->when(request()->filled('payment'), function ($query) {
                    if (request()->payment == 'paid') {
                        return $query->whereNotNull('payment_voucher_id');
                    }
                    $query->whereNull('payment_voucher_id');
                })->when($from && $to, function ($query) use ($from, $to) {
                    return $query->whereBetween('financial_notes.created_at', [$from, $to]);
                });

            return DataTables::eloquent($query)
                ->editColumn('status', function ($note) {
                    return $note->status ? 'Allocated' : 'Unallocated';
                })
                ->editColumn('tax_amount', function ($note) {
                    return manageAmountFormat($note->tax_amount);
                })
                ->editColumn('amount', function ($note) {
                    return manageAmountFormat($note->amount);
                })
                ->addColumn('action', function ($note) {
                    return view('admin.credit_debit_notes.action', [
                        'note' => $note
                    ]);
                })
                ->toJson();
        }

        return view("admin.credit_debit_notes.index", [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                'Credit/Debit Notes' => route('credit-debit-notes.index')
            ],
            'suppliers' => WaSupplier::get()
        ]);
    }

    public function create()
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $accountuserids = UserGeneralLedgerAccount::where('user_id', auth()->user()->id)->get()->pluck('account_id');

        return view("admin.credit_debit_notes.create", [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' =>  [
                'Credit/Debit Notes' => route($this->model . '.index'),
                'Create' => route($this->model . '.create'),
            ],
            'suppliers' =>  WaSupplier::select('id', 'name', 'telephone', 'email', 'address')->get(),
            'branches' => Restaurant::get(),
            'accounts' =>  WaChartsOfAccount::whereIn('id', $accountuserids)->get(),
            'vat_taxes' => TaxManager::get()
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'supplier' => 'required',
            'type' => 'required',
            'note_date' => 'required',
            'location' => 'required',
            'memo' => 'required',
            'file' => 'mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'cu_invoice_number' => 'required|unique:wa_supp_trans,cu_invoice_number|unique:financial_notes,cu_invoice_number',
            'supplier_invoice_number' => 'required|unique:financial_notes,supplier_invoice_number',
        ]);

        try {
            DB::beginTransaction();

            $user = getLoggeduserProfile();
            $accountuingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
            $companyPreference = WaCompanyPreference::with(['creditorControlGlAccount'])->where('id', '1')->first();
            $creaditorAccountGl = $companyPreference->creditorControlGlAccount;
            $accounts = [];

            foreach ($request->accounts as $item) {
                $accounts[] = (object)$item;
            }

            $supplier = WaSupplier::findOrFail($request->supplier);

            $accounts = collect($accounts);
            $noteTotal = $accounts->sum('amount');
            $accounts->map(function ($account) use ($supplier) {
                $taxManager = TaxManager::find($account->tax_manager);
                $account->input_tax_gl_account = $taxManager->input_tax_gl_account;
                $account->tax_rate = $taxManager->tax_value;
                $account->tax_amount = $vat = $account->amount * $taxManager->tax_value / 100;
                $account->withholding_amount = $supplier->tax_withhold ? ceil($vat * (2 / 16)) : 0;

                return $account;
            });

            $financialNote = FinancialNote::create([
                'note_no' => getCodeWithNumberSeries('FINANCIAL_NOTES'),
                'type' => $request->type,
                'supplier_id' => $request->supplier,
                'note_date' => $request->note_date,
                'location_id' => $request->location,
                'cu_invoice_number' => $request->cu_invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'memo' => $request->memo,
                'tax_amount' => $taxAmount = $accounts->sum('tax_amount'),
                'withholding_amount' => $accounts->sum('withholding_amount'),
                'amount' => $noteTotal + $taxAmount,
                'created_by' => $user->id,
            ]);

            $path = 'uploads/financial_notes';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $fileName);

                $financialNote->file_name = $fileName;
                $financialNote->save();
            }

            $document_no = $financialNote->note_no;
            $series_module = WaNumerSeriesCode::where('module', 'FINANCIAL_NOTES')->first();

            $newSupplierTrans = new WaSuppTran();
            $newSupplierTrans->document_no = $document_no;
            $newSupplierTrans->trans_date = $financialNote->created_at;
            $newSupplierTrans->suppreference = $financialNote->supplier_invoice_number;
            $newSupplierTrans->cu_invoice_number = $financialNote->cu_invoice_number;
            $newSupplierTrans->supplier_no = $financialNote->supplier->supplier_code;
            $newSupplierTrans->grn_type_number = $series_module->type_number;
            $newSupplierTrans->vat_amount = $financialNote->tax_amount;
            $newSupplierTrans->total_amount_inc_vat = $financialNote->type == 'DEBIT' ? $financialNote->amount : '-' . $financialNote->amount;
            $newSupplierTrans->save();

            $accountGl = new WaGlTran();
            $accountGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
            $accountGl->grn_type_number = $series_module->type_number;
            $accountGl->trans_date = $financialNote->created_at;
            $accountGl->wa_supp_tran_id = $newSupplierTrans->id;
            $accountGl->restaurant_id = $financialNote->location->id;
            $accountGl->tb_reporting_branch = $financialNote->location->id;
            $accountGl->grn_last_used_number = $series_module->last_number_used;
            $accountGl->transaction_type = $series_module->description;
            $accountGl->transaction_no = $financialNote->note_no;
            $accountGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
            $accountGl->account = $creaditorAccountGl->account_code;
            $accountGl->supplier_account_number = $financialNote->supplier->supplier_code;
            $accountGl->user_id = $user->id;
            $accountGl->amount = $financialNote->type == 'DEBIT' ? '-' . $financialNote->amount : $financialNote->amount;
            $accountGl->reference = $financialNote->supplier_invoice_number;
            $accountGl->save();

            foreach ($accounts as $account) {
                $financialNoteItem = FinancialNoteItem::create([
                    'financial_note_id' => $financialNote->id,
                    'account_id' => $account->id,
                    'memo' => $account->memo,
                    'tax_rate' => $account->tax_rate,
                    'tax_amount' => $account->tax_amount,
                    'withholding_amount' => $account->withholding_amount,
                    'amount' => $account->amount,
                ]);

                $accountGl = new WaGlTran();
                $accountGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $accountGl->grn_type_number = $series_module->type_number;
                $accountGl->trans_date = $financialNote->created_at;
                $accountGl->wa_supp_tran_id = $newSupplierTrans->id;
                $accountGl->restaurant_id = $financialNote->location->id;
                $accountGl->tb_reporting_branch = $financialNote->location->id;
                $accountGl->grn_last_used_number = $series_module->last_number_used;
                $accountGl->transaction_type = $series_module->description;
                $accountGl->transaction_no = $financialNote->note_no;
                $accountGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
                $accountGl->account = WaChartsOfAccount::find($account->id)->account_code;
                $accountGl->supplier_account_number = $financialNote->supplier->supplier_code;
                $accountGl->user_id = $user->id;
                $accountGl->amount = $financialNote->type == 'DEBIT' ?  $account->amount : '-' . $account->amount;
                $accountGl->reference = $financialNote->supplier_invoice_number;
                $accountGl->save();

                // Pay Tax
                if ($financialNoteItem->tax_amount > 0) {
                    $taxGl = new WaGlTran();
                    $taxGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                    $taxGl->grn_type_number = $series_module->type_number;
                    $taxGl->trans_date = $financialNote->created_at;
                    $taxGl->restaurant_id = $financialNote->location->id;
                    $taxGl->tb_reporting_branch = $financialNote->location->id;
                    $taxGl->grn_last_used_number = $series_module->last_number_used;
                    $taxGl->transaction_type = $series_module->description;
                    $taxGl->transaction_no = $financialNote->note_no;
                    $taxGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
                    $taxGl->account = WaChartsOfAccount::find($account->input_tax_gl_account)->account_code;
                    $taxGl->wa_supp_tran_id = $newSupplierTrans->id;
                    $taxGl->supplier_account_number = $financialNote->supplier->supplier_code;
                    $taxGl->user_id = $user->id;
                    $taxGl->amount = $financialNote->type == 'DEBIT' ? $account->tax_amount : '-' . $account->tax_amount;
                    $taxGl->reference = $financialNote->supplier_invoice_number;
                    $taxGl->save();
                }
            }

            updateUniqueNumberSeries('FINANCIAL_NOTES', $financialNote->note_no);

            DB::commit();

            Session::flash('success', 'Payment voucher created successfully.');

            return redirect()->route("$this->model.index");
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());

            return redirect()->route("$this->model.index")->withErrors(['An error occurred' . $e->getMessage()]);
        }
    }

    public function edit(FinancialNote $note)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view("admin.credit_debit_notes.edit", [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' =>  [
                'Credit/Debit Notes' => route($this->model . '.index'),
                'Create' => route($this->model . '.create'),
            ],
            'suppliers' =>  WaSupplier::select('id', 'name', 'telephone', 'email', 'address')->get(),
            'branches' => Restaurant::get(),
            'accounts' =>  WaChartsOfAccount::get(),
            'vat_taxes' => TaxManager::get(),
            'note' => $note
        ]);
    }

    public function update(Request $request, FinancialNote $note)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $financialNote = $note;

        $this->validate($request, [
            'supplier' => 'required',
            'type' => 'required',
            'note_date' => 'required',
            'location' => 'required',
            'memo' => 'required',
            'cu_invoice_number' => [
                'required',
                Rule::unique('financial_notes')->ignore($financialNote->id),
                Rule::unique('wa_supp_trans')->ignore($financialNote->note_no, 'document_no'),
            ],
            'supplier_invoice_number' => 'required',
        ]);

        if ($note->hasPayment() || $note->isReturnDocument() || $note->isDiscountDocument()) {
            $financialNote->update([
                'note_date' => $request->note_date,
                'cu_invoice_number' => $request->cu_invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'memo' => $request->memo,               
            ]);

            Session::flash('success', 'Payment voucher updated successfully.');

            return redirect()->route("$this->model.index");
        }        

        try {
            DB::beginTransaction();

            $user = getLoggeduserProfile();
            $accountuingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
            $companyPreference = WaCompanyPreference::with(['creditorControlGlAccount'])->where('id', '1')->first();
            $creaditorAccountGl = $companyPreference->creditorControlGlAccount;
            $accounts = [];

            foreach ($request->accounts as $item) {
                $accounts[] = (object)$item;
            }

            $supplier = WaSupplier::findOrFail($request->supplier);

            $accounts = collect($accounts);
            $noteTotal = $accounts->sum('amount');
            $accounts->map(function ($account) use ($supplier) {
                $taxManager = TaxManager::find($account->tax_manager);
                $account->input_tax_gl_account = $taxManager->input_tax_gl_account;
                $account->tax_rate = $taxManager->tax_value;
                $account->tax_amount = $vat = $account->amount * $taxManager->tax_value / 100;
                $account->withholding_amount = $supplier->tax_withhold ? ceil($vat * (2 / 16)) : 0;

                return $account;
            });

            $financialNote->update([
                'type' => $request->type,
                'supplier_id' => $request->supplier,
                'note_date' => $request->note_date,
                'location_id' => $request->location,
                'cu_invoice_number' => $request->cu_invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'memo' => $request->memo,
                'tax_amount' => $taxAmount = $accounts->sum('tax_amount'),
                'withholding_amount' => $accounts->sum('withholding_amount'),
                'amount' => $noteTotal + $taxAmount,
                'created_by' => $user->id,
            ]);

            $path = 'uploads/financial_notes';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $fileName);

                $financialNote->file_name = $fileName;
                $financialNote->save();
            }

            $suppTran = $financialNote->suppTran;
            $suppTran->suppreference = $financialNote->supplier_invoice_number;
            $suppTran->cu_invoice_number = $financialNote->cu_invoice_number;
            $suppTran->vat_amount = $financialNote->tax_amount;
            $suppTran->total_amount_inc_vat = $financialNote->type == 'DEBIT' ? $financialNote->amount : '-' . $financialNote->amount;
            $suppTran->save();

            // Remove previous transactions
            $financialNote->items()->delete();
            $financialNote->glTransactions()->delete();
            $series_module = WaNumerSeriesCode::where('module', 'FINANCIAL_NOTES')->first();

            $accountGl = new WaGlTran();
            $accountGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
            $accountGl->grn_type_number = $series_module->type_number;
            $accountGl->trans_date = $financialNote->created_at;
            $accountGl->wa_supp_tran_id = $suppTran->id;
            $accountGl->restaurant_id = $financialNote->location->id;
            $accountGl->tb_reporting_branch = $financialNote->location->id;
            $accountGl->grn_last_used_number = $series_module->last_number_used;
            $accountGl->transaction_type = $series_module->description;
            $accountGl->transaction_no = $financialNote->note_no;
            $accountGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
            $accountGl->account = $creaditorAccountGl->account_code;
            $accountGl->supplier_account_number = $financialNote->supplier->supplier_code;
            $accountGl->user_id = $user->id;
            $accountGl->amount = $financialNote->type == 'DEBIT' ? '-' . $financialNote->amount : $financialNote->amount;
            $accountGl->reference = $financialNote->supplier_invoice_number;
            $accountGl->save();


            foreach ($accounts as $account) {
                $financialNoteItem = FinancialNoteItem::create([
                    'financial_note_id' => $financialNote->id,
                    'account_id' => $account->id,
                    'memo' => $account->memo,
                    'tax_rate' => $account->tax_rate,
                    'tax_amount' => $account->tax_amount,
                    'withholding_amount' => $account->withholding_amount,
                    'amount' => $account->amount,
                ]);

                $accountGl = new WaGlTran();
                $accountGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $accountGl->grn_type_number = $series_module->type_number;
                $accountGl->trans_date = $financialNote->created_at;
                $accountGl->wa_supp_tran_id = $suppTran->id;
                $accountGl->restaurant_id = $financialNote->location->id;
                $accountGl->tb_reporting_branch = $financialNote->location->id;
                $accountGl->grn_last_used_number = $series_module->last_number_used;
                $accountGl->transaction_type = $series_module->description;
                $accountGl->transaction_no = $financialNote->note_no;
                $accountGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
                $accountGl->account =  WaChartsOfAccount::find($account->id)->account_code;
                $accountGl->supplier_account_number = $financialNote->supplier->supplier_code;
                $accountGl->user_id = $user->id;
                $accountGl->amount = $financialNote->type == 'DEBIT' ? $account->amount : $account->amount * -1;
                $accountGl->reference = $financialNote->supplier_invoice_number;
                $accountGl->save();

                // Pay Tax
                if ($financialNoteItem->tax_amount > 0) {
                    $taxGl = new WaGlTran();
                    $taxGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                    $taxGl->grn_type_number = $series_module->type_number;
                    $taxGl->trans_date = $financialNote->created_at;
                    $taxGl->restaurant_id = $financialNote->location->id;
                    $taxGl->tb_reporting_branch = $financialNote->location->id;
                    $taxGl->grn_last_used_number = $series_module->last_number_used;
                    $taxGl->transaction_type = $series_module->description;
                    $taxGl->transaction_no = $financialNote->note_no;
                    $taxGl->narrative = "Supplier $financialNote->type note - $financialNote->note_no";
                    $taxGl->account = WaChartsOfAccount::find($account->input_tax_gl_account)->account_code;
                    $taxGl->wa_supp_tran_id = $suppTran->id;
                    $taxGl->supplier_account_number = $financialNote->supplier->supplier_code;
                    $taxGl->user_id = $user->id;
                    $taxGl->amount = $financialNote->type == 'DEBIT' ?  $account->tax_amount : $account->tax_amount * -1;
                    $taxGl->reference = $financialNote->supplier_invoice_number;
                    $taxGl->save();
                }
            }

            DB::commit();

            Session::flash('success', 'Payment voucher updated successfully.');

            return redirect()->route("$this->model.index");
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());

            return redirect()->route("$this->model.index")->withErrors(['An error occurred' . $e->getMessage()]);
        }
    }

    public function allocate(Request $request)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'note' => 'required',
            'invoice' => 'required',
        ]);

        $note = FinancialNote::findOrFail($request->note);
        $note->update([
            'wa_supp_tran_id' => $request->input('invoice'),
            'status' => 1
        ]);

        Session::flash('success', 'Payment voucher allocated successfully.');

        return redirect()->route("$this->model.index");
    }

    public function deallocate(FinancialNote $note)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $note->update([
            'wa_supp_tran_id' => null,
            'status' => 0
        ]);

        Session::flash('success', 'Payment voucher deallocated successfully.');

        return redirect()->route("$this->model.index");
    }

    public function destroy(FinancialNote $note)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $type = $note->type;

        if ($note->hasPayment()) {
            return redirect()->route("$this->model.index")
                ->withErrors(['errors' => "The $type note cannot be deleted"]);
        }

        if ($note->isReturnDocument()) {
            return redirect()->route("$this->model.index")
                ->withErrors(['errors' => "The $type note cannot be deleted"]);
        }

        DB::beginTransaction();

        try {
            WaSuppTran::where('document_no', $note->note_no)->delete();

            WaGlTran::where('transaction_no', $note->note_no)->delete();

            $note->items()->delete();
            $note->delete();

            DB::commit();

            Session::flash('success', "$type note deleted successfully");

            return redirect()->route("$this->model.index");
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['errors' => $th->getMessage()]);
        }

        Session::flash('success', 'Payment voucher deallocated successfully.');

        return redirect()->route("$this->model.index");
    }

    public function supplierInvoices()
    {
        return response()->json([
            "invoices" => WaSupplierInvoice::query()
                ->with('lpo')
                ->whereHas('suppTrans', function ($query) {
                    $query->doesntHave('payments');
                })
                ->where('supplier_id', request()->supplier)
                ->get()
        ]);
    }
}
