<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\OutOfStockReport;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OutOfStockReportController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'out-of-stock-report';
        $this->title = 'Out of Stock Report';
    }

    public function index()
    {
        if (!can('out-of-stock-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $qohQuery = "SELECT SUM(qauntity) FROM 
                        `wa_stock_moves`  
                    WHERE `wa_inventory_item_id` = `wa_inventory_items`.`id` 
                        AND wa_location_and_store_id = " . request()->location;

        $qooQuery = "SELECT SUM(quantity) FROM
                        `wa_purchase_order_items`
                            JOIN
                        `wa_purchase_orders` ON `wa_purchase_order_items`.`wa_purchase_order_id` = `wa_purchase_orders`.`id`
                            LEFT JOIN
                        `wa_grns` ON `wa_purchase_orders`.`id` = `wa_grns`.`wa_purchase_order_id`
                     WHERE
                        `wa_purchase_order_items`.`wa_inventory_item_id` = `wa_inventory_items`.`id`
                            AND `status` = 'APPROVED'
                            AND `is_hide` <> 'YES'
                            AND `wa_grns`.id IS NULL
                            AND `wa_location_and_store_id` = " . request()->location;

        // 7 Days         
        $start_7 = now()->subDays(7)->format('Y-m-d 00:00:00');
        $end_7 = now()->format('Y-m-d 23:59:59');
        $sales_7 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_7' AND '$end_7'
                AND wa_location_and_store_id = " . request()->location;

        // 30 Days         
        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $sales_30 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_30' AND '$end_30'
                AND wa_location_and_store_id = " . request()->location;

        // 180 Days         
        $start_150 = now()->subDays(180)->format('Y-m-d 00:00:00');
        $end_150 = now()->subDays(30)->format('Y-m-d 23:59:59');

        $sales_180 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_150' AND '$end_150'
                AND wa_location_and_store_id = " . request()->location;

        $query = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.wa_inventory_category_id',
                'max_stocks.max_stock',
                'max_stocks.re_order_level',
                DB::raw("($qohQuery) as qty_on_hand"),
                DB::raw("($qooQuery) as qty_on_order"),
                DB::raw("(max_stocks.max_stock - ($qohQuery) - ($qooQuery)) as qty_to_order"),
                DB::raw("($sales_7) as sales_7_days"),
                DB::raw("($sales_30) as sales_30_days"),
                DB::raw("($sales_180) as sales_180_days"),
            ])
            ->with([
                'category',
                'suppliers',
                'suppliers.users'
            ])
            ->join('wa_inventory_location_stock_status as max_stocks', function ($join) {
                $join->on('max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('max_stocks.wa_location_and_stores_id', request()->location);
            })
            ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->whereRaw("($qohQuery) <= 1.1 * re_order_level")
            ->whereRaw("(ABS(($sales_7)) > 0 OR ABS(($sales_30)) > 0 OR ABS(($sales_180)) > 0)")
            ->where('wa_inventory_items.status', 1);

        if (request()->user) {
            $supplierSub = WaInventoryItemSupplier::query()
                ->select([
                    'wa_inventory_item_id',
                    'user_id',
                ])
                ->join('wa_user_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_inventory_item_suppliers.wa_supplier_id')
                ->where('user_id',  request()->user);

            $query->joinSub($supplierSub, 'supp', 'supp.wa_inventory_item_id', '=', 'wa_inventory_items.id');
        }

        if (request()->action == 'excel') {
            $items = $query->get()->map(function ($item) {
                $item->suppliers = implode(',', $item->suppliers->pluck('name')->toArray());

                return $item;
            });

            $export = new OutOfStockReport($items);
            return Excel::download($export, 'out_of_stock_items' . date('Y_m_d_H_i_s') . '.xlsx');
        }

        if (request()->action == 'pdf') {
            $items = $query->get()->map(function ($item) {

                $users = [];

                $item->suppliers->each(function ($supplier) use (&$users) {
                    $users = array_merge($users, $supplier->users->pluck('name')->toArray());
                });

                $item->users = implode(',', $users);

                $item->suppliers = implode(',', $item->suppliers->pluck('name')->toArray());

                return $item;
            });

            $description = request()->user ? 'User: ' . User::find(request()->user)->name . ' ' : '';
            $description .= 'Store: ' . WaLocationAndStore::find(46)->location_name;

            $pdf = Pdf::loadView('admin.reports.print.out_of_stock_report', [
                'items' => $items,
                'description' => $description
            ])->setPaper('a4', 'landscape');

            return $pdf->download('out_of_stock_report_' . date('Y_m_d_H_i_s') . '.pdf');
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('sales_7_days', function ($stock) {
                    return number_format(abs($stock->sales_7_days));
                })
                ->editColumn('sales_30_days', function ($stock) {
                    return number_format(abs($stock->sales_30_days));
                })
                ->editColumn('sales_180_days', function ($stock) {
                    return number_format(abs($stock->sales_180_days));
                })
                ->addColumn('qty_to_order', function ($stock) {
                    return number_format($stock->qty_to_order > 0 ? $stock->qty_to_order : 0);
                })
                ->addColumn('suppliers', function ($stock) {
                    return implode(',', $stock->suppliers->pluck('name')->toArray());
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

        return view('admin.reports.out_of_stock_report', [
            'model' => $this->model,
            'title' => $this->title,
            'locations' => WaLocationAndStore::get(),
            'users' => User::whereHas('suppliers')->get(),
        ]);
    }
}
