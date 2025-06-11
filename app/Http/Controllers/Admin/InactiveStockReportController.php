<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InactiveStockReportController extends Controller
{
    protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'inactive-stock-report';
        $this->title = 'Inactive Report';
    }

    public function index()
    {
        if (!can('inactive-stock-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $maxStockSub = WaInventoryLocationStockStatus::query()
            ->select([
                'wa_inventory_item_id',
                're_order_level',
                'max_stock'
            ])
            ->where('wa_location_and_stores_id', request()->location);

        $openingStockSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as opening_stock_count')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('created_at', '<', $from)
            ->groupBy('stock_id_code');

        $purchasesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as purchases_count')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('document_no', 'like', 'GRN-%')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $transferInSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as transfers_in_count')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('qauntity', '>', 0)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'TRANS-%')
                    ->orWhere('document_no', 'like', 'MARCH24-%');
            })
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $transferOutSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as transfers_out_count')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('qauntity', '<', 0)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'TRANS-%')
                    ->orWhere('document_no', 'like', 'MARCH24-%');
            })
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $exclSalesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as excl_total_sales')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('document_no', 'like', 'INV-%')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $returnsSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as returns_count')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('document_no', 'like', 'RTN-%')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $salesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as total_sales')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $smallPacksSub = WaStockMove::query()
            ->select([
                'items.wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity) / conversion_factor) as pack_sales')
            ])
            ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wa_stock_moves.wa_inventory_item_id')
            ->where('wa_location_and_store_id', request()->location)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->groupBy('stock_id_code');

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->groupBy('stock_id_code');

        $qooSub = WaPurchaseOrderItem::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(quantity) as qty_on_order')
            ])
            ->whereHas('getPurchaseOrder', function ($query) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->doesntHave('grns');

                $query->where('wa_location_and_store_id', request()->location);
            })->groupBy('wa_inventory_item_id');


        $lastLpoDateSub = WaPurchaseOrderItem::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('MAX(created_at) as last_lpo_date')
            ])
            ->whereHas('getPurchaseOrder', function ($query) {
                $query->where(function ($query) {
                    $query->where('status', 'APPROVED')
                        ->orWhere('status', 'COMPLETED');
                })
                    ->where('is_hide', '<>', 'Yes')
                    ->where('wa_location_and_store_id', request()->location);
            })
            ->groupBy('wa_inventory_item_id');


        $lastGrnDateSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('MAX(created_at) as last_grn_date')
            ])
            ->where('wa_location_and_store_id', request()->location)
            ->where('document_no', 'like', 'GRN-%')
            ->groupBy('wa_inventory_item_id');

        $query = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.description',
                'wa_inventory_items.wa_inventory_category_id',
                'max_stocks.max_stock',
                'max_stocks.re_order_level',
                'last_lpo_dates.last_lpo_date',
                'last_grn_dates.last_grn_date',
                DB::raw('IFNULL(opening_stocks.opening_stock_count, 0) as opening_stock_count'),
                DB::raw('IFNULL(purchases.purchases_count, 0) as purchases_count'),
                DB::raw('IFNULL(transfers_in.transfers_in_count, 0) as transfers_in_count'),
                DB::raw('IFNULL(transfers_out.transfers_out_count, 0) as transfers_out_count'),
                DB::raw('IFNULL(excl_sales.excl_total_sales, 0) as excl_total_sales'),
                DB::raw('IFNULL(returns.returns_count, 0) as returns_count'),
                DB::raw('ROUND(IFNULL(sales.total_sales,0) + IFNULL(packs.pack_sales, 0),2) as total_sales'),
                DB::raw('ROUND(IFNULL(packs.pack_sales, 0), 2) as pack_sales'),
                DB::raw('IFNULL(qoh.quantity, 0) as qoh'),
                DB::raw('IFNULL(lpo.qty_on_order, 0) as qty_on_order'),
                DB::raw('(SELECT category_description FROM wa_inventory_categories WHERE wa_inventory_categories.id = wa_inventory_items.wa_inventory_category_id)  as category'),

            ])
            ->with([
                'suppliers.users'
            ])
            ->leftJoinSub($maxStockSub, 'max_stocks', 'max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($openingStockSub, 'opening_stocks', 'opening_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($purchasesSub, 'purchases', 'purchases.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($transferInSub, 'transfers_in', 'transfers_in.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($transferOutSub, 'transfers_out', 'transfers_out.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($exclSalesSub, 'excl_sales', 'excl_sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($returnsSub, 'returns', 'returns.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($smallPacksSub, 'packs', 'packs.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qooSub, 'lpo', 'lpo.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($lastLpoDateSub, 'last_lpo_dates', 'last_lpo_dates.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($lastGrnDateSub, 'last_grn_dates', 'last_grn_dates.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->where(function ($query) {
                $query->where('qoh.quantity', 0)
                    ->orWhereNull('qoh.quantity');
            })
            ->where(DB::raw('IFNULL(total_sales,0) + IFNULL(pack_sales,0)'), 0)
            ->where('wa_inventory_items.status', 1);

        if (request()->supplier) {
            $query->whereHas('suppliers', function ($query) {
                $query->where('wa_suppliers.id', request()->supplier);
            });
        }


        if (request()->action) {
            $items = $query->get()->map(function ($item) {
                $users = [];
                $item->suppliers->each(function ($supplier) use (&$users) {
                    $users = array_merge($users, $supplier->users->pluck('name')->toArray());
                });

                $item->users = implode(',', $users);

                $item->suppliers = implode(',', $item->suppliers->pluck('name')->toArray());

                $item->variance = $item->qoh - $item->max_stock;

                return $item;
            });

            if (request()->action == 'excel') {
                $view = view('admin.reports.export.inactive_stock_report', [
                    'date_range' => Carbon::parse($from)->format('d/M/Y') . ' - ' . Carbon::parse($to)->format('d/M/Y'),
                    'items' => $items->sortBy('users'),
                    'store' => WaLocationAndStore::find(request()->location)->location_name
                ]);

                return Excel::download(new ExportViewToExcel($view), 'missing_items_report_' . date('YmdHis') . '.xlsx');
            }
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->addColumn('suppliers', function ($inventory) {
                    return implode(',', $inventory->suppliers->pluck('name')->toArray());
                })
                ->addColumn('users', function ($inventory) {
                    $users = [];
                    $inventory->suppliers->each(function ($supplier) use (&$users) {
                        $users = array_merge($users, $supplier->users->pluck('name')->toArray());
                    });

                    return implode(',', $users);
                })
                ->editColumn('qoh', function ($inventory) {
                    return $inventory->qoh == 0 ? "<b class='text-danger'>$inventory->qoh</b>" : manageAmountFormat($inventory->qoh);
                })
                ->addColumn('variance', function ($inventory) {
                    $balance = $inventory->qoh - $inventory->max_stock;
                    $formatted = manageAmountFormat($balance);
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
                ->rawColumns(['variance', 'qoh'])
                ->toJson();
        }


        return view('admin.reports.inactive_stock_report', [
            'model' => $this->model,
            'title' => $this->title,
            'locations' => WaLocationAndStore::get(),
            'suppliers' => WaSupplier::get(),
        ]);
    }
}
