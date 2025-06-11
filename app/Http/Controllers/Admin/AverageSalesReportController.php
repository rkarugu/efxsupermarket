<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Exports\Reports\AverageSalesExport;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AverageSalesReportController extends Controller
{
    protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'average-sales-report';
    }

    public function index()
    {
        if (!can('average-sales-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->title = 'Max Stock Report';
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $binSub = WaInventoryLocationUom::query()
            ->select([
                'wa_inventory_location_uom.*',
                'wa_unit_of_measures.title as bin_title'
            ])
            ->join('wa_unit_of_measures', 'wa_unit_of_measures.id', '=', 'wa_inventory_location_uom.uom_id')
            ->where('location_id', request()->location);


        $maxStockSub = WaInventoryLocationStockStatus::query()
            ->select([
                'wa_inventory_item_id',
                're_order_level',
                'max_stock'
            ])
            ->where('wa_location_and_stores_id', request()->location);
        $movesSub = WaStockMove::query()
        ->select([
            'wa_inventory_item_id',
            'stock_id_code',
            DB::raw("
                SUM(CASE 
                    WHEN created_at < '{$from}' THEN qauntity 
                    ELSE 0 
                END) as opening_stock_count
            "),
            DB::raw("
                SUM(CASE 
                    WHEN document_no LIKE 'GRN-%' AND created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END) as purchases_count
            "),
            DB::raw("
                SUM(CASE 
                    WHEN qauntity > 0 AND 
                         (document_no LIKE 'TRANS-%' OR document_no LIKE 'MARCH24-%') AND 
                         created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END) as transfers_in_count
            "),
            DB::raw("
                ABS(SUM(CASE 
                    WHEN qauntity < 0 AND 
                         (document_no LIKE 'TRANS-%' OR document_no LIKE 'MARCH24-%') AND 
                         created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END)) as transfers_out_count
            "),
            DB::raw("
                ABS(SUM(CASE 
                    WHEN document_no LIKE 'INV-%' AND created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END)) as excl_total_sales
            "),
            DB::raw("
                ABS(SUM(CASE 
                    WHEN document_no LIKE 'RTN-%' AND created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END)) as returns_count
            "),
            DB::raw("
                ABS(SUM(CASE 
                    WHEN (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at BETWEEN '{$from}' AND '{$to}' THEN qauntity 
                    ELSE 0 
                END)) as total_sales
            "),
            DB::raw('SUM(qauntity) as quantity')
        ])
        ->where('wa_location_and_store_id', request()->location)
        ->groupBy('wa_inventory_item_id', 'stock_id_code');
    

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

        $query = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.description',
                'wa_inventory_items.wa_inventory_category_id',
                'bin.bin_title',
                'max_stocks.max_stock',
                'max_stocks.re_order_level',
                'suppliers_data.user_names as users',
                'suppliers_data.supplier_names',
                DB::raw('IFNULL(moves.opening_stock_count, 0) as opening_stock_count'),
                DB::raw('IFNULL(moves.purchases_count, 0) as purchases_count'),
                DB::raw('IFNULL(moves.transfers_in_count, 0) as transfers_in_count'),
                DB::raw('IFNULL(moves.transfers_out_count, 0) as transfers_out_count'),
                DB::raw('IFNULL(moves.excl_total_sales, 0) as excl_total_sales'),
                DB::raw('IFNULL(moves.returns_count, 0) as returns_count'),
                DB::raw('ROUND(IFNULL(moves.total_sales,0) + IFNULL(packs.pack_sales, 0),2) as total_sales'),
                DB::raw('ROUND(IFNULL(packs.pack_sales, 0), 2) as pack_sales'),
                DB::raw('IFNULL(moves.quantity, 0) as qoh'),
                DB::raw('IFNULL(lpo.qty_on_order, 0) as qty_on_order'),
            ])
            ->with([
                'category',
                // 'suppliers.users'
            ])
            ->leftJoinSub($binSub, 'bin', 'bin.inventory_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($maxStockSub, 'max_stocks', 'max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($smallPacksSub, 'packs', 'packs.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($movesSub, 'moves', 'moves.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qooSub, 'lpo', 'lpo.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($suppliersSub, 'suppliers_data', 'suppliers_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->where('wa_inventory_items.status', 1)
            ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13]);

        if (request()->supplier) {
            $query->whereHas('suppliers', function ($query) {
                $query->where('wa_suppliers.id', request()->supplier);
            });
        }

        $from = Carbon::createFromFormat('Y-m-d H:i:s',  $from);
        $to = Carbon::createFromFormat('Y-m-d H:i:s',  $to);
        $days = $to->diffInDays($from);
        $months = $days / 30.4;

        if (request()->action) {
            $items = $query->get()->map(function ($item) use ($months) {
                $item->variance = $item->qoh - $item->max_stock;
                $item->suggested_max = ceil($max = 1.15 * ($item->total_sales + $item->pack_sales) / $months);
                $item->suggested_reorder = ceil(0.3 * $max);

                return $item;
            });
            $store = WaLocationAndStore::find(request()->location)->location_name;
            if (request()->action == 'excel') {
                $view = view('admin.reports.export.average_sales_report', [
                    'date_range' => Carbon::parse($from)->format('d/M/Y') . ' - ' . Carbon::parse($to)->format('d/M/Y'),
                    'store' => $store,
                    'items' => $items->sortBy('suppliers'),
                    'months' => $months
                ]);

                return Excel::download(new ExportViewToExcel($view), 'average_sales_report_' . date('YmdHis') . '.xlsx');
            } else if (request()->action == 'download') {
                return $this->exportToExcel($items);
            }
        }

        if (request()->wantsJson()) {
            $resultCount = $this->countRecords(request()->location ?? 46);
            return DataTables::eloquent($query)
                ->setTotalRecords($resultCount)
                ->addColumn('suppliers', function ($inventory) {
                    return $inventory->supplier_names;
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
                ->addColumn('suggested_max_stock', function ($inventory) use ($months) {
                    return ceil(1.15 * ($inventory->total_sales + $inventory->pack_sales) / $months);
                })
                ->addColumn('suggested_reorder', function ($inventory) use ($months) {
                    return ceil(0.3 * (1.15 * ($inventory->total_sales + $inventory->pack_sales) / $months));
                })
                ->rawColumns(['variance', 'qoh'])
                ->toJson();
        }

        return view('admin.reports.average_sales_report', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::get(),
        ]);
    }
    public function countRecords($location){
        return WaInventoryItem::
        // leftJoin('wa_inventory_location_stock_status as bin', 'bin.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            where('status', 1)
            ->whereIn('pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            // ->where('bin.wa_location_and_stores_id', $location)
            ->count();

    
    }

    protected function exportToExcel($items)
    {
        $export = new AverageSalesExport($items, request()->action, null);

        return Excel::download($export, 'average_sales_report' . date('Y-m-d-h-i-s') . '.xlsx');
    }
}
