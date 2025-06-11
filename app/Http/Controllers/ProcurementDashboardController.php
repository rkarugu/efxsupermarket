<?php

namespace App\Http\Controllers;

use App\Model\WaExternalRequisition;
use App\Model\WaGrn;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrder;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Model\WaUserSupplier;
use App\Models\TradeAgreement;
use App\Models\TradeDiscountDemand;
use App\Models\WaReturnDemand;
use App\PaymentVoucher;
use App\Services\Inventory\StockStats;
use App\WaDemand;
use App\WaSupplierInvoice;
use Illuminate\Support\Facades\DB;

class ProcurementDashboardController extends Controller
{
    protected $model = 'procurement-dashboard';

    protected $title = 'Procurement Dashboard';

    public function index()
    {
        if (!can('view', 'procurement-dashboard')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.procurement-dashboard.index', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => ''
            ]
        ]);
    }

    public function lpoStats()
    {
        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $requisitions = WaExternalRequisition::where('status', '!=', 'RESOLVED')
            ->where('is_hide', 'No')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })->where('wa_store_location_id', request()->store);

        $lpos = WaPurchaseOrder::query()
            ->where('is_hide', 'No')
            ->whereNotIn('wa_purchase_orders.status', ['PRELPO', 'COMPLETED'])
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->where('wa_location_and_store_id', request()->store)
            ->doesntHave('grns');

        $trades = TradeAgreement::query()
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->where('status',  'Approved');


        return response()->json([
            'branch_requisitions' => $requisitions->count(),
            'pending_lpos' => $lpos->count(),
            'locked_suppliers' => $trades->clone()->where('is_locked', true)->count(),
            'signed_portal' => $trades->clone()->where('linked_to_portal', 1)->count(),
            'total_suppliers' => $trades->count(),
        ]);
    }

    public function stockStats()
    {
        $stats = (new StockStats(request()->store));

        switch (request()->type) {
            case 'missing_items':
                $count =  $stats->getMissingItemsCount();
                break;
            case 'reorder_items':
                $count =  $stats->getReorderItemsCount();
                break;
            case 'over_stocked_items':
                $count =  $stats->getOverStockedItemCount();
                break;
            case 'dead_stock_items':
                $count =  $stats->getDeadStockCount();
                break;
            case 'slow_moving_items':
                $count =  $stats->getSlowMovingStockCount();
                break;
            default:
                $count = 0;
                break;
        }

        return response()->json([
            'count' => $count,
        ]);
    }

    public function supplierStats()
    {
        $supplierIds = [];
        $supplierNos = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
            $supplierNos = WaSupplier::find($supplierIds)
                ->pluck('supplier_code')
                ->toArray();
        }

        $total_payable = WaSuppTran::query()
            ->select([
                DB::raw('SUM(total_amount_inc_vat) as total_amount')
            ])
            ->when(count($supplierNos) > 0, function ($query) use ($supplierNos) {
                $query->whereIn('supplier_no', $supplierNos);
            })
            ->first();

        $processing_payments = PaymentVoucher::query()
            ->select([
                'wa_supplier_id',
                DB::raw('IFNULL(SUM(amount),0) as total_amount'),
            ])
            ->processing()
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })->first();

        $pending_grns = WaGrn::query()
            ->select([
                'grn_number',
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) AS total_amount'),
            ])
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->doesntHave('invoice')
            ->groupBy('grn_number')
            ->get()
            ->count();

        $unpaid_invoices = WaSupplierInvoice::query()
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('supplier_id', $supplierIds);
            })
            ->doesntHave('payments')
            ->get()
            ->count();

        $pending_vouchers =  PaymentVoucher::query()
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->processing()
            ->get()
            ->count();

        return response()->json([
            'total_payable' => manageAmountFormat($total_payable->total_amount - $processing_payments->total_amount),
            'pending_grns' => $pending_grns,
            'unpaid_invoices' => $unpaid_invoices,
            'pending_vouchers' =>  $pending_vouchers,
        ]);
    }

    public function purchasesVsSales()
    {
        $currentYear = now()->year;
        $supplierIds = [];
        $itemIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
            $itemIds = WaInventoryItemSupplier::whereIn('wa_supplier_id', $supplierIds)->get()
                ->pluck('wa_inventory_item_id')->toArray();
        }

        $purchases = WaGrn::query()
            ->select([
                DB::raw('MONTH(delivery_date) as month'),
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) AS total_amount'),
            ])
            ->whereHas('stockMoves')
            ->whereYear('delivery_date', $currentYear)
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->groupBy(DB::raw('MONTH(delivery_date)'))
            ->get();

        $sales = WaStockMove::query()
            ->select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_cost) as total_amount')
            ])
            ->where('document_no', 'like', 'INV%')
            ->whereYear('created_at', $currentYear)
            ->when(count($itemIds) > 0, function ($query) use ($itemIds) {
                $query->whereIn('wa_inventory_item_id', $itemIds);
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        return response()->json([
            'purchases' => $this->matchMonth($purchases),
            'sales' => $this->matchMonth($sales),
        ]);
    }

    protected function matchMonth($items)
    {
        $values = array_fill(0, 12, null);

        $items->map(function ($item) use (&$values) {
            $values[$item->month - 1] = $item->total_amount;
        });

        return collect($values);
    }

    public function stockValue()
    {
        $itemIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
            $itemIds = WaInventoryItemSupplier::whereIn('wa_supplier_id', $supplierIds)->get()
                ->pluck('wa_inventory_item_id')->toArray();
        }

        $qohSub = WaStockMove::query()
            ->select([
                DB::raw('IFNULL(SUM(qauntity),0) * wa_inventory_items.standard_cost')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->whereColumn('wa_stock_moves.wa_inventory_item_id', 'wa_inventory_items.id');

        $items = WaInventoryItem::query()
            ->select([
                DB::raw("stock_id_code as item")
            ])
            ->selectSub($qohSub, 'stock_value')
            ->when(count($itemIds) > 0, function ($query) use ($itemIds) {
                $query->whereIn('id', $itemIds);
            })
            ->orderBy('stock_value', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'values' => $items->pluck('stock_value')->flatten(),
            'items' => $items->pluck('item')->flatten(),
        ]);
    }

    public function deliverySchedule()
    {
        $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));

        $location = WaLocationAndStore::find(request()->location);

        $bookedSlots = $api->postRequest(env('SUPPLIER_PORTAL_BOOKED_SLOTS', '/api/lpo/get-booked-slots'), [
            'date' => $request->date ?? date('Y-m-d'), 
            'from' => env('SUPPLIER_SOURCE'),
            'store' => $location->location_name,
        ]);

        $slots = [];
        if (isset($bookedSlots['result']) == 1) {
            $slots = $bookedSlots['data'];
        }

        return response()->json([
            'slots' => $slots
        ]);
    }

    public function supplierBalances()
    {
        $qtySub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity'),
            ])
            ->whereRaw("wa_location_and_store_id = " . request()->location)
            ->groupBy('wa_inventory_item_id');

        $inventorySub = WaInventoryItem::query()
            ->select([
                'quantities.wa_inventory_item_id',
                DB::raw("quantities.quantity * wa_inventory_items.selling_price as inventory_value")
            ])
            ->joinSub($qtySub, 'quantities', 'quantities.wa_inventory_item_id', 'wa_inventory_items.id');

        $stockValueSub = WaInventoryItemSupplier::query()
            ->select([
                'wa_inventory_item_suppliers.wa_supplier_id',
                DB::raw("SUM(inventory_value) AS stock_value")
            ])
            ->joinSub($inventorySub, 'inventories', 'inventories.wa_inventory_item_id', 'wa_inventory_item_suppliers.wa_inventory_item_id')
            ->groupBy('wa_inventory_item_suppliers.wa_supplier_id');

        // GRN value queries
        $grnSub = WaGrn::query()
            ->select([
                'wa_grns.wa_supplier_id',
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) AS grn_value')
            ])
            ->leftJoin('wa_supplier_invoices as invoices', 'invoices.grn_number', 'wa_grns.grn_number')
            ->whereNull('invoices.grn_number')
            ->groupBy('wa_grns.wa_supplier_id');

        $transSub = WaSuppTran::query()
            ->select([
                'supplier_no',
                DB::raw('SUM(total_amount_inc_vat) as balance'),
            ])
            ->groupBy('supplier_no');

        $vouchersSub = PaymentVoucher::query()
            ->select([
                'wa_supplier_id',
                DB::raw('IFNULL(SUM(amount),0) as processing_amount'),
            ])
            ->processing()
            ->groupBy('wa_supplier_id');

        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $suppliers = WaSupplier::query()
            ->select([
                'wa_suppliers.id',
                'wa_suppliers.name',
                'wa_suppliers.supplier_code',
                'transactions.balance',
                DB::raw('IFNULL(processing_amount, 0) AS processing_amount'),
                DB::raw('IFNULL(grn_value,0) As grn_value'),
                DB::raw('IFNULL(stock_value,0) As stock_value'),
                DB::raw("(transactions.balance + IFNULL(grn_value,0) - IFNULL(stock_value,0)) as to_pay"),
                DB::raw("(transactions.balance + IFNULL(grn_value,0) - IFNULL(stock_value,0) - IFNULL(processing_amount, 0)) as variance")
            ])
            ->leftJoinSub($stockValueSub, 'stock_values', 'stock_values.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($grnSub, 'grns', 'grns.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($transSub, 'transactions', 'transactions.supplier_no', 'wa_suppliers.supplier_code')
            ->leftJoinSub($vouchersSub, 'vouchers', 'vouchers.wa_supplier_id', 'wa_suppliers.id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_suppliers.id', $supplierIds);
            })
            ->orderBy('to_pay', 'desc')
            ->get()
            ->map(function ($supplier) {
                $supplier->to_pay = $supplier->to_pay < 0 ? 0 : $supplier->to_pay;
                $supplier->variance = $supplier->variance < 0 ? 0 : $supplier->variance;

                return $supplier;
            });

        return response()->json([
            'suppliers' => $suppliers,
            'totals' => [
                'to_pay' => $suppliers->sum('to_pay'),
                'processing_amount' => $suppliers->sum('processing_amount'),
                'variance' => $suppliers->sum('variance'),
            ]
        ]);
    }

    public function supplierInformation()
    {
        $supplierIds = [];
        $from = now()->subDays(30)->startOfDay()->toDateString();
        $to = now()->endOfDay()->toDateString();

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $grnSub = WaGrn::query()
            ->select([
                'wa_grns.wa_supplier_id',
                DB::raw('COUNT(DISTINCT wa_grns.grn_number) as pending_grn_count')
            ])
            ->leftJoin('wa_supplier_invoices as invoices', 'invoices.grn_number', 'wa_grns.grn_number')
            ->whereNull('invoices.grn_number')
            ->groupBy('wa_grns.wa_supplier_id');

        $invoicesSub = WaSupplierInvoice::query()
            ->select([
                'supplier_id as wa_supplier_id',
                DB::raw('COUNT(wa_supplier_invoices.wa_supp_tran_id) as pending_invoice_count')
            ])
            ->leftJoin('payment_voucher_items as voucher_items', function ($join) {
                $join->on('voucher_items.payable_id', 'wa_supplier_invoices.wa_supp_tran_id')
                    ->where('payable_type', 'invoice');
            })
            ->whereNull('voucher_items.payable_id')
            ->groupBy('supplier_id');

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('IFNULL(SUM(qauntity),0) AS qty_on_hand'),
            ])
            ->where('wa_location_and_store_id', request()->store)
            ->groupBy('wa_inventory_item_id');

        $salesSub = WaInternalRequisitionItem::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('IFNULL(SUM(quantity),0) AS total_sales')
            ])
            ->join('wa_internal_requisitions as wir', 'wir.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->where('to_store_id', request()->store)
            ->whereBetween('wa_internal_requisition_items.created_at', [$from, $to])
            ->groupBy('wa_inventory_item_id');

        $itemsSub = WaInventoryItemSupplier::query()
            ->from('wa_inventory_item_suppliers as item_suppliers')
            ->select([
                'item_suppliers.wa_supplier_id',
                DB::raw('COUNT(item_suppliers.wa_supplier_id) as item_count')
            ])
            ->leftJoinSub($qohSub, 'quantities', 'quantities.wa_inventory_item_id', 'item_suppliers.wa_inventory_item_id')
            ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', 'item_suppliers.wa_inventory_item_id')
            ->join('wa_inventory_location_stock_status as stock_status', function ($query) {
                $query->on('stock_status.wa_inventory_item_id', 'item_suppliers.wa_inventory_item_id')
                    ->where('stock_status.wa_location_and_stores_id', request()->store);
            })
            ->join('wa_inventory_items as items', function ($query) {
                $query->on('items.id', 'item_suppliers.wa_inventory_item_id');
            })
            ->join('pack_sizes as sizes', function ($query) {
                $query->on('sizes.id', 'items.pack_size_id');
            })
            ->where('sizes.can_order', 1)
            ->groupBy('item_suppliers.wa_supplier_id');

        $missingItemsSub = $itemsSub->clone()
            ->whereRaw('IFNULL(qty_on_hand,0) = 0')
            ->whereRaw('IFNULL(total_sales,0) > 0');

        $reorderItemsSub = $itemsSub->clone()
            ->whereRaw('IFNULL(qty_on_hand, 0) <= re_order_level')
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('total_sales > 0');

        $deadItemsSub = $itemsSub->clone()
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('IFNULL(total_sales,0) = 0');

        $slowItemsSub = $itemsSub->clone()
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('IFNULL(total_sales, 0) > 0')
            ->whereRaw('IFNULL(total_sales, 0) <= 5');

        $overStockItemsSub = $itemsSub->clone()
            ->whereRaw('qty_on_hand > stock_status.max_stock');

        $suppliers = WaSupplier::query()
            ->select([
                'wa_suppliers.id',
                'wa_suppliers.name',
                DB::raw('IFNULL(pending_grn_count, 0) AS pending_grn_count'),
                DB::raw('IFNULL(pending_invoice_count, 0) AS pending_invoice_count'),
                DB::raw('IFNULL(missing_items.item_count, 0) AS missing_items_count'),
                DB::raw('IFNULL(reorder_items.item_count, 0) AS reorder_items_count'),
                DB::raw('IFNULL(over_stock_items.item_count, 0) AS over_stock_items_count'),
                DB::raw('IFNULL(dead_items.item_count, 0) AS dead_stock_items_count'),
                DB::raw('IFNULL(slow_items.item_count, 0) AS slow_moving_items_count'),
            ])
            ->leftJoinSub($grnSub, 'grns', 'grns.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($invoicesSub, 'invoices', 'invoices.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($missingItemsSub, 'missing_items', 'missing_items.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($reorderItemsSub, 'reorder_items', 'reorder_items.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($overStockItemsSub, 'over_stock_items', 'over_stock_items.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($deadItemsSub, 'dead_items', 'dead_items.wa_supplier_id', 'wa_suppliers.id')
            ->leftJoinSub($slowItemsSub, 'slow_items', 'slow_items.wa_supplier_id', 'wa_suppliers.id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_suppliers.id', $supplierIds);
            })
            ->get();

        return response()->json([
            'suppliers' => $suppliers,
        ]);
    }

    public function discounts()
    {
        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $discounts = [];

        return response()->json([
            'discounts' => $discounts
        ]);
    }

    public function returns()
    {
        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $returns = WaReturnDemand::query()
            ->select([
                'suppliers.name AS supplier_name',
                'demands.created_at',
                'demands.demand_no',
                DB::raw("'from store' AS type"),
                'demands.return_document_no AS document_no',
                'demands.demand_amount AS amount',
            ])
            ->from('wa_return_demands as demands')
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'demands.wa_supplier_id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->where('wa_supplier_id', $supplierIds);
            })
            ->where('return_document_no', 'LIKE', 'RFS%')
            ->whereNull('credit_note_no');

        $discounts = TradeDiscountDemand::query()
            ->select([
                'suppliers.name AS supplier_name',
                'demands.created_at',
                'demands.demand_no',
                DB::raw("'discount' AS type"),
                DB::raw("'' AS document_no"),
                'demands.amount',
            ])
            ->from('trade_discount_demands as demands')
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'demands.supplier_id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->where('supplier_id', $supplierIds);
            })
            ->whereNull('credit_note_no');

        $prices = WaDemand::query()
            ->select([
                'suppliers.name AS supplier_name',
                'demands.created_at',
                'demands.demand_no',
                DB::raw("'price' AS type"),
                DB::raw("'' AS document_no"),
                'demands.demand_amount AS amount',
            ])
            ->from('wa_demands as demands')
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'demands.wa_supplier_id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->where('wa_supplier_id', $supplierIds);
            })
            ->whereNull('credit_note_no');

        $demands = $returns
            ->unionAll($discounts)
            ->unionAll($prices)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'demands' => $demands
        ]);
    }

    public function turnoverPurchases()
    {
        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $startOfMonth = now()->firstOfMonth()->toDateString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateString();
        $last90Days = now()->subDays(90)->toDateString();
        $startOfYear = now()->startOfYear();
        $lastYear = now()->subYear()->endOfYear()->toDateString();

        $grnSub = WaGrn::query()
            ->select([
                'wa_supplier_id',
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) AS grn_value')
            ])
            ->groupBy('wa_supplier_id');

        $purchases = WaSupplier::query()
            ->select([
                DB::raw('wa_suppliers.name AS supplier_name'),
                DB::raw('IFNULL(current.grn_value,0) As current_month'),
                DB::raw('IFNULL(last.grn_value,0) AS last_month'),
                DB::raw('IFNULL(last_90.grn_value,0) As last_90'),
                DB::raw('IFNULL(current_year.grn_value,0) As current_year'),
                DB::raw('IFNULL(last_year.grn_value,0) AS last_year'),
            ])
            ->leftJoinSub($grnSub->clone()->where('delivery_date', '>=', $startOfMonth), 'current', 'current.wa_supplier_id', 'id')
            ->leftJoinSub($grnSub->clone()->whereBetween('delivery_date', [$startOfLastMonth, $endOfLastMonth]), 'last', 'last.wa_supplier_id', 'id')
            ->leftJoinSub($grnSub->clone()->where('delivery_date', '>=', $last90Days), 'last_90', 'last_90.wa_supplier_id', 'id')
            ->leftJoinSub($grnSub->clone()->where('delivery_date', '>=', $startOfYear), 'current_year', 'current_year.wa_supplier_id', 'id')
            ->leftJoinSub($grnSub->clone()->where('delivery_date', '<=', $lastYear), 'last_year', 'last_year.wa_supplier_id', 'id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_suppliers.id', $supplierIds);
            })
            ->orderBy('current_year', 'desc')
            ->get();

        return response()->json([
            'purchases' => $purchases,
            'totals' => [
                'current_year' => $purchases->sum('current_year'),
                'last_year' => $purchases->sum('last_year'),
            ],
        ]);
    }

    public function turnoverSales()
    {
        $supplierIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }

        $startOfMonth = now()->firstOfMonth()->toDateString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateString();
        $last90Days = now()->subDays(90)->toDateString();
        $startOfYear = now()->startOfYear();
        $lastYear = now()->subYear()->endOfYear()->toDateString();

        $salesSub = WaStockMove::query()
            ->from('wa_stock_moves as moves')
            ->select([
                'suppliers.wa_supplier_id',
                DB::raw('SUM(total_cost) AS total_sales')
            ])
            ->where('document_no', 'like', '%INV%')
            ->join('wa_inventory_item_suppliers as suppliers', 'suppliers.wa_inventory_item_id', 'moves.wa_inventory_item_id')
            ->groupBy('wa_supplier_id');

        $sales = WaSupplier::query()
            ->select([
                DB::raw('wa_suppliers.name AS supplier_name'),
                DB::raw('IFNULL(current.total_sales,0) As current_month'),
                DB::raw('IFNULL(last.total_sales,0) AS last_month'),
                DB::raw('IFNULL(last_90.total_sales,0) As last_90'),
                DB::raw('IFNULL(current_year.total_sales,0) As current_year'),
                DB::raw('IFNULL(last_year.total_sales,0) AS last_year'),
            ])
            ->leftJoinSub($salesSub->clone()->where('moves.created_at', '>=', $startOfMonth), 'current', 'current.wa_supplier_id', 'id')
            ->leftJoinSub($salesSub->clone()->whereBetween('moves.created_at', [$startOfLastMonth, $endOfLastMonth]), 'last', 'last.wa_supplier_id', 'id')
            ->leftJoinSub($salesSub->clone()->where('moves.created_at', '>=', $last90Days), 'last_90', 'last_90.wa_supplier_id', 'id')
            ->leftJoinSub($salesSub->clone()->where('moves.created_at', '>=', $startOfYear), 'current_year', 'current_year.wa_supplier_id', 'id')
            ->leftJoinSub($salesSub->clone()->where('moves.created_at', '<=', $lastYear), 'last_year', 'last_year.wa_supplier_id', 'id')
            ->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                $query->whereIn('wa_suppliers.id', $supplierIds);
            })
            ->orderBy('current_year', 'desc')
            ->get();

        return response()->json([
            'sales' => $sales,
            'totals' => [
                'current_year' => $sales->sum('current_year'),
                'last_year' => $sales->sum('last_year'),
            ],
        ]);
    }
}
