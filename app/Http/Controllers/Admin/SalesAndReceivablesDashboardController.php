<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaPosCashSalesItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalesAndReceivablesDashboardController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'sales-and-receivables-dashboard';
        $this->title = 'Dashboard Reports';
        $this->pmodule = 'dashboard_report';
      
    }
    public function index(Request $request)
    {
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $title = 'Sales and Receivables Dashboard';
        $model = 'sales-and-receivables-dashboard';
        if (isset($permission['module-dashboards__sales-and-receivables']) || $permission == 'superadmin') {
            $restro_count = Restaurant::count();
            $users_count = User::whereIn('role_id', ['11'])->count();
            $earningStats = $this->getTotalEarningStats();
            $start_date = date('Y-m-d');
            $end_date =  date('Y-m-d', strtotime("-15 days", strtotime(date('Y-m-d'))));



            $sale_transaction_month = date('m');
            $sale_transaction_year = date('Y');

            if ($request->has('sale_transaction_month')) {
                $sale_transaction_month = $request->input('sale_transaction_month');
            }

            if ($request->has('sale_transaction_year')) {
                $sale_transaction_year = $request->input('sale_transaction_year');
            }

            $sales_transaction_stats = $this->getSalesTransactionDetails($sale_transaction_month, $sale_transaction_year);
            $highestsellingsalesman = $this->gethighestsellingsalesman();
            $highestsellingproducts = $this->gethighestsellingproducts();

            return view('admin.dashboard_report.sales_and_receivables_dashboard', compact('title', 'model','permission','pmodule', 'restro_count', 'users_count','earningStats',
            'sales_transaction_stats', 'highestsellingsalesman','highestsellingproducts'));

            
        }
        else{
                Session::flash('warning', 'Permission denied');
                return redirect()->back();
        }
    }
    public function getSalesTransactions(Request $request){
        $selectedMonth = $request->month;
        $selectedYear = $request->year;

        $startDate = $selectedYear . '-' . $selectedMonth . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        $startDate = $startDate.' 00:00:00';
        $endDate =  $endDate.' 23:59:59';

        $query = "
            SELECT 
                DATE(created_at) AS sales_date, 
                SUM(total_cost_with_vat) AS total_sales
            FROM 
                wa_internal_requisition_items
            WHERE 
                created_at BETWEEN ? AND ? 
            GROUP BY 
                sales_date 
            ORDER BY 
                sales_date ASC
        ";

        $saleSummary = DB::select($query, [$startDate, $endDate]);

        $routePerformance = DB::table('routes')->select([
            'routes.route_name as route',
            DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items 
            join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id 
            where wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate') 
            as gross_sales"),
            DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) 
            from wa_inventory_location_transfer_item_returns 
            join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id 
            join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id 
            where wa_inventory_location_transfers.route = routes.route_name and wa_inventory_location_transfer_item_returns.created_at between '$startDate' and '$endDate') 
            as returns"),
        ])
            ->join('route_user', 'routes.id', '=', 'route_user.route_id')
            ->join('users', function ($join) {
                $join->on('route_user.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->get()
            ->map(function ($record) {
                $record->net_sales = $record->gross_sales - $record->returns;
                return (object)[
                    'route' => $record->route,
                    'net_sales' => $record->net_sales,
                ];
            })->sortBy('net_sales', descending: true)
            ->take(10);
        $productPerformance = WaInventoryLocationTransferItem::select(DB::raw('SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as sale_amount'), 'wa_inventory_items.stock_id_code as item_no', 'wa_inventory_items.title as item_name')
            ->groupBy('wa_inventory_item_id')
            ->join('wa_inventory_items', function ($w) {
                $w->on('wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id');
            })
            ->whereMonth('wa_inventory_location_transfer_items.created_at', $selectedMonth)
            ->orderBy('sale_amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($row){
                return (object)[
                    'item' => $row->item_name,
                    'code' => $row->item_no,
                    'amount' => $row->sale_amount,
                ];
            });
        $customerPerformance = DB::table('wa_route_customers')->select([
                'wa_route_customers.bussiness_name as business_name',
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items 
                join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id 
                where wa_internal_requisitions.wa_route_customer_id = wa_route_customers.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate') 
                as gross_sales"),
                // DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) 
                // from wa_inventory_location_transfer_item_returns 
                // join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id 
                // join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id 
                // where wa_inventory_location_transfers.route = routes.route_name and wa_inventory_location_transfer_item_returns.created_at between '$startDate' and '$endDate') 
                // as returns"),
            ])
            ->orderBy('gross_sales', 'desc')
            ->limit(6)
            ->get();
            // dd($customerPerformance->toArray());

            $chartData = [$saleSummary, $routePerformance->toArray(), $productPerformance->toArray(), $customerPerformance->toArray()];
            // dd($chartData);

        return $chartData;
  
  }
    public function gethighestsellingproducts()
    {
        $lists = WaInventoryLocationTransferItem::select(DB::raw('COUNT(wa_inventory_location_transfer_items.id) as cnt'), 'wa_inventory_items.stock_id_code as item_no', 'wa_inventory_items.title as item_name')
            ->groupBy('wa_inventory_item_id')
            ->join('wa_inventory_items', function ($w) {
                $w->on('wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id');
            })
            ->whereMonth('wa_inventory_location_transfer_items.created_at', date('m'))
            ->orderBy('cnt', 'desc')
            ->limit(5)
            ->get();
        return $lists;
    }

    public function getTotalEarning($start_date = null, $end_date = null)
    {
        if (!$start_date) {
       
            $poscashtotal = WaPosCashSalesItems::whereDate('created_at', '<=', $end_date)
                ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));

            $salesinvoicereal = WaInventoryLocationTransferItem::whereDate('created_at', '<=', $end_date)
                ->sum(DB::raw('total_cost_with_vat'));
            $salesreturn = WaInventoryLocationTransferItem::whereDate('return_date', '<=', $end_date)
                ->sum(DB::raw('selling_price * return_quantity'));
            $salestotal = $salesinvoicereal - $salesreturn;
        } else {
            $start_date = $start_date;
            $end_date = $end_date;
        
            $poscashtotal = WaPosCashSalesItems::whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));

            $salesinvoicereal = WaInventoryLocationTransferItem::whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('total_cost_with_vat'));
            $salesreturn = WaInventoryLocationTransferItem::whereBetween('return_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('selling_price * return_quantity'));
            $salestotal = $salesinvoicereal - $salesreturn;
        }
        return ($poscashtotal + $salestotal);
    }

    public function getTotalsalesAmount($date)
    {
        $poscashtotal = WaPosCashSalesItems::whereDate('created_at', $date)
            ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));
        $salesinvoicereal = WaInventoryLocationTransferItem::whereDate('created_at', $date)
            ->sum(DB::raw('total_cost_with_vat'));
        $salesreturn = WaInventoryLocationTransferItem::whereDate('return_date', $date)
            ->sum(DB::raw('selling_price * return_quantity'));
        $salestotal = $salesinvoicereal - $salesreturn;
        return ($salestotal + $poscashtotal);
    }

    public function gethighestsellingsalesman()
    {
        $lists = WaInventoryLocationTransfer::with(['getrelatedEmployee'])->select(
            'id',
            'user_id',
            DB::raw('(select SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as totalamnt from wa_inventory_location_transfer_items where wa_inventory_location_transfer_id = `wa_inventory_location_transfers`.`id`) as totalamount')
        )
            ->whereMonth('created_at', date('m'))
            ->orderBy('totalamount', 'desc')
            ->groupBy('user_id')
            ->limit(5)
            ->get();
        return $lists;
    }

    public function getSalesTransactionDetails($month, $year)
    {
        $final_data = [];
        $rows = DB::table('wa_cash_sales')
            ->selectRaw('DAY(created_at) as created_day, id, DATE(created_at) as createdate')
            ->whereRaw("created_at LIKE '$year-$month%'")
            ->groupByRaw('DAY(created_at), MONTH(created_at), YEAR(created_at)')
            ->get();
        foreach ($rows as $key => $row) {
            $inner_array = [$row->created_day, round($this->getTotalsalesAmount($row->createdate), 2)];
            $final_data[] = $inner_array;
        } 
        return $final_data;
    }
  
    public function getTotalEarningStats()
    {

      
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
        $previous_week = strtotime("-1 week");
        $start_week = strtotime("last sunday", $previous_week);
        $end_week = strtotime("next saturday", $start_week);
        $currentYear = date('Y') . '-01-01';
        $currentdate = date('Y-m-d');

        $borrwed_amountbyDateArray = [
            'today' =>manageAmountFormat($this->getTotalEarning($currentdate, $currentdate)),
            'this_week' => manageAmountFormat($this->getTotalEarning(date("Y-m-d", $monday), date("Y-m-d", $sunday))),
            'last_week' => manageAmountFormat($this->getTotalEarning(date("Y-m-d", $start_week), date("Y-m-d", $end_week))),
            'this_month' => manageAmountFormat($this->getTotalEarning(date('Y-m-01'), date('Y-m-t'))),
            'last_month' => manageAmountFormat($this->getTotalEarning(date('Y-m-01', strtotime('last month')), date('Y-m-t', strtotime('last month')))),
            'till_date' => manageAmountFormat($this->getTotalEarning($currentYear, $currentdate)),
            'last_year' => manageAmountFormat($this->getTotalEarning(null, date('Y-m-d', strtotime("-1 years"))))
        ];
        return $borrwed_amountbyDateArray;
    }
}
