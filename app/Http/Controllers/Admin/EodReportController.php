<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Excel;
use App\Model\User;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaStockMove;
use App\Model\WaDebtorTran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Enums\PaymentChannel;
use App\Model\WaInventoryItem;
use App\Model\WaPettyCashItem;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\WaInventoryLocationTransferItemReturn;
use App\Model\WaPosCashSales;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use App\Models\WaPettyCashRequestItem;
use App\Model\WaInternalRequisition;
use App\Models\WaPettyCashRequest;
use App\Model\RegisterCheque;
use App\Model\WaPosCashSalesItemReturns;
class EodReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'summary_report';
        $this->title = 'EOD Report';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->pageUrl = 'eod-report';
    }
    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Report';
        $model = $this->model;
        if (isset($permission[$pmodule . '___eod-report']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.eod_report.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function report(Request $request)
    {
        $user = getLoggeduserProfile();
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Report';
        $model = $this->model;
        $type = $request->type;
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        $vooma = PaymentChannel::Vooma->value;
        $kcb = PaymentChannel::KCB->value;
        $equity = PaymentChannel::Equity->value;
        $eazzy = PaymentChannel::Eazzy->value;
        $mpesa = PaymentChannel::Mpesa->value;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        $yesterday = Carbon::parse($request->date)->subDay()->toDateString();
        $yesterdayStart = Carbon::parse($request->date)->subDay()->startOfDay();
        $yesterdayEnd = Carbon::parse($request->date)->subDay()->endOfDay();
        $startDate = $request->date ? $request->date . ' 00:00:00' : now()->format('Y-m-d 00:00:00');
        $endDate = $request->date ? $request->date . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        if($request->branch && $request->branch == 'all'){
            $sales = null;
            $pos = null;
            $branch = null;

        }else{
            $branch = Restaurant::find($request->branch);
            $sales = "AND wa_internal_requisitions.restaurant_id = " . $request->branch;
            $pos = "AND branch_id = " . $request->branch;
        }
        if($request->type == 'route')
        {
            //ROUTE STUFF ONLY

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
                ->where('petty_cash_transactions.amount', '>', 10)
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
                ->where('petty_cash_transactions.amount', '>', 10)
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

            //sales

            $inventoryTransfers = DB::table('wa_customers')
                ->select(
                    'wa_customers.id',
                    'wa_customers.is_invoice_customer',
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as vcs'),
                    // DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    // FROM wa_inventory_location_transfer_item_returns 
                    // LEFT JOIN wa_inventory_location_transfer_items ON 
                    // wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                    // LEFT JOIN wa_inventory_location_transfers ON 
                    // wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                    // WHERE wa_inventory_location_transfer_item_returns.status = "received" 
                    // AND wa_inventory_location_transfer_item_returns.return_status = "1" 
                    // AND wa_inventory_location_transfers.route_id = routes.id
                    // AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) = "' . $request->date . '")
                    // ) as returns')
                )
                ->leftJoin('routes', 'wa_customers.route_id', 'routes.id')

                ->leftJoin('wa_internal_requisitions', function($join) use ($startDate, $endDate) {
                    $join->on('wa_internal_requisitions.customer_id', '=', 'wa_customers.id')
                        ->whereBetween('wa_internal_requisitions.created_at', [$startDate, $endDate]);
                })
                ->leftjoin('wa_internal_requisition_items', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id');
            if($request->branch && $request->branch != 'all'){
                $inventoryTransfers = $inventoryTransfers->where('wa_internal_requisitions.restaurant_id', $request->branch);
            }
            $inventoryTransfers = $inventoryTransfers->groupBy('wa_customers.id')
                ->get()
                ->keyBy('id');
            $returnsForToday = DB::table('wa_inventory_location_transfer_item_returns')
                ->select(
                    'wa_inventory_location_transfers.customer_id as id',
                    DB::raw("SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS returns")
                    )
                ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_items.id', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id')
                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->where('wa_inventory_location_transfer_item_returns.status', "received" )
                ->where('wa_inventory_location_transfer_item_returns.return_status', 1) 
                ->whereDate('wa_inventory_location_transfer_item_returns.updated_at', $request->date);
            if($request->branch && $request->branch != 'all'){
                $returnsForToday = $returnsForToday->where('wa_inventory_location_transfers.restaurant_id', $request->branch);

            }
            $returnsForToday = $returnsForToday->groupBy('wa_inventory_location_transfers.customer_id')->get()
                ->keyBy('id');
            
            $inventoryTransfersYesterday = DB::table('wa_customers')
                ->leftJoin('wa_internal_requisitions', function($join) use ($yesterdayStart, $yesterdayEnd) {
                    $join->on('wa_internal_requisitions.customer_id', '=', 'wa_customers.id')
                        ->whereBetween('wa_internal_requisitions.created_at', [$yesterdayStart,  $yesterdayEnd]);
                })
                ->leftjoin('wa_internal_requisition_items', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
                ->select(
                    'wa_customers.id',
                    'wa_customers.is_invoice_customer',
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as vcs')
                );
            if($request->branch && $request->branch != 'all'){
                $inventoryTransfersYesterday = $inventoryTransfersYesterday->where('wa_internal_requisitions.restaurant_id', $request->branch);
            }
            $inventoryTransfersYesterday = $inventoryTransfersYesterday->groupBy('wa_customers.id')
                ->get()
                ->keyBy('id');
              
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
            
             // cheques
             $cheques = DB::table('register_cheque')
                ->select(
                    'wa_customers.id',
                    DB::raw('SUM(register_cheque.amount) as amount'),
                    DB::raw('COUNT(register_cheque.amount) as count'),

                )
                ->whereDate('register_cheque.clearance_date',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->where('register_cheque.status', 'Cleared')
                ->whereIn('wa_customers.id', $customerIds);
                if($request->branch && $request->branch != 'all'){
                    $cheques = $cheques->where('routes.restaurant_id', $request->branch);
                }
                $cheques = $cheques->groupBy('wa_customers.id')
                    ->get()  
                    ->keyBy('id');                    

                $data = $customers->map(function ($customer) use ($inventoryTransfers, $returnsForToday, $inventoryTransfersYesterday, $tenderEntries, $pettyCashTransactions, $cheques ) {
                    $customerCode = $customer->customer_code;
                    $customerId = $customer->id;
        
                    // Inventory Location Transfers Data
                    if (isset($inventoryTransfers[$customerId])) {
                        if($inventoryTransfers[$customerId]->is_invoice_customer == 1){
                            $customer->invoiceSales = $inventoryTransfers[$customerId]->vcs;
                            $customer->vcs  = 0;
                        }else{
                            $customer->vcs = $inventoryTransfers[$customerId]->vcs;
                            $customer->invoiceSales = 0;
                        }
                        // $customer->returns = $inventoryTransfers[$customerId]->returns;
                    } else {
                        $customer->vcs = 0;
                        // $customer->returns = 0;
                        $customer->invoiceSales = 0;

                    }
                      // Returns for Today
                      if (isset($returnsForToday[$customerId])) {
                        $customer->returns = $returnsForToday[$customerId]->returns;
                    } else {
                        $customer->returns = 0;

                    }

                    // Inventory Location Transfers Data Yesterday
                    if (isset($inventoryTransfersYesterday[$customerId])) {
                        if($inventoryTransfersYesterday[$customerId]->is_invoice_customer == 1){
                            $customer->yesterdaySalesInv =  $inventoryTransfersYesterday[$customerId]->vcs;
                            $customer->yesterdaySales = 0;
                        }else{
                            $customer->yesterdaySales = $inventoryTransfersYesterday[$customerId]->vcs;
                            $customer->yesterdaySalesInv = 0;

                        }
                    } else {
                        $customer->yesterdaySales = 0;
                        $customer->yesterdaySalesInv = 0;
                    }
                     
                    // Cheques
                    if (isset($cheques[$customerId])) { 
                        $customer->Cheques = $cheques[$customerId]->amount;
                        $customer->Cheques_count = $cheques[$customerId]->count;

                    } else {
                        $customer->Cheques = 0;
                        $customer->Cheques_count = 0;

                 

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
                });
                // ->sortByDesc('vcs');
            $wirBranchFilter = '';
            $wiltBranchFilter = '';
            $subQueryBranchFilter = '';
            $posRouteFilter = '';
    
            if ($request->branch && $request->branch !== 'all') {
                $wirBranchFilter = " AND wa_internal_requisitions.restaurant_id = $request->branch";
                $wiltBranchFilter = " AND wa_inventory_location_transfers.restaurant_id = $request->branch";
                $subQueryBranchFilter = " AND wa_pos_cash_sales.branch_id = $request->branch";
    
            }
            if($request->type == 'route'){
                $posRouteFilter = " AND wa_internal_requisitions.requisition_no like 'INV%' ";
    
            }else{
                $posRouteFilter = " AND wa_internal_requisitions.requisition_no like 'CIV%' ";
    
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
                    $posRouteFilter
                GROUP BY 
                    sales_date 
                ORDER BY 
                    sales_date DESC
            ";
    
            $saleSummary = DB::select($query, [$yesterday . ' 00:00:00', $yesterday . ' 23:59:59']);

            $date = $request->date;
            $debtorsrecords = DB::table('wa_debtor_trans as trans')
                ->join('wa_customers as customers', 'customers.id', '=', 'trans.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'customers.route_id')
                ->where('routes.restaurant_id', $request->branch)
                ->select(
                    'customers.customer_name',
                    DB::raw('SUM(CASE WHEN trans.trans_date < "' . $date . '" THEN trans.amount ELSE 0 END) as balance_bf'),
                DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $date . '" AND trans.amount > 0 THEN trans.amount ELSE 0 END) as debits'),
                DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $date . '" AND trans.amount < 0 THEN trans.amount ELSE 0 END) as credits'),
                DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $date . '" AND trans.reference like "%Discount Allowed" AND trans.amount < 0 THEN trans.amount ELSE 0 END) as discounts'),
                DB::raw('MAX(CASE WHEN DATE(trans.trans_date) = "' . $date . '" THEN trans.trans_date ELSE NULL END) as last_trans_time')
                //pd cheques
        
                )
                ->groupBy('customers.customer_name')
                ->get()
                ->map(function ($record) {
                    return [
                        'customer' => $record->customer_name,
                        'balance_bf' => $record->balance_bf,
                        'debits' => $record->debits + ($record->discounts ?? 0),
                        'credits' => $record->credits +  (($record->discounts ?? 0) * -1),
                        'discounts' => $record->discounts,
                        'last_trans_time' => $record->last_trans_time ? Carbon::parse($record->last_trans_time)->format('d/m/Y H:i:s') : '-',
                        'pd_cheques' => 0,
                        'balance' => ($record->balance_bf + $record->debits) + $record->credits,
                    ];
            });
    
            $debtors = collect($debtorsrecords)->sortBy('balance', descending: true)->all();
          
            $invoiceSalesDetails = DB::table('wa_internal_requisitions')
                ->select(
                    'wa_internal_requisitions.*',
                    // 'wa_customers.id',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as vcs')
                )
                ->leftjoin('wa_internal_requisition_items', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
                ->leftJoin('wa_customers', 'wa_internal_requisitions.customer_id', '=', 'wa_customers.id') 
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->whereBetween('wa_internal_requisitions.created_at', [$yesterdayStart,  $yesterdayEnd])
                ->where('wa_customers.is_invoice_customer', 1)
                ;
            if($request->branch && $request->branch != 'all'){
                $invoiceSalesDetails = $invoiceSalesDetails->where('wa_internal_requisitions.restaurant_id', $request->branch);
            }
            $invoiceSalesDetails = $invoiceSalesDetails->groupBy('wa_internal_requisitions.id')->get();
            
            $maturedCheques = DB::table('register_cheque')
                ->select(
                    'register_cheque.*',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    'users.name as depositer'
                )
                ->whereDate('register_cheque.clearance_date',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->where('register_cheque.status', 'Cleared');
                if($request->branch && $request->branch != 'all'){
                    $maturedCheques = $maturedCheques->where('routes.restaurant_id', $request->branch);
                }
                $maturedCheques = $maturedCheques
                ->get();

                  //expenses 
                $pettyCashRequestTypes = DB::table('wa_petty_cash_request_types')
                  ->select('name', 'slug')
                  ->whereNotIn('id', [3, 4])
                  ->orderBy('id', 'desc')
                  ->get();
                  
 
                $pettyCashRequestTypesData = WaPettyCashRequestItem::with(['pettyCashRequest', 'pettyCashRequest.vehicle', 'deliverySchedule', 'deliverySchedule.vehicle', 'deliverySchedule.driver', 'route', 'employee','deliverySchedule.route', 'grn', 'grn.supplier', 'transfer', 'transfer.fromStoreDetail', 'transfer.toStoreDetail'])
                  ->whereHas('pettyCashRequest', function($query) use($request){
                      if($request->branch && $request->branch !== 'all'){
                          $query->where('restaurant_id', $request->branch)
                          ->where('final_approval', 1)
                          ;
                      }
                  })
                  ->whereBetween('created_at', [$request->date . ' 00:00:00', $request->date . ' 23:59:59']);
              $pettyCashRequestTypesData = $pettyCashRequestTypesData->get();

            $pdf = Pdf::loadView('admin.eod_report.report', compact('user', 'branch', 'data', 'saleSummary', 'pettyCashTransactions', 'deliveryPettyCashTransactions', 'salesmanPettyCashTransactions', 'debtors', 'type', 'yesterday', 'maturedCheques', 'invoiceSalesDetails', 'pettyCashRequestTypes', 'pettyCashRequestTypesData'))->setPaper('a4', 'landscape');
            // return $pdf->download('EOD-Report-' . $request->date . '-' . $request->date . '.pdf');
            return $pdf->stream('EOD-Report-' . $request->date . '-' . $request->date . '.pdf');
            // return view('admin.eod_report.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        }else{
            //POS STUFF ONLY
            $posCashSales = [];
            //filter out chief  cashier
            $invoiceUsers = WaInternalRequisition::whereBetween('created_at', [$startDate, $endDate])->where('invoice_type','Backend')->pluck('user_id')->toArray();
            $pettyCashUsers = WaPettyCashRequest::whereBetween('created_at', [$startDate, $endDate])->where('restaurant_id', $request->branch)->where('final_approval', 1)->where('type','supplier-cash-payments')->pluck('created_by')->toArray();
            $posSalesUserIds =  WaPosCashSales::whereBetween('paid_at', [$startDate, $endDate])->whereNot('attending_cashier')->pluck('attending_cashier')->toArray();
            $chequeUsers = RegisterCheque::whereBetween('date_received', [$startDate, $endDate])->where('branch_id', $request->branch)->pluck('user_id')->toArray();
            $crcUsers = WaDebtorTran::whereBetween('trans_date', [$startDate, $endDate])->where('document_no', 'like', 'CRC%')->where('branch_id', $request->branch)->pluck('user_id')->toArray();
            $mergedUsers = array_merge($invoiceUsers, $posSalesUserIds, $pettyCashUsers, $crcUsers, $chequeUsers);
            $posSales = DB::table('users')
                ->select(
                    'users.id',
                    'users.name as cashier',

                //     DB::raw("
                //     (
                //         SELECT SUM(rounded_sales.rounded_total) 
                //         FROM (
                //             SELECT 
                //                 CEIL(SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount)) AS rounded_total
                //             FROM wa_pos_cash_sales_items 
                //             LEFT JOIN wa_pos_cash_sales 
                //                 ON wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                //             WHERE wa_pos_cash_sales.attending_cashier = users.id
                //                 AND wa_pos_cash_sales.status = 'Completed'
                //                 AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                //             GROUP BY wa_pos_cash_sales.id
                //         ) AS rounded_sales
                //     ) AS cash_sales
                // "),


                    // DB::raw("(SELECT SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount)
                    //     FROM wa_pos_cash_sales_items 
                    //     LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    //     WHERE wa_pos_cash_sales.attending_cashier = users.id
                    //         AND wa_pos_cash_sales.status = 'Completed'
                    //          AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                    // ) AS cash_sales"),

                    DB::raw("(SELECT SUM((wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty) - wa_pos_cash_sales_items.discount_amount)
                        FROM wa_pos_cash_sales_items 
                        LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                        WHERE wa_pos_cash_sales.attending_cashier = users.id
                            AND wa_pos_cash_sales.status = 'Completed'
                             AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                    ) AS cash_sales"),

                    DB::raw("(SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                        FROM wa_pos_cash_sales_items_return
                        LEFT JOIN wa_pos_cash_sales_items on wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                        LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                        WHERE (DATE(wa_pos_cash_sales_items_return.updated_at)  BETWEEN '".$request->date."' AND '".$request->date."') 
                            AND wa_pos_cash_sales_items_return.accepted = '1'
                            AND wa_pos_cash_sales.attending_cashier = users.id
                             AND wa_pos_cash_sales.status = 'Completed'
                            AND(wa_pos_cash_sales.is_tablet_sale = '0' OR (wa_pos_cash_sales.is_tablet_sale = '1' AND wa_pos_cash_sales.attending_cashier != wa_pos_cash_sales.user_id ) )
                    ) AS cash_returns"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                        FROM wa_pos_cash_sales_payments
                        LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                        LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                        LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                        WHERE wa_pos_cash_sales.attending_cashier = users.id
                            AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                            AND payment_providers.slug = 'kcb'
                             AND wa_pos_cash_sales.status = 'Completed'
                    ) AS Vooma"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                        FROM wa_pos_cash_sales_payments
                        LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                        LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                        LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                        WHERE wa_pos_cash_sales.attending_cashier = users.id
                            AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                            AND payment_providers.slug = 'equity-bank'
                             AND wa_pos_cash_sales.status = 'Completed'
                    ) AS Eazzy"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount)
                        FROM wa_pos_cash_sales_payments
                        LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                        LEFT JOIN payment_methods ON wa_pos_cash_sales_payments.payment_method_id = payment_methods.id
                        LEFT JOIN payment_providers ON payment_methods.payment_provider_id = payment_providers.id
                        WHERE wa_pos_cash_sales.attending_cashier = users.id
                            AND (DATE(wa_pos_cash_sales_payments.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                            AND payment_providers.slug = 'mpesa'
                             AND wa_pos_cash_sales.status = 'Completed'
                    ) AS Mpesa"),

                    // Invoice Sales
                    DB::raw("(SELECT SUM(wa_internal_requisition_items.total_cost_with_vat)
                        FROM wa_internal_requisition_items
                        LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id 
                        WHERE wa_internal_requisitions.user_id = users.id 
                        AND wa_internal_requisitions.invoice_type ='Backend' 
                        AND (DATE(wa_internal_requisitions.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                    ) AS invoice_sales"),

                    //Returns
                      // Invoice Sales
                    DB::raw("(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                    FROM wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id 
                    LEFT JOIN wa_internal_requisitions ON  wa_internal_requisitions.requisition_no = wa_inventory_location_transfers.transfer_no
                    WHERE wa_inventory_location_transfers.user_id = users.id 
                    AND wa_internal_requisitions.invoice_type ='Backend' 
                    AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN '".$request->date."' AND '".$request->date."')
                  ) AS invoice_sales_returns"),

                  //Cheques Received
                  DB::raw("(SELECT SUM(amount)
                    FROM  register_cheque
                    WHERE register_cheque.user_id = users.id
                    AND (DATE(register_cheque.date_received) BETWEEN '".$request->date."' AND '".$request->date."')
                  ) AS Cheque"),
                   
                  //EXPENSES
                //   DB::raw("(SELECT SUM(wa_petty_cash_request_items.amount)
                //     FROM wa_petty_cash_request_items
                //     LEFT JOIN wa_petty_cash_requests ON wa_petty_cash_requests.id =  wa_petty_cash_request_items.wa_petty_cash_request_id
                //     WHERE wa_petty_cash_requests.created_by = users.id 
                //     AND wa_petty_cash_requests.final_approval = '1'
                //     AND wa_petty_cash_requests.type != 'supplier-cash-payments'
                //      AND (DATE(wa_petty_cash_requests.final_approval_date) BETWEEN '".$request->date."' AND '".$request->date."')   
                //   ) AS exp"),

                  //CRC
                  DB::raw("(SELECT SUM(amount)
                    FROM wa_debtor_trans
                    WHERE wa_debtor_trans.user_id = users.id
                    AND wa_debtor_trans.document_no  like 'CRC%'
                    AND (DATE(wa_debtor_trans.trans_date) BETWEEN '".$request->date."' AND '".$request->date."')
                  ) AS CRC"),
                    //SCP
                  DB::raw("(SELECT SUM(wa_petty_cash_request_items.amount)
                    FROM wa_petty_cash_request_items
                    LEFT JOIN wa_petty_cash_requests ON wa_petty_cash_requests.id =  wa_petty_cash_request_items.wa_petty_cash_request_id
                    WHERE wa_petty_cash_requests.created_by = users.id 
                    AND wa_petty_cash_requests.type = 'supplier-cash-payments'
                     AND (DATE(wa_petty_cash_requests.final_approval_date) BETWEEN '".$request->date."' AND '".$request->date."')   
                    ) AS SCP"),

                    //CDM
                    // DB::raw("(SELECT SUM(banked_amount)
                    //     FROM cash_drop_transactions
                    //     WHERE cash_drop_transactions.cashier_id = users.id
                    //     AND (DATE(cash_drop_transactions.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                    // ) as CDM"),
                    DB::raw("(SELECT SUM(bd.amount)
                        FROM banked_drop_transactions as bd
                        JOIN cash_drop_transactions as cd on bd.cash_drop_transaction_id = cd.id 
                        WHERE cd.cashier_id = users.id
                        AND (DATE(cd.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                        AND DATE(bd.created_at) >= '".$request->date."'
                    ) as CDM")

                    )
                ->whereIn('users.id', $mergedUsers);
                if($request->branch && $request->branch != 'all')
                {
                    $posSales = $posSales->where('users.restaurant_id', $request->branch);
                }            
                $posSales = $posSales->get()
                    ->map(function($record){
                        $record->CRD = 0;
                        return $record;
                    });

                $CrcsFromOtherBranches = DB::table('wa_debtor_trans')
                ->select(
                    'users.name as cashier',
                    'users.id',
                    DB::raw("SUM(amount * -1) as amount"),

                    )
                    ->leftJoin('users', 'users.id', 'wa_debtor_trans.user_id')
                    ->where('wa_debtor_trans.document_no', 'like', 'CRC%')
                    ->whereBetween('wa_debtor_trans.trans_date', [$request->date. ' 00:00:00', $request->date. ' 23:59:59']);
            if($request->branch && $request->branch != 'all')
            {
                $CrcsFromOtherBranches = $CrcsFromOtherBranches->where('wa_debtor_trans.branch_id', $request->branch)
                    ->whereNot('users.restaurant_id', $request->branch);
            }
            $CrcsFromOtherBranches = $CrcsFromOtherBranches->groupBy('users.id')->get();

            foreach($CrcsFromOtherBranches as $record){
                $payload = (object)[
                    'cashier' => $record->cashier,
                    'id' => $record->id,
                    // 'exp' =>  0,
                    'cash_sales' => 0,
                    'cash_returns' => 0,
                    'Vooma' => 0,
                    'Eazzy' => 0,   
                    'Mpesa' => 0,
                    'invoice_sales' => 0,
                    'invoice_sales_returns' => 0,
                    'Cheque' => 0,
                    'CRC' => $request->amount ?? 0,
                    'SCP'=>  0,
                    'CRD' => 0,
                    'CDM' => 0,
                ];
                $posSales->push($payload);

            }

                $chequesFromOtherBranches = DB::table('register_cheque')
                    ->select(
                        'users.name as cashier',
                        'users.id',
                        DB::raw('SUM(register_cheque.amount) as amount')

                    )
                    ->leftJoin('users', 'users.id', 'register_cheque.user_id')
                    ->whereBetween('register_cheque.date_received', [$request->date. ' 00:00:00', $request->date. ' 23:59:59']);
                if($request->branch && $request->branch != 'all')
                {
                    $chequesFromOtherBranches = $chequesFromOtherBranches->where('register_cheque.branch_id', $request->branch)
                        ->whereNot('users.restaurant_id', $request->branch);
                }
                $chequesFromOtherBranches = $chequesFromOtherBranches->groupBy('register_cheque.user_id')
                    ->get();
                    foreach($chequesFromOtherBranches as $record){
                        $payload = (object)[
                            'cashier' => $record->cashier,
                            'id' => $record->id,
                            // 'exp' => 0,
                            'cash_sales' => 0,
                            'cash_returns' => 0,
                            'Vooma' => 0,
                            'Eazzy' => 0,   
                            'Mpesa' => 0,
                            'invoice_sales' => 0,
                            'invoice_sales_returns' => 0,
                            'Cheque' => $record->amount ?? 0,
                            'CRC' => 0,
                            'SCP'=> 0,
                            'CRD' => 0,
                            'CDM' => 0,


                        ];
                        $posSales->push($payload);
    
                    }
                $expensesFromOtherBranches = DB::table('wa_petty_cash_request_items')
                    ->select(
                        'users.name as cashier',
                        'users.id',
                        DB::raw("(SUM(CASE WHEN wa_petty_cash_requests.type != 'supplier-cash-payments' THEN wa_petty_cash_request_items.amount ELSE 0 END)) AS amount"),
                        DB::raw("(SUM(CASE WHEN wa_petty_cash_requests.type = 'supplier-cash-payments' THEN wa_petty_cash_request_items.amount ELSE 0 END)) AS SCP")

                        )
                    ->leftJoin('wa_petty_cash_requests', 'wa_petty_cash_requests.id', 'wa_petty_cash_request_items.wa_petty_cash_request_id')
                    ->whereBetween('wa_petty_cash_requests.final_approval_date', [$request->date. ' 00:00:00', $request->date. ' 23:59:59'])
                    ->leftJoin('users', 'users.id', 'wa_petty_cash_requests.created_by');
                if($request->branch && $request->branch != 'all')
                {
                    $expensesFromOtherBranches = $expensesFromOtherBranches->where('wa_petty_cash_requests.restaurant_id', $request->branch)
                        ->whereNot('users.restaurant_id', $request->branch);
                }
                $expensesFromOtherBranches = $expensesFromOtherBranches->groupBy('wa_petty_cash_requests.created_by')->get();

                $otherBranchExpenses = [];

                if($posSales->count() > 0)
                {
                    $posSalesExist = true;
                }else{
                    $posSalesExist = false;
                }
            $tabletReturns = DB::table('wa_pos_cash_sales_items_return')
                ->select(
                    DB::raw("SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) AS tabletSalesReturns"),
                    )
                    ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
                    ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
                    ->leftJoin('users', 'users.id', 'wa_pos_cash_sales.attending_cashier')
                    ->whereBetween('wa_pos_cash_sales_items_return.updated_at', [$request->date. ' 00:00:00', $request->date. ' 23:59:59'])
                    ->where('wa_pos_cash_sales_items_return.accepted', 1)
                    ->where('wa_pos_cash_sales.is_tablet_sale', 1)
                    ->whereColumn('wa_pos_cash_sales.user_id', '=', 'wa_pos_cash_sales.attending_cashier');
            if($request->branch && $request->branch != 'all')
            {
                $tabletReturns = $tabletReturns->where('users.restaurant_id', $request->branch);
            }            
            $tabletReturns = $tabletReturns->value('tabletSalesReturns');

            //returns for backend cashiers with no sales for that day
            $backendCashierReturns = DB::table('wa_pos_cash_sales_items_return')
                    ->select(
                        DB::raw("SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) AS tabletSalesReturns"),
                        DB::raw("(SELECT SUM((wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty) - wa_pos_cash_sales_items.discount_amount)
                            FROM wa_pos_cash_sales_items 
                            LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                            WHERE wa_pos_cash_sales.attending_cashier = users.id
                                AND wa_pos_cash_sales.status = 'Completed'
                                AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$request->date."' AND '".$request->date."')
                        ) AS cash_sales"),
                    )
                    ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
                    ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
                    ->leftJoin('users', 'users.id', 'wa_pos_cash_sales.attending_cashier')
                    ->whereBetween('wa_pos_cash_sales_items_return.updated_at', [$request->date. ' 00:00:00', $request->date. ' 23:59:59'])
                    ->where('wa_pos_cash_sales_items_return.accepted', 1)
                    ->where(function ($query) {
                        $query->where('wa_pos_cash_sales.is_tablet_sale', 0)
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('wa_pos_cash_sales.is_tablet_sale', 1)
                                         ->whereColumn('wa_pos_cash_sales.user_id', '!=', 'wa_pos_cash_sales.attending_cashier');
                            });                    
                        });
            if($request->branch && $request->branch != 'all')
            {
                $backendCashierReturns = $backendCashierReturns->where('users.restaurant_id', $request->branch);
            }            
            $backendCashierReturns = $backendCashierReturns->havingRaw('cash_sales = 0')
                ->value('tabletSalesReturns');

            $tabletReturns = $tabletReturns + $backendCashierReturns;

            //cash Banking
            $cashBanking = DB::table('chief_cashier_declarations as cb')
            ->select(
                'cb.*',
                'statements.channel'
            )
            ->join('payment_verification_banks as statements', 'cb.bank_statement_id', '=', 'statements.id')
            ->whereBetween('cb.created_at', [$request->date. ' 00:00:00', $request->date. ' 23:59:59'])
            ->where('cb.branch_id', $request->branch)
            ->where('banked_amount', '>', 0)
            ->sum('banked_amount');

            //pos cash payments
            $posCashPayments  =  DB::table('pos_cash_payments')
                ->select(
                    DB::raw("SUM(pos_cash_payments.amount) AS posCashPayments"),
                        )
                ->whereBetween('pos_cash_payments.disbursed_at', [$request->date. ' 00:00:00', $request->date. ' 23:59:59']);
            if($request->branch && $request->branch != 'all'){
                $posCashPayments = $posCashPayments->where('branch_id', $request->branch);
            }
            $posCashPayments =  $posCashPayments->value('posCashPayments');

            $unconsumedPosPayments = DB::table('wa_tender_entries')
                ->select(
                    DB::raw('SUM(CASE WHEN payment_methods.payment_provider_id = 2 THEN wa_tender_entries.amount ELSE 0 END) as Vooma'),
                    DB::raw('SUM(CASE WHEN payment_methods.payment_provider_id = 3 THEN wa_tender_entries.amount ELSE 0 END) as Eazzy'),
                    DB::raw('SUM(CASE WHEN payment_methods.payment_provider_id = 1 THEN wa_tender_entries.amount ELSE 0 END) as Mpesa'),
                )
                ->leftJoin('payment_methods', 'payment_methods.id', 'wa_tender_entries.wa_payment_method_id')
                ->leftJoin('wa_chart_of_accounts_branches as branches', 'payment_methods.gl_account_id', 'branches.wa_chart_of_account_id')
                ->whereBetween('wa_tender_entries.created_at', [$request->date, $request->date])
                ->where('payment_methods.use_in_pos', true)
                ->where('wa_tender_entries.consumed', false);
            if($request->branch  && $request->branch != 'all'){
                $unconsumedPosPayments = $unconsumedPosPayments->where('branches.restaurant_id', $request->branch);
            }
            $unconsumedPosPayments = $unconsumedPosPayments
                ->get()->map(function($record){
                    return $record;
                });
            $totalVooma = 0;
            $totalEazzy = 0;
            $totalMpesa = 0;
            $totalCheque = 0;
            foreach ($unconsumedPosPayments as $record) {
                $totalVooma += $record->Vooma;
                $totalEazzy += $record->Eazzy;
                $totalMpesa += $record->Mpesa;
                $totalCheque += $record->Cheque ?? 0; 
            }
            // Create a single object to hold the summed amounts
            $unconsumedPosPayments = (object) [
                'Vooma' => $totalVooma,
                'Eazzy' => $totalEazzy,
                'Mpesa' => $totalMpesa,
                'Cheque' => 0 // Add Cheque as 0 if needed
            ];

            //? revisit section
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
                ->whereBetween('wa_debtor_trans.trans_date', [$request->date . ' 00:00:00', $request->date . ' 23:59:59'])
                ->get();
    
            $expenses = WaPettyCashItem::with(['chart_of_account', 'parent.user'])
                ->whereBetween('created_at', [$request->date . ' 00:00:00', $request->date . ' 23:59:59'])
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
            $posRouteFilter = '';
    
            if ($request->branch && $request->branch !== 'all') {
                $wirBranchFilter = " AND wa_internal_requisitions.restaurant_id = $request->branch";
                $wiltBranchFilter = " AND wa_inventory_location_transfers.restaurant_id = $request->branch";
                $subQueryBranchFilter = " AND wa_pos_cash_sales.branch_id = $request->branch";
    
            }
            if($request->type == 'route'){
                $posRouteFilter = " AND wa_internal_requisitions.requisition_no like 'INV%' ";
    
            }else{
                // $posRouteFilter = " AND wa_internal_requisitions.requisition_no like 'CIV%' OR ( wa_internal_requisitions.requisition_no like 'INV%' AND wa_internal_requisitions.invoice_type = 'Backend' )";
                $posRouteFilter = " AND (wa_internal_requisitions.requisition_no like 'CIV%' 
                     OR (wa_internal_requisitions.requisition_no like 'INV%' 
                         AND wa_internal_requisitions.invoice_type = 'Backend'))";
    
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
                DATE(wa_internal_requisition_items.created_at) = '$request->date'
                    $wirBranchFilter
                    $posRouteFilter
                GROUP BY 
                    sales_date 
                ORDER BY 
                    sales_date DESC
                ";
                $saleSummary = DB::select($query);

                //expenses 
                $pettyCashRequestTypes = DB::table('wa_petty_cash_request_types')
                    ->select('name', 'slug')
                    ->whereNotIn('id', [3, 4, 9])
                    ->orderBy('id', 'desc')
                    ->get();
                    
   
                $pettyCashRequestTypesData = WaPettyCashRequestItem::with(['pettyCashRequest', 'pettyCashRequest.vehicle', 'deliverySchedule', 'deliverySchedule.vehicle', 'deliverySchedule.driver', 'route', 'employee','deliverySchedule.route', 'grn', 'grn.supplier', 'transfer', 'transfer.fromStoreDetail', 'transfer.toStoreDetail'])
                    ->whereHas('pettyCashRequest', function($query) use($request){
                        $query->whereBetween('final_approval_date', [$request->date . ' 00:00:00', $request->date . ' 23:59:59']);
                        if($request->branch && $request->branch !== 'all'){
                            $query->where('restaurant_id', $request->branch)
                            ->where('final_approval', 1)
                            ;
                        }
                    });
                $pettyCashRequestTypesData = $pettyCashRequestTypesData->get();
    
            

            $receivedCheques = DB::table('register_cheque')
                ->select(
                    'register_cheque.*',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    'users.name as depositer'
                )
                ->whereDate('register_cheque.date_received',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by');
                if($request->branch && $request->branch != 'all'){
                    $receivedCheques = $receivedCheques->where('register_cheque.branch_id', $request->branch);
                }
                $receivedCheques = $receivedCheques
                ->get();
            $bankedCheques = DB::table('register_cheque')
                ->select(
                    'register_cheque.*',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    'users.name as depositer',
                    'cheque_banks.bank as bank'
                )
                ->whereDate('register_cheque.deposited_date',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->leftJoin('cheque_banks', 'cheque_banks.id', 'register_cheque.bank_deposited');
                if($request->branch && $request->branch != 'all'){
                    $bankedCheques = $bankedCheques->where('register_cheque.branch_id', $request->branch);
                }
            $bankedCheques = $bankedCheques
                ->get();
            $unbankedCheques = DB::table('register_cheque')
                ->select(
                    'register_cheque.*',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    'users.name as depositer',
                    'cheque_banks.bank as bank'
                )
                ->whereDate('register_cheque.cheque_date',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->leftJoin('cheque_banks', 'cheque_banks.id', 'register_cheque.bank_deposited')
                ->whereNull('register_cheque.deposited_date');
                if($request->branch && $request->branch != 'all'){
                    $unbankedCheques = $unbankedCheques->where('register_cheque.branch_id', $request->branch);
                }
            $unbankedCheques = $unbankedCheques
                ->get();
            $unpaidCheques = DB::table('register_cheque')
                ->select(
                    'register_cheque.*',
                    'wa_customers.customer_name',
                    'routes.route_name',
                    'users.name as depositer',
                    'cheque_banks.bank as bank',
                    'cheque_banks.bounce_penalty'
                )
                ->whereDate('register_cheque.cheque_date',  $request->date)
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.delivery_route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->leftJoin('cheque_banks', 'cheque_banks.id', 'register_cheque.bank_deposited')
                ->where('register_cheque.status', 'Bounced');
                if($request->branch && $request->branch != 'all'){
                    $unpaidCheques = $unpaidCheques->where('register_cheque.branch_id', $request->branch);
                }
                $unpaidCheques = $unpaidCheques
                ->get();

            // $cdmTransactions = DB::table('cash_drop_transactions')
            //     ->select(
            //         'chiefcashier.name as chiefcashier_name',
            //         'cashier.name as cashier_name',
            //         // 'cash_drop_transactions.amount',
            //         DB::raw(" CASE
            //             WHEN ROW_NUMBER() OVER (PARTITION BY cash_drop_transactions.id ORDER BY banked_drop_transactions.id) = 1
            //             THEN cash_drop_transactions.amount
            //             ELSE 0
            //         END as amount"),
            //         'cash_drop_transactions.reference',
            //         'banked_drop_transactions.amount as banked_amount',
            //         'banked_drop_transactions.bank_reference as bank_receipt_number',

            //     )
            //     ->leftJoin('banked_drop_transactions', 'cash_drop_transactions.id', 'banked_drop_transactions.cash_drop_transaction_id')
            //     ->leftJoin('users as chiefcashier', 'chiefcashier.id', 'cash_drop_transactions.user_id')
            //     ->leftJoin('users as cashier', 'cashier.id', 'cash_drop_transactions.cashier_id')
            //     ->whereBetween('cash_drop_transactions.created_at', [$request->date . ' 00:00:00', $request->date . ' 23:59:59'])
            //     ->whereDate('banked_drop_transactions.created_at', '>=',$request->date);
            // if ($request->branch && $request->branch !== 'all') {
            //     $cdmTransactions = $cdmTransactions->where('cashier.restaurant_id', $request->branch);
            // }
            // $cdmTransactions = $cdmTransactions->get();


            $cdmTransactions = DB::table(DB::raw('(
                SELECT 
                    cash_drop_transactions.*,
                    banked_drop_transactions.id as bdt_id,
                    banked_drop_transactions.amount as banked_amount_cdt,
                    banked_drop_transactions.bank_reference as bank_receipt_number_cdt,
                    ROW_NUMBER() OVER (PARTITION BY cash_drop_transactions.id ORDER BY banked_drop_transactions.id) as rn
                FROM cash_drop_transactions
                LEFT JOIN banked_drop_transactions ON cash_drop_transactions.id = banked_drop_transactions.cash_drop_transaction_id
                AND DATE(banked_drop_transactions.created_at) >= '."$request->date".'
            ) as cdt'))
            ->select(
                'chiefcashier.name as chiefcashier_name',
                'cashier.name as cashier_name',
                DB::raw('CASE WHEN rn = 1 THEN cdt.amount ELSE 0 END as amount'),
                'cdt.reference',
                'cdt.banked_amount_cdt as banked_amount',
                'cdt.bank_receipt_number_cdt as bank_receipt_number'
            )
            ->leftJoin('users as chiefcashier', 'chiefcashier.id', 'cdt.user_id')
            ->leftJoin('users as cashier', 'cashier.id', 'cdt.cashier_id')
            ->whereBetween('cdt.created_at', [$request->date . ' 00:00:00', $request->date . ' 23:59:59'])
            ->when($request->branch && $request->branch !== 'all', function($query) use ($request) {
                return $query->where('cashier.restaurant_id', $request->branch);
            })
            ->get();





            $crcBankings = DB::table('crc_records')
                ->select(
                    'crc_records.*',
                    'users.name as chiefCashier'
                )
                ->leftJoin('users', 'users.id', 'crc_records.user_id')
                ->whereBetween('crc_records.created_at', [$request->date . ' 00:00:00', $request->date . ' 23:59:59']);
            if ($request->branch && $request->branch !== 'all') {
                $crcBankings = $crcBankings->where('crc_records.branch_id', $request->branch);
            }
            $crcBankings = $crcBankings->get();

            foreach($crcBankings as $record){
                $payload = (object)[
                    'chiefcashier_name' => $record->chiefCashier ?? 0,
                    'cashier_name' => '-',
                    'amount' => $record->amount ?? 0,
                    'reference' => $record->reference ?? '-',
                    'bank_receipt_number'=> $record->bank_reference ?? '-',
                    'banked_amount' => $record->banked_amount ?? 0,
 
                ];
                $cdmTransactions->push($payload);

            }

        $debtorsrecords = DB::table('wa_debtor_trans as trans')
            ->join('wa_customers as customers', 'customers.id', '=', 'trans.wa_customer_id')
            ->leftJoin('routes', 'routes.id', 'customers.route_id')
            ->where('routes.restaurant_id', $request->branch)
            ->where('routes.is_pos_route', 0)
            ->select(
                'customers.customer_name',
                DB::raw('SUM(CASE WHEN trans.trans_date < "' . $request->date . '" THEN trans.amount ELSE 0 END) as balance_bf'),
            DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $request->date . '" AND trans.amount > 0 THEN trans.amount ELSE 0 END) as debits'),
            DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $request->date . '" AND trans.amount < 0 THEN trans.amount ELSE 0 END) as credits'),
            DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $request->date . '" AND trans.reference like "%Discount Allowed" AND trans.amount < 0 THEN trans.amount ELSE 0 END) as discounts'),
            DB::raw('MAX(CASE WHEN DATE(trans.trans_date) = "' . $request->date . '" THEN trans.trans_date ELSE NULL END) as last_trans_time'),

            DB::raw("(SELECT SUM(cheques.amount)
                FROM register_cheque as cheques
                LEFT JOIN  wa_customers as cheque_customer ON  cheques.wa_customer_id = cheque_customer.id
                WHERE cheque_customer.id = customers.id
                AND cheques.status = 'Registered'
                AND DATE(cheques.cheque_date) > '$request->date'
            )as pd_cheques")
    
            )
            ->groupBy('customers.customer_name')
            ->get()
            ->map(function ($record) {
                return [
                    'customer' => $record->customer_name,
                    'balance_bf' => $record->balance_bf,
                    'debits' => $record->debits + ($record->discounts ?? 0),
                    'credits' => $record->credits +  (($record->discounts ?? 0) * -1),
                    'discounts' => $record->discounts,
                    'last_trans_time' => $record->last_trans_time ? Carbon::parse($record->last_trans_time)->format('d/m/Y H:i:s') : '-',
                    'pd_cheques' => $record->pd_cheques ?? 0,
                    'balance' => ($record->balance_bf + $record->debits) + $record->credits,
                ];
        });

        $debtors = collect($debtorsrecords)->sortBy('balance', descending: true)->all();

        $crcRecords = DB::table('wa_debtor_trans')
            ->select(
                DB::raw('(wa_debtor_trans.amount * -1) AS amount'),
                'users.name AS received_by',
                'wa_customers.customer_name AS customer',
            )
            ->leftJoin('users', 'wa_debtor_trans.user_id', 'users.id')
            ->leftJoin('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
            ->where('document_no', 'like', 'CRC%')
            ->whereBetween('trans_date', [$request->date . ' 00:00:00', $request->date . ' 23:59:59']);
        if($request->branch && $request->branch !== 'all') {
                $crcRecords = $crcRecords->where('wa_debtor_trans.branch_id', $request->branch);
            }
        $crcRecords = $crcRecords->get();

        $scpRecords = DB::table('wa_petty_cash_request_items')
            ->select(
                'users.name as created_by',
                'wa_petty_cash_request_items.amount',
                'wa_petty_cash_request_items.payee_name',
                'wa_petty_cash_request_items.payment_reason',
                'wa_petty_cash_requests.petty_cash_no'
            )
            ->leftJoin('wa_petty_cash_requests', 'wa_petty_cash_requests.id', 'wa_petty_cash_request_items.wa_petty_cash_request_id')
            ->leftJoin('users', 'users.id', 'wa_petty_cash_requests.created_by')
            ->whereBetween('wa_petty_cash_requests.final_approval_date', [$request->date . ' 00:00:00', $request->date . ' 23:59:59'])
            ->where('wa_petty_cash_requests.type', 'supplier-cash-payments');
        if($request->branch && $request->branch !== 'all') {
            $scpRecords = $scpRecords->where('wa_petty_cash_requests.restaurant_id', $request->branch);
        }
        $scpRecords = $scpRecords->get();
        
            $pdf = Pdf::loadView('admin.eod_report.pos', compact('user', 'branch', 'invoiceReturn', 'cashreceipt', 'expenses', 'saleSummary', 'posSales', 'posSalesExist','type', 'yesterday', 'unconsumedPosPayments', 'pettyCashRequestTypes', 'pettyCashRequestTypesData', 'tabletReturns', 'cdmTransactions', 'receivedCheques', 'bankedCheques', 'unpaidCheques', 'unbankedCheques', 'debtors', 'crcRecords', 'scpRecords', 'posCashPayments', 'cashBanking'))->setPaper('a4', 'landscape');
            
            // return $pdf->download('EOD-Report-Counter' . $request->date . '-' . $request->date . '.pdf');
            return $pdf->stream('EOD-Report-Counter' . $request->date . '-' . $request->date . '.pdf');
            // return view('admin.eod_report.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        }

    }
}
