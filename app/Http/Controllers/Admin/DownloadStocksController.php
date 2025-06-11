<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DownloadStocksController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'download-stocks';
        $this->pmodel = 'utility';
        $this->title = 'Download Stocks';
        $this->pmodule = 'utility';
    }

    public function index()
    {
        if (!can('download-stocks', $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $suppliers = WaSupplier::get();
        
        return view('admin.utility.download_stocks', compact('title', 'model', 'pmodule', 'permission', 'suppliers'));
    }

    public function processDownloadStocks(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Download') {

                $start_date = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
                $end_date = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');

                $data_query = DB::table('wa_inventory_items')
                    ->select(
                        'wa_inventory_items.title as title',
                        'wa_inventory_items.stock_id_code as stock_id_code',
                        'wa_inventory_categories.category_description as category',
                        'pack_sizes.title as pack_size',
                        'wa_inventory_items.standard_cost',
                        'wa_inventory_items.selling_price',
                        'wa_inventory_items.actual_margin',
                        DB::RAW(' (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code'
                            . ($request->branch ? ' AND wa_stock_moves.wa_location_and_store_id = ' . (int)$request->branch : '')
                            . ') as item_total_quantity'),
                        'tax_managers.title as tax_manager',
                        'wa_inventory_items.image',
                        DB::RAW("(SELECT GROUP_CONCAT(wa_suppliers.name SEPARATOR ', ') 
                        FROM wa_inventory_item_suppliers 
                        LEFT JOIN wa_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_suppliers.id 
                        WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                        ) AS suppliers"),
                        DB::RAW("(SELECT GROUP_CONCAT(users.name SEPARATOR ', ')
                        FROM wa_inventory_item_suppliers
                        LEFT JOIN wa_user_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_user_suppliers.wa_supplier_id
                        LEFT JOIN users ON  wa_user_suppliers.user_id = users.id
                        WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                        ) AS users"),

                    )
                    ->leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', 'wa_inventory_categories.id')
                    ->leftJoin('pack_sizes', 'pack_sizes.id', 'wa_inventory_items.pack_size_id')
                    ->leftJoin('tax_managers', 'wa_inventory_items.tax_manager_id', 'tax_managers.id')
                    ->whereBetween('wa_inventory_items.created_at', [$start_date, $end_date]);

                if ($request->category) {
                    $data_query = $data_query->where('wa_inventory_items.wa_inventory_category_id', $request->category);
                }
                if ($request->branch) {
                    $data_query = $data_query->addSelect(
                        DB::RAW("(SELECT wa_unit_of_measures.title
                                  FROM wa_inventory_location_uom
                                  LEFT JOIN wa_unit_of_measures ON wa_unit_of_measures.id = wa_inventory_location_uom.uom_id
                                  WHERE wa_inventory_location_uom.inventory_id = wa_inventory_items.id
                                  AND wa_inventory_location_uom.location_id = $request->branch
                                  ) AS bin_location")
                    );
                }
                if ($request->supplier) {
                    $supplierItemIds = DB::table('wa_inventory_item_suppliers')
                        ->where('wa_supplier_id', $request->supplier)
                        ->pluck('wa_inventory_item_id')
                        ->toArray();
                    $data_query = $data_query->whereIn('wa_inventory_items.id', $supplierItemIds);
                }
                $data_query = $data_query->get();

                $arrays = [];

                if (!$data_query->isEmpty()) {
                    foreach ($data_query as $row) {
                        $arrays[] = [
                            'Stock Id Code' => (string)($row->stock_id_code),
                            'Title' => $row->title,
                            'Item Category' => $row->category ?? '',
                            'Pack Size' => (string)($row->pack_size ?? ''),
                            'Standard Cost' => (string)$row->standard_cost,
                            'Selling Price' => (string)$row->selling_price,
                            '% MARGIN' => number_format($row->standard_cost != 0 ? ((($row->selling_price - $row->standard_cost) / $row->standard_cost) * 100) : 0, 2),
                            'Quantity' => (string)(@$row->item_total_quantity ?? 0),
                            'Tax Category' => (string)@$row->tax_manager ?? '',
                            'image' => $row->image ?? '',
                            'Suppliers' => $row->suppliers ?? '',
                            'Users' => $row->users ?? '',
                            'Bin Locations' => $row->bin_location ?? '',
                        ];
                    }
                }

                $mainheadings = ['STOCK ID CODE', 'TITLE', 'CATEGORY', 'PACK SIZE', 'STANDARD COST', 'SELLING PRICE', 'PERCENTAGE MARGIN', 'QUANTITY', 'TAX CATEGORY', 'IMAGE', 'SUPPLIERS', 'USERS'];

                $exceldata = $arrays;
                $filename = "Stocks-List-{$start_date}-to-{$end_date}.xlsx";
                return ExcelDownloadService::download($filename, collect($exceldata), $mainheadings);
            } else {
                return response()->json([
                    'error' => 'No file uploaded or incorrect intent',
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error:' . $th->getMessage(),
            ], 500);
        }
    }
}
