<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MissingItemssalesReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Missing Items Sales';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.missing_items_sales';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $start = $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->todate ? Carbon::parse($request->todate)->endOfDay() : Carbon::now()->endOfDay();
        $missingItems = DB::table('missing_items_sales')
            ->select(
                'missing_items_sales.created_at as created_at',
                'missing_items_sales.invoice_number as invoice_number',
                'missing_items_sales.order_quantity',
                'missing_items_sales.qoh as qoh_as_at',
                'wa_inventory_items.title as item_name',
                'wa_inventory_items.selling_price',
                'wa_inventory_items.stock_id_code as stock_id_code',
                'routes.route_name as route',
                DB::raw("(SELECT (wa_grns.created_at) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as last_purchase_date"),
                DB::raw("(SELECT (wa_internal_requisition_items.created_at)
                    FROM wa_internal_requisition_items
                    WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                    ORDER BY wa_internal_requisition_items.created_at DESC
                    LIMIT 1
                ) AS last_sale_date"),
                DB::raw("(SELECT (wa_suppliers.name) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    LEFT JOIN wa_suppliers ON wa_grns.wa_supplier_id = wa_suppliers.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as supplier"),
                DB::RAW("(SELECT GROUP_CONCAT(users.name SEPARATOR ', ')
                    FROM wa_inventory_item_suppliers
                    LEFT JOIN wa_user_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_user_suppliers.wa_supplier_id
                    LEFT JOIN users ON  wa_user_suppliers.user_id = users.id
                    WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                ) AS procurement_users"),
            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'missing_items_sales.wa_inventory_item_id')
            ->leftJoin('salesman_shifts', 'salesman_shifts.id', '=', 'missing_items_sales.shift_id')
            ->leftJoin('routes', 'routes.id', '=', 'salesman_shifts.route_id')
            ->whereBetween('missing_items_sales.created_at', [$start, $end]);
            if($request->branch){
                $missingItems = $missingItems->where('routes.restaurant_id', $request->branch);
            }
          
        $missingItems = $missingItems->get();
        if($request->intent && $request->intent == 'Excel'){
            $data = [];
            foreach ($missingItems as $item){
                $payload = [
                    'created_at' => Carbon::parse($item->created_at)->toDateString(),
                    'route' => $item->route,
                    'invoice_number' => $item->invoice_number,
                    'stock_id_code' => $item->stock_id_code,
                    'item_name' => $item->item_name,
                    'last_purchase_date' => $item->last_purchase_date,
                    'last_sale_date' => $item->last_sale_date,
                    'supplier' => $item->supplier,
                    'procurement_users' => $item->procurement_users,
                    'qoh_as_at' => $item->qoh_as_at,
                   'selling_price' => $item->selling_price,
                   'order_quantity' => $item->order_quantity,
                ];
                $data[] = $payload;
            }
            return ExcelDownloadService::download('missing_items'.$start.'_'.$end, collect($data), ['DATE', 'ROUTE', 'INVOICE NO.', 'STOCK ID CODE', 'ITEM', 'LAST PURCHASE DATE', 'LAST SALE DATE', 'SUPPLIER','PROCUREMENT USERS', 'QOH AS AT', 'SELLING PRICE', 'ORDER QUANTITY']);
        }
        if (isset($permission[$pmodule . '___missing-items-sales']) || $permission == 'superadmin') {
            $breadcum = [$title => route('missing-items-sales.index'), 'Listing' => ''];
            return view('admin.missing_items_sales.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'missingItems'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
