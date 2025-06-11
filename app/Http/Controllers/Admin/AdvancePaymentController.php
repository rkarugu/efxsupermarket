<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaGrn;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Models\AdvancePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class AdvancePaymentController extends Controller
{
    protected $model = 'advance-payments';

    protected $title = 'Advance Payments';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $query = AdvancePayment::query()
            ->with([
                'lpo',
                'supplier',
                'preparedBy',
            ]);

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($advance) {
                    return $advance->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('vat_amount', function ($advance) {
                    return manageAmountFormat($advance->vat_amount);
                })
                ->editColumn('amount', function ($advance) {
                    return manageAmountFormat($advance->amount);
                })
                ->editColumn('delivered_amount', function ($advance) {
                    return manageAmountFormat($advance->delivered_amount);
                })
                ->addColumn('actions', function ($advance) {
                    return view('admin.advance_payments.actions', compact('advance'));
                })
                ->toJson();
        }

        return view('admin.advance_payments.index', [
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

        return view('admin.advance_payments.create', [
            'title' => $this->title,
            'model' => $this->model,
        ]);
    }

    public function orders()
    {
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
            ->selectSub($amountSub, 'total_amount')
            ->where('status', 'APPROVED')
            ->where('is_hide', 'No')
            ->where('advance_payment', true)
            ->where('supplier_accepted', true)
            ->doesntHave('grns')
            ->doesntHave('advance');

        return DataTables::eloquent($query)
            ->editColumn('id', function ($lpo) {
                return view('admin.advance_payments.input', compact('lpo'));
            })
            ->editColumn('total_amount', function ($lpo) {
                return manageAmountFormat($lpo->total_amount);
            })
            ->toJson();
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, ['lpo' => 'required']);

        $lpo = WaPurchaseOrder::findOrFail($request->lpo);
        $vatAmount = $lpo->purchaseOrderItems()->sum('vat_amount');
        $totalAmount = $lpo->purchaseOrderItems()->sum('total_cost_with_vat') -
            $lpo->purchaseOrderItems()->sum('other_discounts_total');

        AdvancePayment::create([
            'supplier_id' => $lpo->wa_supplier_id,
            'wa_purchase_order_id' => $lpo->id,
            'vat_amount' => $vatAmount,
            'amount' => $totalAmount,
            'prepared_by' => auth()->user()->id,
        ]);

        Session::flash('success', 'Advance payment created successfully');

        return redirect()->route('advance-payments.index');
    }

    public function deliverySchedule($advancePaymentId)
    {
        $advancePayment = AdvancePayment::query()
            ->with([
                'supplier',
                'lpo',
                'payment',
            ])
            ->findOrFail($advancePaymentId);

        $grns = WaGrn::query()
            ->select([
                'wa_grns.*',
                'orders.vehicle_reg_no',
                'locations.location_name'
            ])
            ->join('wa_receive_purchase_order_items as items', 'items.id', '=', 'wa_grns.wa_receive_purchase_order_item_id')
            ->join('wa_receive_purchase_orders as orders', 'orders.id', '=', 'items.wa_receive_purchase_order_id')
            ->join('wa_location_and_stores as locations', 'locations.id', '=', 'orders.wa_location_and_store_id')
            ->where('wa_grns.wa_purchase_order_id', $advancePayment->wa_purchase_order_id)
            ->get();

        $pdf = Pdf::loadView('admin.advance_payments.schedule', compact('advancePayment', 'grns'));

        return $pdf->stream('delivery_schedule_' . date('Y-m-d-H-i-s') . '.pdf');
    }

    public function destroy(AdvancePayment $payment)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $payment->delete();

        Session::flash('success', 'Advance payment deleted successfully');

        return redirect()->route('advance-payments.index');
    }
}
