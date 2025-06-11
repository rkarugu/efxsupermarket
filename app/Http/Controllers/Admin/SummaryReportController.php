<?php

namespace App\Http\Controllers\admin;

use Excel;
use App\Model\User;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaStockMove;
use App\Model\WaDebtorTran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\PaymentChannel;
use App\Model\WaInventoryItem;
use App\Model\WaPettyCashItem;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\WaInventoryLocationTransferItemReturn;
use App\Model\WaPosCashSales;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;


class SummaryReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'summary_report';
        $this->title = 'Summary Report';
        $this->pmodule = 'summary-report';
        $this->pageUrl = 'summary-report';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Detailed Report';
        $model = $this->model;
        if (isset($permission[$pmodule . '___detailed']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.summary_report.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function sales_summary_index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Detailed Report';
        $model = $this->model;
        $branch = Restaurant::find($request->branch);
        if ($permission != 'superadmin')
        {
            $branch =  Auth::user()->restaurant_id;
        }


        $sales = $branch ? "AND restaurant_id = " . $branch : null;
        $pos = $branch ? "AND branch_id = " . $branch : null;
        $user = getLoggeduserProfile();
        $wirBranchFilter = '';
        $wiltBranchFilter = '';
        $subQueryBranchFilter = '';

        if ($request->branch && $request->branch !== 'all') {
            $wirBranchFilter = " AND wa_internal_requisitions.restaurant_id = $request->branch";
            $wiltBranchFilter = " AND wa_inventory_location_transfers.restaurant_id = $request->branch";
            $subQueryBranchFilter = " AND wa_pos_cash_sales.branch_id = $request->branch";
        }
        if (isset($permission[$pmodule . '___sales_summary']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $fromDate = $request->date . " 00:00:00";
            $toDate = $request->todate . " 23:59:59";

            // SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_16,
            // SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '2' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_0,
            // SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '3' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_exempt, 

            $query = "
            SELECT 
                DATE(wa_internal_requisition_items.created_at) AS sales_date, 
                SUM(wa_internal_requisition_items.total_cost_with_vat) AS total_sales,
                    SUM(CASE WHEN (wa_internal_requisition_items.tax_manager_id = '1' AND wa_internal_requisitions.requisition_no  like 'INV%') THEN wa_internal_requisition_items.total_cost_with_vat WHEN (wa_internal_requisition_items.tax_manager_id = '1' AND wa_internal_requisitions.requisition_no  like 'CIV%')  THEN (wa_internal_requisition_items.total_cost_with_vat - wa_internal_requisition_items.discount)  ELSE 0 END) AS total_sale_16,
            SUM(CASE WHEN (wa_internal_requisition_items.tax_manager_id = '2' AND wa_internal_requisitions.requisition_no like 'INV%') THEN wa_internal_requisition_items.total_cost_with_vat WHEN (wa_internal_requisition_items.tax_manager_id = '2' AND wa_internal_requisitions.requisition_no like 'CIV%') THEN (wa_internal_requisition_items.total_cost_with_vat - wa_internal_requisition_items.discount) ELSE 0 END) AS total_sale_0,
            SUM(CASE WHEN (wa_internal_requisition_items.tax_manager_id = '3' AND wa_internal_requisitions.requisition_no like 'INV%') THEN wa_internal_requisition_items.total_cost_with_vat when (wa_internal_requisition_items.tax_manager_id = '3' AND wa_internal_requisitions.requisition_no like 'CIV%') THEN (wa_internal_requisition_items.total_cost_with_vat - wa_internal_requisition_items.discount) ELSE 0 END) AS total_sale_exempt, 
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_16,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '2' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_0,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '3' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_exempt,
                  (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '1' 
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_16,
                  (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.vat_amount / wa_inventory_location_transfer_items.quantity))
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '1' 
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_vat_16,
                 (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '2'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_0,
                 (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '3'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_exempt,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.accepted_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '1'
                        $subQueryBranchFilter
                ) AS cash_return_16,
                 (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * (wa_pos_cash_sales_items.vat_amount / wa_pos_cash_sales_items.qty)) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.accepted_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '1'
                        $subQueryBranchFilter
                ) AS cash_return_vat_16,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.accepted_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '2'
                        $subQueryBranchFilter
                ) AS cash_return_0,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.accepted_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '3'
                        $subQueryBranchFilter
                ) AS cash_return_exempt

            FROM 
                wa_internal_requisition_items
            LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
            WHERE 
                wa_internal_requisition_items.created_at BETWEEN ? AND ? 
                $wirBranchFilter
            GROUP BY 
                sales_date 
            ORDER BY 
                sales_date DESC
            ";

            $salesData = DB::select($query, [$fromDate, $toDate]);
            $stockSalesQuery = "
        SELECT 
            DATE(stock_debtor_tran_items.created_at) AS sales_date,
             SUM(CASE 
                WHEN stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS total_sales,
             SUM(CASE 
                WHEN stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS total_returns,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS stock_sale_16,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS stock_return_16, 
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.vat 
                ELSE 0 
            END) AS stock_sale_vat_16,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.vat 
                ELSE 0 
            END) AS stock_return_vat_16,
            (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '2' AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS sales_zero_rated,
              (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '2' AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS returns_zero_rated,
             (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '3' AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS sales_exempt,
              (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '3' AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS returns_exempt                
        FROM stock_debtor_tran_items
        WHERE stock_debtor_tran_items.created_at BETWEEN ? AND ? 
        GROUP BY 
            sales_date
        ORDER BY
            sales_date DESC
        
        ";
        $stockSaleSummary = DB::select($stockSalesQuery, [$fromDate, $toDate]);
            if ($request->download) {
                $pdf = Pdf::loadView('admin.summary_report.sales_summary_report_pdf', compact('user', 'branch', 'salesData', 'stockSaleSummary'))->setPaper('a4', 'portrait');

                return $pdf->download('Sales-summary' . $request->date . '-' . $request->todate . '.pdf');
            }
            if ($request->request_type) {
                return view('admin.summary_report.sales_summary_report_pdf', compact('user', 'branch', 'salesData', 'stockSaleSummary'));
            }
            return view('admin.summary_report.sales_summary', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesData', 'stockSaleSummary'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function report(Request $request)
    {

        $vooma = PaymentChannel::Vooma->value;
        $kcb = PaymentChannel::KCB->value;
        $equity = PaymentChannel::Equity->value;
        $eazzy = PaymentChannel::Eazzy->value;
        $mpesa = PaymentChannel::Mpesa->value;

        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (!$request->date && (!isset($permission[$pmodule . '___detailed']) && $permission == 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('summary_report.index');
        }
        if(!$request->branch){
            Session::flash('warning', 'Please select a branch to continue');
            return redirect()->route('summary_report.index');
        }

        $startDate = $request->todate ? $request->date . ' 00:00:00' : now()->format('Y-m-d 00:00:00');
        $endDate = $request->todate ? $request->todate . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        if($request->branch && $request->branch == 'all'){
            $sales = null;
            $pos = null;
            $branch = null;

        }else{
            $branch = Restaurant::find($request->branch);
            $sales = "AND restaurant_id = " . $request->branch;
            $pos = "AND branch_id = " . $request->branch;
        }

        $user = getLoggeduserProfile();

        $salesmanPettyCashTransactions = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.reference',
                'travel_expense_transactions.route_id',
                'users.name as recipient',
                'users.phone_number',
                'routes.route_name',
                'salesman_shifts.shift_type',
                'salesman_shifts.id as shift_id',
                DB::raw("(ABS(petty_cash_transactions.amount)) as amount"),
                DB::raw("'TRAVEL - ORDER TAKING' as description"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                    join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                    as gross_sales"),
                DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    from wa_inventory_location_transfer_item_returns
                    join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                    join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    join wa_internal_requisitions on wa_inventory_location_transfers.transfer_no = wa_internal_requisitions.requisition_no
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
                    as returns"),
                DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                as tonnage"),
            )
            ->join('users', function ($join) use ($request) {
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->join('travel_expense_transactions', 'petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id')
            ->join('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.shift_id')
            ->join('salesman_shifts', 'delivery_schedules.shift_id', '=', 'salesman_shifts.id')
            ->join('routes', 'travel_expense_transactions.route_id', '=', 'routes.id')
            ->whereBetween('petty_cash_transactions.created_at', [$startDate, $endDate])
            ->where('petty_cash_transactions.amount', '>', 0)
            ->where('petty_cash_transactions.initial_approval_status', 'approved');
        if($request->branch && $request->branch != 'all'){
            $salesmanPettyCashTransactions  = $salesmanPettyCashTransactions->where('routes.restaurant_id', $request->branch);
        }
          $salesmanPettyCashTransactions = $salesmanPettyCashTransactions->get()
            ->map(function ($transaction) {
                $transaction->sales_amount = $transaction->gross_sales - $transaction->returns;

                return $transaction;
            });


        $deliveryPettyCashTransactions = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.reference',
                'travel_expense_transactions.route_id',
                DB::raw("(ABS(petty_cash_transactions.amount)) as amount"),
                'users.name as recipient',
                'users.phone_number',
                'routes.route_name',
                DB::raw("'TRAVEL - DELIVERY' as description"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                    join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                    as gross_sales"),
                DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    from wa_inventory_location_transfer_item_returns
                    join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                    join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    join wa_internal_requisitions on wa_inventory_location_transfers.transfer_no = wa_internal_requisitions.requisition_no
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
                    as returns"),
                DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                    left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                    left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                    as tonnage"),
            )
            ->join('users', function ($join) use ($request) {
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 6);
            })
            ->join('travel_expense_transactions', 'petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id')
            ->join('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.id')
            ->join('routes', 'travel_expense_transactions.route_id', '=', 'routes.id')
            ->whereBetween('petty_cash_transactions.created_at', [$startDate, $endDate])
            ->where('petty_cash_transactions.amount', '>', 0)
            ->where('petty_cash_transactions.initial_approval_status', 'approved');
            if($request->branch && $request->branch != 'all'){
                $deliveryPettyCashTransactions  = $deliveryPettyCashTransactions->where('routes.restaurant_id', $request->branch);
            }
            $deliveryPettyCashTransactions  = $deliveryPettyCashTransactions->get()
            ->map(function ($transaction) {
                $transaction->sales_amount = $transaction->gross_sales - $transaction->returns;

                return $transaction;
            });


        $pettyCashTransactions = $salesmanPettyCashTransactions->merge($deliveryPettyCashTransactions);


        $customers = WaCustomer::select([
            'wa_customers.id',
            'wa_customers.customer_name as name',
            'wa_customers.customer_code as customer_code',
            'wa_customers.route_id',
        ])->leftjoin('routes', 'wa_customers.route_id', '=', 'routes.id')
        ->where('routes.is_pos_route', 0);
        if($request->branch && $request->branch != 'all'){
            $customers = $customers->where('routes.restaurant_id', $request->branch);
        }
        $customers= $customers->get();


        $customerCodes = $customers->pluck('customer_code')->toArray();
        $customerIds = $customers->pluck('id')->toArray();

        $posCashSales = [];

        $inventoryTransfers = DB::table('wa_customers')
            ->leftJoin('wa_internal_requisitions', function($join) use ($startDate, $endDate) {
                $join->on('wa_internal_requisitions.customer_id', '=', 'wa_customers.id')
                     ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate]);
            })
            ->leftjoin('wa_inventory_location_transfers', function($join){
                $join->on('wa_inventory_location_transfers.transfer_no', '=', 'wa_internal_requisitions.requisition_no')
                    ->on('wa_inventory_location_transfers.route_id', '=', 'wa_internal_requisitions.route_id');
            })
            ->leftjoin('wa_internal_requisition_items', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->whereIn('wa_customers.id', $customerIds)
            ->select(
                'wa_customers.id',
                DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as vcs'),
                DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                FROM wa_inventory_location_transfers 
                LEFT JOIN wa_inventory_location_transfer_items ON 
                wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                LEFT JOIN wa_inventory_location_transfer_item_returns ON 
                wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                WHERE wa_inventory_location_transfer_item_returns.status = "received" 
                AND wa_inventory_location_transfer_item_returns.return_status = "1" 
                AND wa_inventory_location_transfers.customer_id = wa_customers.id ' .
                    $sales . ' AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) 
                BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as returns')
            )
            ->groupBy('wa_customers.id')
            ->get()
            ->keyBy('id');
        $posSalesUserIds =  WaPosCashSales::whereBetween('paid_at', [$startDate, $endDate])->pluck('user_id')->toArray();
        $posSales = DB::table('users')
            ->select(
                'users.name as cashier',
                DB::raw("(SELECT SUM(wa_pos_cash_sales_items.total)
                    FROM wa_pos_cash_sales_items 
                    LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE wa_pos_cash_sales.attending_cashier = users.id
                         AND (DATE(wa_pos_cash_sales.paid_at) BETWEEN '".$request->date."' AND '".$request->todate."')
                ) AS cash_sales"),
                DB::raw("(SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items on wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE (DATE(wa_pos_cash_sales_items_return.updated_at)  BETWEEN '".$request->date."' AND '".$request->todate."') 
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales.attending_cashier = users.id
                ) AS cash_returns"),
                DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                    LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                    LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                    WHERE wa_pos_cash_sales.attending_cashier = users.id
                        AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->todate."')
                        AND payment_providers.slug = 'kcb'
                ) AS Vooma"),
                DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                    LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                    LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                    WHERE wa_pos_cash_sales.attending_cashier = users.id
                        AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->todate."')
                        AND payment_providers.slug = 'equity-bank'
                ) AS Eazzy"),
                DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                    LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                    LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                    WHERE wa_pos_cash_sales.user_id = users.id
                        AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->todate."')
                        AND payment_providers.slug = 'mpesa'
                ) AS Mpesa"),

                )
            ->whereIn('users.id', $posSalesUserIds);
            if($request->branch && $request->branch != 'all')
            {
                $posSales = $posSales->where('users.restaurant_id', $request->branch);
            }            
            $posSales = $posSales->get();
            if($posSales->count() > 0)
            {
                $posSalesExist = true;
            }else{
                $posSalesExist = false;
            }

        // dd($posSales);


        // Debtor Transactions
        $debtorTrans = DB::table('wa_debtor_trans')
            ->whereIn('wa_debtor_trans.wa_customer_id', $customerIds)
            ->where(function ($query) {
                $query->whereNotNull('wa_debtor_trans.paid_by')
                    ->orWhere('wa_debtor_trans.reference', 'Book Clearance');
            })
            ->where('wa_debtor_trans.amount', '<', 0)
            ->whereNotNull('wa_debtor_trans.reference')
            ->where('wa_debtor_trans.type_number', '12')
            ->whereBetween('wa_debtor_trans.trans_date', [$request->date, $request->todate])
            ->select(
                'wa_debtor_trans.wa_customer_id',
                DB::raw('SUM(wa_debtor_trans.amount) as csr2')
            )
            ->groupBy('wa_debtor_trans.wa_customer_id')
            ->get()
            ->keyBy('wa_customer_id');

        // Tender Entries
        $tenderEntries = DB::table('wa_tender_entries')
            ->whereIn('wa_tender_entries.customer_id', $customerIds)
            ->whereBetween('wa_tender_entries.trans_date', [$startDate, $endDate])
            ->select(
                'wa_tender_entries.customer_id',
                DB::raw('SUM(CASE WHEN wa_tender_entries.channel = "' . $eazzy . '" OR wa_tender_entries.channel = "' . $equity . '" THEN wa_tender_entries.amount ELSE 0 END) as Eazzy'),
                DB::raw('SUM(CASE WHEN wa_tender_entries.channel = "' . $vooma . '" OR wa_tender_entries.channel = "' . $kcb . '" THEN wa_tender_entries.amount ELSE 0 END) as Vooma'),
                DB::raw('SUM(CASE WHEN wa_tender_entries.channel = "' . $mpesa . '" THEN wa_tender_entries.amount ELSE 0 END) as Mpesa'),
                DB::raw('COUNT(CASE WHEN wa_tender_entries.channel = "' . $eazzy . '" OR wa_tender_entries.channel = "' . $equity . '" THEN wa_tender_entries.amount ELSE NULL END) as Eazzy_count'),
                DB::raw('COUNT(CASE WHEN wa_tender_entries.channel = "' . $vooma . '" OR wa_tender_entries.channel = "' . $kcb . '" THEN wa_tender_entries.amount ELSE NULL END) as Vooma_count'),
                DB::raw('COUNT(CASE WHEN wa_tender_entries.channel = "' . $mpesa . '" THEN wa_tender_entries.amount ELSE NULL END) as Mpesa_count')
            )
            ->groupBy('wa_tender_entries.customer_id')
            ->get()
            ->keyBy('customer_id');

        $data = $customers->map(function ($customer) use ($posCashSales, $inventoryTransfers, $debtorTrans, $tenderEntries, $pettyCashTransactions) {
            $customerCode = $customer->customer_code;
            $customerId = $customer->id;

            // POS Cash Sales Data
            if (isset($posCashSales[$customerCode])) {
                $customer->cs = $posCashSales[$customerCode]->cs;
                $customer->csr = $posCashSales[$customerCode]->csr;
            } else {
                $customer->cs = 0;
                $customer->csr = 0;
            }

            // Inventory Location Transfers Data
            if (isset($inventoryTransfers[$customerId])) {
                $customer->vcs = $inventoryTransfers[$customerId]->vcs;
                $customer->returns = $inventoryTransfers[$customerId]->returns;
            } else {
                $customer->vcs = 0;
                $customer->returns = 0;
            }

            // Debtor Transactions Data
            if (isset($debtorTrans[$customerId])) {
                $customer->csr2 = $debtorTrans[$customerId]->csr2;
            } else {
                $customer->csr2 = 0;
            }

            // Tender Entries Data
            if (isset($tenderEntries[$customerId])) {
                $customer->Eazzy = $tenderEntries[$customerId]->Eazzy;
                $customer->Vooma = $tenderEntries[$customerId]->Vooma;
                $customer->Mpesa = $tenderEntries[$customerId]->Mpesa;
                $customer->Eazzy_count = $tenderEntries[$customerId]->Eazzy_count;
                $customer->Vooma_count = $tenderEntries[$customerId]->Vooma_count;
                $customer->Mpesa_count = $tenderEntries[$customerId]->Mpesa_count;
            } else {
                $customer->Eazzy = 0;
                $customer->Vooma = 0;
                $customer->Mpesa = 0;
                $customer->Eazzy_count = 0;
                $customer->Vooma_count = 0;
                $customer->Mpesa_count = 0;
            }

            // Petty Cash Transactions
            $customer->petty_cash = $pettyCashTransactions->where('route_id', $customer->route_id)->sum('amount');

            return $customer;
        })->sortByDesc('vcs');


        $debtorTrans = WaDebtorTran::select([
            'wa_debtor_trans.wa_customer_id',
            'wa_debtor_trans.trans_date',
            'wa_debtor_trans.amount',
            'wa_debtor_trans.type_number',
            'wa_debtor_trans.reference',
            'wa_debtor_trans.paid_by',
            'wa_customers.customer_name'
        ])
            ->join('wa_customers', 'wa_customers.id', '=', 'wa_debtor_trans.wa_customer_id')
            ->whereBetween('wa_debtor_trans.trans_date', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])
            ->get();

        $expenses = WaPettyCashItem::with(['chart_of_account', 'parent.user'])
            ->whereBetween('created_at', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])
            ->orderBy('created_at', 'DESC')
            ->get();

        $invoiceReturn = $debtorTrans->filter(function ($trans) {
            return $trans->type_number == 109;
        })->groupBy('wa_customer_id')->map(function ($group) {
            return [
                'wa_customer_id' => $group->first()->wa_customer_id,
                'trans_date' => $group->first()->trans_date,
                'max_total' => $group->sum('amount'),
                'customer' => $group->first()->customer_name,
            ];
        })->sortBy('customer')->values();

        $cashreceipt = $debtorTrans->filter(function ($trans) {
            return $trans->type_number == 12 && !is_null($trans->reference) && !is_null($trans->paid_by);
        })->loadMissing(['customerDetail', 'paid_user'])->filter(function ($trans) {
            return $trans->paid_user?->role_id != 4;
        })->sortByDesc('trans_date')->values();

        $realinvoices = $debtorTrans->filter(function ($trans) {
            return $trans->type_number == 51;
        })->groupBy('wa_customer_id')->map(function ($group) {
            return [
                'wa_customer_id' => $group->first()->wa_customer_id,
                'trans_date' => $group->first()->trans_date,
                'max_total' => $group->sum('amount'),
                'customer' => $group->first()->customer_name,
            ];
        })->sortBy('customer')->values();

        $expenses = $expenses->map(function ($expense) {
            return [
                'chart_of_account' => $expense->chart_of_account,
                'parent_user' => $expense->parent->user,
                'created_at' => $expense->created_at,
            ];
        });
        $wirBranchFilter = '';
        $wiltBranchFilter = '';
        $subQueryBranchFilter = '';

        if ($request->branch && $request->branch !== 'all') {
            $wirBranchFilter = " AND wa_internal_requisitions.restaurant_id = $request->branch";
            $wiltBranchFilter = " AND wa_inventory_location_transfers.restaurant_id = $request->branch";
            $subQueryBranchFilter = " AND wa_pos_cash_sales.branch_id = $request->branch";
        }

        $query = "
            SELECT 
                DATE(wa_internal_requisition_items.created_at) AS sales_date, 
                SUM(wa_internal_requisition_items.total_cost_with_vat) AS total_sales,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_16,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '2' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_0,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '3' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END) AS total_sale_exempt, 
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_16,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '2' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_0,
                SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '3' THEN wa_internal_requisition_items.vat_amount ELSE 0 END) AS total_vat_amount_exempt,
                  (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '1' 
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_16,
                  (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.vat_amount / wa_inventory_location_transfer_items.quantity))
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '1' 
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_vat_16,
                 (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '2'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_0,
                 (
                    SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
                    WHERE 
                        wa_inventory_items.tax_manager_id = '3'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) = sales_date 
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        $wiltBranchFilter
                ) AS returns_exempt,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.updated_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '1'
                        $subQueryBranchFilter
                ) AS cash_return_16,
                 (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * (wa_pos_cash_sales_items.vat_amount / wa_pos_cash_sales_items.qty)) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.updated_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '1'
                        $subQueryBranchFilter
                ) AS cash_return_vat_16,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.updated_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '2'
                        $subQueryBranchFilter
                ) AS cash_return_0,
                (SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price ) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE DATE(wa_pos_cash_sales_items_return.updated_at) = sales_date
                        AND wa_pos_cash_sales_items_return.accepted = '1'
                        AND wa_pos_cash_sales_items.tax_manager_id = '3'
                        $subQueryBranchFilter
                ) AS cash_return_exempt

            FROM 
                wa_internal_requisition_items
            LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
            WHERE 
                wa_internal_requisition_items.created_at BETWEEN ? AND ? 
                $wirBranchFilter
            GROUP BY 
                sales_date 
            ORDER BY 
                sales_date DESC
            ";
        

        $saleSummary = DB::select($query, [$request->date . ' 00:00:00', $request->todate . ' 23:59:59']);

        $salesData = DB::select("
        SELECT 
            SUM(CASE WHEN document_no LIKE 'INV-%' THEN price ELSE 0 END) as total_invoices,
            SUM(CASE WHEN document_no LIKE 'RTN-%' THEN price ELSE 0 END) as total_returns
        FROM wa_stock_moves
        WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
        AND created_at BETWEEN ? AND ?
        ", [$request->date . ' 00:00:00', $request->todate . ' 23:59:59']);

        $salesLedgerInvoices = $salesData[0]->total_invoices;
        $salesLedgerReturns = $salesData[0]->total_returns;

        $stockSalesQuery = "
        SELECT 
            DATE(stock_debtor_tran_items.created_at) AS sales_date,
             SUM(CASE 
                WHEN stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS total_sales,
             SUM(CASE 
                WHEN stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS total_returns,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS stock_sale_16,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.total 
                ELSE 0 
            END) AS stock_return_16, 
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                THEN stock_debtor_tran_items.vat 
                ELSE 0 
            END) AS stock_sale_vat_16,
            SUM(CASE 
                WHEN stock_debtor_tran_items.vat_percentage = '16' 
                     AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                THEN stock_debtor_tran_items.vat 
                ELSE 0 
            END) AS stock_return_vat_16,
            (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '2' AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS sales_zero_rated,
              (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '2' AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS returns_zero_rated,
             (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '3' AND stock_debtor_tran_items.document_no LIKE 'SAS%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS sales_exempt,
              (
                SELECT SUM(stock_debtor_tran_items.total)
                FROM stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE wa_inventory_items.tax_manager_id = '3' AND stock_debtor_tran_items.document_no LIKE 'SAR%' 
                AND DATE(stock_debtor_tran_items.created_at) = sales_date
            ) AS returns_exempt                
        FROM stock_debtor_tran_items
        WHERE stock_debtor_tran_items.created_at BETWEEN ? AND ? 
        GROUP BY 
            sales_date
        ORDER BY
            sales_date DESC
        
        ";
        $stockSaleSummary = DB::select($stockSalesQuery, [$request->date . ' 00:00:00', $request->todate . ' 23:59:59']);

        if ($request->request_type) {
            return view('admin.summary_report.report', compact('user', 'branch', 'data', 'invoiceReturn', 'cashreceipt', 'expenses', 'saleSummary', 'stockSaleSummary', 'posSales', 'posSalesExist'));
        }
        // dd($user, $stockSaleSummary, $invoiceReturn, $saleSummary, $stockSaleSummary, $posSales, $posSalesExist, $data, $pettyCashTransactions, $deliveryPettyCashTransactions,  $expenses);
        $pdf = Pdf::loadView('admin.summary_report.report', compact('user', 'branch', 'data', 'invoiceReturn', 'cashreceipt', 'expenses', 'saleSummary', 'pettyCashTransactions', 'deliveryPettyCashTransactions', 'salesmanPettyCashTransactions', 'stockSaleSummary', 'posSales', 'posSalesExist'))->setPaper('a4', 'portrait');

        return $pdf->download('EOD-Report-' . $request->date . '-' . $request->todate . '.pdf');
    }

    public function salesLedgerVsStocksLedger(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Sales vs Stocks Ledger';
        // $model = 'till-direct-banking-report';
        $model = 'sales-and-receivables-reports';
        $branch = Restaurant::find($request->branch);
        $user = getLoggeduserProfile();
        if (isset($permission[$model . '___till-direct-banking-report']) || $permission == 'superadmin') {
            $breadcum = [$title => route('summary_report.sales_vs_stocks_ledger'), 'Listing' => ''];
            $fromDate = $request->date . " 00:00:00";
            $toDate = $request->todate . " 23:59:59";
            $sales = $request->branch ? "AND restaurant_id = " . $request->branch : null;
            $pos = $request->branch ? "AND branch_id = " . $request->branch : null;
            if ($request->date && $request->todate) {


                $data = WaCustomer::select([
                    'wa_customers.customer_name as name',
                    'wa_customers.customer_code as customer_code',
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty)
             FROM wa_pos_cash_sales 
             LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
             WHERE wa_customers.customer_code = \'CUST-00001\' and wa_pos_cash_sales.status = "Completed" ' . $pos . ' AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as cs'),

                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.return_quantity) 
             FROM wa_pos_cash_sales 
             LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
             WHERE wa_customers.customer_code = \'CUST-00001\' AND is_return = 1 ' . $pos . ' and wa_pos_cash_sales.status = "Completed" AND (DATE(wa_pos_cash_sales_items.created_at)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as csr'),


                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat)
             FROM wa_inventory_location_transfers 
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.customer_id = wa_customers.id ' . $sales . ' AND (DATE(wa_inventory_location_transfers.transfer_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as vcs'),


                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.total_cost_with_vat / wa_inventory_location_transfer_items.quantity))
            FROM wa_inventory_location_transfers 
            LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
            LEFT JOIN wa_inventory_location_transfer_item_returns ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
            WHERE wa_inventory_location_transfers.customer_id = wa_customers.id ' . $sales . ' AND (DATE(wa_inventory_location_transfer_item_returns.created_at)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as returns'),

                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)
             FROM wa_inventory_location_transfers
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.customer_id = wa_customers.id ' . $sales . ' AND wa_inventory_location_transfer_items.is_return = 1 AND (DATE(wa_inventory_location_transfer_items.return_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as vcr'),

                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat)
             FROM wa_inventory_location_transfers 
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.customer_id = wa_customers.id ' . $sales . ' AND wa_inventory_location_transfers.invoice_type="Backend" AND (DATE(wa_inventory_location_transfers.transfer_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as inv_backend'),


                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)
             FROM wa_inventory_location_transfers
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.customer_id = wa_customers.id ' . $sales . ' AND wa_inventory_location_transfers.invoice_type="Backend" AND wa_inventory_location_transfer_items.is_return = 1 AND (DATE(wa_inventory_location_transfer_items.return_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as inv_backend_return')


                ])->orderBy('vcs', 'DESC')->get();


                $salesLedgerInvoices = WaStockMove::where('document_no', 'like', 'INV-%')
                    ->whereDate('created_at', '>=', $request->date . ' 00:00:00')
                    ->whereDate('created_at', '<=', $request->todate . ' 23:59:59')
                    ->get()
                    ->sum('price');

                $salesLedgerReturns = WaStockMove::where('document_no', 'like', 'RTN-%')
                    ->whereDate('created_at', '>=', $request->date . ' 00:00:00')
                    ->whereDate('created_at', '<=', $request->todate . ' 23:59:59')
                    ->get()
                    ->sum('price');
            }


            if ($request->download) {
                $pdf = Pdf::loadView('admin.summary_report.sales_vs_stocks_ledger_pdf', compact('user', 'branch', 'salesLedgerInvoices', 'salesLedgerReturns', 'data'))->setPaper('a4', 'portrait');

                return $pdf->download('Sales-summary' . $request->date . '-' . $request->todate . '.pdf');
            }
            if ($request->request_type) {
                return view('admin.summary_report.sales_vs_stocks_ledger_pdf', compact('user', 'branch', 'salesLedgerInvoices', 'salesLedgerReturns', 'data'));
            }
            return view('admin.summary_report.sales_vs_stocks_ledger', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function reportNew(Request $request)
    {
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (!$request->date && (!isset($permission[$pmodule . '___detailed']) && $permission == 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('summary_report.index');
        }
        $user = getLoggeduserProfile();
        $dataQuery = User::selectRaw('users.name, SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty) as cash_sales')
            ->join('wa_pos_cash_sales', function ($e) use ($request) {
                $e->on('wa_pos_cash_sales.user_id', '=', 'users.id');
            })->leftJoin('wa_pos_cash_sales_items', function ($e) use ($request) {
                $date = [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'];
                $e->on('wa_pos_cash_sales_items.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id');
                $e->whereRaw('(DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $request->date . '" AND "' . $request->todate . '")');
            });

        $data = $dataQuery->orderBy('cash_sales', 'DESC')->paginate(1);
        $invoiceReturn = WaDebtorTran::select([
            'wa_debtor_trans.wa_customer_id',
            'wa_debtor_trans.trans_date',
            DB::RAW('SUM(wa_debtor_trans.amount) as max_total'),
            DB::RAW('(SELECT customer_name from `wa_customers` where wa_customers.id = wa_debtor_trans.wa_customer_id limit 1) as customer')
        ])->whereBetween('trans_date', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])
            ->where('type_number', 109)
            ->orderBy('customer', 'ASC')
            ->groupBy('wa_customer_id')
            ->get();
        $cashreceipt = WaDebtorTran::with(['customerDetail', 'paid_user' => function ($w) {
            $w->where('role_id', '!=', 4);
        }])->whereHas('paid_user', function ($w) {
            $w->where('role_id', '!=', 4);
        })
            ->whereNotNUll('reference')
            ->whereNotNUll('paid_by')
            ->whereBetween('trans_date', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])
            ->where('type_number', 12)
            ->orderBy('trans_date', 'DESC')
            ->get();
        $expenses = WaPettyCashItem::with(['chart_of_account', 'parent.user'])->whereBetween('created_at', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])->orderBy('created_at', 'DESC')->get();
        $realinvoices = WaDebtorTran::select([
            'wa_debtor_trans.wa_customer_id',
            'wa_debtor_trans.trans_date',
            DB::RAW('SUM(wa_debtor_trans.amount) as max_total'),
            DB::RAW('(SELECT customer_name from `wa_customers` where wa_customers.id = wa_debtor_trans.wa_customer_id limit 1) as customer')
        ])->whereBetween('trans_date', [$request->date . ' 00:00:00', $request->todate . ' 23:59:59'])
            ->where('type_number', 51)
            ->orderBy('customer', 'ASC')
            ->groupBy('wa_customer_id')
            ->get();
        if ($request->request_type) {
            return view('admin.summary_report.report', compact('user', 'data', 'invoiceReturn', 'realinvoices', 'cashreceipt', 'expenses'));
        }
        $pdf = \PDF::loadView('admin.summary_report.report', compact('user', 'data', 'invoiceReturn', 'realinvoices', 'cashreceipt', 'expenses'))->setPaper('a4', 'landscape');
        return $pdf->download('EOD-Report-' . $request->date . '-' . $request->todate . '.pdf');
    }

    public function merge_payment_report(Request $request)
    {
        $title = 'Merge Payment';
        $model = "merged-payments";
        $pmodule = "merged-payments";
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $all_item = \App\Model\WaMergedPayments::select('wa_merged_payments.*')->with(['getShiftDetail', 'getShiftDetail.getDeliveryNoteDetail', 'getShiftDetail.getSalesManDetail']);
            if ($request->has('start-date')) {
                $all_item = $all_item->whereDate('wa_merged_payments.created_at', '>=', $request->input('start-date'));
            }
            if ($request->has('end-date')) {
                $all_item = $all_item->whereDate('wa_merged_payments.created_at', '<=', $request->input('end-date'));
            }
            $all_item = $all_item->leftjoin('wa_shifts', 'wa_shifts.id', '=', 'wa_merged_payments.shift_id')->where(function ($w) use ($request) {
                if ($request->salesman_id) {
                    $w->where('wa_shifts.salesman_id', $request->salesman_id);
                }
                if ($request->shift_id && count($request->shift_id) > 0) {
                    $w->whereIn('wa_shifts.id', $request->shift_id);
                }
            })->orderBy('id', 'DESC')->where('is_posted_to_account', 0);
            $all_item = $all_item->get();

            $lists = $all_item;
            $breadcum = [$title => '', 'Merged Payment' => ''];
            return view('admin.summary_report.merge_payment_report', compact('title', 'lists', 'all_item', 'model', 'breadcum', 'all_item'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function merged_reverse_transactions(Request $request, $id)
    {
        $title = 'Merge Payment';
        $model = "merged-payments";
        $pmodule = "merged-payments";
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission[$pmodule . '___reverse-transaction']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $id = base64_decode($id);
        $item = DB::table('wa_merged_payments')->where('id', $id)->first();
        if (!$item) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        DB::transaction(function () use ($id) {
            \App\Model\WaSalesmanTran::where('wa_merged_id', $id)->update([
                'wa_merged_id' => '',
                'is_settled' => 0
            ]);
            DB::table('wa_merged_payments')->where('id', $id)->delete();
        });
        Session::flash('success', 'Payment Reversed successfully');
        return redirect()->back();
    }

    public function summaryindex()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Summary Report';
        $model = $this->model;
        if (isset($permission[$pmodule . '___summary']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.summary_report.summaryindex', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function summaryreport(Request $request)
    {
        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', '0');

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!$request->date && (!isset($permission[$pmodule . '___summary']) && $permission == 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('summary_report.summaryindex');
        }
        $user = getLoggeduserProfile();
        $data = User::select([
            'users.name',
            DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty)
             from wa_pos_cash_sales 
             LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
             WHERE wa_pos_cash_sales.user_id = users.id  AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as cash_sales'),


            DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.return_quantity) 
             from wa_pos_cash_sales 
             LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
             WHERE wa_pos_cash_sales.user_id = users.id AND is_return = 1 AND (DATE(wa_pos_cash_sales_items.created_at)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as pos_cash_sales_returns'),


            DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.return_quantity) 
             from wa_pos_cash_sales 
             LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
             WHERE wa_pos_cash_sales.user_id = users.id AND is_return = 1 AND (DATE(wa_pos_cash_sales_items.return_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as cash_sales_returns'),

            DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat)
             from wa_inventory_location_transfers 
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.user_id = users.id AND (DATE(wa_inventory_location_transfers.transfer_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as invoices'),


            DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)
             from wa_inventory_location_transfers
             LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
             WHERE wa_inventory_location_transfers.user_id = users.id AND wa_inventory_location_transfer_items.is_return = 1 AND (DATE(wa_inventory_location_transfer_items.return_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as invoices_return'),

            DB::RAW('(SELECT SUM(wa_petty_cash.total_amount) from wa_petty_cash WHERE wa_petty_cash.user_id = users.id AND (DATE(wa_petty_cash.created_at)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) as petty_cash'),
            DB::RAW('(SELECT SUM(wa_debtor_trans.amount) from wa_debtor_trans WHERE 
             (wa_debtor_trans.paid_by is NOT NULL OR wa_debtor_trans.reference = "Book Clearance") AND wa_debtor_trans.amount < 0 
             AND wa_debtor_trans.reference is NOT NULL AND ((wa_debtor_trans.user_id = users.id AND wa_debtor_trans.reference != "Book Clearance") OR (wa_debtor_trans.reference = "Book Clearance" AND wa_debtor_trans.salesman_user_id = users.id))
             AND type_number = "12" AND (DATE(wa_debtor_trans.trans_date)  BETWEEN "' . $request->date . '" AND "' . $request->todate . '")) 
             as customer_receipt')
        ])->orderBy('cash_sales', 'DESC')->get();

        if ($request->request_type) {
            return view('admin.summary_report.summaryreport', compact('user', 'data'));
        }
        $pdf = \PDF::loadView('admin.summary_report.summaryreport', compact('user', 'data'))->setPaper('a4', 'landscape');
        return $pdf->download('EOD-Summary-Report-' . $request->date . '-' . $request->todate . '.pdf');
    }

    public function inventory_sales_report(Request $request)
    {
        $title = 'Inventory Sales Report';
        $model = 'inventory_sales_report';

        $date = $request->filled('date') ? $request->date . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $quantitySub = WaStockMove::query()
            ->selectRaw('wa_inventory_item_id, SUM(qauntity) as qoh')
            ->whereDate('created_at', '<=', $date);
        if ($request->filled('location')) {
            $quantitySub->where('wa_location_and_store_id', $request->location);
        }
        $quantitySub->groupby('wa_inventory_item_id');

        $valuesSub = WaInventoryItem::query()
            ->from('wa_inventory_items as items')
            ->select([
                'items.wa_inventory_category_id',
                // 'items.id',
                // 'items.stock_id_code',
                // DB::raw('SUM(
                // IF(
                //     IFNULL(history.standard_cost, 0) > 0, 
                //     history.standard_cost, 
                //     items.standard_cost
                // )) as cost_used') ,
                // DB::raw('SUM(quantities.qoh * items.standard_cost) as total')
                DB::raw('SUM(quantities.qoh * 
                IF(
                    IFNULL(history.standard_cost, 0) > 0, 
                    history.standard_cost, 
                    items.standard_cost
                )) as total')                
                 ])
                ->leftJoin(DB::raw("
                (
                    SELECT
                        price_history.wa_inventory_item_id,
                        price_history.standard_cost
                    FROM
                        wa_inventory_item_price_history as price_history
                    WHERE
                        price_history.created_at <= '$date'
                              AND price_history.standard_cost > 0
                            AND price_history.created_at = (
                                SELECT MAX(sub_ph.created_at)
                                FROM wa_inventory_item_price_history as sub_ph
                                WHERE sub_ph.wa_inventory_item_id = price_history.wa_inventory_item_id
                                AND sub_ph.created_at <= '$date'
                            )
                   
                ) as history
            "), 'history.wa_inventory_item_id', '=', 'items.id'
            )
            ->joinSub($quantitySub, 'quantities', 'quantities.wa_inventory_item_id', '=', 'items.id')
            // ->groupBy('items.id')->limit(10);
            // dd($valuesSub->get());
            ->groupBy('wa_inventory_category_id');

        $categories = \App\Model\WaInventoryCategory::query()
            ->select('wa_inventory_categories.*')
            ->addSelect('values.total')
            ->joinSub($valuesSub, 'values', function ($join) {
                $join->on('values.wa_inventory_category_id', '=', 'wa_inventory_categories.id');
            })->orderby('total', 'desc');

        $date = (new Carbon($date))->format('Y-m-d');

        if ($request->has('print')) {
            $description = '';
            if ($request->location) {
                $description .= "Location: " . WaLocationAndStore::find($request->location)->location_name;
            }

            $categories = $categories->get();

            $pdf = Pdf::loadView('admin.summary_report.reports.value', compact('categories', 'description', 'date'));

            return $pdf->setPaper('a4')
                ->setWarnings(false)
                ->download('inventory_value_summary_' . date('d_m_Y_H_i_s') . '.pdf');
        }

        $categories = $categories->get();

        $breadcum = [$title => '', 'Listing' => ''];

        return view('admin.summary_report.inventory_sales_report', compact('categories', 'title', 'model', 'breadcum', 'date'));
    }

    public function category_items_sales(Request $request, $categoryID, $date)
    {
        $date = $date ? $date . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
        $title = 'Inventory Category Items Sales Report';

        $model = 'inventory_sales_report';

        $category = WaInventoryCategory::findOrFail($categoryID);

        $quantitySub = WaStockMove::query()
            ->selectRaw('wa_inventory_item_id, SUM(qauntity) as qoh')
            ->whereDate('created_at', '<=', $date);
        if ($request->filled('location')) {
            $quantitySub->where('wa_location_and_store_id', $request->location);
        }
        $quantitySub->groupby('wa_inventory_item_id');

        $query = WaInventoryItem::query()
            ->from('wa_inventory_items as items')
            ->select([
                'items.stock_id_code',
                'items.title',
                'items.standard_cost',
                'quantities.qoh as qoh',
                DB::raw('
                IF(
                    IFNULL(history.standard_cost, 0) > 0, 
                    history.standard_cost, 
                    items.standard_cost
                ) as cost_used') ,
                // DB::raw('quantities.qoh * items.standard_cost as total')
                DB::raw('(quantities.qoh * 
                IF(
                    IFNULL(history.standard_cost, 0) > 0, 
                    history.standard_cost, 
                    items.standard_cost
                )) as total')                
                 ])
                ->leftJoin(DB::raw("
                (
                    SELECT
                        price_history.wa_inventory_item_id,
                        price_history.standard_cost
                    FROM
                        wa_inventory_item_price_history as price_history
                    WHERE
                        price_history.created_at <= '$date'
                              AND price_history.standard_cost > 0
                            AND price_history.created_at = (
                                SELECT MAX(sub_ph.created_at)
                                FROM wa_inventory_item_price_history as sub_ph
                                WHERE sub_ph.wa_inventory_item_id = price_history.wa_inventory_item_id
                                AND sub_ph.created_at <= '$date'
                            )
                   
                ) as history
            "), 'history.wa_inventory_item_id', '=', 'items.id'
            )
            ->joinSub($quantitySub, 'quantities', 'quantities.wa_inventory_item_id', '=', 'items.id');

        $query->where('items.wa_inventory_category_id', $category->id);

        $description = 'Category: ' . $category->category_description;

        if ($request->filled('action')) {
            if ($request->location) {
                $description .= " Location: " . WaLocationAndStore::find($request->location)->location_name;
            }

            $items = $query->get();
            $pdf = Pdf::loadView('admin.summary_report.reports.items_value', compact('items', 'description'));

            return $pdf->setPaper('a4')
                ->download('inventory_category_items_value_summary_' . date('d_m_Y_H_i_s') . '.pdf');
        }

        $items = $query->get();
        $locations = WaLocationAndStore::get();

        $breadcum = [$title => '', 'Listing' => ''];

        return view('admin.summary_report.inventory_category_items_sales_report', compact('locations', 'category', 'items', 'title', 'model', 'breadcum', 'date'));
    }

    public function downloadExcelFile($data, $type, $file_name)
    {
        return Excel::create($file_name, function ($excel) use ($data) {
            $from = "A1";
            $to = "G5";
            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function detailed_sales_report(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Detailed Sales Report';
        $model = 'detailed_sales_report';
        $suppliers = WaSupplier::all();
        if (isset($permission[$pmodule . '___detailed_sales_report']) || $permission == 'superadmin') {
            $user = getLoggeduserProfile();
            $from = $request->from ?? date('Y-m-d');
            $to = $request->to ?? date('Y-m-d');
            $salesBranchFilter = "";
            $posBranchFilter =  "";
            if($request->location && $request->location != '-1' ){
                $branch = WaLocationAndStore::find($request->location)->wa_branch_id;
                $salesBranchFilter =  " AND wa_inventory_location_transfers.restaurant_id = " . $branch;
                $posBranchFilter =  " AND wa_pos_cash_sales.branch_id = " . $branch;
            }
            $items = WaInventoryItem::select([
                "wa_inventory_items.stock_id_code",
                "wa_inventory_items.title",
                DB::RAW("SUM(pos_cash.pos_cash_sum) as pos_cash_sum_total"),
                DB::RAW("SUM(pos_cash.pos_cash_qty) as pos_cash_qty_total"),
                DB::RAW("SUM(pos_cash.pos_vat_amount) as pos_cash_vat_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_sum) as pos_cash_return_sum_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_qty) as pos_cash_return_qty_total"),
                DB::RAW("SUM(pos_cash_return.pos_vat_amount) as pos_cash_return_vat_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_sum) as pos_cash_returns_sum_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_qty) as pos_cash_returns_qty_total"),
                DB::RAW("SUM(pos_cash_returns.pos_vat_amount) as pos_cash_returns_vat_total"),

                DB::RAW("SUM(invoices.invoice_total) as invoices_sum_total"),
                DB::RAW("SUM(invoices.invoice_qty) as invoices_qty_total"),

                DB::RAW("SUM(invoices_return.invoice_total) as invoices_return_sum_total"),
                DB::RAW("SUM(invoices_return.invoice_qty) as invoices_return_qty_total"),

                DB::RAW("SUM(invoices.invoice_vat_amount) as invoices_vat"),
                DB::RAW("SUM(invoices_return.invoice_vat_amount) as invoices_return_vat"),
            ])
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                wa_inventory_location_transfer_items.wa_inventory_item_id
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE (DATE(wa_inventory_location_transfers.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 '. "$salesBranchFilter".'
                 GROUP BY wa_inventory_location_transfer_items.wa_inventory_item_id) as invoices'),
                    function ($e) {
                        $e->on('invoices.wa_inventory_item_id', 'wa_inventory_items.id');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.return_quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                wa_inventory_location_transfer_items.wa_inventory_item_id
                 from wa_inventory_location_transfer_item_returns
                 LEFT JOIN  wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfer_item_returns.return_status = "1"
                 AND wa_inventory_location_transfer_item_returns.status = "received"
                '. "$salesBranchFilter".'
                 AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_inventory_location_transfer_items.wa_inventory_item_id) as invoices_return'),
                    function ($e) {
                        $e->on('invoices_return.wa_inventory_item_id', 'wa_inventory_items.id');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, 
                wa_pos_cash_sales_items.wa_inventory_item_id, 
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount

                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 '. "$posBranchFilter".'
                 AND (DATE(wa_pos_cash_sales_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash_returns'),
                    function ($e) {
                        $e->on('pos_cash_returns.wa_inventory_item_id', 'wa_inventory_items.id');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.qty) as pos_cash_qty, 
                wa_pos_cash_sales.status, 
                wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty)*vat_percentage/100) as pos_vat_amount

                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed"
                 '. "$posBranchFilter".'
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash'),
                    function ($e) {
                        $e->on('pos_cash.wa_inventory_item_id', 'wa_inventory_items.id');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, 
                wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount

                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                '. "$posBranchFilter".'
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash_return'),
                    function ($e) {
                        $e->on('pos_cash_return.wa_inventory_item_id', 'wa_inventory_items.id');
                    }
                )
                ->leftJoin('wa_inventory_item_suppliers', 'wa_inventory_items.id', 'wa_inventory_item_suppliers.wa_inventory_item_id');
            if($request->supplier){
                $items = $items->where('wa_inventory_item_suppliers.wa_supplier_id', $request->supplier);

            }
            $items = $items->groupBy('wa_inventory_items.id')
                ->having(DB::RAW('((COALESCE(pos_cash_qty_total,0) + COALESCE(pos_cash_return_qty_total,0) - COALESCE(pos_cash_returns_qty_total,0)) + (COALESCE(invoices_qty_total,0) - COALESCE(invoices_return_qty_total,0)))'), '!=', 0)
                ->orderBy(DB::RAW('(COALESCE(SUM(pos_cash.pos_cash_qty),0) + COALESCE(SUM(invoices.invoice_qty),0) - COALESCE(SUM(invoices_return.invoice_qty),0))'), 'DESC')
                ->get();
            if ($request->manage) {
                $data = [];
                $total_qty = 0;
                $total_sum = 0;
                $total_vat = 0;
                foreach ($items as $i) {
                    $qty = ($i->pos_cash_qty_total + $i->pos_cash_return_qty_total - $i->pos_cash_returns_qty_total + $i->invoices_qty_total - $i->invoices_return_qty_total);
                    $total = ($i->pos_cash_sum_total + $i->pos_cash_return_sum_total - $i->pos_cash_returns_sum_total + $i->invoices_sum_total - $i->invoices_return_sum_total);
                    $vat = ($i->pos_cash_vat_total + $i->pos_cash_return_vat_total - $i->pos_cash_returns_vat_total + $i->invoices_vat - $i->invoices_return_vat);
                    $total_qty += $qty;
                    $total_sum += $total;
                    $total_vat += $vat;
                    $child = [
                        'Stock ID' => $i->stock_id_code,
                        'Product' => $i->title,
                        'Quantity' => manageAmountFormat($qty),
                        'Sales' => manageAmountFormat($total),
                        'VAT' => manageAmountFormat($vat)
                    ];
                    $data[] = $child;
                }
                $child = [
                    'Stock ID' => "Grand Total",
                    'Product' => "",
                    'Quantity' => manageAmountFormat($total_qty),
                    'Sales' => manageAmountFormat($total_sum),
                    'VAT' => manageAmountFormat($total_vat)
                ];
                $data[] = $child;
                // return $this->downloadExcelFile($data, 'xls', 'detailed_sales_report');
            $columnsArray = ['STOCK ID CODE', 'PRODUCT', 'QUANTITY', 'SALES', 'VAT'];
            return ExcelDownloadService::download('sales_of_products_by_date'.$from.'_'.$to, collect($data), $columnsArray);
        }
            $breadcum = [$title => route('summary_report.detailed_sales_report'), 'Listing' => ''];
            return view('admin.summary_report.detailed_sales_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'user','suppliers', 'items', 'from', 'to'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function sales_by_date_report(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Detailed Sales Report';
        $model = 'sales_by_date_report';
        if (isset($permission[$pmodule . '___sales_by_date_report']) || $permission == 'superadmin') {
            $user = getLoggeduserProfile();
            $from = $request->from ?? date('Y-m-d');
            $to = $request->to ?? date('Y-m-d');

            $sql = "WITH recursive Date_Ranges AS (
                select '" . $from . "' as Date
               union all
               select Date + interval 1 day
               from Date_Ranges
               where Date < '" . $to . "')
               select * from Date_Ranges";

            $location = $request->location && $request->location != '-1' ? " AND wa_stock_moves.wa_location_and_store_id = " . $request->location : "";
            $items = DB::query()->from(DB::raw("($sql) as date_ranges"))->select([
                'Date',
                DB::RAW("SUM(pos_cash.pos_cash_sum) as pos_cash_sum_total"),
                DB::RAW("SUM(pos_cash.pos_cash_qty) as pos_cash_qty_total"),
                DB::RAW("SUM(pos_cash.pos_standard_cost) as pos_standard_cost_total"),
                DB::RAW("SUM(pos_cash.pos_vat_amount) as pos_cash_vat_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_sum) as pos_cash_return_sum_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_qty) as pos_cash_return_qty_total"),
                DB::RAW("SUM(pos_cash_return.pos_standard_cost) as pos_return_standard_cost_total"),
                DB::RAW("SUM(pos_cash_return.pos_vat_amount) as pos_cash_return_vat_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_sum) as pos_cash_returns_sum_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_qty) as pos_cash_returns_qty_total"),
                DB::RAW("SUM(pos_cash_returns.pos_standard_cost) as pos_returns_standard_cost_total"),
                DB::RAW("SUM(pos_cash_returns.pos_vat_amount) as pos_cash_returns_vat_total"),

                DB::RAW("SUM(invoices.invoice_total) as invoices_sum_total"),
                DB::RAW("SUM(invoices.invoice_standard_cost) as invoice_sum_standard_cost"),
                DB::RAW("SUM(invoices.invoice_qty) as invoices_qty_total"),

                DB::RAW("SUM(invoices_return.invoice_total) as invoices_return_sum_total"),
                DB::RAW("SUM(invoices_return.invoice_standard_cost) as invoice_return_sum_standard_cost"),
                DB::RAW("SUM(invoices_return.invoice_qty) as invoices_return_qty_total"),

                DB::RAW("SUM(invoices.invoice_vat_amount) as invoices_vat"),
                DB::RAW("SUM(invoices_return.invoice_vat_amount) as invoices_return_vat"),
            ])
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.quantity) as invoice_standard_cost, 
                DATE(wa_inventory_location_transfers.transfer_date) as transfer_date,
                wa_inventory_location_transfer_items.wa_inventory_item_id
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED"
                 AND (DATE(wa_inventory_location_transfers.transfer_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_inventory_location_transfers.transfer_date)) as invoices'),
                    function ($e) {
                        $e->on('invoices.transfer_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.return_quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                wa_inventory_location_transfer_items.wa_inventory_item_id,
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.return_quantity) as invoice_standard_cost, 

                DATE(wa_inventory_location_transfer_items.return_date) as return_date
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED" AND is_return = 1
                 AND (DATE(wa_inventory_location_transfer_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_inventory_location_transfer_items.return_date)) as invoices_return'),
                    function ($e) {
                        $e->on('invoices_return.return_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id, 
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.created_at)) as pos_cash_return'),
                    function ($e) {
                        $e->on('pos_cash_return.created_at', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.return_date) as return_date
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.return_date)) as pos_cash_returns'),
                    function ($e) {
                        $e->on('pos_cash_returns.return_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty) as pos_cash_sum, SUM(wa_pos_cash_sales_items.qty) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.qty) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed"
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.created_at)) as pos_cash'),
                    function ($e) {
                        $e->on('pos_cash.created_at', 'date_ranges.Date');
                    }
                )
                ->groupBy('date_ranges.Date')
                ->orderBy('date_ranges.Date', 'ASC')
                ->having(DB::RAW('((COALESCE(pos_cash_qty_total,0) + COALESCE(pos_cash_return_qty_total,0) - COALESCE(pos_cash_returns_qty_total,0)) + (COALESCE(invoices_qty_total,0) - COALESCE(invoices_return_qty_total,0)))'), '!=', 0)
                ->get();

            if ($request->manage) {
                $data = [];
                $total_qty = 0;
                $total_supply_cost = 0;
                $total_sum = 0;
                $total_vat = 0;
                foreach ($items as $i) {
                    $supply_cost = ($i->pos_standard_cost_total + $i->invoice_sum_standard_cost - $i->invoice_return_sum_standard_cost);
                    $qty = ($i->pos_cash_qty_total + $i->invoices_qty_total - $i->invoices_return_qty_total);
                    $total = ($i->pos_cash_sum_total + $i->invoices_sum_total - $i->invoices_return_sum_total);
                    $vat = ($i->pos_cash_vat_total + $i->invoices_vat - $i->invoices_return_vat);
                    $total_qty += $qty;
                    $total_sum += $total;
                    $total_supply_cost += $supply_cost;
                    $total_vat += $vat;
                    $child = [
                        'Date' => $i->Date,
                        'Item Sold' => manageAmountFormat($qty),
                        'Margin' => manageAmountFormat($total - $supply_cost),
                        'Supply Cost' => manageAmountFormat($supply_cost),
                        'Total Sales' => manageAmountFormat($total),
                        'Tax Value' => manageAmountFormat($vat)
                    ];
                    $data[] = $child;
                }
                $child = [
                    'Date' => "Grand Total",
                    'Item Sold' => manageAmountFormat($total_qty),
                    'Margin' => manageAmountFormat($total_sum - $total_supply_cost),
                    'Supply Cost' => manageAmountFormat($total_supply_cost),
                    'Total Sales' => manageAmountFormat($total_sum),
                    'Tax Value' => manageAmountFormat($total_vat)
                ];
                $data[] = $child;
                return $this->downloadExcelFile($data, 'xls', 'sales_by_date_report');
            }
            $breadcum = [$title => route('summary_report.sales_by_date_report'), 'Listing' => ''];
            return view('admin.summary_report.sales_by_date_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'user', 'items', 'from', 'to'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function vat_sales_summary_report(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'VAT Sales Summary Report';
        $model = 'vat_sales_summary_report';
        if (isset($permission[$pmodule . '___vat_sales_summary_report']) || $permission == 'superadmin') {
            $user = getLoggeduserProfile();
            $from = $request->from ?? date('Y-m-d');
            $to = $request->to ?? date('Y-m-d');

            $sql = "WITH recursive Date_Ranges AS (
                select '" . $from . "' as Date
               union all
               select Date + interval 1 day
               from Date_Ranges
               where Date < '" . $to . "')
               select * from Date_Ranges";

            $location = $request->location && $request->location != '-1' ? " AND wa_stock_moves.wa_location_and_store_id = " . $request->location : "";
            $items = DB::query()->from(DB::raw("($sql) as date_ranges"))->select([
                'Date',
                DB::RAW("SUM(pos_cash.pos_cash_sum) as pos_cash_sum_total"),
                DB::RAW("SUM(pos_cash.pos_cash_qty) as pos_cash_qty_total"),
                DB::RAW("SUM(pos_cash.pos_standard_cost) as pos_standard_cost_total"),
                DB::RAW("SUM(pos_cash.pos_vat_amount) as pos_cash_vat_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_sum) as pos_cash_return_sum_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_qty) as pos_cash_return_qty_total"),
                DB::RAW("SUM(pos_cash_return.pos_standard_cost) as pos_return_standard_cost_total"),
                DB::RAW("SUM(pos_cash_return.pos_vat_amount) as pos_cash_return_vat_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_sum) as pos_cash_returns_sum_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_qty) as pos_cash_returns_qty_total"),
                DB::RAW("SUM(pos_cash_returns.pos_standard_cost) as pos_returns_standard_cost_total"),
                DB::RAW("SUM(pos_cash_returns.pos_vat_amount) as pos_cash_returns_vat_total"),

                DB::RAW("SUM(invoices.invoice_total) as invoices_sum_total"),
                DB::RAW("SUM(invoices.invoice_standard_cost) as invoice_sum_standard_cost"),
                DB::RAW("SUM(invoices.invoice_qty) as invoices_qty_total"),

                DB::RAW("SUM(invoices_return.invoice_total) as invoices_return_sum_total"),
                DB::RAW("SUM(invoices_return.invoice_standard_cost) as invoice_return_sum_standard_cost"),
                DB::RAW("SUM(invoices_return.invoice_qty) as invoices_return_qty_total"),

                DB::RAW("SUM(invoices.invoice_vat_amount) as invoices_vat"),
                DB::RAW("SUM(invoices_return.invoice_vat_amount) as invoices_return_vat"),
            ])
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.quantity) as invoice_standard_cost, 
                DATE(wa_inventory_location_transfers.transfer_date) as transfer_date,
                wa_inventory_location_transfer_items.wa_inventory_item_id
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED"
                 AND (DATE(wa_inventory_location_transfers.transfer_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_inventory_location_transfers.transfer_date)) as invoices'),
                    function ($e) {
                        $e->on('invoices.transfer_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.return_quantity) as invoice_qty, 
                SUM((wa_inventory_location_transfer_items.return_quantity * wa_inventory_location_transfer_items.selling_price)*vat_rate/100) as invoice_vat_amount, 
                wa_inventory_location_transfer_items.wa_inventory_item_id,
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.return_quantity) as invoice_standard_cost, 

                DATE(wa_inventory_location_transfer_items.return_date) as return_date
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED" AND is_return = 1
                 AND (DATE(wa_inventory_location_transfer_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_inventory_location_transfer_items.return_date)) as invoices_return'),
                    function ($e) {
                        $e->on('invoices_return.return_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id, 
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.created_at)) as pos_cash_return'),
                    function ($e) {
                        $e->on('pos_cash_return.created_at', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, 
                SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.return_date) as return_date
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.return_date)) as pos_cash_returns'),
                    function ($e) {
                        $e->on('pos_cash_returns.return_date', 'date_ranges.Date');
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty) as pos_cash_sum, SUM(wa_pos_cash_sales_items.qty) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM((wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty)*vat_percentage/100) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.qty) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed"
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY DATE(wa_pos_cash_sales_items.created_at)) as pos_cash'),
                    function ($e) {
                        $e->on('pos_cash.created_at', 'date_ranges.Date');
                    }
                )
                ->groupBy('date_ranges.Date')
                ->orderBy('date_ranges.Date', 'ASC')
                ->having(DB::RAW('((COALESCE(pos_cash_qty_total,0) + COALESCE(pos_cash_return_qty_total,0) - COALESCE(pos_cash_returns_qty_total,0)) + (COALESCE(invoices_qty_total,0) - COALESCE(invoices_return_qty_total,0)))'), '!=', 0)
                ->get();

            if ($request->manage) {
                $data = [];
                $total_qty = 0;
                $total_supply_cost = 0;
                $total_sum = 0;
                $total_vat = 0;
                foreach ($items as $i) {
                    $supply_cost = ($i->pos_standard_cost_total + $i->invoice_sum_standard_cost - $i->invoice_return_sum_standard_cost);
                    $qty = ($i->pos_cash_qty_total + $i->invoices_qty_total - $i->invoices_return_qty_total);
                    $total = ($i->pos_cash_sum_total + $i->invoices_sum_total - $i->invoices_return_sum_total);
                    $vat = ($i->pos_cash_vat_total + $i->invoices_vat - $i->invoices_return_vat);
                    $total_qty += $qty;
                    $total_sum += $total;
                    $total_supply_cost += $supply_cost;
                    $total_vat += $vat;
                    $child = [
                        'Date' => $i->Date,
                        'Item Sold' => manageAmountFormat($qty),
                        'Margin' => manageAmountFormat($total - $supply_cost),
                        'Supply Cost' => manageAmountFormat($supply_cost),
                        'Total Sales' => manageAmountFormat($total),
                        'Tax Value' => manageAmountFormat($vat)
                    ];
                    $data[] = $child;
                }
                $child = [
                    'Date' => "Grand Total",
                    'Item Sold' => manageAmountFormat($total_qty),
                    'Margin' => manageAmountFormat($total_sum - $total_supply_cost),
                    'Supply Cost' => manageAmountFormat($total_supply_cost),
                    'Total Sales' => manageAmountFormat($total_sum),
                    'Tax Value' => manageAmountFormat($total_vat)
                ];
                $data[] = $child;
                return $this->downloadExcelFile($data, 'xls', 'vat_sales_summary_report');
            }
            $breadcum = [$title => route('summary_report.vat_sales_summary_report'), 'Listing' => ''];
            return view('admin.summary_report.vat_sales_summary_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'user', 'items', 'from', 'to'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function __sales_by_date_report(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Detailed Sales Report';
        $model = 'sales_by_date_report';
        if (isset($permission[$pmodule . '___sales_by_date_report']) || $permission == 'superadmin') {
            $user = getLoggeduserProfile();
            $from = $request->from ?? date('Y-m-d');
            $to = $request->to ?? date('Y-m-d');
            $sql = "WITH recursive Date_Ranges AS (
                select '" . $from . "' as Date
               union all
               select Date + interval 1 day
               from Date_Ranges
               where Date < '" . $to . "')
               select * from Date_Ranges";

            $location = $request->location && $request->location != '-1' ? " AND wa_stock_moves.wa_location_and_store_id = " . $request->location : "";
            $items = DB::query()->from(DB::raw("($sql) as date_ranges"))->select([
                'Date',
                DB::RAW("SUM(pos_cash.pos_cash_sum) as pos_cash_sum_total"),
                DB::RAW("SUM(pos_cash.pos_cash_qty) as pos_cash_qty_total"),
                DB::RAW("SUM(pos_cash.pos_standard_cost) as pos_standard_cost_total"),
                DB::RAW("SUM(pos_cash.pos_vat_amount) as pos_cash_vat_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_sum) as pos_cash_return_sum_total"),
                DB::RAW("SUM(pos_cash_return.pos_cash_qty) as pos_cash_return_qty_total"),
                DB::RAW("SUM(pos_cash_return.pos_standard_cost) as pos_return_standard_cost_total"),
                DB::RAW("SUM(pos_cash_return.pos_vat_amount) as pos_cash_return_vat_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_sum) as pos_cash_returns_sum_total"),
                DB::RAW("SUM(pos_cash_returns.pos_cash_qty) as pos_cash_returns_qty_total"),
                DB::RAW("SUM(pos_cash_returns.pos_standard_cost) as pos_returns_standard_cost_total"),
                DB::RAW("SUM(pos_cash_returns.pos_vat_amount) as pos_cash_returns_vat_total"),

                DB::RAW("SUM(invoices.invoice_total) as invoices_sum_total"),
                DB::RAW("SUM(invoices.invoice_standard_cost) as invoice_sum_standard_cost"),
                DB::RAW("SUM(invoices.invoice_qty) as invoices_qty_total"),

                DB::RAW("SUM(invoices_return.invoice_total) as invoices_return_sum_total"),
                DB::RAW("SUM(invoices_return.invoice_standard_cost) as invoice_return_sum_standard_cost"),
                DB::RAW("SUM(invoices_return.invoice_qty) as invoices_return_qty_total"),

                DB::RAW("SUM(invoices.invoice_vat_amount) as invoices_vat"),
                DB::RAW("SUM(invoices_return.invoice_vat_amount) as invoices_return_vat"),
            ])
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.quantity) as invoice_qty, 
                SUM(wa_inventory_location_transfer_items.vat_amount) as invoice_vat_amount, 
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.quantity) as invoice_standard_cost, 
                DATE(wa_inventory_location_transfers.transfer_date) as transfer_date,
                wa_inventory_location_transfer_items.wa_inventory_item_id
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED"
                 AND (DATE(wa_inventory_location_transfers.transfer_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_inventory_location_transfer_items.wa_inventory_item_id) as invoices'),
                    function ($e) {
                        $e->on('invoices.transfer_date', DB::RAW('date_ranges.Date'));
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as invoice_total, 
                SUM(wa_inventory_location_transfer_items.quantity) as invoice_qty, 
                SUM(wa_inventory_location_transfer_items.vat_amount) as invoice_vat_amount, 
                wa_inventory_location_transfer_items.wa_inventory_item_id,
                SUM(wa_inventory_location_transfer_items.standard_cost * wa_inventory_location_transfer_items.quantity) as invoice_standard_cost, 

                DATE(wa_inventory_location_transfer_items.return_date) as return_date
                 from wa_inventory_location_transfer_items JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 WHERE wa_inventory_location_transfers.status = "COMPLETED" AND is_return = 1
                 AND (DATE(wa_inventory_location_transfer_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_inventory_location_transfer_items.wa_inventory_item_id) as invoices_return'),
                    function ($e) {
                        $e->on('invoices_return.return_date', DB::RAW('date_ranges.Date'));
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id, 
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                SUM(wa_pos_cash_sales_items.vat_amount) as pos_vat_amount,
                DATE(wa_pos_cash_sales_items.return_date) as return_date
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.return_date) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash_returns'),
                    function ($e) {
                        $e->on('pos_cash_returns.return_date', DB::RAW('date_ranges.Date'));
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.qty) as pos_cash_sum, SUM(wa_pos_cash_sales_items.qty) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM(wa_pos_cash_sales_items.vat_amount) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.qty) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed"
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash'),
                    function ($e) {
                        $e->on('pos_cash.created_at', DB::RAW('date_ranges.Date'));
                    }
                )
                ->leftJoin(
                    DB::RAW('(SELECT SUM(wa_pos_cash_sales_items.selling_price*wa_pos_cash_sales_items.return_quantity) as pos_cash_sum, SUM(wa_pos_cash_sales_items.return_quantity) as pos_cash_qty, 
                wa_pos_cash_sales.status, wa_pos_cash_sales_items.wa_inventory_item_id,
                SUM(wa_pos_cash_sales_items.vat_amount) as pos_vat_amount,
                SUM(wa_pos_cash_sales_items.standard_cost * wa_pos_cash_sales_items.return_quantity) as pos_standard_cost, 
                DATE(wa_pos_cash_sales_items.created_at) as created_at
                 from wa_pos_cash_sales_items JOIN wa_pos_cash_sales ON wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                 WHERE wa_pos_cash_sales.status = "Completed" AND is_return = 1
                 AND (DATE(wa_pos_cash_sales_items.created_at) BETWEEN "' . $from . '" AND "' . $to . '")
                 GROUP BY wa_pos_cash_sales_items.wa_inventory_item_id) as pos_cash_return'),
                    function ($e) {
                        $e->on('pos_cash_return.created_at', DB::RAW('date_ranges.Date'));
                    }
                )
                ->groupBy('date_ranges.Date')
                ->orderBy('date_ranges.Date', 'ASC')
                ->having(DB::RAW('(COALESCE(SUM(pos_cash.pos_cash_qty),0) + COALESCE(SUM(invoices.invoice_qty),0) - COALESCE(SUM(invoices_return.invoice_qty),0))'), '>', 0)
                ->get();

            if ($request->manage) {
                $data = [];
                $total_qty = 0;
                $total_supply_cost = 0;
                $total_sum = 0;
                $total_vat = 0;
                foreach ($items as $i) {
                    $supply_cost = ($i->pos_standard_cost_total + $i->pos_return_standard_cost_total - $i->pos_returns_standard_cost_total + $i->invoice_sum_standard_cost - $i->invoice_return_sum_standard_cost);
                    $qty = ($i->pos_cash_qty_total + $i->pos_cash_return_qty_total - $i->pos_cash_returns_qty_total + $i->invoices_qty_total - $i->invoices_return_qty_total);
                    $total = ($i->pos_cash_sum_total + $i->pos_cash_return_sum_total - $i->pos_cash_returns_sum_total + $i->invoices_sum_total - $i->invoices_return_sum_total);
                    $vat = ($i->pos_cash_vat_total + $i->pos_cash_return_vat_total - $i->pos_cash_returns_vat_total + $i->invoices_vat - $i->invoices_return_vat);
                    $total_qty += $qty;
                    $total_sum += $total;
                    $total_supply_cost += $supply_cost;
                    $total_vat += $vat;
                    $child = [
                        'Date' => $i->Date,
                        'Item Sold' => manageAmountFormat($qty),
                        'Margin' => manageAmountFormat($total - $supply_cost),
                        'Supply Cost' => manageAmountFormat($supply_cost),
                        'Total Sales' => manageAmountFormat($total),
                        'Tax Value' => manageAmountFormat($vat)
                    ];
                    $data[] = $child;
                }
                $child = [
                    'Date' => "Grand Total",
                    'Item Sold' => manageAmountFormat($total_qty),
                    'Margin' => manageAmountFormat($total_sum - $total_supply_cost),
                    'Supply Cost' => manageAmountFormat($total_supply_cost),
                    'Total Sales' => manageAmountFormat($total_sum),
                    'Tax Value' => manageAmountFormat($total_vat)
                ];
                $data[] = $child;
                return $this->downloadExcelFile($data, 'xls', 'sales_by_date_report');
            }
            $breadcum = [$title => route('summary_report.sales_by_date_report'), 'Listing' => ''];
            return view('admin.summary_report.sales_by_date_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'user', 'items', 'from', 'to'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
