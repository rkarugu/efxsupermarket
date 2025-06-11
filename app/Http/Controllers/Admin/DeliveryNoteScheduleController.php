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
use Yajra\DataTables\Facades\DataTables;

class DeliveryNoteScheduleController extends Controller
{
    protected $title = 'Delivery Notes Schedule';

    protected $model = 'delivery-notes-schedules';

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
            ->select([
                'wa_purchase_orders.*'
            ])
            ->with([
                'supplier',
                'user'
            ])
            ->selectSub($vatSub, 'vat_amount')
            ->selectSub($amountSub, 'total_amount')
            ->where('status', 'APPROVED')
            ->where('is_hide', '<>', 'Yes')
            ->where('advance_payment', true)
            ->whereHas('advance');

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($order) {
                    return $order->created_at?->format('Y-m-d H:i:s');
                })
                ->editColumn('vat_amount', function ($order) {
                    return manageAmountFormat($order->vat_amount);
                })
                ->editColumn('total_amount', function ($order) {
                    return manageAmountFormat($order->total_amount);
                })
                ->addColumn('actions', function ($order) {
                    return view('admin.delivery_notes_schedules.actions', compact('order'));
                })
                ->toJson();
        }

        return view('admin.delivery_notes_schedules.index', [
            'title' => $this->title,
            'model' => $this->model,
        ]);
    }

    public function print($lpoId)
    {
        $advancePayment = AdvancePayment::query()
            ->with([
                'supplier',
                'lpo',
                'payment',
            ])
            ->where('wa_purchase_order_id', $lpoId)
            ->firstOrFail();

        $grns = WaGrn::query()
            ->select([
                'wa_grns.*',
                'orders.vehicle_reg_no',
                'locations.location_name'
            ])
            ->join('wa_purchase_orders as orders', 'orders.id', '=', 'wa_grns.wa_purchase_order_id')
            ->join('wa_location_and_stores as locations', 'locations.id', '=', 'orders.wa_location_and_store_id')
            ->where('orders.mother_lpo', $advancePayment->wa_purchase_order_id)
            ->get();

        $pdf = Pdf::loadView('admin.delivery_notes_schedules.schedule', compact('advancePayment', 'grns'));

        return $pdf->stream('delivery_schedule_' . date('Y-m-d-H-i-s') . '.pdf');
    }
}
