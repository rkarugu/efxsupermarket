<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaGlTran;
use App\Model\WaGrn;
use App\Model\WaLocationAndStore;
use App\Model\WaNumerSeriesCode;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Model\WaUnitOfMeasure;
use App\Models\AdvancePaymentAllocation;
use App\WaSupplierInvoice;
use App\WaSupplierInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DeliveryNoteInvoiceController extends Controller
{
    protected $title = 'Delivery Invoices';

    protected $model = 'delivery-notes-invoices';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $query = WaGrn::query()
            ->select([
                'wa_grns.id',
                'wa_grns.delivery_date',
                'wa_grns.grn_number',
                'wa_grns.supplier_invoice_no',
                'wa_grns.cu_invoice_number',
                'orders.purchase_no',
                'suppliers.name AS supplier_name',
                'users.name AS received_by',
                'locations.location_name',
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty") AS total_amount'),
            ])
            ->join('wa_purchase_orders AS orders', 'orders.id', 'wa_grns.wa_purchase_order_id')
            ->join('wa_suppliers AS suppliers', 'suppliers.id', 'orders.wa_supplier_id')
            ->join('wa_location_and_stores AS locations', 'locations.id', 'orders.wa_location_and_store_id')
            ->join('wa_stock_moves AS moves', function ($query) {
                $query->on('moves.stock_id_code', '=', 'wa_grns.item_code')
                    ->whereColumn('grn_number', '=', 'document_no');
            })
            ->join('users', 'users.id', 'moves.user_id')
            ->whereNotNull('orders.mother_lpo')
            ->whereDoesntHave('invoice')
            ->groupBy('wa_grns.grn_number');

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('total_amount', function ($grn) {
                    return manageAmountFormat($grn->total_amount);
                })
                ->addColumn('actions', function ($grn) {
                    return view('admin.delivery_notes_invoices.actions', compact('grn'));
                })
                ->toJson();
        }

        return view('admin.delivery_notes_invoices.index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                $this->title => ''
            ]
        ]);
    }

    public function create()
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $grn = WaGrn::with([
            'purchaseOrder'
        ])->where('grn_number', request()->grn)
            ->firstOrFail();

        return view('admin.delivery_notes_invoices.create', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                $this->title => route('delivery-notes-invoices.index'),
                'Create' => '',
            ],
            'grn' => $grn,
            'branches' => Restaurant::get(),
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::get(),
            'bins' => WaUnitOfMeasure::whereHas('get_uom_linked', function ($query) use ($grn) {
                $query->where('location_id', $grn->purchaseOrder->wa_location_and_store_id);
            })->get()->pluck('title', 'id')->toArray(),

        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'cu_invoice_number' => 'required|unique:wa_supplier_invoices,cu_invoice_number',
        ]);

        $grns = WaGrn::where('grn_number', $request->grn_number)->get();
        $grn = $grns->first();
        $order = WaPurchaseOrder::findOrFail($grns->first()->wa_purchase_order_id);
        $advance = $order->mother->advance;
        $dateTime = now();
        $series_module = $SUPPLIER_INVOICE_NO_series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_INVOICE_NO')->first();
        $roundVat = 0;
        $vat_amount = $order->purchaseOrderItems()->sum('vat_amount');
        $totalAmount = $order->purchaseOrderItems()->sum('total_cost_with_vat') -
            $order->purchaseOrderItems()->sum('other_discounts_total');
        $WaAccountingPeriod = \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();

        DB::beginTransaction();

        try {
            $suppTran = new WaSuppTran();
            $suppTran->grn_type_number = $SUPPLIER_INVOICE_NO_series_module->type_number;
            $suppTran->supplier_no = $order->supplier->supplier_code;
            $suppTran->suppreference = $request->supplier_invoice_number;
            $suppTran->trans_date = $dateTime;
            $suppTran->document_no = $request->supplier_invoice_number;
            $due_date_number = '1';
            if (isset($order->getSupplier) && $order->getSupplier->getPaymentTerm && $order->getSupplier->getPaymentTerm->due_after_given_month == '1') {
                $due_date_number = @$order->getSupplier->getPaymentTerm->days_in_following_months;
            }
            $suppTran->due_date = date('Y-m-d', strtotime($suppTran->trans_date . ' + ' . $due_date_number . ' days'));
            $suppTran->settled = '0';
            $suppTran->rate = '1';
            $suppTran->round_off = $roundVat;
            $suppTran->total_amount_inc_vat = $totalAmount;
            $suppTran->vat_amount = $vat_amount;
            $suppTran->wa_purchase_order_id = $order->id;
            $suppTran->cu_invoice_number = $request->cu_invoice_number;
            $suppTran->prepared_by = auth()->user()->id;
            $suppTran->save();

            $grns->each->update([
                'invoiced' => 1
            ]);

            $invoice = WaSupplierInvoice::create([
                'wa_purchase_order_id' => $order->id,
                'wa_supp_tran_id' => $suppTran->id,
                'grn_number' => $grn->grn_number,
                'grn_date' => $grn->created_at,
                'supplier_invoice_date' => $dateTime,
                'invoice_number' => getCodeWithNumberSeries('SUPPLIER_INVOICE_NO'),
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'cu_invoice_number' => $suppTran->cu_invoice_number,
                'supplier_id' => $order->getSupplier->id,
                'prepared_by' => $suppTran->prepared_by,
                'vat_amount' => $vat_amount,
                'amount' => $totalAmount,
            ]);

            updateUniqueNumberSeries('SUPPLIER_INVOICE_NO', $invoice->invoice_number);

            foreach ($order->getRelatedItem as $key => $value) {
                WaSupplierInvoiceItem::create([
                    'wa_supplier_invoice_id' => $invoice->id,
                    'code' => $value->getInventoryItemDetail->stock_id_code,
                    'description' => $value->getInventoryItemDetail->title,
                    'quantity' => $value->quantity,
                    'standart_cost_unit' => $value->order_price,
                    'discount_amount' => 0,
                    'vat_amount' =>  $value->vat_amount,
                    'amount' => $value->total_cost_with_vat,
                ]);
            }

            $suppTran->allocated_amount = $totalAmount;
            $suppTran->save();

            AdvancePaymentAllocation::create([
                'advance_payment_id' => $advance->id,
                'wa_supp_trans_id' => $suppTran->id,
                'amount' => $totalAmount,
            ]);

            $cr = new WaGlTran();
            $cr->wa_supp_tran_id = $suppTran->id;
            $cr->grn_type_number = $series_module->type_number;
            $cr->transaction_type = $series_module->description;
            $cr->transaction_no = $request->supplier_invoice_number;
            $cr->grn_last_used_number = $series_module->last_number_used;
            $cr->trans_date = $dateTime;
            $cr->restaurant_id = $order->restaurant_id;
            $cr->tb_reporting_branch = $order->restaurant_id;
            $cr->supplier_account_number = $order->supplier->supplier_code;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->account = $order->getBranch->getAssociateCompany->good_receive->account_code;
            $cr->amount = $totalAmount;
            $cr->narrative = $order->purchase_no . '/' . $order->supplier->supplier_code . '/' . $request->supplier_invoice_number;
            $cr->wa_purchase_order_id = $order->id;
            $cr->reference = $request->supplier_invoice_number;
            $cr->save();

            $dr = new WaGlTran();
            $dr->wa_supp_tran_id = $suppTran->id;
            $dr->grn_type_number = $series_module->type_number;
            $dr->transaction_type = $series_module->description;
            $dr->transaction_no = $request->supplier_invoice_number;
            $dr->grn_last_used_number = $series_module->last_number_used;
            $dr->trans_date = $dateTime;
            $dr->restaurant_id =  $order->restaurant_id;
            $dr->tb_reporting_branch =  $order->restaurant_id;
            $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $dr->supplier_account_number = $order->supplier->supplier_code;
            $dr->account = $order->getBranch->getAssociateCompany->creditorControlGlAccount->account_code;
            $dr->amount = '-' . $totalAmount;
            $dr->narrative =  $order->purchase_no . '/' . $order->supplier->supplier_code . '/' . $request->supplier_invoice_number;
            $dr->reference = $request->supplier_invoice_number;
            $dr->wa_purchase_order_id = $order->id;
            $dr->save();

            $order->invoiced = 'Yes';
            $order->save();


            DB::commit();

            Session::flash('success', 'Delivery invoice posted successfully');

            return redirect()->route('delivery-notes.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('errors', $e->getMessage());

            return redirect()
                ->route('delivery-notes.create', ['grn' => $grn->grn_number]);
        }
    }
}
