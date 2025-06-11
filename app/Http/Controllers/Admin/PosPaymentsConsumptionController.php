<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaPosCashSalesPayments;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosPaymentsConsumptionController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'pos-payments-consumption';
        $this->title = 'POS Payments';
        $this->pmodule = 'pos-payments-consumption';
        $this->basePath = 'admin.pos_payments';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $cashiers = User::orderBy('id', 'DESC')->get();
        $branches = Restaurant::all();
        $paymentMethods = PaymentMethod::all();
        $user = Auth::user();
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
       
        // if (isset($permission['cashier-management___pos-payments-consumption']) || $permission == 'superadmin') {
            $tenderPayments = DB::table('wa_tender_entries')
            ->select(
                'wa_tender_entries.created_at as created_at',
                'wa_tender_entries.channel',
                'wa_tender_entries.document_no as receipt_no',
                'wa_tender_entries.reference as reference',
                'wa_tender_entries.paid_by as paid_by',
                'wa_pos_cash_sales.customer as customer_name',
                'wa_pos_cash_sales.sales_no as sales_no',
                'users.name as cashier',
                'users.restaurant_id',
                'wa_pos_cash_sales.paid_at as allocated_at',
                'wa_tender_entries.amount as payment_amount',
                DB::raw("( SELECT SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount)
                FROM wa_pos_cash_sales_items
                WHERE wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                ) AS sale_amount")
            )
            ->leftJoin('wa_pos_cash_sales_payments', 'wa_tender_entries.id', '=', 'wa_pos_cash_sales_payments.wa_tender_entry_id')
            ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_payments.wa_pos_cash_sales_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'wa_tender_entries.wa_payment_method_id')
            // ->leftJoin('users', 'users.id', '=', 'wa_pos_cash_sales_payments.cashier_id')
            ->leftJoin('users', 'users.id', 'wa_pos_cash_sales.attending_cashier')
            ->where('payment_methods.use_in_pos', 1);
            if($request->trans_type && $request->trans_type == 'utilised'){
                $tenderPayments = $tenderPayments->whereNotNull('wa_pos_cash_sales.id');

            }
            if($request->trans_type && $request->trans_type == 'unutilised'){
                $tenderPayments = $tenderPayments->whereNull('wa_pos_cash_sales.id');

            }
            $tenderPayments = $tenderPayments->whereBetween('wa_tender_entries.created_at', [$start,$end]);
            if ($permission != 'superadmin' || $user->role_id != 162) {
                $tenderPayments = $tenderPayments->where('users.restaurant_id', auth()->user()->restaurant_id);
            }
            if($request->payment_method){
                $tenderPayments = $tenderPayments->where('payment_methods.id', $request->payment_method);
            }
            if($request->cashier){
                $tenderPayments = $tenderPayments->where('wa_pos_cash_sales.attending_cashier', $request->cashier);
            }

            //fetch cash payments
            $cashPayments = DB::table('wa_pos_cash_sales_payments')
                ->select(
                    'wa_pos_cash_sales_payments.created_at as created_at',
                    'payment_methods.title as channel',
                    'wa_pos_cash_sales.sales_no as receipt_no',
                    'wa_pos_cash_sales.sales_no as reference',
                    'wa_pos_cash_sales.customer as paid_by',
                 
                    'wa_pos_cash_sales.customer as customer_name',
                    'wa_pos_cash_sales.sales_no as sales_no',
                    'users.name as cashier',
                    'users.restaurant_id',
                    'wa_pos_cash_sales.paid_at as allocated_at',
                    'wa_pos_cash_sales_payments.amount as payment_amount',
                    DB::raw("( SELECT SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount)
                    FROM wa_pos_cash_sales_items
                    WHERE wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_pos_cash_sales.id
                    ) AS sale_amount")

                )
                ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_payments.wa_pos_cash_sales_id')
                ->leftJoin('users', 'users.id', 'wa_pos_cash_sales.attending_cashier')
                ->leftJoin('payment_methods', 'payment_methods.id', 'wa_pos_cash_sales_payments.payment_method_id')
                ->where('payment_methods.use_in_pos', 1)
                ->where('payment_methods.is_cash', 1);
                if($request->trans_type && $request->trans_type == 'utilised'){
                    $cashPayments = $cashPayments->whereNotNull('wa_pos_cash_sales.id');
    
                }
                if($request->trans_type && $request->trans_type == 'unutilised'){
                    $cashPayments = $cashPayments->whereNull('wa_pos_cash_sales.id');
    
                }
                $cashPayments = $cashPayments->whereBetween('wa_pos_cash_sales_payments.created_at', [$start,$end]);
                if ($permission != 'superadmin' && $user->role_id != 162) {
                    $cashPayments = $cashPayments->where('users.restaurant_id', auth()->user()->restaurant_id);
                }
                if($request->payment_method){
                    $cashPayments = $cashPayments->where('payment_methods.id', $request->payment_method);
                }
                if($request->cashier){
                    $cashPayments = $cashPayments->where('wa_pos_cash_sales.attending_cashier', $request->cashier);
                }
               
                $payments = $tenderPayments->union($cashPayments)->get();

                $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            if($request->intent && $request->intent == 'Download'){
                return ExcelDownloadService::download('pos_cash_sale_payments'.$start.'_'.$end,$payments, ['DATE', 'CHANNEL', 'RECEIPT NO', 'REFERENCE', 'PAID BY', 'CUSTOMER', 'SALE NO', 'CASHIER','ALLOCATED AT', 'PAYMENT_AMOUNT', 'SALE AMOUNT']);

            }
            return view('admin.pos_payments.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'payments', 'branches', 'paymentMethods', 'cashiers'));
        // } else {
        //     Session::flash('warning', 'Permission denied');
        //     return redirect()->back();
        // }
    }
}
