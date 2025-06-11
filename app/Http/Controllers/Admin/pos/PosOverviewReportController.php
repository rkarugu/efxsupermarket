<?php

namespace App\Http\Controllers\Admin\pos;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesItemReturns;
use App\PaymentProvider;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosOverviewReportController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'pos-overview-report';
        $this->base_route = 'pos-overview-report';
        $this->base_title = 'POS Overview Report';
        $this->permissions_module = 'sales-and-receivables-reports';
    }
    public function index(Request $request)
    {
        $title = 'Pos Overview Report';
        $model = $this->model;
        $pmodule = $this->permissions_module;
        $branches = Restaurant::get();
        $user = Auth::user();
        $branch = $request->branch ?? Restaurant::find($user->restaurant_id)->id;
        $branchDetails = Restaurant::find($branch);
        $payment_providers = PaymentProvider::all();
        $permission =  $this->mypermissionsforAModule();
        $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfDay();
        $to_date = $request->to_date? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();

        if (isset($permission[$pmodule. '___pos-overview-report']) || $permission == 'superadmin') {
            $orders = WaPosCashSales::where('branch_id', $branch)
                ->where('status', 'Completed')
                ->whereBetween('paid_at', [$from_date, $to_date])
                ->get();
            // $paymentTotals = $orders->pluck('id')
            //     ->flatMap(function($orderId) {
            //         return DB::table('wa_pos_cash_sales_payments')
            //             ->join('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            //             ->where('wa_pos_cash_sales_payments.wa_pos_cash_sales_id', $orderId)
            //             ->select('payment_methods.title', DB::raw('SUM(wa_pos_cash_sales_payments.amount) as total'))
            //             ->groupBy('payment_methods.title')
            //             ->get();
            //     });
            // $payments = $paymentTotals->groupBy('title')->map(function($group) {
            //     return $group->sum('total');
            // });
            $orderIds = $orders->pluck('id')->toArray();
            if (empty($orderIds)) {
                $payments = collect(); // Return an empty collection
            }else{
            $idsString = implode(',', $orderIds);
            $payments = DB::table('wa_pos_cash_sales_payments')
                ->select(
                    DB::raw("DATE(wa_pos_cash_sales_payments.created_at) AS date"),
                    DB::raw("SUM(wa_pos_cash_sales_payments.amount) AS total_payments"),
                    DB::raw("COUNT(wa_pos_cash_sales_payments.amount) AS total_count"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '1'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS Mpesa"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '1'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS MpesaCount"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '3'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS Eazzy"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '3'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS EazzyCount"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '2'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS Vooma"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '2'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS VoomaCount"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '5'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND payment_methods.use_in_pos = '1'
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS Cash"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_payments.amount) 
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    LEFT JOIN payment_providers ON payment_providers.id = payment_methods.payment_provider_id
                    WHERE payment_providers.id = '5'
                    AND wa_pos_cash_sales_payments.wa_pos_cash_sales_id IN  ($idsString)
                    AND payment_methods.use_in_pos = '1'
                    AND DATE(wa_pos_cash_sales_payments.created_at) = date) AS CashCount"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    WHERE wa_pos_cash_sales_items_return.accepted = '0'
                    AND wa_pos_cash_sales_items_return.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_items_return.updated_at) = date
                    ) AS pending_returns"),
                    DB::raw("(SELECT SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    WHERE wa_pos_cash_sales_items_return.accepted = '1'
                    AND wa_pos_cash_sales_items_return.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_items_return.updated_at) = date
                    ) AS accepted_returns"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    WHERE wa_pos_cash_sales_items_return.accepted = '0'
                    AND wa_pos_cash_sales_items_return.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_items_return.updated_at) = date
                    ) AS pending_returns_count"),
                    DB::raw("(SELECT COUNT(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) 
                    FROM wa_pos_cash_sales_items_return
                    LEFT JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales_items.id = wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id
                    WHERE wa_pos_cash_sales_items_return.accepted = '1'
                    AND wa_pos_cash_sales_items_return.wa_pos_cash_sales_id IN  ($idsString)
                    AND DATE(wa_pos_cash_sales_items_return.updated_at) = date
                    ) AS accepted_returns_count"),
                )
                ->whereBetween('wa_pos_cash_sales_payments.created_at', [$from_date, $to_date])
                ->whereIn('wa_pos_cash_sales_id', $orders->pluck('id')->toArray())
                ->groupBy('date')
                ->get();         
            }  
            $stockMovesReturns =  DB::table('wa_stock_moves')
                ->select(DB::raw("SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price) as total_stock_value"))
                ->whereBetween('wa_stock_moves.created_at', [$from_date, $to_date])
                ->where('wa_stock_moves.restaurant_id', $branch)
                ->whereIn('wa_stock_moves.wa_pos_cash_sales_id', $orders->pluck('id')->toArray())
                ->where('document_no', 'like', 'RTN%')
                ->first();
            $stockMovesSales =  DB::table('wa_stock_moves')
                ->select(DB::raw("SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price) as total_stock_value"))
                ->whereBetween('wa_stock_moves.created_at', [$from_date, $to_date])
                ->where('wa_stock_moves.restaurant_id', $branch)
                ->where('document_no', 'like', 'CIV%')
                ->first();
            $stockMoves = (($stockMovesSales->total_stock_value * -1) - $stockMovesReturns->total_stock_value );
            
            $customers = WaPosCashSales::where('branch_id', $branch)
                ->with('items')
                ->where('status', 'Completed')
                ->whereBetween('paid_at', [$from_date, $to_date])
                ->distinct('wa_route_customer_id')
                ->count('wa_route_customer_id');
            $total_sales = $orders->sum->gross_total;
            $accepted_returns = $orders->sum->acceptedReturnsTotal;
            $pending_returns = $orders->sum->pendingReturnsTotal;
            $total_returns = $accepted_returns ;
            $total_transaction = $orders->count();
            if ($request->download)
            {
                $pdf=   Pdf::loadView('admin.sales_and_receivables_reports.pos-overview-pdf', compact(

                    'title','branches','model',
                    'total_sales',
                    'total_returns',
                    'total_transaction',
                    'payments',
                    'pending_returns',
                    'accepted_returns',
                    'branch',
                    'customers',
                    'branchDetails',
                    'user',
                    'from_date',
                    'to_date',
                    'stockMoves'
                ));
                return $pdf->download('pos-sales-vs-payments' . date('Y_m_d_h_i_s') . '.pdf');
            }

            return view('admin.sales_and_receivables_reports.pos-overview', compact(
                'title','branches','model',
                'total_sales',
                'total_returns',
                'total_transaction',
                'payments',
                'pending_returns',
                'accepted_returns',
                'customers',
                'branch',
                'stockMoves',
                'from_date',
                'to_date'
            ));
        }else{
            return  redirect()->back()->with('warning','Invalid Request');
        }

    }
    public function posSales(Request $request, $startDate, $endDate, $branch)
    {
        // dd($startDate, $endDate, $branch);
        $title = 'Pos Overview Report';
        $model = $this->model;
        $pmodule = $this->permissions_module;
        $user = Auth::user();
        $permission =  $this->mypermissionsforAModule();
        $branchDetails =  Restaurant::find($branch);
        if (isset($permission[$pmodule. '___pos-overview-report']) || $permission == 'superadmin') {
            $posSales =  WaPosCashSales::with(['user', 'items'])->where('branch_id', $branch)
            ->where('status', 'Completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->get();
        
            return view('admin.sales_and_receivables_reports.pos_sales', compact('model', 'title', 'pmodule', 'user', 'posSales', 'branchDetails', 'startDate', 'endDate'));
        }else{
            return  redirect()->back()->with('warning','Permission denied');
        }

    }
    public function posReturns(Request $request, $startDate, $endDate, $branch)
    {
        // dd($startDate, $endDate, $branch);
        $title = 'Pos Overview Report';
        $model = $this->model;
        $pmodule = $this->permissions_module;
        $user = Auth::user();
        $permission =  $this->mypermissionsforAModule();
        $branchDetails =  Restaurant::find($branch);
        if (isset($permission[$pmodule. '___pos-overview-report']) || $permission == 'superadmin') {
            $posReturns =  WaPosCashSalesItemReturns::leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
            ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id', 'wa_pos_cash_sales_items.id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_pos_cash_sales_items.wa_inventory_item_id')
            ->leftJoin('users', 'users.id', 'wa_pos_cash_sales.user_id')
            ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', 'wa_inventory_items.id')
            ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', 'wa_inventory_location_uom.uom_id')
            ->where('wa_pos_cash_sales.status', 'Completed')
            ->whereBetween('wa_pos_cash_sales_items_return.updated_at', [$startDate, $endDate])
            ->where('wa_pos_cash_sales.branch_id', $branch)
            ->whereColumn('wa_inventory_location_uom.location_id', 'users.wa_location_and_store_id')
            ->select(
                'wa_pos_cash_sales_items_return.updated_at',
                'wa_pos_cash_sales_items_return.return_grn',
                'wa_pos_cash_sales_items_return.return_quantity',
                'sales_no',
                'customer',
                'users.name as cashier',
                'wa_pos_cash_sales_items.selling_price',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_unit_of_measures.title as bin',

            )
            ->get();
        
            return view('admin.sales_and_receivables_reports.pos_returns', compact('model', 'title', 'pmodule', 'user', 'posReturns', 'branchDetails', 'startDate', 'endDate'));
        }else{
            return  redirect()->back()->with('warning','Permission denied');
        }

    }
}
