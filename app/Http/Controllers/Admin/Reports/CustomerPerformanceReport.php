<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaDebtorTran;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPerformanceReport extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'customer-performance-report';
        $this->base_route = 'customer-performance-report';
        $this->base_title = 'Customer Performance Report';
        $this->permissions_module = 'sales-and-receivables-reports';
    }
    public function index(Request $request)
    {
        $title = 'Route Customers Performance Report';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];

        $branches = Restaurant::all();
        $routes = Route::select('id', 'route_name')->get();
        if (!$request->datePicker) {

            return view("admin.sales_and_receivables_reports.customer-performance-report", compact('title', 'model', 'breadcum', 'routes', 'branches'));
        } else {
            if ($request->selectionType  == 'single') {
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();
            } else {
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
            return view("admin.sales_and_receivables_reports.customer-performance-report", compact('title', 'model', 'breadcum', 'routes', 'branches'));
        }
        $months  = $this->getMonthsInRange($startDate, $endDate);
        $multiplier = 1;
        if ($startDate and $endDate) {
            $number_of_days = $endDate->diffInDays($startDate) + 1;
            if ($number_of_days == 1) {
                $multiplier = 1;
            } else {
                $multiplier = floor((($number_of_days / 7)));
            }
        }

        $monthlySalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->select(
                'wa_internal_requisitions.wa_route_customer_id',
                DB::raw('DATE_FORMAT(wa_internal_requisitions.created_at, "%Y-%m") as sales_month'),
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as monthly_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_internal_requisitions.wa_route_customer_id', 'sales_month');

        $totalSalesSubquery = DB::table('wa_internal_requisitions')
            ->join('wa_internal_requisition_items', 'wa_internal_requisitions.id', '=', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->select(
                'wa_internal_requisitions.wa_route_customer_id',
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as total_sales')
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->groupBy('wa_internal_requisitions.wa_route_customer_id');

        $subquery = DB::table('wa_internal_requisitions')
            ->select('wa_route_customer_id', DB::raw('MAX(created_at) as last_order_date'))
            ->where('created_at', '<=', $endDate)
            ->groupBy('wa_route_customer_id');
        $sugarSubQuery = DB::table('wa_internal_requisition_items')
                ->select(
                    'wa_route_customer_id',
                    DB::raw("SUM(wa_internal_requisition_items.total_cost_with_vat) as october_sugar_sales")
                    )
                ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_internal_requisition_items.wa_inventory_item_id')
                ->where('wa_inventory_items.wa_inventory_category_id', 107)
                ->whereDate('wa_internal_requisitions.created_at', '>=', '2024-10-01')
                ->groupBy('wa_internal_requisitions.wa_route_customer_id');

        $invoices = DB::table('wa_internal_requisitions')
            ->join('wa_route_customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'wa_route_customers.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->leftJoinSub($subquery, 'last_orders', function ($join) {
                $join->on('wa_route_customers.id', '=', 'last_orders.wa_route_customer_id');
            })
            ->leftJoinSub($monthlySalesSubquery, 'monthly_sales', function ($join) {
                $join->on('wa_route_customers.id', '=', 'monthly_sales.wa_route_customer_id');
            })
            ->leftJoinSub($totalSalesSubquery, 'total_sales', function ($join) {
                $join->on('wa_route_customers.id', '=', 'total_sales.wa_route_customer_id');
            })
            ->leftJoinSub($sugarSubQuery,'sugarSubQuery', 'sugarSubQuery.wa_route_customer_id', 'wa_route_customers.id')
            ->select(
                'wa_internal_requisitions.created_at',
                'wa_route_customers.id as wa_route_customer_id',
                'wa_route_customers.name as customer_name',
                'wa_route_customers.phone as customer_phone',
                'wa_route_customers.bussiness_name as bussiness_name',
                'delivery_centres.name as delivery_centre_name',
                'routes.route_name',
                'routes.group as group',
                'routes.order_frequency',
                DB::raw('total_sales.total_sales as sales_amount'),  // Use total_sales from subquery
                DB::raw('routes.order_frequency * ' . $multiplier . ' as frequency'),
                DB::raw('COUNT(DISTINCT DATE(wa_internal_requisitions.created_at)) as order_days_count'),
                DB::raw('DATE_FORMAT(MAX(last_orders.last_order_date), "%Y-%m-%d") as last_order_date'),
                DB::raw('JSON_OBJECTAGG(monthly_sales.sales_month, monthly_sales.monthly_sales) as sales_for_month'),
                'sugarSubQuery.october_sugar_sales as october_sugar_sales'
            )
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->when($routeId, function ($query, $routeId) {
                return $query->where('routes.id', $routeId);
            })
            ->when($group, function ($query, $group) {
                return $query->where('routes.group', $group);
            })
            ->groupBy('wa_route_customers.id')
            ->get();

        $invoices = $invoices->map(function ($item) {
            $monthlySales = json_decode($item->sales_for_month, true);
            $item->gross_sales = array_sum($monthlySales);
            $item->monthly_sales = $monthlySales;
            return $item;
        });

        $months = collect($invoices)->flatMap(function ($invoice) {
            return array_keys($invoice->monthly_sales);
        })->unique()->sort()->values()->all();


        if ($request->intent == 'EXCEL') {

            $headings = array_merge(['ROUTE', 'GROUP', 'CENTER', 'CUSTOMER NAME', 'CUSTOMER PHONE', 'BUSINESS NAME', 'FREQUENCY', 'ORDER DAYS COUNT', 'LAST ORDER DATE', 'OCT SUGAR SALES','SALES TOTAL'], array_map(function ($month) {
                return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
            }, $months));

            $filename = "CUSTOMER PERFORMANCE REPORT $startDate - $endDate";
            $excelData = [];

            foreach ($invoices as $row) {
                $payload = [
                    'route' => $row->route_name,
                    'group' => $row->group,
                    'center' => $row->delivery_centre_name,
                    'name' => $row->customer_name,
                    'phone' => $row->customer_phone,
                    'business_name' => $row->bussiness_name,
                    'freq' => $row->frequency,
                    'order_days' => $row->order_days_count,
                    'last_order_date' => $row->last_order_date,
                    'Oct Sugar Sales' => manageAmountFormat($row->october_sugar_sales),
                    'sales' => manageAmountFormat($row->sales_amount),
                ];

                foreach ($months as $month) {
                    $payload[\Carbon\Carbon::createFromFormat('Y-m', $month)->format('M')] = manageAmountFormat($row->monthly_sales[$month] ?? 0);
                }

                $excelData[] = $payload;
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }
        return view("admin.sales_and_receivables_reports.customer-performance-report", compact('invoices', 'months', 'multiplier', 'title', 'model', 'breadcum', 'routes', 'branches'));
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

    public function customer(Request $request)
    {

        $title = 'Customer Performance Report';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];
        if (!$request->datePicker) {

            return redirect()->back()->with('message', 'Select date range');
        } else {
            if ($request->selectionType  == 'single') {
                $start_date = Carbon::parse($request->datePicker)->startOfDay();
                $end_date = Carbon::parse($request->datePicker)->endOfDay();
            } else {
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->startOfDay();
                $end_date = Carbon::parse($dates[1])->endOfDay();
            }
        }
        $startDate = $start_date;
        $endDate = $end_date;
        $customer_id = $request->customer_id;
        $month = $request->month;
        if ($month) {
            $requestedStart = Carbon::parse($month)->startOfMonth();
            $requestedEnd = Carbon::parse($month)->endOfMonth();

            // Ensure requested start is within initial range
            $startDate = max($startDate, $requestedStart);

            // Ensure requested end is within initial range
            $endDate = min($endDate, $requestedEnd);
        }

        $items = WaInternalRequisitionItem::select(
            'wa_internal_requisition_items.id',
            'wa_internal_requisition_items.created_at',
            'wa_internal_requisition_items.wa_internal_requisition_id',
            'wa_internal_requisition_items.wa_inventory_item_id',
            'wa_internal_requisition_items.quantity',
            'wa_internal_requisition_items.total_cost_with_vat',
            'customers.name as customer_name',
            'customers.phone as customer_phone',
            'customers.bussiness_name',
            'items.title as item_name',
            'items.stock_id_code as item_code',
            'routes.route_name as route_name',
            'centres.name as centre_name',
        )
            ->leftJoin('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->leftJoin('wa_route_customers as customers', 'wa_internal_requisitions.wa_route_customer_id', '=', 'customers.id')
            ->leftJoin('wa_inventory_items as items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'items.id')
            ->leftJoin('routes as routes', 'customers.route_id', '=', 'routes.id')
            ->leftJoin('delivery_centres as centres', 'customers.delivery_centres_id', '=', 'centres.id')
            ->where('wa_internal_requisitions.wa_route_customer_id', $customer_id)
            ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate])
            ->get();
        return view("admin.sales_and_receivables_reports.customer-items-report", compact('items', 'title', 'model', 'breadcum'));
    }

    public function salesVsPayments(Request $request)
    {
        $title = 'Sales vs payments Report';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'customer Performance Report' => ''];
        $routes = Route::all();

        $start_date = today()->toDateString();
        $end_date = today()->toDateString();

        $route_id = $request->input('route_id'); // Assuming route_id is passed in the request

        if ($request->datePicker) {
            if ($request->selectionType == 'single') {
                $start_date = Carbon::parse($request->datePicker)->toDateString();
                $end_date = $start_date; // For single selection, both start and end dates are the same
            } else {
                $dates = explode(' to ', $request->datePicker);
                $start_date = Carbon::parse($dates[0])->toDateString();
                $end_date = Carbon::parse($dates[1])->toDateString();
            }
        }

        // Subquery for total sales per day, conditionally applying the date range filter and route_id
        $salesSubquery = DB::table('wa_debtor_trans')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->select(
                DB::raw('DATE(wa_debtor_trans.trans_date) as date'),
                DB::raw('SUM(wa_debtor_trans.amount) as total_sales')
            )
            ->where('wa_debtor_trans.document_no', 'LIKE', 'INV-%')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('wa_debtor_trans.trans_date', [$start_date, $end_date]);
            })
            ->when($route_id, function ($query) use ($route_id) {
                return $query->where('wa_customers.route_id', $route_id);
            })
            ->groupBy(DB::raw('DATE(wa_debtor_trans.trans_date)'));

        $returnsSubquery = DB::table('wa_debtor_trans')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->select(
                DB::raw('DATE(wa_debtor_trans.trans_date) as date'),
                DB::raw('SUM(wa_debtor_trans.amount) as total_returns')
            )
            ->where('wa_debtor_trans.document_no', 'LIKE', 'RTN-%')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('wa_debtor_trans.trans_date', [$start_date, $end_date]);
            })
            ->when($route_id, function ($query) use ($route_id) {
                return $query->where('wa_customers.route_id', $route_id);
            })
            ->groupBy(DB::raw('DATE(wa_debtor_trans.trans_date)'));

        $paymentsSubquery = DB::table('wa_debtor_trans')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->select(
                DB::raw('DATE(wa_debtor_trans.trans_date) as date'),
                DB::raw('SUM(wa_debtor_trans.amount) as total_payments')
            )
            ->where('wa_debtor_trans.document_no', 'LIKE', 'RCT-%')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $start_date_next = Carbon::parse($start_date)->addDay()->toDateString();
                $end_date_next = Carbon::parse($end_date)->addDay()->toDateString();
                return $query->whereBetween('wa_debtor_trans.trans_date', [$start_date_next, $end_date_next]);
            })
            ->when($route_id, function ($query) use ($route_id) {
                return $query->where('wa_customers.route_id', $route_id);
            })
            ->groupBy(DB::raw('DATE(wa_debtor_trans.trans_date)'));

        $salesAndPayments = DB::table(DB::raw("({$salesSubquery->toSql()}) as sales"))
            ->addBinding($salesSubquery->getBindings()) // Bindings for sales subquery
            ->leftJoin(DB::raw("({$paymentsSubquery->toSql()}) as payments"), DB::raw('payments.date'), '=', DB::raw('DATE(DATE_ADD(sales.date, INTERVAL 1 DAY))'))
            ->addBinding($paymentsSubquery->getBindings()) // Bindings for payments subquery
            ->leftJoin(DB::raw("({$returnsSubquery->toSql()}) as returns"), 'returns.date', '=', 'sales.date')
            ->addBinding($returnsSubquery->getBindings()) // Bindings for returns subquery
            ->select(
                'sales.date as sales_date',
                'sales.total_sales',
                DB::raw('IFNULL(payments.total_payments, 0) as total_payments_following_day'),
                DB::raw('IFNULL(returns.total_returns, 0) as total_returns')
            )
            ->get();

        /*downloads for PDF*/
        if ($request->get('manage-request') == "pdf") {
            $pdf = PDF::loadView('admin.sales_and_receivables_reports.sales-vs-payemnts-pdf', compact('title', 'model', 'salesAndPayments'));
            return $pdf->download('sales-vs-payments' . date('Y_m_d_h_i_s') . '.pdf');
        }

        return view("admin.sales_and_receivables_reports.sales-vs-payemnts", compact('salesAndPayments', 'title', 'model', 'breadcum', 'routes'));
    }
}
