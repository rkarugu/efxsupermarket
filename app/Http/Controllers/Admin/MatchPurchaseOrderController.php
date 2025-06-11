<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class MatchPurchaseOrderController extends Controller
{
    protected $title = 'Match Purchase Orders';

    protected $model = 'match-purchase-orders';

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
                    return $order->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('vat_amount', function ($order) {
                    return manageAmountFormat($order->vat_amount);
                })
                ->editColumn('total_amount', function ($order) {
                    return manageAmountFormat($order->total_amount);
                })
                ->addColumn('actions', function ($order) {
                    return view('admin.match_purchase_orders.actions', compact('order'));
                })
                ->toJson();
        }

        return view('admin.match_purchase_orders.index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                $this->title => ''
            ]
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, ['orders' => 'required']);

        foreach ($request->orders as $orderId) {
            $order =  WaPurchaseOrder::find($orderId);
            $order->update([
                'mother_lpo' => $request->mother_lpo
            ]);
        }

        Session::flash('success', 'LPOs matches successfully');

        return redirect()->route('match-purchase-orders.index');
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
            ->where('is_hide', '<>', 'Yes')
            ->where('advance_payment', true)
            ->whereNull('mother_lpo')
            ->doesntHave('grns')
            ->doesntHave('advance');

        return DataTables::eloquent($query)
            ->editColumn('id', function ($order) {
                return view('admin.match_purchase_orders.input', compact('order'));
            })
            ->editColumn('total_amount', function ($order) {
                return manageAmountFormat($order->total_amount);
            })
            ->toJson();
    }

    public function children()
    {
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
                'branch',
                'storeLocation',
                'supplier',
                'user',
            ])
            ->selectSub($vatSub, 'vat_amount')
            ->selectSub($amountSub, 'total_amount')
            ->where('status', 'APPROVED')
            ->where('is_hide', '<>', 'Yes')
            ->where('advance_payment', true)
            ->where('mother_lpo', request()->mother);

        return response()->json([
            'success' => true,
            'orders' => $query->get()
        ]);
    }
}
