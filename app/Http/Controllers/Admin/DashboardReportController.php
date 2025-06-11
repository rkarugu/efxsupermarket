<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\Order;
use Illuminate\Support\Facades\Validator;
use App\Model\ReceiptSummaryPayment;
use App\Model\WalletTransaction;
use App\Model\Bill;
use App\Model\OrderReceipt;
use App\Model\WaCashSales;
use App\Model\WaCashSalesItem;
use App\Model\WaStockMove;
use App\Model\WaSalesInvoice;
use App\Model\WaSalesInvoiceItem;
use File, DB, Session;

class DashboardReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'dashboard_report';
        $this->title = 'Dashboard Reports';
        $this->pmodule = 'dashboard_report';
        // ini_set('memory_limit', '4096M');
        // set_time_limit(30000000); // Extends to 5 minutes.
    }


    public function index(Request $request)
    {

        $show_dashboard = 1;

        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();


        if (isset($permission['sales-and-receivables-reports___dashboard-report']) || $permission == 'superadmin') {
            $title = 'Dashboard Report';




            $restro_count = Restaurant::count();
            $users_count = User::whereIn('role_id', ['11'])->count();
            $earningStats = $this->getTotalEarningStats();
            //get user registarion stats
            $start_date = date('Y-m-d');
            $end_date =  date('Y-m-d', strtotime("-15 days", strtotime(date('Y-m-d'))));


            //get sales transaction stats

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


            $model = $this->model;
            return view('admin.' . $this->model . '.index', compact('title', 'restro_count', 'users_count', 'end_date', 'show_dashboard', 'earningStats', 'highestsellingproducts', 'sales_transaction_stats', 'highestsellingsalesman', 'model'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }



        // return view('admin.page.dashboard',compact());
    }


    public function gethighestsellingproducts()
    {
        $lists = \App\Model\WaInventoryLocationTransferItem::select(DB::raw('COUNT(wa_inventory_location_transfer_items.id) as cnt'), 'wa_inventory_items.stock_id_code as item_no', 'wa_inventory_items.title as item_name')
            ->groupBy('wa_inventory_item_id')
            ->join('wa_inventory_items', function ($w) {
                $w->on('wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id');
            })
            ->whereMonth('wa_inventory_location_transfer_items.created_at', date('m'))
            ->orderBy('cnt', 'desc')
            ->limit(5)
            ->get();
        return $lists;
        //echo "<pre>"; print_r($lists); die;
    }

    public function getTotalEarning($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            // print_r("expression");die;
            // $total_amount = WaCashSales::whereDate('order_date','<=' ,$end_date)
            // ->join('wa_cash_sales_items','wa_cash_sales_items.wa_cash_sales_id','=','wa_cash_sales.id')
            // ->sum(DB::raw('wa_cash_sales_items.unit_price * wa_cash_sales_items.quantity'));

            // $total_sales_invoice = WaSalesInvoice::where('order_date','<=',$end_date)
            //         ->join('wa_sales_invoice_items','wa_sales_invoice_items.wa_sales_invoice_id','=','wa_sales_invoices.id')
            //         ->sum(DB::raw('wa_sales_invoice_items.unit_price * wa_sales_invoice_items.quantity'));

            $poscashtotal = \App\Model\WaPosCashSalesItems::whereDate('created_at', '<=', $end_date)
                ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));

            $salesinvoicereal = \App\Model\WaInventoryLocationTransferItem::whereDate('created_at', '<=', $end_date)
                ->sum(DB::raw('total_cost_with_vat'));
            $salesreturn = \App\Model\WaInventoryLocationTransferItem::whereDate('return_date', '<=', $end_date)
                ->sum(DB::raw('selling_price * return_quantity'));
            $salestotal = $salesinvoicereal - $salesreturn;
        } else {
            // die("oso");
            $start_date = $start_date;
            $end_date = $end_date;
            // $total_amount = WaCashSales::whereBetween('order_date', [$start_date, $end_date])
            // ->join('wa_cash_sales_items','wa_cash_sales_items.wa_cash_sales_id','=','wa_cash_sales.id')
            // ->sum(DB::raw('wa_cash_sales_items.unit_price * wa_cash_sales_items.quantity'));

            // $total_sales_invoice = WaSalesInvoice::whereBetween('order_date', [$start_date, $end_date])
            //         ->join('wa_sales_invoice_items','wa_sales_invoice_items.wa_sales_invoice_id','=','wa_sales_invoices.id')
            //         ->sum(DB::raw('wa_sales_invoice_items.unit_price * wa_sales_invoice_items.quantity'));
            $poscashtotal = \App\Model\WaPosCashSalesItems::whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));

            $salesinvoicereal = \App\Model\WaInventoryLocationTransferItem::whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('total_cost_with_vat'));
            $salesreturn = \App\Model\WaInventoryLocationTransferItem::whereBetween('return_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->sum(DB::raw('selling_price * return_quantity'));
            $salestotal = $salesinvoicereal - $salesreturn;
        }
        return ($poscashtotal + $salestotal);
    }

    public function getTotalsalesAmount($date)
    {
        $poscashtotal = \App\Model\WaPosCashSalesItems::whereDate('created_at', $date)
            ->sum(DB::raw('wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty'));
        $salesinvoicereal = \App\Model\WaInventoryLocationTransferItem::whereDate('created_at', $date)
            ->sum(DB::raw('total_cost_with_vat'));
        $salesreturn = \App\Model\WaInventoryLocationTransferItem::whereDate('return_date', $date)
            ->sum(DB::raw('selling_price * return_quantity'));
        $salestotal = $salesinvoicereal - $salesreturn;
        return ($salestotal + $poscashtotal);
    }

    public function gethighestsellingsalesman()
    {
        $lists = \App\Model\WaInventoryLocationTransfer::with(['getrelatedEmployee'])->select(
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

        // print_r(date('Y-m-d',strtotime("-1 years"))); die;
        //current week date range
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
        //previous week date range
        $previous_week = strtotime("-1 week");
        $start_week = strtotime("last sunday", $previous_week);
        $end_week = strtotime("next saturday", $start_week);
        $currentYear = date('Y') . '-01-01';
        $currentdate = date('Y-m-d');

        $borrwed_amountbyDateArray = [
            'this_week' => manageAmountFormat($this->getTotalEarning(date("Y-m-d", $monday), date("Y-m-d", $sunday))),
            'last_week' => manageAmountFormat($this->getTotalEarning(date("Y-m-d", $start_week), date("Y-m-d", $end_week))),
            'this_month' => manageAmountFormat($this->getTotalEarning(date('Y-m-01'), date('Y-m-t'))),
            'last_month' => manageAmountFormat($this->getTotalEarning(date('Y-m-01', strtotime('last month')), date('Y-m-t', strtotime('last month')))),
            'till_date' => manageAmountFormat($this->getTotalEarning($currentYear, $currentdate)),
            'last_year' => manageAmountFormat($this->getTotalEarning(null, date('Y-m-d', strtotime("-1 years"))))
        ];
        //  echo "<pre>"; print_r($borrwed_amountbyDateArray); die;
        return $borrwed_amountbyDateArray;
    }
}
