<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PriceListCostReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'inventory-reports';
        $this->title = 'Price List Cost';
        $this->pmodule = 'inventory-reports';
        $this->basePath = 'admin.price_list_cost_reports';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $categories = WaInventoryCategory::get();
        $suppliers = WaSupplier::get();
        $data_query = DB::table('wa_inventory_items')
        ->select(
            'wa_inventory_items.title as title',
            'wa_inventory_items.stock_id_code as stock_id_code',
            'wa_inventory_categories.category_description as category',
            'pack_sizes.title as pack_size',
            'wa_inventory_items.price_list_cost',
            // 'wa_inventory_items.last_grn_cost',
            // 'wa_inventory_items.weighted_average_cost',
            'wa_inventory_items.standard_cost',
            'wa_inventory_items.selling_price',
            // 'wa_inventory_items.actual_margin',
            DB::RAW(' (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as qoh'),
            // 'tax_managers.title as tax_manager',
            // 'wa_inventory_items.image',
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
        ->where('wa_inventory_items.status', 1)
        ->where(function($query) {
            $query->whereNull('wa_inventory_items.price_list_cost')
                  ->orWhere('wa_inventory_items.price_list_cost', '<', 10);
        });
        if($request->category){
            $data_query = $data_query->where('wa_inventory_items.wa_inventory_category_id', $request->category);
        }
     
        $data_query = $data_query->get();
    
        
        if($request->intent && $request->intent == 'Excel'){
            $data = [];
            foreach ($data_query as $row){
                $payload = [
                    'Stock Id Code' => (string)($row->stock_id_code),
                    'Title' => $row->title,
                    'Item Category' => $row->category ?? '',
                    'Pack Size' => (string)($row->pack_size ?? ''),
                    'Price List Cost' => manageAmountFormat($row->price_list_cost) ?? '',
                    // 'Last GRN Cost' => manageAmountFormat($row->last_grn_cost) ?? '',
                    // 'Weighted Average Cost' => manageAmountFormat($row->weighted_average_cost) ?? '',
                    'Standard Cost' => (string)$row->standard_cost,
                    'Selling Price' => (string)$row->selling_price,
                    // '% MARGIN' => number_format($row->standard_cost != 0 ? ((($row->selling_price - $row->standard_cost) / $row->standard_cost) * 100) : 0, 2),
                    'Quantity' => (string)(@$row->qoh ?? 0),
                    // 'Tax Category' => (string)@$row->tax_manager ?? '',
                    // 'image' => $row->image ?? '',
                    'Suppliers' => $row->suppliers ?? '',
                    'Users' => $row->users ?? '',
                    // 'Bin Locations' => $row->bin_location ?? '',
                ];
                $data[] = $payload;
            }
            $headers = ['STOCK ID CODE', 'TITLE', 'CATEGORY', 'PACK SIZE','PRICE LIST COST', 'STANDARD COST', 'SELLING PRICE', 'QUANTITY', 'SUPPLIERS', 'USERS'];

            return ExcelDownloadService::download('price_list_costs', collect($data), $headers);
        }
        if (isset($permission[$pmodule . '___price-list-cost-report']) || $permission == 'superadmin') {
            $breadcum = [$title => route('price-list-costs-reports.index'), 'Listing' => ''];
            return view('admin.price_list_cost_reports.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'categories', 'data_query'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
