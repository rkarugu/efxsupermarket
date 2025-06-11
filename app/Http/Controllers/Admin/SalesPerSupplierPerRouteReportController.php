<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GeneralExcelExport;
use App\Http\Controllers\Controller;
use App\Model\WaCustomer;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesPerSupplierPerRouteReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-per-supplier-per-route';
        $this->title = 'Sales Per Supplier';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.salesreceiablesreports';
    }
    public function index(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $customers  = WaCustomer::orderBy('customer_name')->pluck('customer_name', 'id')->toArray();
        $dateFilter = null;
        $dateFilter2 = null;
        $routeFilter = null;
        $routeFilter2 = null;
        $bindings = [];
        if($request->route_id){
            $route  = $request->route_id;
            $routeFilter = "wa_internal_requisitions.customer_id = ? and";
            $bindings [] = $route;
        }

        if ($request->from && $request->to){
            $fromDate = \Carbon\Carbon::parse($request->from)->startOfDay();
            $toDate = \Carbon\Carbon::parse($request->to)->endOfDay();        
            $dateFilter = "and wa_internal_requisition_items.created_at between ? and ?";
            $bindings[] = $fromDate;
            $bindings[] = $toDate; 

        }
        if($request->route_id){
            $route  = $request->route_id;
            $routeFilter2 = "wa_internal_requisitions.customer_id = ? and";
            $bindings [] = $route;
        }

        if ($request->from && $request->to){
            $fromDate = \Carbon\Carbon::parse($request->from)->startOfDay();
            $toDate = \Carbon\Carbon::parse($request->to)->endOfDay();        
            $dateFilter2 = "and wa_internal_requisition_items.created_at between ? and ?";
            $bindings[] = $fromDate;
            $bindings[] = $toDate;

        }
        if ($request->has('manage-request') || $request->has('type')){
            $query = DB::table('wa_inventory_item_suppliers');
            $data = $query->select([
                // 'wa_suppliers.name as supplier_name',
                'wa_inventory_items.stock_id_code as stock_id_code',
                'wa_inventory_items.title as item_name',
                'wa_inventory_items.selling_price as price',
                DB::raw("(select sum(wa_internal_requisition_items.quantity) from wa_internal_requisition_items 
                left join wa_internal_requisitions on wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                where $routeFilter wa_internal_requisition_items.wa_inventory_item_id  = wa_inventory_items.id $dateFilter ) as qty"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items 
                left join wa_internal_requisitions on wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                where $routeFilter2 wa_internal_requisition_items.wa_inventory_item_id  = wa_inventory_items.id $dateFilter2 ) as gross_sales"),
    
            ])->leftJoin('wa_suppliers', 'wa_suppliers.id', '=', 'wa_inventory_item_suppliers.wa_supplier_id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_item_suppliers.wa_inventory_item_id')
            ->whereNotNull('wa_suppliers.name');

            if($request->supplier_id){
                $data = $data->where('wa_suppliers.id', $request->supplier_id);
                $bindings[] = $request->supplier_id; 
    
            }
            $data = $data->setBindings($bindings)->get();
          

        }else{
            $data = [];
        }      

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if($request->type && $request->type == 'Excel'){
               return ExcelDownloadService::download('sales-per-route', collect($data), ['STOCK ID CODE', 'TITLE', 'PRICE', 'QTY', 'GROSS SALES']);
            }
        
           
            $breadcum = [$title => route('sales-per-supplier-per-route'), 'Listing' => ''];
            return view($basePath . '.sales_per_supplier_per_route', compact('title', 'model', 'breadcum', 'pmodule', 'permission','customers', 'data'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        

    }
}

