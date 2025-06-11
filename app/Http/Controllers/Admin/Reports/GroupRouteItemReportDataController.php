<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Route;
use App\Model\WaCustomer;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaStockMove;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryItem;
use App\Model\WaRouteCustomer;
use Carbon\Carbon;
use App\SalesmanShift;
use App\Services\ExcelDownloadService;


class GroupRouteItemReportDataController extends Controller
{

    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'route-ctns-dzns-performance-report';
        $this->base_route = 'route-performance-report';
        $this->resource_folder = 'admin.salesreceiablesreports';
        $this->base_title = 'Route CTNS / DNZS Performance Report';
        $this->permissions_module = 'sales-and-receivables-reports';
    }

    public function index(Request $request)
    {

        $title = 'Route Ctns/Dnzs Report';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'Route Ctns/Dnzs Report' => ''];

        if ($request->has('ctns_dzns') && $request->ctns_dzns == 'unmet') {
            return $this->getUnmetShops($request);
        }
        $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        $routes = Route::select('id','route_name')->get();
        $routeId = $request->route_id;
        $route = Route::find($routeId);

        if ($route)
        {
            $records = DB::table('wa_internal_requisition_items')
                ->whereBetween('wa_internal_requisition_items.created_at', [$startDate, $endDate])
                ->select(
                    'wa_internal_requisition_items.selling_price',
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'pack_sizes.title as pack_size',
                    DB::raw('sum(wa_internal_requisition_items.quantity) as quantity'),
                    DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity) as returned_quantity'),
                    DB::raw('wa_inventory_items.net_weight * wa_internal_requisition_items.quantity / 1000 as tonnage'),
                )
                ->join('wa_internal_requisitions', function ($join) use ($routeId) {
                    $join->on('wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')->where('wa_internal_requisitions.route_id', $routeId);
                })
                ->join('wa_inventory_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->join('wa_inventory_location_transfer_items', 'wa_internal_requisition_items.id', '=', 'wa_inventory_location_transfer_items.wa_internal_requisition_item_id')
                ->leftJoin('wa_inventory_location_transfer_item_returns', 'wa_inventory_location_transfer_items.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id')
                ->join('pack_sizes', function ($join) use ($request) {
                    $query = $join->on('wa_inventory_items.pack_size_id', '=', 'pack_sizes.id');
                    if ($request->ctns_dzns == 'ctns') {
                        $query = $query->whereIn('pack_sizes.id', [3]);
                    } else {
                        $query = $query->whereIn('pack_sizes.id', [6, 9, 17, 4, 10, 1]);
                    }
                })->groupBy('wa_internal_requisition_items.wa_inventory_item_id')
                ->orderBy('quantity', 'DESC')
                ->get();
            if ($request->intent == 'EXCEL')
            {
                $headings = ['ROUTE','ITEM', 'PACK SIZE', 'SELLING PRICE', 'QTY SOLD', 'QTY RETURNED', 'TOTAL SALES','TONNAGE'];
                $filename = "$route->route_name ROUTE PERFORMANCE REPORT  $startDate - $endDate";
                $excelData = [];
                foreach ($records as $record) {

                    $payload = [
                        'route' => $route->route_name ,
                        'item' => $record->stock_id_code .' - '. $record->title ,
                        'pack_size' => $record->pack_size ,
                        'selling_price' =>  manageAmountFormat($record->selling_price),
                        'qty_sold' => $record->quantity,
                        'qrt_returned' => $record ->returned_quantity ?? 0 ,
                        'total_sales' =>  manageAmountFormat(($record->quantity - $record->returned_quantity) * $record->selling_price),
                        'tonnage' =>  number_format($record->tonnage,4),
                    ];
                    $excelData[] = $payload;
                }
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            }
            return view("admin.salesreceiablesreports.group-filter-route-item-report", compact('title', 'model', 'breadcum', 'route', 'records','routes'));
        }

        return view("admin.salesreceiablesreports.group-filter-route-item-report", compact('title', 'model', 'breadcum', 'route','routes'));
    }

    public function getGroupedItems(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        $routeId = $request->route;

        $query = DB::table('routes');
        if ($request->route) {
            $query->where('routes.id', $request->route);
        }

        $data = $query->select([
            'routes.id as route_id',
            'routes.route_name as route',
            'routes.group as group',
            'users.name as salesman',
            'routes.order_taking_days',
            'routes.tonnage_target',
            'routes.sales_target',
            'routes.ctn_target',
            'routes.dzn_target',
            DB::raw("(select count(*) FROM delivery_centres where delivery_centres.route_id = routes.id and delivery_centres.deleted_at is null) as centre_count"),
            DB::raw("(select count(*) FROM wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as shop_count"),
            // DB::raw("(select count(distinct wa_route_customer_id) FROM wa_internal_requisitions where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.created_at between '$startDate' and '$endDate') as met_shops"),
            DB::raw("(select count(distinct salesman_shift_customers.route_customer_id) 
            from wa_internal_requisitions 
            join salesman_shift_customers on wa_internal_requisitions.wa_shift_id = salesman_shift_customers.salesman_shift_id
            where salesman_shift_customers.visited = 1 and wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.created_at between '$startDate' and '$endDate') as met_shops"),
            DB::raw("(SELECT SUM(CASE WHEN pack_sizes.title = 'CTN' THEN wa_internal_requisition_items.quantity ELSE 0 END) 
                        FROM wa_internal_requisition_items
                        LEFT JOIN wa_inventory_items ON wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                        LEFT JOIN pack_sizes ON wa_inventory_items.pack_size_id = pack_sizes.id
                        LEFT JOIN wa_internal_requisitions ON wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                        WHERE wa_internal_requisitions.route_id = routes.id AND wa_internal_requisition_items.created_at BETWEEN '$startDate' AND '$endDate')
                        AS ctns"),
            DB::raw("(SELECT GROUP_CONCAT(CONCAT(wa_inventory_items.id, ':', wa_inventory_items.title, ':', wa_internal_requisition_items.quantity, ':', wa_inventory_items.stock_id_code) SEPARATOR ';') 
                        FROM wa_internal_requisition_items
                        LEFT JOIN wa_inventory_items ON wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                        LEFT JOIN pack_sizes ON wa_inventory_items.pack_size_id = pack_sizes.id
                        LEFT JOIN wa_internal_requisitions ON wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                        WHERE wa_internal_requisitions.route_id = routes.id AND wa_internal_requisition_items.created_at BETWEEN '$startDate' AND '$endDate' 
                                AND pack_sizes.title = 'CTN')
                        AS ctn_items"),
            DB::raw("(SELECT SUM(CASE WHEN pack_sizes.id IN (1,6,9,17,4,10) THEN wa_internal_requisition_items.quantity ELSE 0 END) 
                        FROM wa_internal_requisition_items
                        LEFT JOIN wa_inventory_items ON wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                        LEFT JOIN pack_sizes ON wa_inventory_items.pack_size_id = pack_sizes.id
                        LEFT JOIN wa_internal_requisitions ON wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                        WHERE wa_internal_requisitions.route_id = routes.id AND wa_internal_requisition_items.created_at BETWEEN '$startDate' AND '$endDate')
                        AS dzns"),

            DB::raw("(SELECT GROUP_CONCAT(CONCAT(wa_inventory_items.id, ':', wa_inventory_items.title, ':', wa_internal_requisition_items.quantity, ':', wa_inventory_items.stock_id_code) SEPARATOR ';') 
                        FROM wa_internal_requisition_items
                        LEFT JOIN wa_inventory_items ON wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                        LEFT JOIN pack_sizes ON wa_inventory_items.pack_size_id = pack_sizes.id
                        LEFT JOIN wa_internal_requisitions ON wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                        WHERE wa_internal_requisitions.route_id = routes.id AND wa_internal_requisition_items.created_at BETWEEN '$startDate' AND '$endDate' 
                                AND pack_sizes.id IN (1,6,9,17,4,10))
                        AS dzn_items"),
            DB::raw("(SELECT GROUP_CONCAT(CONCAT(wa_route_customers.id, ':', wa_route_customers.name) SEPARATOR ', ') 
                        FROM wa_route_customers 
                        WHERE wa_route_customers.route_id = routes.id 
                        AND wa_route_customers.deleted_at IS NULL) AS shop_names_and_ids"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT CONCAT(wa_route_customers.id, ':', wa_route_customers.name) SEPARATOR ', ') 
                        FROM wa_internal_requisitions
                        JOIN wa_route_customers ON wa_route_customers.id = wa_internal_requisitions.wa_route_customer_id
                        WHERE wa_internal_requisitions.route_id = routes.id 
                        AND wa_internal_requisitions.created_at BETWEEN '$startDate' AND '$endDate') AS met_shop_names_and_ids"),
        ])
            ->join('route_user', 'routes.id', '=', 'route_user.route_id')
            ->join('users', function ($join) {
                $join->on('route_user.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->get();

        if ($request->group) {
            $data = $data->where('group', $request->group)->all();
        }

        $data = collect($data);

        $groupedData = $data->groupBy('group');

        $ctnItems = [];
        $dznItems = [];
        $ctnsItems = [];
        $dznsItems = [];
        $mainUnmetShops = [];
        $totalCtnsQuantity = 0;
        $totalDznsQuantity = 0;
        $groupSums = [];
        $groupdata = null;
        $unmetShops = [];
        $unmetShopNamesAndIds = [];
        $unmetShopCount = 0;


        foreach ($groupedData as $group => $item) {
            $groupSums[$group] = $item;
            $groupdata = $group;
        }

        if ($request->has('ctns_dzns') && $request->ctns_dzns == 'unmet') {
            $unmetShops = [];

            foreach ($groupSums[$group] as $row) {
                $shopNamesAndIds = explode(', ', $row->shop_names_and_ids);
                $metShopNamesAndIds = explode(', ', $row->met_shop_names_and_ids);
                $unmetShopsInGroup = array_diff($shopNamesAndIds, $metShopNamesAndIds);
                $unmetShops = array_merge($unmetShops, $unmetShopsInGroup);
            }

            $mainUnmetShops = [];

            foreach ($unmetShops as $shopNameAndId) {
                $parts = explode(':', $shopNameAndId);
                if(count($parts) === 2){
                    [$id, $name] = $parts;
                    $mainUnmetShops[] = ['id' => $id, 'name' => $name];

                }
                
            }

            $unmetShopCount = count($mainUnmetShops);
        }

        $ctnTotal = [];

        foreach ($groupSums[$group] as $group) {
            $ctnItems = !empty($group->ctn_items) ? explode(';', $group->ctn_items) : [];
            $dznItems = !empty($group->dzn_items) ? explode(';', $group->dzn_items) : [];

            if ($request->has('ctns_dzns') && $request->ctns_dzns == 'ctns') {
                foreach ($ctnItems as $ctnItem) {
                    if (!empty($ctnItem)) {
                        $ctnDetails = explode(':', $ctnItem);
                        $item = WaInventoryItem::with('pack_size')->find($ctnDetails[0]);
                        $ctnsItems[$ctnDetails[0]] = [
                            'id' => isset($ctnDetails[0]) ? $ctnDetails[0] : null,
                            'title' => isset($ctnDetails[1]) ? $ctnDetails[1] : null,
                            'quantity' => isset($ctnDetails[2]) ? floatval($ctnDetails[2]) : null,
                            'pack_size' => isset($item->pack_size->title) ? $item->pack_size->title : null,
                            'stock_id_code' => isset($ctnDetails[3]) ? $ctnDetails[3] : null
                        ];
                        $totalCtnsQuantity = ($ctnDetails[2] ?? 0 );
                    }
                }
            }

            if ($request->has('ctns_dzns') && $request->ctns_dzns == 'dzns') {
                foreach ($dznItems as $dznItem) {
                    if (!empty($dznItem)) {
                        $dznDetails = explode(':', $dznItem);
                        $item = WaInventoryItem::with('pack_size')->find($dznDetails[0]);
                        $dznsItems[$dznDetails[0]] = [
                            'id' => isset($dznDetails[0]) ? $dznDetails[0] : null,
                            'title' => isset($dznDetails[1]) ? $dznDetails[1] : null,
                            'quantity' => isset($dznDetails[2]) ? floatval($dznDetails[2]) : null,
                            'pack_size' => isset($item->pack_size->title) ? $item->pack_size->title : null,
                            'stock_id_code' => isset($dznDetails[3]) ? $dznDetails[3] : null
                        ];
                        $totalDznsQuantity = $dznDetails[2] ?? 0;
                    }
                }
            }
        }

        if ($request->intent == 'EXCEL') {

            if (!empty($unmetShops) && $request->has('ctns_dzns') && $request->ctns_dzns == 'unmet') {
                $headings = ['SHOP ID', 'SHOP NAME'];
                $filename = "UNMET SHOPS REPORT $startDate - $endDate";
            } elseif (!empty($ctnsItems) && $request->has('ctns_dzns') && $request->ctns_dzns == 'ctns') {
                $headings = ['ITEM ID', 'ITEM STOCK CODE', 'ITEM DESCRIPTION', 'PACK SIZE', 'QUANTITY'];
                $filename = "CTNS REPORT $startDate - $endDate";
            } elseif (!empty($dznsItems) && $request->has('ctns_dzns') && $request->ctns_dzns == 'dzns') {
                $headings = ['ITEM ID', 'ITEM STOCK CODE', 'ITEM DESCRIPTION', 'PACK SIZE', 'QUANTITY'];
                $filename = "DZNS REPORT $startDate - $endDate";
            }

            $excelData = [];

            if (!empty($ctnsItems) && $request->has('ctns_dzns') && $request->ctns_dzns == 'ctns') {
                foreach ($ctnsItems as $item) {
                    $payload = [
                        'ITEM ID' => $item['id'],
                        'ITEM STOCK CODE' => $item['stock_id_code'],
                        'ITEM NAME' => $item['title'],
                        'PACK SIZE' => $item['pack_size'],
                        'QUANTITY' => $item['quantity'],
                    ];
                    $excelData[] = $payload;
                }
            } elseif (!empty($dznsItems) && $request->has('ctns_dzns') && $request->ctns_dzns == 'dzns') {
                foreach ($dznsItems as $item) {
                    $payload = [
                        'ITEM ID' => $item['id'],
                        'ITEM STOCK CODE' => $item['stock_id_code'],
                        'ITEM NAME' => $item['title'],
                        'PACK SIZE' => $item['pack_size'],
                        'QUANTITY' => $item['quantity'],
                    ];
                    $excelData[] = $payload;
                }
            } elseif (!empty($unmetShops) && $request->has('ctns_dzns') && $request->ctns_dzns == 'unmet') {

                foreach ($mainUnmetShops as $item) {
                    $payload = [
                        'SHOP ID' => $item['id'],
                        'SHOP NAME' => $item['name'],
                    ];
                    $excelData[] = $payload;
                }
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        $title = $this->base_title;
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'Route CTNS / DNZS Report' => ''];
        $routes = Route::select('id', 'route_name')->get();

        return view("$this->resource_folder.group-main-filter-route-item-report", compact('title', 'model', 'breadcum', 'routes', 'ctnsItems', 'dznsItems', 'totalCtnsQuantity', 'totalDznsQuantity', 'groupdata', 'unmetShopCount', 'mainUnmetShops'));

    }

    public function getUnmetShops(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        $routeId = $request->route_id;
        $route = Route::find($routeId);
        $orderFrequency = null;

        if ($request->frequency_filter) {
            $orderFrequency = $request->frequency_filter;
        }

        $query = DB::table('routes');
        if ($request->route_id) {
            $query->where('routes.id', $request->route_id)->first();
        }

        $data = $query->select([
            'routes.id as route_id',
            'routes.route_name as route',
            'routes.group as group',
            'users.name as salesman',
            'routes.order_taking_days',
            'routes.tonnage_target',
            'routes.sales_target',
            'routes.ctn_target',
            'routes.dzn_target',
            DB::raw("(select count(*) FROM delivery_centres where delivery_centres.route_id = routes.id and delivery_centres.deleted_at is null) as centre_count"),
            DB::raw("(select count(*) FROM wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as shop_count"),
            DB::raw("(select count(distinct wa_route_customer_id) FROM wa_internal_requisitions where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.created_at between '$startDate' and '$endDate') as met_shops"),
            DB::raw("(SELECT GROUP_CONCAT(CONCAT(wa_route_customers.id, ':', wa_route_customers.name, ':', wa_route_customers.bussiness_name, ':', wa_route_customers.phone, ':', delivery_centres.name) SEPARATOR ', ') 
                    FROM wa_route_customers 
                    LEFT JOIN delivery_centres ON wa_route_customers.delivery_centres_id = delivery_centres.id
                    WHERE wa_route_customers.route_id = routes.id 
                    AND wa_route_customers.deleted_at IS NULL) AS shop_names_and_ids_with_centre"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT CONCAT(wa_route_customers.id, ':', wa_route_customers.name, ':', wa_route_customers.bussiness_name, ':', wa_route_customers.phone, ':', delivery_centres.name) SEPARATOR ', ') 
                    FROM wa_internal_requisitions
                    JOIN wa_route_customers ON wa_route_customers.id = wa_internal_requisitions.wa_route_customer_id
                    LEFT JOIN delivery_centres ON wa_route_customers.delivery_centres_id = delivery_centres.id
                    WHERE wa_internal_requisitions.route_id = routes.id 
                    AND wa_internal_requisitions.created_at BETWEEN '$startDate' AND '$endDate') AS met_shop_names_and_ids_with_centre"),
        ])
            ->join('route_user', 'routes.id', '=', 'route_user.route_id')
            ->join('users', function ($join) {
                $join->on('route_user.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->get();

        $unmetShops = [];
        $ctnsItems = [];
        $dznsItems = [];
        $shopNamesAndIds = [];
        $metShopNamesAndIds = [];
        $unmetShopsInGroup = [];
        $mainUnmetShops = [];
        $totalCtnsQuantity = 0;
        $totalDznsQuantity = 0;
        $unmetShopNamesAndIds = [];
        $unmetShopCount = 0;
        $arrayofshops = [];

        foreach ($data as $item) {
            $shopNamesAndIdsWithCentre = explode(', ', $item->shop_names_and_ids_with_centre);
            $metShopNamesAndIdsWithCentre = explode(', ', $item->met_shop_names_and_ids_with_centre);
        
            if ($request->has('ctns_dzns') && $request->ctns_dzns == 'unmet') {
                $unmetShopsInGroup = array_diff($shopNamesAndIdsWithCentre, $metShopNamesAndIdsWithCentre);
                $unmetShops = array_merge($unmetShops, $unmetShopsInGroup);
        
                $mainUnmetShops = [];
        
                foreach ($unmetShops as $shopNameAndIdWithCentre) {
                    [$id, $name, $businessname, $phone, $centreName] = explode(':', $shopNameAndIdWithCentre) + [null, null, null, null, null];
                    $mainUnmetShops[] = ['id' => $id, 'name' => $name, 'businessname' => $businessname, 'phone' => $phone, 'centre' => $centreName];
                }
                
                $unmetShopCount = count($mainUnmetShops);
            }
        }

        if ($request->intent == 'EXCEL') {

            $headings = ['BUSINESS NAME', 'SHOP OWNER NAME', 'SHOP OWNER PHONE', 'DELIVERY CENTRE'];
            $filename = "$route->route_name-UNMET-SHOPS-REPORT-$startDate-$endDate";

            $excelData = [];

            foreach ($mainUnmetShops as $item) {
                $payload = [
                    'BUSINESS NAME' => $item['businessname'],
                    'SHOP OWNER NAME' => $item['name'],
                    'SHOP OWNER PHONE' => $item['phone'],
                    'DELIVERY CENTRE' => $item['centre'],
                ];
                $excelData[] = $payload;
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        
        $model = $this->model;
        $title = 'Route Unmet Shops Performance Report';
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'Route Unmet Shops Report' => ''];

        $routes = Route::select('id', 'route_name')->get();

        return view("$this->resource_folder.group-filter-route-unmet-item-report", compact('title', 'model', 'breadcum', 'route', 'routes', 'orderFrequency', 'routeId', 'ctnsItems', 'dznsItems', 'totalCtnsQuantity', 'totalDznsQuantity', 'unmetShopCount', 'mainUnmetShops'));

    }
    public function getUnmetShops2(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        $routeId = $request->route_id;
        $route = Route::find($routeId);
        $orderFrequency = null;

        if ($request->frequency_filter) {
            $orderFrequency = $request->frequency_filter;
        }
        $allCustomers = WaRouteCustomer::where('route_id', $routeId)
            ->whereHas('lastOrder', function ($query) use ($startDate) {
                $query->whereDate('created_at', '<', $startDate);
            })
            ->with(['lastOrder', 'center' => function ($query) {
                $query->withCount('waRouteCustomers');
            }])
            ->get();

        $query = DB::table('routes');
        $data = $query->select([
           
           'wa_route_customers.id',
       
        ])
            ->leftJoin('salesman_shifts', function($leftJoin) use($startDate, $endDate) {
                $leftJoin->on('salesman_shifts.route_id', '=', 'routes.id')->where('salesman_shifts.created_at','>=', $startDate)->where('salesman_shifts.created_at','<=', $endDate);
            })
            
            ->leftJoin('salesman_shift_customers', function($leftJoin2) use($startDate, $endDate){
                $leftJoin2->on('salesman_shift_customers.salesman_shift_id', '=', 'salesman_shifts.id')->where('visited', '=', '1')->where('salesman_shift_customers.created_at','>=', $startDate)->where('salesman_shift_customers.created_at','<=', $endDate);
            })
            ->leftJoin('wa_route_customers', 'wa_route_customers.id', '=', 'salesman_shift_customers.route_customer_id')
            ->distinct()
            ->pluck('id')
            ->toArray();

        if ($request->intent == 'EXCEL') {

            $headings = ['BUSINESS NAME', 'SHOP OWNER NAME', 'SHOP OWNER PHONE', 'DELIVERY CENTRE','LAST ORDER DATE','SHOPS IN SAME CENTER'];
            $filename = "$route->route_name-UNMET-SHOPS-REPORT-$startDate-$endDate";

            $excelData = [];

            foreach ($allCustomers as $item) {
                if(!in_array($item->id, $data)){
                $payload = [
                    'BUSINESS NAME' => $item->bussiness_name,
                    'SHOP OWNER NAME' => $item->name,
                    'SHOP OWNER PHONE' => $item->phone,
                    'DELIVERY CENTRE' => $item->center?->name,
                    'LAST ORDER DATE' => $item->lastOrder?->created_at->format('Y-m-d'),
                    'SHOPS IN SAME CENTER' => $item->center ->wa_route_customers_count,
                ];
                $excelData[] = $payload;
            }
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }
        
        
        $model = $this->model;
        $title = 'Route Unmet Shops Performance Report';
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'Route Unmet Shops Report' => ''];

        $routes = Route::select('id', 'route_name')->get();

//        dd($data);

        return view("$this->resource_folder.group-filter-route-unmet-item-report", compact('title', 'model', 'breadcum', 'route', 'routes', 'orderFrequency', 'routeId', 'data', 'allCustomers'));

    }

}
