<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GeneralExcelExport;
use App\Http\Controllers\Controller;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HqDashboardReportsController extends Controller
{
    protected $title;
    protected $model;
    protected $pmodel;

    public function __construct()
    {
        $this->title = 'HQ Dashboard';
        $this->model = 'HQ Dashboard';
        $this->pmodel = 'hq-dashboard-reports';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $authuser = Auth::user();

        if ($authuser->role_id == 1 || isset($permission['employees___chairman_dashboard'])) {
            $model = 'super-admin-dashboard';
            $title = 'Chairman Dashboard';
            return view('admin.page.dashboards.hq_dashboard', compact('model', 'title'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }
    public function orderTakingAndPosSummary(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->toDateString() : Carbon::now()->toDateString();
        $to = $request->to ? Carbon::parse($request->to)->toDateString() : Carbon::now()->toDateString();
        $permission =  $this->mypermissionsforAModule();
        $authuser = Auth::user();
        if ($authuser->role_id == 1 || isset($permission['employees___chairman_dashboard'])) {
            $model = 'super-admin-dashboard';
            $title = 'Order Taking & POS Summary';
            $route_sales_subquery = DB::table('wa_internal_requisition_items')
                ->select(
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as sub_query_route_sales'),
                    'wa_internal_requisitions.restaurant_id',
                )
                ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
               ->where('wa_internal_requisitions.requisition_no', 'like', 'INV%')
                ->whereBetween('wa_internal_requisitions.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->groupBy('wa_internal_requisitions.restaurant_id');

            $pos_sales_subquery = DB::table('wa_internal_requisition_items')
                ->select(
                    DB::raw('COUNT(DISTINCT wa_internal_requisitions.wa_route_customer_id) as pos_met_customers'),
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as sub_query_pos_sales'),
                    'wa_internal_requisitions.restaurant_id',
                )
                ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
               ->where('wa_internal_requisitions.requisition_no', 'like', 'CIV%')
                ->whereBetween('wa_internal_requisitions.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->groupBy('wa_internal_requisitions.restaurant_id');
            
            $tonnage_subquery = DB::table('wa_internal_requisition_items')
                ->select(
                    DB::raw('SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000) as sub_query_tonnage'),
                    'wa_internal_requisitions.restaurant_id',
                )
                ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_internal_requisition_items.wa_inventory_item_id')
                ->whereBetween('wa_internal_requisitions.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->groupBy('wa_internal_requisitions.restaurant_id');

            $routes_subquery = DB::table('wa_internal_requisitions')
                ->select(
                    DB::raw('COUNT(DISTINCT wa_internal_requisitions.route_id) as sub_query_routes'),
                    'wa_internal_requisitions.restaurant_id',
                )
                ->leftJoin('routes', 'routes.id', 'wa_internal_requisitions.route_id')
                ->where('routes.is_physical_route', 1)
                ->where('wa_internal_requisitions.requisition_no', 'like', 'INV%')
                ->whereBetween('wa_internal_requisitions.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->groupBy('wa_internal_requisitions.restaurant_id');

            $customers_subquery = DB::table('salesman_shifts')
                ->select(
                    DB::raw('COUNT(DISTINCT wa_route_customers.id) as sub_query_route_customers'),
                    'routes.restaurant_id',
                )
                ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                ->leftJoin('wa_route_customers', 'routes.id', 'wa_route_customers.route_id')
                ->where('routes.is_physical_route', 1)
                ->whereNull('wa_route_customers.deleted_at')
                ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->groupBy('routes.restaurant_id');
            $visited_customers = DB::table('salesman_shift_customers')
                    ->select(
                        DB::raw('COUNT(DISTINCT salesman_shift_customers.route_customer_id) as sub_query_visited_customers'),
                        'routes.restaurant_id',
                    )
                    ->leftJoin('salesman_shifts', 'salesman_shifts.id', 'salesman_shift_customers.salesman_shift_id')
                    ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                    ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->where('salesman_shift_customers.visited', 1)
                    ->groupBy('routes.restaurant_id');
            $visited_centers = DB::table('salesman_shift_customers')
                    ->select(
                        DB::raw('COUNT(DISTINCT wa_route_customers.delivery_centres_id) as sub_query_visited_centres'),
                        'routes.restaurant_id',
                    )
                    ->leftJoin('salesman_shifts', 'salesman_shifts.id', 'salesman_shift_customers.salesman_shift_id')
                    ->leftJoin('wa_route_customers', 'wa_route_customers.id', 'salesman_shift_customers.route_customer_id')
                    ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                    ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->where('salesman_shift_customers.visited', 1)
                    ->groupBy('routes.restaurant_id');
            $expected_delivery_centres_sub_query = DB::table('salesman_shifts')
                    ->select(
                        DB::raw('COUNT(DISTINCT delivery_centres.id) as sub_query_expected_centres'),
                        'routes.restaurant_id',
    
                    )
                    ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                    ->leftJoin('delivery_centres', 'delivery_centres.route_id', 'routes.id')
                    ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->where('routes.is_physical_route', 1)
                    ->groupBy('routes.restaurant_id');
            $expected_tonnage = DB::table(DB::raw('(SELECT DISTINCT route_id, DATE(created_at) as shift_date FROM salesman_shifts) as distinct_shifts'))
                    ->select(
                        DB::raw('SUM(routes.tonnage_target) as sub_query_expected_tonnage'),
                        'routes.restaurant_id',
                    )
                    // ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                    ->leftJoin('routes', 'routes.id', 'distinct_shifts.route_id')

                    ->where('routes.is_physical_route', 1)
                    // ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->whereBetween('distinct_shifts.shift_date', [$from, $to])

                    ->groupBy('routes.restaurant_id');

            $expected_routes_sub_query = DB::table('salesman_shifts')
                ->select(
                    DB::raw('COUNT(DISTINCT routes.id) as sub_query_expected_routes'),
                    'routes.restaurant_id',

                )
                ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
                ->whereBetween('salesman_shifts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->where('routes.is_physical_route', 1)
                ->groupBy('routes.restaurant_id');

            $data = DB::table('restaurants')
                ->select(
                    'restaurants.name',
                    'route_sales_subquery.sub_query_route_sales as route_sales',
                    'pos_sales_subquery.sub_query_pos_sales as pos_sales',
                    'pos_sales_subquery.pos_met_customers as pos_customers',
                    'tonnage_subquery.sub_query_tonnage as tonnage',
                    'routes_subquery.sub_query_routes as routes_with_orders',
                    'customers_subquery.sub_query_route_customers as branch_customers',
                    'expected_routes_subquery.sub_query_expected_routes as expected_routes',
                    'visited_customers_subquery.sub_query_visited_customers as visited_customers',
                    'visited_centres_subquery.sub_query_visited_centres as visited_centres',
                    'expected_centres_subquery.sub_query_expected_centres as centers',
                    'expected_tonnage_subquery.sub_query_expected_tonnage as expected_tonnage',




                    DB::raw("(SELECT COUNT(routes.id)
                        FROM routes 
                        WHERE routes.restaurant_id = restaurants.id
                        AND routes.is_physical_route = '1'
                    ) as total_routes"),
                    // DB::raw("(SELECT COUNT(delivery_centres.id)
                    //     FROM delivery_centres
                    //     LEFT JOIN routes ON routes.id = delivery_centres.route_id 
                    //     WHERE routes.restaurant_id = restaurants.id
                    //     AND routes.is_physical_route = '1'
                    // ) as centers"),
               
                    )
                ->leftJoinSub($route_sales_subquery, 'route_sales_subquery', 'route_sales_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($pos_sales_subquery, 'pos_sales_subquery', 'pos_sales_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($tonnage_subquery, 'tonnage_subquery', 'tonnage_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($routes_subquery, 'routes_subquery', 'routes_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($customers_subquery, 'customers_subquery', 'customers_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($expected_routes_sub_query, 'expected_routes_subquery', 'expected_routes_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($visited_customers, 'visited_customers_subquery', 'visited_customers_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($visited_centers, 'visited_centres_subquery', 'visited_centres_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($expected_delivery_centres_sub_query, 'expected_centres_subquery', 'expected_centres_subquery.restaurant_id', '=', 'restaurants.id')
                ->leftJoinSub($expected_tonnage, 'expected_tonnage_subquery', 'expected_tonnage_subquery.restaurant_id', '=', 'restaurants.id')




                ->get()->map(function ($record){
                    $record->total_sales = $record->route_sales + $record->pos_sales;
                    return $record;
                })->sortByDesc('total_sales');
            if($request->download && $request->download == 'download'){
                $excelData = [];
                foreach ($data as $record) {
                    $payload = [
                        'branch' => $record->name,
                        'visited_routes' => ($record->routes_with_orders ?? 0) .' / '. ($record->expected_routes ?? 0),
                        'centers' => ($record->visited_centres ?? 0) . ' / '. ($record->centers ?? 0),
                        'customers' => ($record->visited_customers ?? 0) . ' / '. ($record->branch_customers ?? 0),
                        'pos_customers' => $record->pos_customers,
                        'tonnage' => $record->tonnage ?? 0,
                        'route_sales' => $record->route_sales ?? 0,
                        'pos_sales' => $record->pos_sales ?? 0,
                        'total_sales' => $record->total_sales ?? 0
                    ];
                    $excelData[] = $payload;
                }
                $headers = ['BRANCH', 'VISITED ROUTES', 'CENTERS','CUSTOMERS','POS CUSTOMERS', 'TONNAGE', 'ROUTE SALES', 'POS SALES', 'TOTAL SALES'];
                return ExcelDownloadService::download('order_taking_and_pos_summary'.$from.'-'.$to, collect($excelData), $headers);

            }
            return view('admin.page.dashboards.hq_dashboard.order_taking_summary', compact('model', 'title', 'data'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

}
