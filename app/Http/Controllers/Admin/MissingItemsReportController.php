<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaGrn;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Model\WaUserSupplier;
use App\WaInventoryLocationTransferItemReturn;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Model\WaInventoryItemSupplier;
use Illuminate\Support\Facades\Auth;

class MissingItemsReportController extends Controller
{
    protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'missing-items-report';
        $this->title = 'Missing Items Stock Report';
    }

    public function index()
    {
        if (!can('missing-items-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $user = Auth::user();
            $users = DB::table('wa_user_suppliers')
                ->select(
                    'users.id as user_id',
                    'users.name as user_name'
                )
                ->join('users', 'users.id', '=', 'wa_user_suppliers.user_id')
                ->groupBy('users.id')
                ->get();
            $userString = request()->filled('user') ? User::find(request()->user)->name : null;

            $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
            $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
            $stockSub = WaStockMove::query()
                ->select([
                    'wa_inventory_item_id',
                    DB::raw("SUM(CASE WHEN created_at < '$from' THEN qauntity ELSE 0 END) as opening_stock_count"),
                    DB::raw('SUM(qauntity) as quantity')
                ])
                ->where('wa_location_and_store_id', request()->location)
                ->groupBy('wa_inventory_item_id');

        

            // $transferInSub = WaStockMove::query()
            //     ->select([
            //         'wa_inventory_item_id',
            //         DB::raw('SUM(qauntity) as transfers_in_count')
            //     ])
            //     ->where('wa_location_and_store_id', request()->location)
            //     ->where('qauntity', '>', 0)
            //     ->where(function ($query) {
            //         $query->where('document_no', 'like', 'TRANS-%')
            //             ->orWhere('document_no', 'like', 'MARCH24-%');
            //     })
            //     ->whereBetween('created_at', [$from, $to])
            //     ->groupBy('wa_inventory_item_id');

            // $transferOutSub = WaStockMove::query()
            //     ->select([
            //         'wa_inventory_item_id',
            //         DB::raw('ABS(SUM(qauntity)) as transfers_out_count')
            //     ])
            //     ->where('wa_location_and_store_id', request()->location)
            //     ->where('qauntity', '<', 0)
            //     ->where(function ($query) {
            //         $query->where('document_no', 'like', 'TRANS-%')
            //             ->orWhere('document_no', 'like', 'MARCH24-%');
            //     })
            //     ->whereBetween('created_at', [$from, $to])
            //     ->groupBy('wa_inventory_item_id');

            $returnsSub = WaInventoryLocationTransferItemReturn::query()
                ->from('wa_inventory_location_transfer_item_returns as returns')
                ->select([
                    'wa_inventory_item_id',
                    DB::raw('SUM(returns.received_quantity) as returns_count')
                ])
                ->join('wa_inventory_location_transfer_items as items', 'items.id', 'returns.wa_inventory_location_transfer_item_id')
                ->where('store_location_id', request()->location)
                ->whereBetween('returns.created_at', [$from, $to])
                ->groupBy('wa_inventory_item_id');

            $salesSub = WaInternalRequisitionItem::query()
                ->join('wa_internal_requisitions as wir', 'wir.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
                ->leftJoin('wa_location_and_stores as wls', 'wls.wa_branch_id', 'wir.restaurant_id')
                ->select(
                    'wa_internal_requisition_items.wa_inventory_item_id',
                    DB::raw('SUM(quantity) AS total_sales'),
                    DB::raw('MAX(wa_internal_requisition_items.created_at) as last_sales_date')

                )
                ->where('wls.id', request()->location)
                ->whereBetween('wir.created_at', [$from, $to])
                ->groupBy('wa_internal_requisition_items.wa_inventory_item_id');

            $smallPacksSub = WaInternalRequisitionItem::query()
                ->from('wa_internal_requisition_items as wirt')
                ->select([
                    'items.wa_inventory_item_id',
                    DB::raw('SUM(quantity) / conversion_factor as pack_sales')
                ])
                ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wirt.wa_inventory_item_id')
                ->leftJoin('wa_internal_requisitions as wir', 'wir.id', 'wirt.wa_internal_requisition_id')
                ->leftJoin('wa_location_and_stores as wls', 'wls.wa_branch_id', 'wir.restaurant_id')
                ->where('wls.id', request()->location)
                ->whereBetween('wir.created_at', [$from, $to])
                ->groupBy('wa_inventory_item_id');

            $qooAndLpoDateSub = WaPurchaseOrderItem::query()
                ->select([
                    'wa_purchase_order_items.wa_inventory_item_id',
                    DB::raw('SUM(CASE WHEN wa_purchase_orders.status = "APPROVED" 
                                    AND wa_purchase_orders.is_hide <> "Yes" 
                                    AND NOT EXISTS (SELECT 1 FROM wa_grns WHERE wa_grns.wa_purchase_order_id = wa_purchase_orders.id)
                                    THEN wa_purchase_order_items.quantity ELSE 0 END) as qty_on_order'),
                    DB::raw('MAX(wa_purchase_order_items.created_at) as last_lpo_date')
                ])
                ->join('wa_purchase_orders', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->where('wa_purchase_orders.wa_location_and_store_id', request()->location)
                ->groupBy('wa_purchase_order_items.wa_inventory_item_id');


            // $lastGrnDateSub = WaGrn::query()
            //     ->select([
            //         'item_code',
            //         DB::raw('MAX(delivery_date) as last_grn_date')
            //     ])
            //     ->whereHas('lpo', function ($query) {
            //         $query->where('wa_location_and_store_id', request()->location);
            //     })
            //     ->groupBy('item_code');

            // $purchasesSub = WaGrn::query()
            //     ->select([
            //         'item_code',
            //         DB::raw('SUM(invoice_info->"$.qty") as purchases_count')
            //     ])
            //     ->whereHas('lpo', function ($query) {
            //         $query->where('wa_location_and_store_id', request()->location);
            //     })
            //     ->whereBetween('delivery_date', [$from, $to])
            //     ->groupBy('item_code');

            $purchasesCombinedSub = WaGrn::query()
            ->select([
                'item_code',
                DB::raw('MAX(delivery_date) as last_grn_date'),
                // DB::raw('SUM(CASE WHEN delivery_date BETWEEN ? AND ? THEN invoice_info->"$.qty" ELSE 0 END) as purchases_count')
                DB::raw("SUM(CASE WHEN delivery_date BETWEEN '$from' AND '$to' THEN invoice_info->'$.qty' ELSE 0 END) as purchases_count")
            ])
            ->whereHas('lpo', function ($query) {
                $query->where('wa_location_and_store_id', request()->location);
            })
            ->groupBy('item_code');

            $usersSub = DB::table('users')
                ->select(
                    'wa_supplier_id',
                    DB::raw('GROUP_CONCAT(users.name) as user_names')
                )
                ->join('wa_user_suppliers', 'users.id', '=', 'wa_user_suppliers.user_id')
                ->groupBy('wa_supplier_id');

            $suppliersSub = DB::table('wa_suppliers')
                ->select(
                    'wa_inventory_item_suppliers.wa_inventory_item_id',
                    DB::raw('GROUP_CONCAT(wa_suppliers.name) as supplier_names'),
                    'users.user_names'
                )
                ->join('wa_inventory_item_suppliers', 'wa_suppliers.id', '=', 'wa_inventory_item_suppliers.wa_supplier_id')
                ->leftJoinSub($usersSub, 'users', 'users.wa_supplier_id', 'wa_suppliers.id')
                ->groupBy('wa_inventory_item_suppliers.wa_inventory_item_id');

            $purchaseOrders = DB::raw("(SELECT 
                GROUP_CONCAT(purchase_no) 
                FROM wa_purchase_orders                 
            WHERE EXISTS (SELECT id FROM wa_purchase_order_items where wa_purchase_orders.id = wa_purchase_order_items.wa_purchase_order_id AND wa_inventory_items.id = wa_purchase_order_items.wa_inventory_item_id)
            AND NOT EXISTS (SELECT id FROM wa_grns where wa_grns.wa_purchase_order_id = wa_purchase_orders.id)
            AND wa_purchase_orders.status = 'APPROVED' 
            AND wa_purchase_orders.is_hide <> 'Yes' 
            AND wa_purchase_orders.wa_location_and_store_id = " . request()->location . ") As purchase_order_numbers");

            $query = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.id',
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'wa_inventory_items.description',
                    'wa_inventory_items.wa_inventory_category_id',
                    'categories.category_description as category',
                    'max_stocks.max_stock',
                    'max_stocks.re_order_level',
                    // 'last_grn_dates.last_grn_date',
                    'suppliers.supplier_names',
                    'suppliers.user_names',
                    'sales.last_sales_date',
                    // DB::raw('IFNULL(purchases.purchases_count, 0) as purchases_count'),
                    DB::raw('IFNULL(purchasesCombined.purchases_count, 0) as purchases_count'),
                    'purchasesCombined.last_grn_date',
                    // DB::raw('IFNULL(transfers_in.transfers_in_count, 0) as transfers_in_count'),
                    // DB::raw('IFNULL(transfers_out.transfers_out_count, 0) as transfers_out_count'),
                    DB::raw('IFNULL(sales.total_sales, 0) as excl_total_sales'),
                    DB::raw('IFNULL(returns.returns_count, 0) as returns_count'),
                    DB::raw('ROUND(IFNULL(sales.total_sales,0) + IFNULL(packs.pack_sales, 0) - IFNULL(returns.returns_count, 0),2) as total_sales'),
                    DB::raw('ROUND(IFNULL(packs.pack_sales, 0), 2) as pack_sales'),
                    DB::raw('IFNULL(stocks.opening_stock_count, 0) as opening_stock_count'),
                    DB::raw('IFNULL(stocks.quantity, 0) as qoh'),
                    DB::raw('IFNULL(orders.qty_on_order, 0) as qty_on_order'),
                    DB::raw('IFNULL(orders.last_lpo_date, NULL) as last_lpo_date'),
                    $purchaseOrders
                ])
                ->join('wa_inventory_categories as categories', 'categories.id', 'wa_inventory_items.wa_inventory_category_id')
                ->join('wa_inventory_location_stock_status as max_stocks', function ($query) {
                    $query->on('max_stocks.wa_inventory_item_id', 'wa_inventory_items.id')
                        ->where('max_stocks.wa_location_and_stores_id', request()->location);
                })
                ->leftJoinSub($stockSub, 'stocks', 'stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoinSub($purchasesCombinedSub, 'purchasesCombined', 'purchasesCombined.item_code', '=', 'wa_inventory_items.stock_id_code')
                // ->leftJoinSub($purchasesSub, 'purchases', 'purchases.item_code', '=', 'wa_inventory_items.stock_id_code')
                // ->leftJoinSub($transferInSub, 'transfers_in', 'transfers_in.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                // ->leftJoinSub($transferOutSub, 'transfers_out', 'transfers_out.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoinSub($returnsSub, 'returns', 'returns.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoinSub($smallPacksSub, 'packs', 'packs.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoinSub($qooAndLpoDateSub, 'orders', 'orders.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                // ->leftJoinSub($lastGrnDateSub, 'last_grn_dates', 'last_grn_dates.item_code', '=', 'wa_inventory_items.stock_id_code')
                ->leftJoinSub($suppliersSub, 'suppliers', 'suppliers.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->join('pack_sizes AS sizes', 'sizes.id', 'wa_inventory_items.pack_size_id')
                ->where('sizes.can_order', 1)
                ->where(function ($query) {
                    $query->where('stocks.quantity', 0)
                        ->orWhereNull('stocks.quantity');
                })
                ->where(DB::raw('IFNULL(total_sales,0)'), '>', 0)
                ->where('wa_inventory_items.status', 1)
                ->distinct('wa_inventory_items.id');
            
            $query = $query->orderBy('suppliers.user_names', 'desc');

            if (request()->supplier) {
                $query->whereHas('suppliers', function ($query) {
                    $query->where('wa_suppliers.id', request()->supplier);
                });
            } else {
                if (!auth()->user()->isAdministrator()) {
                    $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                        ->pluck('wa_supplier_id')->toArray();

                    $query->whereHas('suppliers', function ($query) use ($supplierIds) {
                        $query->whereIn('wa_suppliers.id', $supplierIds);
                    });
                }
            }
            if(request()->user &&  request()->user != null){
                $query = $query->having('user_names', 'LIKE', "%$userString%");
            }

            if (request()->action) {
                $items = $query->get();

                if (request()->action == 'excel') {
                    $view = view('admin.reports.export.missing_items_report', [
                        'date_range' => Carbon::parse($from)->format('d/M/Y') . ' - ' . Carbon::parse($to)->format('d/M/Y'),
                        'items' => $items->sortBy('users'),
                        'store' => WaLocationAndStore::find(request()->location)->location_name
                    ]);

                    return Excel::download(new ExportViewToExcel($view), 'missing_items_report_' . date('YmdHis') . '.xlsx');
                }
            }

            if (request()->wantsJson()) {
                $resultCount = $this->countRecords(request()->location ?? 46, $from, $to);
                return DataTables::eloquent($query)
                ->setTotalRecords($resultCount)
                ->addColumn('suppliers', function ($inventory) {
                    return implode(',', $inventory->suppliers->pluck('name')->toArray());
                })
                    ->editColumn('qoh', function ($inventory) {
                        return $inventory->qoh == 0 ? "<b class='text-danger'>$inventory->qoh</b>" : manageAmountFormat($inventory->qoh);
                    })
                    ->addColumn('variance', function ($inventory) {
                        $balance = $inventory->qoh - $inventory->max_stock;
                        $formatted = manageAmountFormat(abs($balance));
                        return $balance > 0 ? "<b class='text-info'>$formatted</b>" : $formatted;
                    })
                    ->editColumn('pack_sales', function ($inventory) {
                        return round($inventory->pack_sales, 2);
                    })
                    ->editColumn('last_lpo_date', function ($inventory) {
                        return is_null($inventory->last_lpo_date) ? '' : Carbon::parse($inventory->last_lpo_date)->format('d/m/Y');
                    })
                    ->editColumn('last_grn_date', function ($inventory) {
                        return is_null($inventory->last_grn_date) ? '' : Carbon::parse($inventory->last_grn_date)->format('d/m/Y');
                    })
                    ->editColumn('last_sales_date', function ($inventory) {
                        return is_null($inventory->last_sales_date) ? '' : Carbon::parse($inventory->last_sales_date)->format('d/m/Y');
                    })
                    ->rawColumns(['variance', 'qoh'])
                    ->toJson();
            }

        return view('admin.reports.missing_item_report', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::get(),
            'users' => $users,
            'user' => $user
        ]);
    }

    public function countRecords($location, $start, $end){
        $from = $start;
        $to = $end;

        $salesSub  =  WaInternalRequisitionItem::join('wa_internal_requisitions as wir', 'wir.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->leftJoin('wa_location_and_stores as wls', 'wls.wa_branch_id', 'wir.restaurant_id')
            ->select(
                'wa_internal_requisition_items.wa_inventory_item_id',
                DB::raw('IFNULL(SUM(quantity),0) AS total_sales')

            )
            ->where('wls.id', $location)
            ->whereBetween('wir.created_at', [$from, $to])
            ->groupBy('wa_internal_requisition_items.wa_inventory_item_id');
        $itemIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
            $itemIds = WaInventoryItemSupplier::whereIn('wa_supplier_id', $supplierIds)->get()
                ->pluck('wa_inventory_item_id')->toArray();
        }

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('IFNULL(SUM(qauntity),0) AS qty_on_hand'),
            ])
            ->where('wa_location_and_store_id', $location)
            ->groupBy('wa_inventory_item_id');

        return WaInventoryItem::query()
            ->select([
                DB::raw("IFNULL(stock_status.max_stock, 0) as max_stock"),
                DB::raw("IFNULL(stock_status.re_order_level, 0) as re_order_level"),
                DB::raw('IFNULL(moves.qty_on_hand,0) as qty_on_hand')
            ])
            ->leftJoinSub($qohSub, 'moves', 'moves.wa_inventory_item_id', 'wa_inventory_items.id')
            ->join('wa_inventory_location_stock_status as stock_status', function ($query) use($location){
                $query->on('stock_status.wa_inventory_item_id', 'wa_inventory_items.id')
                    ->where('stock_status.wa_location_and_stores_id', $location);
            })
            ->join('pack_sizes AS sizes', 'sizes.id', 'wa_inventory_items.pack_size_id')
            ->where('sizes.can_order', 1)
            ->where('wa_inventory_items.status', 1)
            ->when(count($itemIds) > 0, function ($query) use ($itemIds) {
                $query->whereIn('wa_inventory_items.id', $itemIds);
            }) ->selectRaw('IFNULL(sales.total_sales,0)')
            ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', 'wa_inventory_items.id')
            ->whereRaw('IFNULL(qty_on_hand,0) = 0')
            ->whereRaw('total_sales > 0')
            ->get()
            ->count();

    }
}
