<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MaxStockExport;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaInventoryLocationUom;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MaxStockReportController extends Controller
{
    protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'max-stock-report';
    }

    public function index()
    {
        if (!can('max-stock-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->title = 'Max Stock Report';

        $binSub = WaInventoryLocationUom::query()
            ->select([
                'wa_inventory_location_uom.*',
                'wa_unit_of_measures.title as bin_title'
            ])
            ->join('wa_unit_of_measures', 'wa_unit_of_measures.id', '=', 'wa_inventory_location_uom.uom_id')
            ->where('location_id', 46);


        $maxStockSub = WaInventoryLocationStockStatus::query()
            ->select([
                'wa_inventory_item_id',
                're_order_level',
                'max_stock'
            ])
            ->where('wa_location_and_stores_id', 46);

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->where('wa_location_and_store_id', 46)
            ->groupBy('wa_inventory_item_id');

        // 7 Days         
        $start_7 = now()->subDays(7)->format('Y-m-d 00:00:00');
        $end_7 = now()->format('Y-m-d 23:59:59');

        $salesSub_7 = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as sales_qty_7')
            ])
            ->where('wa_location_and_store_id', 46)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('created_at', [$start_7, $end_7])
            ->groupBy('wa_inventory_item_id');

        // 30 Days         
        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $salesSub_30 = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as sales_qty_30')
            ])
            ->where('wa_location_and_store_id', 46)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('created_at', [$start_30, $end_30])
            ->groupBy('wa_inventory_item_id');

        $query = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.wa_inventory_category_id',
                'bin.bin_title',
                'qoh.quantity',
                'max_stocks.max_stock',
                'max_stocks.re_order_level',
                DB::raw('IFNULL(sales_7.sales_qty_7,0) sales_qty_7'),
                DB::raw('IFNULL(sales_30.sales_qty_30,0) sales_qty_30'),
            ])
            ->with([
                'category',
                'suppliers.users'
            ])
            ->leftJoinSub($binSub, 'bin', 'bin.inventory_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($maxStockSub, 'max_stocks', 'max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($salesSub_7, 'sales_7', 'sales_7.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($salesSub_30, 'sales_30', 'sales_30.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->whereHas('suppliers', function ($query) {
                if (request()->supplier) {
                    $query->where('wa_inventory_item_suppliers.wa_supplier_id', request()->supplier);
                }
            })
            ->where('wa_inventory_items.status',1);


        if (request()->action) {
            $items = $query->get()->map(function ($item) {
                $users = [];

                $item->suppliers->each(function ($supplier) use (&$users) {
                    $users = array_merge($users, $supplier->users->pluck('name')->toArray());
                });

                $item->users = implode(',', $users);

                $item->suppliers = implode(',', $item->suppliers->pluck('name')->toArray());

                return $item;
            });

            return $this->exportToExcel($items);
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
                ->toJson();
        }

        return view('admin.reports.max_stock_report', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
        ]);
    }

    protected function exportToExcel($items)
    {
        $export = new MaxStockExport($items);

        return Excel::download($export, 'MAX STOCK DATA REPORT.xlsx');
    }
}
