<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryLocationItemReturn;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesByCenterReportController extends Controller
{

    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'route-performance-report';
        $this->base_route = 'route-performance-report';
        $this->base_title = 'sales by Centers  Report';
        $this->permissions_module = 'sales-and-receivables-reports';
    }
    public function summary(Request $request)
    {

        $title = 'Sales By Center Report';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if(!$request->datePicker){

            return view("admin.sales_and_receivables_reports.sales_by_center.summary", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }else{
            if ($request->selectionType  == 'single'){
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();

            }else{
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }

        }
        $startDate = $start_date;
        $endDate = $end_date;
        $routeId = $request->route_id;
        $group = $request->group;
        /*get all invoices for given date range */
        $monthRange = getMonthRangeBetweenDate($startDate, $endDate);
        if ($monthRange > 12) {
            Session::flash('warning', "You can't select more than 12 months.");
            return view("admin.sales_and_receivables_reports.sales_by_center.summary", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }
        $months  = $this->getMonthsInRange($startDate, $endDate);
        $multiplier = 1;
        if($startDate and $endDate) {
            $number_of_days = $endDate->diffInDays($startDate) + 1;
            if($number_of_days == 1) {
                $multiplier = 1;
            }else{
                $multiplier = floor((($number_of_days / 7) ));
            }
        }


        $monthlySalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('DATE_FORMAT(wa_internal_requisitions.created_at, "%Y-%m") as sales_month'),
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as monthly_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id', 'sales_month');

        $totalSalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as total_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');

        $subquery = DB::table('wa_internal_requisitions')
            ->select('wa_route_customer_id', DB::raw('MAX(created_at) as last_order_date'))
            ->where('created_at', '<=', $endDate)
            ->groupBy('wa_route_customer_id');
        $totalTonnageSubquery = DB::table('wa_internal_requisition_items')
            ->join('wa_inventory_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('SUM((wa_inventory_items.net_weight * wa_internal_requisition_items.quantity) / NULLIF(1000, 0)) as total_tonnage'),
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');
        $totalReturnsSubquery = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->leftJoin('wa_internal_requisitions', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns'),
                DB::raw('DATE_FORMAT(wa_inventory_location_transfer_item_returns.created_at, "%Y-%m") as sales_month'),
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS monthly_returns'),
            )
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');

        $customerCountSubquery = DB::table('wa_route_customers')
            ->join('wa_internal_requisitions', 'wa_route_customers.id', '=', 'wa_internal_requisitions.wa_route_customer_id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('COUNT(DISTINCT wa_route_customers.id) as customer_count')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');

        $orderDaysSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('COUNT(DISTINCT DATE(wa_internal_requisitions.created_at)) as order_days')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');

        $lastOrderDateSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('MAX(wa_internal_requisitions.created_at) as last_order_date')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');

        $invoices = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->leftJoinSub($subquery, 'last_orders', function ($join) {
                $join->on('wa_route_customers.id', '=', 'last_orders.wa_route_customer_id');
            })
            ->leftJoinSub($totalSalesSubquery, 'total_sales', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'total_sales.delivery_centres_id');
            })
            ->leftJoinSub($totalReturnsSubquery, 'total_returns', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'total_returns.delivery_centres_id');
            })
            ->leftJoinSub($monthlySalesSubquery, 'monthly_sales', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'monthly_sales.delivery_centres_id');
            })
            ->leftJoinSub($totalTonnageSubquery, 'total_tonnage', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'total_tonnage.delivery_centres_id');
            })
            ->leftJoinSub($customerCountSubquery, 'customer_count', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'customer_count.delivery_centres_id');
            })
            ->leftJoinSub($orderDaysSubquery, 'order_days', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'order_days.delivery_centres_id');
            })
            ->leftJoinSub($lastOrderDateSubquery, 'last_order_date', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'last_order_date.delivery_centres_id');
            })
            ->select(
                'wa_route_customers.delivery_centres_id',
                'delivery_centres.name as delivery_centre_name',
                'routes.route_name',
                'routes.group as group',
                'routes.order_frequency',
                DB::raw('total_sales.total_sales as total_sales_per_centre'),
                DB::raw('COALESCE(total_returns.total_returns, 0) as total_returns_per_centre'),
                DB::raw('total_sales.total_sales - COALESCE(total_returns.total_returns, 0) as sales_minus_returns'),
                DB::raw('JSON_OBJECTAGG(monthly_sales.sales_month, COALESCE(monthly_sales.monthly_sales, 0) - COALESCE(total_returns.monthly_returns, 0)) as gross_monthly_sales'),
                DB::raw('total_tonnage.total_tonnage as total_tonnage_per_centre'),
                DB::raw('customer_count.customer_count as customer_count_per_centre'),
                DB::raw('order_days.order_days as order_days_per_centre'),
                DB::raw($multiplier . ' * routes.order_frequency as scheduled_order_days'),
                DB::raw('last_order_date.last_order_date as last_order_date_per_centre'),
                DB::raw('COUNT(DISTINCT DATE(wa_internal_requisitions.created_at)) as order_days_count'),
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->when($routeId, function ($query, $routeId) {
                return $query->where('routes.id', $routeId);
            })
            ->when($group, function ($query, $group) {
                return $query->where('routes.group', $group);
            })
            ->groupBy('wa_route_customers.delivery_centres_id', 'delivery_centres.name', 'total_sales.total_sales')
            ->orderByDesc('total_sales_per_centre')
            ->get();

        $invoices = $invoices->map(function ($item) {
            $monthlySales = json_decode($item->gross_monthly_sales, true);
            $item->gross_sales = array_sum($monthlySales);
            $item->monthly_sales = $monthlySales;
            return $item;
        });


        $months = collect($invoices)->flatMap(function ($invoice) {
            return array_keys($invoice->monthly_sales);
        })->unique()->sort()->values()->all();

        if ($request->intent == 'EXCEL') {
            $headings = array_merge(['ROUTE', 'GROUP', 'CENTER','CUSTOMER COUNT', 'FREQ', 'ORDER DAYS','LAST ORDER DATE','TONNAGE','TOTAL SALES'], array_map(function($month) {
                return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
            }, $months));

            $filename = "SALES BY CENTER  $startDate - $endDate";
            $excelData = [];
            foreach ($invoices as $row) {

                $payload = [
                    'route' => $row->route_name,
                    'group' => $row->group,
                    'center' => $row->delivery_centre_name,
                    'customer_count' => $row->customer_count_per_centre,
                    'frequency' => $row->scheduled_order_days,
                    'order_days_count' => $row->order_days_per_centre,
                    'last_order_date' => Carbon::parse($row->last_order_date_per_centre)->format('Y-m-d'),
                    'tonnage' => $row->total_tonnage_per_centre,
                    'total_sales' => $row->gross_sales_per_center,
                ];
                foreach ($months as $month) {
                    $payload[\Carbon\Carbon::createFromFormat('Y-m', $month)->format('M')] = $row->gross_monthly_sales[$month] ?? 0;
                }
                $excelData[] = $payload;
            }
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

//        dd($invoices);

        return view("admin.sales_and_receivables_reports.sales_by_center.summary", compact('months','invoices','title', 'model', 'breadcum', 'routes', 'branches'));
    }

    public function getMonthsInRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        $months = [];

        while ($start->lte($end)) {
            $months[] = [
                'name' => $start->format('M'),
                'start' => $start->copy()->startOfMonth()->toDateString(),
                'end' => $start->copy()->endOfMonth()->toDateString(),
            ];
            $start->addMonth();
        }
        return $months;
    }

    public function topCenters(Request $request)
    {
        $title = 'Top Centers by Sales and Tonnage';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if(!$request->datePicker){

            return view("admin.sales_and_receivables_reports.sales_by_center.top_centers", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }else{
            if ($request->selectionType  == 'single'){
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();

            }else{
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }

        }
        $startDate = $start_date;
        $endDate = $end_date;
        $routeId = $request->route_id;
        /*get all invoices for given date range */
        $monthRange = getMonthRangeBetweenDate($startDate, $endDate);
        if ($monthRange > 12) {
            Session::flash('warning', "You can't select more than 12 months.");
            return view("admin.sales_and_receivables_reports.sales_by_center.top_centers", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }
        $totalReturnsSubquery = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->leftJoin('wa_internal_requisitions', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.delivery_centres_id',
                DB::raw('SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns')
            )
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.delivery_centres_id');


        $data = DB::table('wa_internal_requisition_items')
            ->join('wa_inventory_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->leftJoinSub($totalReturnsSubquery, 'total_returns', function ($join) {
                $join->on('wa_route_customers.delivery_centres_id', '=', 'total_returns.delivery_centres_id');
            })
            ->select(
                'routes.route_name',
                'delivery_centres.name as delivery_centre_name',
                'routes.group as route_group',
                'wa_route_customers.delivery_centres_id',
                DB::raw('SUM((wa_inventory_items.net_weight * wa_internal_requisition_items.quantity) / NULLIF(1000, 0)) as total_tonnage'),
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) - COALESCE(total_returns.total_returns, 0) as total_sales'),
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->when($routeId, function ($query, $routeId) {
                return $query->where('routes.id', $routeId);
            })
            ->groupBy('wa_route_customers.delivery_centres_id')
            ->orderByDesc('total_sales')
            ->get();


        if ($request->intent == 'EXCEL') {
            $headings = ['ROUTE', 'GROUP', 'CENTER','TOTAL SALES', 'TOTAL TONNAGE'];
            $filename = "Top centers by Tonnage $startDate - $endDate";
            $excelData = [];
            foreach ($data as $row) {

                $payload = [
                    'route' => $row->route_name,
                    'group' => $row->route_group,
                    'center' => $row->delivery_centre_name,
                    'total_sales' => $row->total_sales,
                    'total_tonnage' => $row->total_tonnage,
                ];
                $excelData[] = $payload;
            }
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }
        return view("admin.sales_and_receivables_reports.sales_by_center.top_centers", compact('data','title', 'model', 'breadcum', 'routes', 'branches'));
    }
    public function topCustomers(Request $request)
    {
        $title = 'Top Customers by Sales and Tonnage';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if(!$request->datePicker){

            return view("admin.sales_and_receivables_reports.sales_by_center.top_customers", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }else{
            if ($request->selectionType  == 'single'){
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();

            }else{
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }

        }
        $startDate = $start_date;
        $endDate = $end_date;
        $routeId = $request->route_id;
        $group = $request->group;
        $count = $request->count ?? 50;
        /*get all invoices for given date range */
        $monthRange = getMonthRangeBetweenDate($startDate, $endDate);
        if ($monthRange > 12) {
            Session::flash('warning', "You can't select more than 12 months.");
            return view("admin.sales_and_receivables_reports.sales_by_center.top_customers", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }

        $totalReturnsSubquery = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->leftJoin('wa_internal_requisitions', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->select(
                'wa_route_customers.id as customer_id',
                DB::raw('SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns')
            )
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->groupBy('wa_route_customers.id');

        $data = DB::table('wa_internal_requisition_items')
            ->join('wa_inventory_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->leftJoinSub($totalReturnsSubquery, 'total_returns', function ($join) {
                $join->on('wa_route_customers.id', '=', 'total_returns.customer_id');
            })
            ->select(
                'wa_route_customers.id as customer_id',
                'wa_route_customers.name as customer_name',
                'wa_route_customers.phone as customer_phone',
                'delivery_centres.name as delivery_centre_name',
                'routes.route_name',
                DB::raw('SUM((wa_inventory_items.net_weight * wa_internal_requisition_items.quantity) / NULLIF(1000, 0)) as total_tonnage'),
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) - COALESCE(total_returns.total_returns, 0) as total_sales'),
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->when($routeId, function ($query, $routeId) {
                return $query->where('routes.id', $routeId);
            })
            ->groupBy('wa_route_customers.id')
            ->orderByDesc('total_sales')
            ->get();


        if ($request->intent == 'EXCEL') {
            $headings = ['ROUTE', 'CENTER','NAME', 'PHONE','TOTAL  SALES', 'TOTAL TONNAGE'];
            $filename = "Top Customers by Tonnage $startDate - $endDate";
            $excelData = [];
            foreach ($data as $row) {

                $payload = [
                    'route' => $row->route_name,
                    'center' => $row->delivery_centre_name,
                    'name' => $row->customer_name,
                    'phone' => $row->customer_phone,
                    'total_sales' => $row->total_sales,
                    'total_tonnage' => $row->total_tonnage,
                ];
                $excelData[] = $payload;
            }
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        return view("admin.sales_and_receivables_reports.sales_by_center.top_customers", compact('data','title', 'model', 'breadcum', 'routes', 'branches'));

    }

    public function dormantCustomers(Request $request)
    {
        $title = 'Dormant Customers';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();

        $routeId = $request->route_id;

        $days = $request->days ?? 30;
        $baseQuery = DB::table('wa_route_customers')
            ->leftJoin('wa_internal_requisitions', 'wa_route_customers.id', '=', 'wa_internal_requisitions.wa_route_customer_id')
            ->leftJoin('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->leftJoin('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->select(
                'wa_route_customers.id as customer_id',
                'wa_route_customers.name as customer_name',
                'wa_route_customers.phone as customer_phone',
                'delivery_centres.name as delivery_centre_name',
                'routes.route_name',
                DB::raw('MAX(wa_internal_requisitions.created_at) as last_purchase_date'),
                DB::raw('DATEDIFF(NOW(), MAX(wa_internal_requisitions.created_at)) as days_since_last_purchase')
            )
            ->groupBy('wa_route_customers.id', 'wa_route_customers.name', 'delivery_centres.name', 'routes.route_name')
            ->orderByDesc('days_since_last_purchase');


        $baseQuery->havingRaw('days_since_last_purchase > ?', [$days]);

        $data = $baseQuery->get();

        if ($request->intent == 'EXCEL') {
            $headings = ['ROUTE', 'CENTER','NAME', 'PHONE', 'LAST ORDER DATE', 'DAYS SINCE LAST ORDER'];
            $filename = "Dormant Customers ". today()->format('Y-m-d');
            $excelData = [];
            foreach ($data as $row) {

                $payload = [
                    'route' => $row->route_name,
                    'center' => $row->delivery_centre_name,
                    'name' => $row->customer_name,
                    'phone' => $row->customer_phone,
                    'last_purchase_date' => $row->last_purchase_date,
                    'days_since_last_purchase' => $row->days_since_last_purchase,
                ];
                $excelData[] = $payload;
            }
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        return view("admin.sales_and_receivables_reports.sales_by_center.dormant_customers", compact('data','title', 'model', 'breadcum', 'routes', 'branches'));
    }

    public function globalSales(Request $request)
    {
        $title = 'Global sales Detailed';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if(!$request->datePicker){

            return view("admin.sales_and_receivables_reports.sales_by_center.global_sales", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }else{
            if ($request->selectionType  == 'single'){
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();

            }else{
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }

        }
        $startDate = $start_date;
        $endDate = $end_date;
        $routeId = $request->route_id;
        $group = $request->group;
        /*get all invoices for given date range */
        $monthRange = getMonthRangeBetweenDate($startDate, $endDate);
        if ($monthRange > 12) {
            Session::flash('warning', "You can't select more than 12 months.");
            return view("admin.sales_and_receivables_reports.sales_by_center.global_sales", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }

        $totalSalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->select(
                'routes.id as route_id',
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as total_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('routes.id');

        $monthlySalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->select(
                'routes.id as route_id',
                DB::raw('DATE_FORMAT(wa_internal_requisitions.created_at, "%Y-%m") as sales_month'),
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as monthly_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('routes.id', 'sales_month');
        $totalReturnsSubquery = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->leftJoin('wa_internal_requisitions', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->select(
                'wa_route_customers.route_id',
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns'),
                DB::raw('DATE_FORMAT(wa_inventory_location_transfer_item_returns.created_at, "%Y-%m") as sales_month'),
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS monthly_returns'),
            )
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->groupBy('routes.id');
        $monthlyReturnsSubquery = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->leftJoin('wa_internal_requisitions', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->select(
                'wa_route_customers.route_id',
                DB::raw('DATE_FORMAT(wa_inventory_location_transfer_item_returns.created_at, "%Y-%m") as sales_month'),
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS monthly_returns'),
            )
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->groupBy('routes.id','sales_month');

        $data = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->leftJoinSub($monthlySalesSubquery, 'monthly_sales', function ($join) {
                $join->on('routes.id', '=', 'monthly_sales.route_id');
            })
            ->leftJoinSub($totalSalesSubquery, 'total_sales', function ($join) {
                $join->on('routes.id', '=', 'total_sales.route_id');
            })
            ->leftJoinSub($totalReturnsSubquery, 'total_returns', function ($join) {
                $join->on('routes.id', '=', 'total_returns.route_id');
            })
            ->leftJoinSub($monthlyReturnsSubquery, 'monthly_returns', function ($join) {
                $join->on('routes.id', '=', 'monthly_returns.route_id');
            })
            ->select(
                'wa_internal_requisitions.created_at',
                'routes.route_name',
                'routes.group as group',
                DB::raw('total_sales.total_sales - total_returns.total_returns as sales_amount'),
                DB::raw('JSON_OBJECTAGG(monthly_sales.sales_month, COALESCE(monthly_sales.monthly_sales, 0) - COALESCE(total_returns.monthly_returns, 0)) as sales_for_month'),

            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->when($routeId, function ($query, $routeId) {
                return $query->where('routes.id', $routeId);
            })
            ->when($group, function ($query, $group) {
                return $query->where('routes.group', $group);
            })
            ->groupBy('routes.id')
            ->orderByDesc('sales_amount')
            ->get();

        $invoices = $data->map(function ($item) {
            $monthlySales = json_decode($item->sales_for_month, true);
            $item->gross_sales = array_sum($monthlySales);
            $item->sales_for_month = $monthlySales;
            return $item;
        });

        $months = collect($invoices)->flatMap(function ($invoice) {
            return array_keys($invoice->sales_for_month);
        })->unique()->sort()->values()->all();

        if ($request->intent == 'EXCEL') {

            $headings = array_merge(['ROUTE','TOTAL SALES'], array_map(function($month) {
                return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
            }, $months));
            $filename = "Global Sales Report ". today()->format('Y-m-d');
            $excelData = [];
            foreach ($data as $row) {

                $payload = [
                    'route' => $row->route_name,
                    'total_sales' => number_format($row->sales_amount, 2),
                ];
                foreach ($months as $month) {
                    $payload[\Carbon\Carbon::createFromFormat('Y-m', $month)->format('M')] = number_format($row->sales_for_month[$month] ?? 0, 2);
                }
                $excelData[] = $payload;
            }
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        return view("admin.sales_and_receivables_reports.sales_by_center.global_sales", compact('months','data','title', 'model', 'breadcum', 'routes', 'branches'));
    }
    public function globalSalesSummary(Request $request)
    {
        $title = 'Global sales  Summary';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if(!$request->datePicker){

            return view("admin.sales_and_receivables_reports.sales_by_center.global_sales_summary", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }else{
            if ($request->selectionType  == 'single'){
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();

            }else{
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }

        }
        $startDate = $start_date;
        $endDate = $end_date;
        $routeId = $request->route_id;
        $group = $request->group;
        /*get all invoices for given date range */
        $monthRange = getMonthRangeBetweenDate($startDate, $endDate);
        if ($monthRange > 12) {
            Session::flash('warning', "You can't select more than 12 months.");
            return view("admin.sales_and_receivables_reports.sales_by_center.global_sales_summary", compact('title', 'model', 'breadcum', 'routes', 'branches'));

        }


        $returns = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) as total_returns'),
                DB::raw('YEAR(wa_inventory_location_transfer_item_returns.created_at) as year'),
                DB::raw('MONTH(wa_inventory_location_transfer_item_returns.created_at) as month')
            )
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $salesData = DB::table('wa_internal_requisition_items')->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_cost_with_vat) as monthly_sales')
            )
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $totalSales = $salesData->sum('monthly_sales');
        $totalReturns= $returns->sum('total_returns');
        $grossSales = $totalSales - $totalReturns;

        $salesByMonthFormatted = $salesData->map(function ($item1) use ($returns) {
            $item2 = $returns->first(function ($item2) use ($item1) {
                return $item2->year === $item1->year && $item2->month === $item1->month;
            });

            $monthly_sales = (int) $item1->monthly_sales;
            $total_returns = $item2 ? (int) $item2->total_returns : 0;
            return (object) [
                'year' => $item1->year,
                'month' =>Carbon::create()->month($item1->month)->format('F'),
                'total_sales' => $monthly_sales - $total_returns,
            ];
        });

        $data  = [
            'total_sales' => $grossSales,
            'sales_by_month' => $salesByMonthFormatted,
        ];


        return view("admin.sales_and_receivables_reports.sales_by_center.global_sales_summary", compact('data','title', 'model', 'breadcum', 'routes', 'branches'));
    }



}
