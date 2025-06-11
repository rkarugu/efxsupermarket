<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryCategory;
use App\Model\WaSupplier;
use App\Models\CompetingBrand;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ItemMarginsReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Margins Report';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.item_margins';
    }
    public function index(Request $request)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            $basePath = $this->basePath;
            $suppliers = WaSupplier::all();
            $competingBrands = CompetingBrand::all();
            $categories = WaInventoryCategory::all();
    
            $qoh_sub_query = DB::table('wa_stock_moves')
                ->select(
                    DB::raw("(SELECT SUM(wa_stock_moves.qauntity)) AS qoh"),
                    'wa_stock_moves.stock_id_code'
                )
                ->groupBy('stock_id_code');
    
            $data = DB::table('wa_inventory_items')
                ->select(
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'wa_suppliers.name as suppliers',
                    'wa_inventory_items.selling_price',
                    'wa_inventory_items.standard_cost',
                    'wa_inventory_items.margin_type',
                    'wa_inventory_items.actual_margin',
                    'qoh_sub_query.qoh AS qoh',
    
                    DB::raw("
                    (SELECT JSON_UNQUOTE(JSON_EXTRACT(trade_agreement_discounts.other_options, CONCAT('$.\"', wa_inventory_items.id, '\".discount')))
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'End month Discount'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1
    
                    )AS monthly_discount"),
                    DB::raw("
                    (SELECT discount_value_type
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'End month Discount'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1
    
                    )AS monthly_discount_type"),
                    DB::raw("
                    (SELECT JSON_UNQUOTE(JSON_EXTRACT(trade_agreement_discounts.other_options, CONCAT('$.\"', wa_inventory_items.id, '\".discount')))
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'Quarterly Discount'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1 
    
                    )AS quarterly_discount"),
                    DB::raw("
                    (SELECT discount_value_type
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'Quarterly Discount'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1
    
                    )AS quarterly_discount_type"),
                    DB::raw("
                    (SELECT JSON_UNQUOTE(JSON_EXTRACT(trade_agreement_discounts.other_options, CONCAT('$.\"', wa_inventory_items.id, '\".discount')))
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'Distribution Discount on Delivery'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1
    
                    )AS delivery_discount"),
                    DB::raw("
                    (SELECT discount_value_type
                        FROM trade_agreements
                        LEFT JOIN trade_agreement_discounts ON trade_agreements.id = trade_agreement_discounts.trade_agreements_id
                        WHERE trade_agreement_discounts.discount_type = 'Distribution Discount on Delivery'
                        AND  trade_agreements.wa_supplier_id = wa_inventory_item_suppliers.wa_supplier_id
                        AND JSON_CONTAINS_PATH(trade_agreement_discounts.other_options, 'one', CONCAT('$.\"', wa_inventory_items.id, '\"'))
                        LIMIT 1
    
                    )AS delivery_discount_type"),
                    DB::RAW("(SELECT GROUP_CONCAT(users.name SEPARATOR ', ')
                        FROM wa_user_suppliers
                        LEFT JOIN users ON  wa_user_suppliers.user_id = users.id
                        WHERE wa_user_suppliers.wa_supplier_id = wa_suppliers.id
                    ) AS procurement_users"),
                    )
                ->leftJoin('wa_inventory_item_suppliers', 'wa_inventory_item_suppliers.wa_inventory_item_id', 'wa_inventory_items.id')
                ->leftJoin('wa_suppliers', 'wa_suppliers.id', 'wa_inventory_item_suppliers.wa_supplier_id')
                ->leftJoin('competing_brand_items', 'competing_brand_items.wa_inventory_item_id', 'wa_inventory_items.id')
                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_items.id')
                ->leftJoinSub($qoh_sub_query, 'qoh_sub_query', 'qoh_sub_query.stock_id_code', 'wa_inventory_items.stock_id_code')
                ->whereNull('wa_inventory_assigned_items.destination_item_id')
                ->where('wa_inventory_items.status', 1);
            if($request->supplier){
                $data = $data->where('wa_inventory_item_suppliers.wa_supplier_id', $request->supplier);
            }
            if($request->category){
                $data = $data->where('wa_inventory_items.wa_inventory_category_id', $request->category);
            }
            if($request->brand){
                $data =  $data->where('competing_brand_items.competing_brand_id',  $request->brand);
            }
            
           $data = $data ->orderBy('wa_inventory_items.id', 'desc')
            ->get();
    
            if($request->intent && $request->intent == 'Excel'){
                $excelData = [];
                foreach ($data as $row) {
                    $payload =  [
                        'stock_id_code' => $row->stock_id_code,
                        'title' => $row->title,
                       'supplier' => $row->suppliers,
                       'users' => $row->procurement_users,
                       'qoh' => $row->qoh,
                       'actual_margin' => $row->actual_margin. ($row->margin_type == 1 ? ' %' : ' KES'),
                       'discount_on_delivery' =>  $row->delivery_discount ? $row->delivery_discount . ($row->delivery_discount_type == 'Percentage' ? ' %': ' KES') : '-' ,
                       'end_month_discount' => $row->monthly_discount ? $row->monthly_discount . ($row->monthly_discount_type == 'Percentage' ? ' %': ' KES') : '-' ,
                       'quarterly_discount' =>   $row->quarterly_discount ? $row->quarterly_discount . ($row->quarterly_discount_type == ' Percentage' ? ' %': ' KES') : '-',
                       'total_margin' => ($row->actual_margin + $row->delivery_discount + $row->monthly_discount + $row->quarterly_discount) . ($row->margin_type == 1 ? ' %' : ' KES')
                    ];
                    $excelData [] = $payload;
                }
                $headers = ['STOCK ID CODE',  'TITLE', 'SUPPLIER', 'USERS', 'QOH', "ACTUAL MARGIN", 'DISCOUNT ON  DELIVERY', 'END MONTH DISCOUNT', 'QUARTERLY DISCOUNT', 'TOTAL MARGIN'];
                return ExcelDownloadService::download('item_margins', collect($excelData), $headers);
    
            }
           
            if (isset($permission[$pmodule . '___item-margins-report']) || $permission == 'superadmin') {
                $breadcum = [$title => route('item-margins-report.index'), 'Listing' => ''];
                return view('admin.item_margins.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'competingBrands', 'categories', 'data'));
            } else {
                Session::flash('warning', 'Access Denied');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('warning', $th->getMessage());
            
        }
       
    }
}
