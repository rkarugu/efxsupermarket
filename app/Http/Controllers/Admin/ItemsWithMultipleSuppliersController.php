<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ItemsWithMultipleSuppliersController extends Controller
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
            'wa_inventory_items.standard_cost',
            'wa_inventory_items.selling_price',
            DB::RAW(' (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as qoh'),
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
        ->whereRaw("(SELECT COUNT(wa_inventory_item_suppliers.id)
            FROM wa_inventory_item_suppliers
            WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
            ) > 1");
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
                    'Standard Cost' => (string)$row->standard_cost,
                    'Selling Price' => (string)$row->selling_price,
                    'Quantity' => (string)(@$row->qoh ?? 0),
                    'Suppliers' => $row->suppliers ?? '',
                    'Users' => $row->users ?? '',
                ];
                $data[] = $payload;
            }
            $headers = ['STOCK ID CODE', 'TITLE', 'CATEGORY', 'PACK SIZE','PRICE LIST COST', 'STANDARD COST', 'SELLING PRICE', 'QUANTITY', 'SUPPLIERS', 'USERS'];

            return ExcelDownloadService::download('items_with_multiple_suppliers', collect($data), $headers);
        }
        if (isset($permission[$pmodule . '___items-with-multiple-suppliers']) || $permission == 'superadmin') {
            $breadcum = [$title => route('items-with-multiple-suppliers.index'), 'Listing' => ''];
            return view('admin.multi_supplier_items.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'categories', 'data_query'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
