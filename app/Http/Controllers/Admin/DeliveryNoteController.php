<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\TaxManager;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaGrn;
use App\Model\WaLocationAndStore;
use App\Model\WaNumerSeriesCode;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Model\WaUnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DeliveryNoteController extends Controller
{
    protected $title = 'Delivery Notes';

    protected $model = 'delivery-notes';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $vatSub = WaPurchaseOrderItem::query()
            ->select([
                DB::raw('SUM(vat_amount)')
            ])
            ->whereColumn('wa_purchase_order_items.wa_purchase_order_id', 'wa_purchase_orders.id');

        $amountSub = WaPurchaseOrderItem::query()
            ->select([
                DB::raw('SUM(total_cost_with_vat) - SUM(other_discounts_total)')
            ])
            ->whereColumn('wa_purchase_order_items.wa_purchase_order_id', 'wa_purchase_orders.id');

        $query = WaPurchaseOrder::query()
            ->with([
                'branch',
                'storeLocation',
                'supplier',
                'user',
            ])
            ->selectSub($vatSub, 'vat_amount')
            ->selectSub($amountSub, 'total_amount')
            ->where('status', 'APPROVED')
            ->where('is_hide', 'No')
            ->where('advance_payment', true)
            ->where('supplier_accepted', true)
            ->whereNotNull('mother_lpo')
            ->doesntHave('grns');

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($order) {
                    return $order->created_at->format('Y-m-d');
                })
                ->editColumn('vat_amount', function ($order) {
                    return manageAmountFormat($order->vat_amount);
                })
                ->editColumn('total_amount', function ($order) {
                    return manageAmountFormat($order->total_amount);
                })
                ->addColumn('actions', function ($order) {
                    return view('admin.delivery_notes.actions', compact('order'));
                })
                ->toJson();
        }

        return view('admin.delivery_notes.index', [
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

        $order = WaPurchaseOrder::findOrFail(request()->order);

        return view('admin.delivery_notes.create', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                $this->title => route('delivery-notes.index'),
                'Create' => '',
            ],
            'order' => $order,
            'branches' => Restaurant::get(),
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::get(),
            'bins' => WaUnitOfMeasure::whereHas('get_uom_linked', function ($query) use ($order) {
                $query->where('location_id', $order->wa_location_and_store_id);
            })->get()->pluck('title', 'id')->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'supplier_invoice_no' => 'required',
            'cu_invoice_number' => 'required|unique:wa_grns',
            'vehicle_reg_no' => 'required',
            'receive_note_doc_no' => 'required',
        ]);

        $order = WaPurchaseOrder::where('purchase_no', $request->purchase_no)->firstOrFail();
        $vat_amount = $order->purchaseOrderItems()->sum('vat_amount');
        $totalAmount = $order->purchaseOrderItems()->sum('total_cost_with_vat') -
            $order->purchaseOrderItems()->sum('other_discounts_total');

        $grn_number = getCodeWithNumberSeries('GRN');
        $dateTime = date('Y-m-d H:i:s');
        $series_module = WaNumerSeriesCode::where('module', 'GRN')->first();
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

        DB::beginTransaction();

        try {
            $order->update([
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'receive_note_doc_no' => $request->receive_note_doc_no,
            ]);

            foreach ($order->purchaseOrderItems as $item) {
                $grn = new WaGrn();
                $grn->wa_purchase_order_item_id = $item->id;
                $grn->wa_purchase_order_id = $order->id;
                $grn->wa_supplier_id = $order->wa_supplier_id;
                $grn->grn_number =  $grn_number;
                $grn->item_code = $item->inventoryItem->stock_id_code;
                $grn->supplier_invoice_no = $request->supplier_invoice_no;
                $grn->cu_invoice_number = $request->cu_invoice_number;
                $grn->delivery_date = $dateTime;
                $grn->item_description = $item->inventoryItem->title;
                $grn->qty_received = $item->quantity;
                $grn->qty_invoiced = $item->quantity;
                $grn->standart_cost_unit = $item->order_price;
                $invoice_calculation = [
                    'order_price' => $item->order_price,
                    'vat_rate' => $item->vat_rate,
                    'qty' => $item->quantity,
                    'unit' => $item->get_unit_of_measure->title,
                    'total_dicsount' => $item->discount_amount + $item->other_discounts_total,
                ];
                $grn->invoice_info = json_encode($invoice_calculation);
                $grn->save();

                //move to stock moves start
                $stockMove = new WaStockMove();
                $stockMove->user_id = auth()->user()->id;
                $stockMove->wa_purchase_order_id = $order->id;
                $stockMove->restaurant_id = $order->restaurant_id;
                $stockMove->wa_location_and_store_id = $order->wa_location_and_store_id;
                $stockMove->wa_inventory_item_id = $item->inventoryItem->id;
                $stockMove->stock_id_code = $item->inventoryItem->stock_id_code;
                $stockMove->grn_type_number = $series_module->type_number;
                $stockMove->document_no = $grn_number;
                $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                $stockMove->price = $item->order_price;
                $stockMove->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $stockMove->refrence = ($order->supplier->supplier_code) . '/' . ($order->supplier->name) . '/' . $order->purchase_no;
                $stockMove->qauntity =  $item->quantity;
                $stockMove->standard_cost = $item->order_price;
                $stock_qoh = $item->inventoryItem->getAllFromStockMoves->where('wa_location_and_store_id', $order->wa_location_and_store_id)->sum('qauntity') ?? 0;
                $stock_qoh += $stockMove->qauntity;
                $stockMove->new_qoh = $stock_qoh;
                $stockMove->save();

                $dr =  new WaGlTran();
                $dr->grn_type_number = $series_module->type_number;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $grn_number;
                $dr->trans_date = $dateTime;
                $dr->restaurant_id = $order->restaurant_id;
                $dr->tb_reporting_branch = $order->restaurant_id;
                $dr->reference = $item->inventoryItem->stock_id_code;
                $dr->wa_purchase_order_id = $order->id;
                $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $dr->supplier_account_number = $order->supplier->supplier_code;
                $accountno = $item->inventoryItem->getInventoryCategoryDetail->getStockGlDetail->account_code;
                $dr->account = $accountno;

                $dr->amount = $item->total_cost; //Excl.
                $dr->narrative = $order->purchase_no . '/' . ($order->supplier->supplier_code) . '/' . $item->inventoryItem->stock_id_code . '/' . $item->inventoryItem->title . '/' . $item->quantity . '@' . $item->order_price;
                $dr->save();
            }

            if ($vat_amount > 0) {
                $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
                $vat = new WaGlTran();
                $vat->grn_type_number = $series_module->type_number;
                $vat->transaction_type = $series_module->description;
                $vat->transaction_no = $grn_number;
                $vat->grn_last_used_number = $series_module->last_number_used;
                $vat->trans_date = $dateTime;
                $vat->restaurant_id = $order->restaurant_id;
                $vat->tb_reporting_branch  = $order->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->supplier_account_number = $order->supplier->supplier_code;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = $vat_amount;
                $vat->narrative = $order->purchase_no . '/' . ($order->supplier->supplier_code) . '/' . $grn_number;
                $vat->wa_purchase_order_id = $order->id;
                $vat->save();
            }

            // cr entry start
            $cr = new WaGlTran();
            $cr->grn_type_number = $series_module->type_number;
            $cr->transaction_type = $series_module->description;
            $cr->transaction_no = $grn_number;
            $cr->grn_last_used_number = $series_module->last_number_used;
            $cr->trans_date = $dateTime;
            $cr->restaurant_id = $order->restaurant_id;
            $cr->tb_reporting_branch  = $order->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->supplier_account_number = $order->supplier->supplier_code;
            $cr->account =  $order->branch->getAssociateCompany->good_receive->account_code;
            $cr->amount = '-' . round($totalAmount, 2);
            $cr->narrative = $order->purchase_no . '/' . ($order->supplier->supplier_code) . '/' . $grn_number;
            $cr->wa_purchase_order_id = $order->id;
            $cr->save();

            $roundOff = fmod($totalAmount, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round((1 - $roundOff), 2);
                    $crdrAmnt = '+' . $roundOff;
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                    $crdrAmnt = $roundOff;
                }
                $cr = new WaGlTran();
                $cr->grn_type_number = $series_module->type_number;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $grn_number;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->trans_date = $dateTime;
                $cr->restaurant_id = $order->restaurant_id;
                $cr->tb_reporting_branch  = $order->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = $order->supplier->account_code;
                $cr->account =  "202021";
                $cr->amount = $crdrAmnt;
                $cr->narrative = $order->purchase_no . '/' . ($order->supplier->supplier_code) . '/' . $grn_number;
                $cr->wa_purchase_order_id = $order->id;
                $cr->save();
            }

            DB::commit();

            Session::flash('success', 'Delivery note posted successfully');

            return redirect()->route('delivery-notes.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Session::flash('errors', $e->getMessage());

            return redirect()
                ->route('delivery-notes.create', ['order' => $order->id]);
        }
    }
}
