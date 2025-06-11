<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\TaxManager;
use App\Model\WaAccountGroup;
use App\Model\WaAccountingPeriod;
use App\Model\WaAccountSection;
use App\Model\WaChartsOfAccount;
use App\Model\WaCompanyPreference;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Models\SupplierBill;
use App\Models\SupplierBillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SupplierBillController extends Controller
{
    protected $model = 'supplier-bills';

    protected $title = 'Supplier Bills';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        if (request()->wantsJson()) {

            $from = request()->filled('from') ? request()->from . ' 00:00:00' : false;
            $to = request()->filled('to') ? request()->to . ' 23:59:59' : false;

            $query = SupplierBill::query()
                ->select([
                    'supplier_bills.*',
                    'voucher_items.payment_voucher_id',
                    'vouchers.number as voucher_no',
                ])
                ->with('supplier', 'location', 'items.account')
                ->leftJoin('payment_voucher_items as voucher_items', function ($join) {
                    $join->on('voucher_items.payable_id', '=', 'supplier_bills.id')
                        ->where('voucher_items.payable_type', '=', 'supplier_bill');
                })
                ->leftJoin('payment_vouchers as vouchers', function ($join) {
                    $join->on('vouchers.id', '=', 'voucher_items.payment_voucher_id');
                })
                ->when(request()->filled('supplier'), function ($query) {
                    return $query->where('supplier_bills.wa_supplier_id', request()->supplier);
                })->when(request()->filled('payment'), function ($query) {
                    if (request()->payment == 'paid') {
                        return $query->whereNotNull('payment_voucher_id');
                    }
                    $query->whereNull('payment_voucher_id');
                })->when($from && $to, function ($query) use ($from, $to) {
                    return $query->whereBetween('supplier_bills.created_at', [$from, $to]);
                });


            return DataTables::eloquent($query)
                ->editColumn('status', function ($bill) {
                    return $bill->status ? 'Paid' : 'Unpaid';
                })
                ->editColumn('tax_amount', function ($bill) {
                    return manageAmountFormat($bill->tax_amount);
                })
                ->editColumn('amount', function ($bill) {
                    return manageAmountFormat($bill->amount);
                })
                ->addColumn('action', function ($bill) {
                    return view('admin.supplier-bills.action', compact('bill'));
                })
                ->toJson();
        }

        return view('admin.supplier-bills.index', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => '',
            ],
            'suppliers' => WaSupplier::get(),
        ]);
    }

    public function create()
    {
        if (!can('create', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $expenses = WaAccountSection::where('section_name', 'EXPENSES')->first();
        $groups = WaAccountGroup::where('wa_account_section_id', $expenses->id)->get();
        $accounts = WaChartsOfAccount::where('wa_account_group_id', $groups->pluck('id')->toArray())->get();

        return view('admin.supplier-bills.create', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => route('supplier-bills.index'),
                'Create' => '',
            ],
            'suppliers' => WaSupplier::get(),
            'branches' => Restaurant::get(),
            'accounts' => $accounts,
            'vat_taxes' => TaxManager::get()
        ]);
    }

    public function store(Request $request)
    {
        if (!can('create', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'supplier' => 'required',
            'bill_date' => 'required',
            'location' => 'required',
            'memo' => 'required',
            'file' => 'mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'cu_invoice_number' => 'required|unique:wa_supp_trans,cu_invoice_number|unique:financial_notes,cu_invoice_number',
            'supplier_invoice_number' => 'required|unique:financial_notes,supplier_invoice_number',
        ]);

        try {
            DB::beginTransaction();

            $user = getLoggeduserProfile();
            $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
            $companyPreference = WaCompanyPreference::with(['creditorControlGlAccount'])->where('id', '1')->first();
            $creaditorAccountGl = $companyPreference->creditorControlGlAccount;
            $accounts = [];

            foreach ($request->accounts as $item) {
                $accounts[] = (object)$item;
            }

            $supplier = WaSupplier::findOrFail($request->supplier);

            $accounts = collect($accounts);
            $billTotal = $accounts->sum('amount');
            $accounts->map(function ($account) use ($supplier) {
                $taxManager = TaxManager::find($account->tax_manager);
                $account->input_tax_gl_account = $taxManager->input_tax_gl_account;
                $account->tax_rate = $taxManager->tax_value;
                $account->tax_amount = $vat = $account->amount * $taxManager->tax_value / 100;
                $account->withholding_amount = $supplier->tax_withhold ? ceil($vat * (2 / 16)) : 0;

                return $account;
            });

            $supplierBill = SupplierBill::create([
                'bill_no' => getCodeWithNumberSeries('SUPPLIER_BILLS'),
                'wa_supplier_id' => $request->supplier,
                'bill_date' => $request->bill_date,
                'location_id' => $request->location,
                'cu_invoice_number' => $request->cu_invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'memo' => $request->memo,
                'tax_amount' => $taxAmount = $accounts->sum('tax_amount'),
                'withholding_amount' => $accounts->sum('withholding_amount'),
                'amount' => $billTotal + $taxAmount,
                'created_by' => $user->id,
            ]);

            $path = 'uploads/supplier_bills';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $fileName);

                $supplierBill->file_name = $fileName;
                $supplierBill->save();
            }

            $document_no = $supplierBill->bill_no;
            $series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_BILLS')->first();

            $newSupplierTrans = new WaSuppTran();
            $newSupplierTrans->document_no = $document_no;
            $newSupplierTrans->trans_date = $supplierBill->created_at;
            $newSupplierTrans->suppreference = $supplierBill->supplier_invoice_number;
            $newSupplierTrans->cu_invoice_number = $supplierBill->cu_invoice_number;
            $newSupplierTrans->supplier_no = $supplierBill->supplier->supplier_code;
            $newSupplierTrans->grn_type_number = $series_module->type_number;
            $newSupplierTrans->vat_amount = $supplierBill->tax_amount;
            $newSupplierTrans->total_amount_inc_vat = $supplierBill->amount;
            $newSupplierTrans->save();

            $accountGl = new WaGlTran();
            $accountGl->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
            $accountGl->grn_type_number = $series_module->type_number;
            $accountGl->trans_date = $supplierBill->created_at;
            $accountGl->wa_supp_tran_id = $newSupplierTrans->id;
            $accountGl->restaurant_id = $supplierBill->location->id;
            $accountGl->tb_reporting_branch = $supplierBill->location->id;
            $accountGl->grn_last_used_number = $series_module->last_number_used;
            $accountGl->transaction_type = $series_module->description;
            $accountGl->transaction_no = $supplierBill->bill_no;
            $accountGl->narrative = "Supplier bill - $supplierBill->bill_no";
            $accountGl->account = $creaditorAccountGl->account_code;
            $accountGl->supplier_account_number = $supplierBill->supplier->supplier_code;
            $accountGl->user_id = $user->id;
            $accountGl->amount =  $supplierBill->amount * -1;
            $accountGl->reference = $supplierBill->supplier_invoice_number;
            $accountGl->save();

            foreach ($accounts as $account) {
                $supplierBillItem = SupplierBillItem::create([
                    'supplier_bill_id' => $supplierBill->id,
                    'account_id' => $account->id,
                    'memo' => $account->memo,
                    'tax_rate' => $account->tax_rate,
                    'tax_amount' => $account->tax_amount,
                    'withholding_amount' => $account->withholding_amount,
                    'amount' => $account->amount,
                ]);

                $accountGl = new WaGlTran();
                $accountGl->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $accountGl->grn_type_number = $series_module->type_number;
                $accountGl->trans_date = $supplierBill->created_at;
                $accountGl->wa_supp_tran_id = $newSupplierTrans->id;
                $accountGl->restaurant_id = $supplierBill->location->id;
                $accountGl->tb_reporting_branch = $supplierBill->location->id;
                $accountGl->grn_last_used_number = $series_module->last_number_used;
                $accountGl->transaction_type = $series_module->description;
                $accountGl->transaction_no = $supplierBill->bill_no;
                $accountGl->narrative = "Supplier Bill - $supplierBill->bill_no";
                $accountGl->account = WaChartsOfAccount::find($account->id)->account_code;
                $accountGl->supplier_account_number = $supplierBill->supplier->supplier_code;
                $accountGl->user_id = $user->id;
                $accountGl->amount = $account->amount;
                $accountGl->reference = $supplierBill->supplier_invoice_number;
                $accountGl->save();

                // Pay Tax
                if ($supplierBillItem->tax_amount > 0) {
                    $taxGl = new WaGlTran();
                    $taxGl->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $taxGl->grn_type_number = $series_module->type_number;
                    $taxGl->trans_date = $supplierBill->created_at;
                    $taxGl->restaurant_id = $supplierBill->location->id;
                    $taxGl->tb_reporting_branch = $supplierBill->location->id;
                    $taxGl->grn_last_used_number = $series_module->last_number_used;
                    $taxGl->transaction_type = $series_module->description;
                    $taxGl->transaction_no = $supplierBill->bill_no;
                    $taxGl->narrative = "Supplier Bill - $supplierBill->bill_no";
                    $taxGl->account = WaChartsOfAccount::find($account->input_tax_gl_account)->account_code;
                    $taxGl->wa_supp_tran_id = $newSupplierTrans->id;
                    $taxGl->supplier_account_number = $supplierBill->supplier->supplier_code;
                    $taxGl->user_id = $user->id;
                    $taxGl->amount = $account->tax_amount;
                    $taxGl->reference = $supplierBill->supplier_invoice_number;
                    $taxGl->save();
                }
            }

            updateUniqueNumberSeries('SUPPLIER_BILLS', $supplierBill->bill_no);

            DB::commit();

            Session::flash('success', 'Supplier bill created successfully.');

            return redirect()->route("$this->model.index");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route("$this->model.index")->withErrors(['An error occurred' . $e->getMessage()]);
        }
    }

    public function edit(SupplierBill $supplierBill)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $expenses = WaAccountSection::where('section_name', 'EXPENSES')->first();
        $groups = WaAccountGroup::where('wa_account_section_id', $expenses->id)->get();
        $accounts = WaChartsOfAccount::where('wa_account_group_id', $groups->pluck('id')->toArray())->get();

        return view("admin.supplier-bills.edit", [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' =>  [
                'Supplier Bills' => route($this->model . '.index'),
                'Edit' => '',
            ],
            'suppliers' =>  WaSupplier::select('id', 'name', 'telephone', 'email', 'address')->get(),
            'branches' => Restaurant::get(),
            'vat_taxes' => TaxManager::get(),
            'accounts' =>  $accounts,
            'bill' => $supplierBill
        ]);
    }

    public function update(Request $request, SupplierBill $supplierBill)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'supplier' => 'required',
            'bill_date' => 'required',
            'location' => 'required',
            'memo' => 'required',
            'cu_invoice_number' => [
                'required',
                Rule::unique('financial_notes')->ignore($supplierBill->id),
                Rule::unique('wa_supp_trans')->ignore($supplierBill->bill_no, 'document_no'),
            ],
            'supplier_invoice_number' => 'required',
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

            $supplierBill->update([
                'wa_supplier_id' => $request->supplier,
                'bill_date' => $request->notbill_datee_date,
                'location_id' => $request->location,
                'cu_invoice_number' => $request->cu_invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'memo' => $request->memo,
                'tax_amount' => $taxAmount = $accounts->sum('tax_amount'),
                'withholding_amount' => $accounts->sum('withholding_amount'),
                'amount' => $noteTotal + $taxAmount,
                'created_by' => $user->id,
            ]);

            $path = 'uploads/supplier_bills';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $fileName);

                $supplierBill->file_name = $fileName;
                $supplierBill->save();
            }

            $suppTran = $supplierBill->suppTran;
            $suppTran->suppreference = $supplierBill->supplier_invoice_number;
            $suppTran->cu_invoice_number = $supplierBill->cu_invoice_number;
            $suppTran->vat_amount = $supplierBill->tax_amount;
            $suppTran->total_amount_inc_vat = $supplierBill->amount;
            $suppTran->save();

            // Remove previous transactions
            $supplierBill->items()->delete();
            $supplierBill->glTransactions()->delete();
            $series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_BILLS')->first();

            $accountGl = new WaGlTran();
            $accountGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
            $accountGl->grn_type_number = $series_module->type_number;
            $accountGl->trans_date = $supplierBill->created_at;
            $accountGl->wa_supp_tran_id = $suppTran->id;
            $accountGl->restaurant_id = $supplierBill->location->id;
            $accountGl->tb_reporting_branch = $supplierBill->location->id;
            $accountGl->grn_last_used_number = $series_module->last_number_used;
            $accountGl->transaction_type = $series_module->description;
            $accountGl->transaction_no = $supplierBill->bill_no;
            $accountGl->narrative = "Supplier bill - $supplierBill->bill_no";
            $accountGl->account = $creaditorAccountGl->account_code;
            $accountGl->supplier_account_number = $supplierBill->supplier->supplier_code;
            $accountGl->user_id = $user->id;
            $accountGl->amount = $supplierBill->amount * -1;
            $accountGl->reference = $supplierBill->supplier_invoice_number;
            $accountGl->save();

            foreach ($accounts as $account) {
                $supplierBillItem = SupplierBillItem::create([
                    'supplier_bill_id' => $supplierBill->id,
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
                $accountGl->trans_date = $supplierBill->created_at;
                $accountGl->wa_supp_tran_id = $suppTran->id;
                $accountGl->restaurant_id = $supplierBill->location->id;
                $accountGl->tb_reporting_branch = $supplierBill->location->id;
                $accountGl->grn_last_used_number = $series_module->last_number_used;
                $accountGl->transaction_type = $series_module->description;
                $accountGl->transaction_no = $supplierBill->bill_no;
                $accountGl->narrative = "Supplier Bill - $supplierBill->bill_no";
                $accountGl->account =  WaChartsOfAccount::find($account->id)->account_code;
                $accountGl->supplier_account_number = $supplierBill->supplier->supplier_code;
                $accountGl->user_id = $user->id;
                $accountGl->amount = $account->amount;
                $accountGl->reference = $supplierBill->supplier_invoice_number;
                $accountGl->save();

                // Pay Tax
                if ($supplierBillItem->tax_amount > 0) {
                    $taxGl = new WaGlTran();
                    $taxGl->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                    $taxGl->grn_type_number = $series_module->type_number;
                    $taxGl->trans_date = $supplierBill->created_at;
                    $taxGl->restaurant_id = $supplierBill->location->id;
                    $taxGl->tb_reporting_branch = $supplierBill->location->id;
                    $taxGl->grn_last_used_number = $series_module->last_number_used;
                    $taxGl->transaction_type = $series_module->description;
                    $taxGl->transaction_no = $supplierBill->bill_no;
                    $taxGl->narrative = "Supplier bill - $supplierBill->bill_no";
                    $taxGl->account = WaChartsOfAccount::find($account->input_tax_gl_account)->account_code;
                    $taxGl->wa_supp_tran_id = $suppTran->id;
                    $taxGl->supplier_account_number = $supplierBill->supplier->supplier_code;
                    $taxGl->user_id = $user->id;
                    $taxGl->amount = $account->tax_amount;
                    $taxGl->reference = $supplierBill->supplier_invoice_number;
                    $taxGl->save();
                }
            }

            DB::commit();

            Session::flash('success', 'Supplier bill updated successfully.');

            return redirect()->route("$this->model.index");
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());

            return redirect()->route("$this->model.index")->withErrors(['An error occurred' . $e->getMessage()]);
        }
    }

    public function destroy(SupplierBill $supplierBill)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        if ($supplierBill->payment()->exists()) {
            return redirect()->back()->withErrors(['errors' => "The bill is already paid it cannot be deleted"]);
        }

        DB::beginTransaction();

        try {
            WaSuppTran::where('document_no', $supplierBill->bill_no)->delete();

            WaGlTran::where('transaction_no', $supplierBill->bill_no)->delete();

            $supplierBill->items()->delete();
            $supplierBill->delete();

            DB::commit();

            Session::flash('success', "Bill deleted successfully");

            return redirect()->route("$this->model.index");
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['errors' => $th->getMessage()]);
        }
    }
}
