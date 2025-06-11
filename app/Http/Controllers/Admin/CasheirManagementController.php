<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\UserPermission;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesItemReturns;
use App\Model\WaPosCashSalesPayments;
use App\Models\CashDropTransaction;
use App\Models\CashierDeclaration;
use App\Models\DropLimitAlert;
use App\Models\WaPaymentMode;
use App\PaymentProvider;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\throwException;

class CasheirManagementController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'cashier-management';
        $this->title = 'Cashier Management';
        $this->pmodule = 'cashier-management';
    }

    public function allcashiers(Request $request)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'All Cashiers';
        $model = 'cashier-management-all';
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();
        $branch = $request->restaurant_id ?? Auth::user()->restaurant_id;

        $can_continue = can('view-all-cashiers', 'cashier-management');
        if (!$can_continue) {
            Session::flash('Invalid Request');
            return redirect()->back();
        }

        $role_ids = DB::table('user_permissions')
            ->where('module_name','pos-cash-sales')
            ->where('module_action','add')
            ->pluck('role_id');
        $branch_cashiers = User::where('restaurant_id', $branch)->whereIn('role_id',$role_ids)->get();


        return view('admin.cashierManagement.all-cashiers', compact('branches','user','title', 'model', 'pmodule', 'permission','branch_cashiers'));
    }
    public function updateDropLimit(Request $request)
    {
        $userIds = explode(',', $request->input('user_ids')); // Get array of user IDs
        $dropLimit = $request->input('drop_limit');

        // Update the drop limit for the selected users
        User::whereIn('id', $userIds)->update(['drop_limit' => $dropLimit]);

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        // $branches = $this->getRestaurantList();
        $branches = Restaurant::latest();
        if($permission != 'superadmin' && !can('view_all', 'cashier-management')){
            $branches = $branches->where('id', Auth::user()->restaurant_id);
        }
        $branches = $branches->pluck('name', 'id');

        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        $branch = $request->restaurant_id ?? Auth::user()->restaurant_id;


        $notAdmin = true;
        if( $permission != 'superadmin'){
            $notAdmin =false;
        }

        $payMethods = PaymentMethod::with('provider')
            ->where('use_in_pos', true)->get();

        $today =$request->date ??  today();
        $start = $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->date ? Carbon::parse($request->date)->endOfDay() : Carbon::now()->endOfDay();

        $cashDropsQuery = DB::table('cash_drop_transactions')
            ->select('cashier_id', DB::raw('SUM(amount) as total_drops'))
            ->whereDate('created_at', $today)
            ->groupBy('cashier_id');
        $orders = WaPosCashSales::where('status', 'Completed')
            ->whereDate('created_at', $today)
            ->get();

        $totalsByCashier = $orders->groupBy('attending_cashier')->map(function ($cashierOrders) {
            return $cashierOrders->sum(function ($order) {
                return $order->accepted_returns_total;
            });
        });

        $returnsQuery = DB::table('wa_pos_cash_sales_items_return')
            ->select(
                'wa_pos_cash_sales.attending_cashier AS cashier_id', // Attribute returns to the original cashier
                DB::raw("SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) AS total_returns")
            )
            ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', '=', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
            ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', '=', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
            ->whereDate('wa_pos_cash_sales_items_return.accepted_at', $today)
            ->where('wa_pos_cash_sales_items_return.accepted', 1)
            ->groupBy('wa_pos_cash_sales.attending_cashier'); // Group by the cashier who made the original sale

        $query = DB::table('users')
            ->select(
                'users.*',
                'users.id as user_id',
                'users.name as user_name',
                'restaurants.name as branch',
                'payment_methods.id as payment_method_id',
                'payment_methods.payment_provider_id as provider_id',
                'payment_methods.title as payment_method',
                'payment_methods.is_cash as method_cash',
                'payment_methods.slug as payment_method_slug',
                'payment_providers.slug as payment_provider_slug',
                DB::raw("SUM(wa_pos_cash_sales_payments.amount) AS total_sales"),
                DB::raw("IFNULL(cash_returns.total_returns, 0) AS total_returns"),
                DB::raw("SUM(wa_pos_cash_sales.change) AS total_change"),
                DB::raw("IFNULL(cash_drops.total_drops, 0) AS total_drops"),
                DB::raw("SUM(CASE WHEN payment_methods.is_cash = true THEN wa_pos_cash_sales_payments.amount ELSE 0 END) AS total_cash"),
                DB::raw("(
                    SELECT SUM(wa_pos_cash_sales_payments.amount)
                    FROM wa_pos_cash_sales_payments
                    LEFT JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = wa_pos_cash_sales_payments.wa_pos_cash_sales_id
                    LEFT JOIN payment_methods ON payment_methods.id = wa_pos_cash_sales_payments.payment_method_id
                    WHERE wa_pos_cash_sales.attending_cashier = users.id
                        AND wa_pos_cash_sales.status = 'Completed'
                        AND payment_methods.is_cash = true
                        AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$start."' AND '".$end."')
                ) AS cash_payments"),
                DB::raw("(SELECT SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount)
                    FROM wa_pos_cash_sales_items
                    LEFT JOIN wa_pos_cash_sales on wa_pos_cash_sales.id = wa_pos_cash_sales_items.wa_pos_cash_sales_id
                    WHERE wa_pos_cash_sales.attending_cashier = users.id
                        AND wa_pos_cash_sales.status = 'Completed'
                        AND (DATE(wa_pos_cash_sales.created_at) BETWEEN '".$start."' AND '".$end."')
                ) AS cashier_sales"),
            )
            ->leftJoin('wa_pos_cash_sales', function ($join) use ($today) {
                $join->on('users.id', '=', 'wa_pos_cash_sales.attending_cashier')
                    ->where('wa_pos_cash_sales.status', 'Completed')
                    ->whereDate('wa_pos_cash_sales.created_at', $today);
            })
            ->leftJoin('wa_pos_cash_sales_payments', 'wa_pos_cash_sales.id', '=', 'wa_pos_cash_sales_payments.wa_pos_cash_sales_id')
            ->leftJoinSub($returnsQuery, 'cash_returns', 'cash_returns.cashier_id', '=', 'users.id')
            ->leftJoinSub($cashDropsQuery, 'cash_drops', 'users.id', '=', 'cash_drops.cashier_id')
            ->leftJoin('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('payment_providers', 'payment_providers.id', '=', 'payment_methods.payment_provider_id')
            ->leftJoin('restaurants', 'users.restaurant_id', '=', 'restaurants.id')
            ->where(function ($q) {
                $q->whereNotNull('wa_pos_cash_sales.id')
                ->orWhereNotNull('cash_returns.total_returns');
            })
            ->when($branch, function ($q, $branch) {
                return $q->where('users.restaurant_id', $branch);
            })
            ->groupBy('users.id', 'payment_methods.id')
            ->get();



        $groupedData = [];
        $grandTotals = [];

        foreach ($query as $row) {
            // Initialize user data if not exists
            if (!isset($groupedData[$row->user_id])) {
                $groupedData[$row->user_id] = [
                    'user' => $row,
                    'user_id' => $row->user_id,
                    'user_name' => $row->user_name,
                    'branch' => $row->branch,
                    'payment_methods' => [],
                    'total_sales' => 0,
                    'total_returns' => ceil($row->total_returns) ?? 0,
                    'total_drops' => $row->total_drops,
                    'total_change' => $row->total_change,
                    'total_cash' => $row->cash_payments,
                    'net_cash' => $row->cashier_sales - ($row->total_returns ?? 0),
                    'cash_at_hand' => $row->total_cash - ($row->total_returns ?? 0) - ($row->total_drops ?? 0),
                ];
            }

            // Add payment method data
            $groupedData[$row->user_id]['payment_methods'][] = [
                'method_id' => $row->payment_method_id,
                'method' => $row->payment_method,
                'amount' => $row->total_sales,
                'is_cash' => $row->method_cash,
                'provider_id' => $row->provider_id,
            ];

            // Update user total sales
            $groupedData[$row->user_id]['total_sales'] += $row->total_sales;

            // Track totals by payment method
            if (!isset($grandTotals[$row->payment_method])) {
                $grandTotals[$row->payment_method] = 0;
            }
            $grandTotals[$row->payment_method] += $row->total_sales;
        }
        if ($request->intent =='PDF')
        {
            $branch = Restaurant::find($branch)->name;
            $pdf = \PDF::loadView('admin.cashierManagement.all-pdf', compact('branch','user','title', 'model', 'breadcum', 'pmodule', 'permission','groupedData','payMethods','grandTotals', 'today'));
            $report_name = 'all_cashier_declarations'.date('Y_m_d_H_i_A');
            return $pdf->download($report_name.'.pdf');

        }
        return view('admin.cashierManagement.index', compact('branches','user','title', 'model', 'breadcum', 'pmodule', 'permission','groupedData','payMethods','grandTotals'));
    }

    public function dropCash(Request $request)
    {

       $request->validate([
           'amount'=>'required|integer',
       ]);

        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();


        if ($request->ajax()) {
            $cashier = User::find($request->cashier_id);
            $cth = $cashier->cashAtHand();
            if ($cth < 1)
            {
                return response()->json([
                    'status'=> false,
                    'message'=> "Your cash at hand is 0",
                ], 320);
            }
            /*get pending Returns*/
            $pending_returns = WaPosCashSalesItemReturns::with('PosCashSale')
                ->whereHas('PosCashSale', function ($q) use ($cashier) {
                    $q->where('attending_cashier',$cashier->id);
                })
                ->whereDate('return_date', today())
                ->whereNull('accepted_at')->count();

            if ($pending_returns > 0) {
                return response()->json([
                    'status'=> false,
                    'message'=> "Cashier has $pending_returns Pending Return. The returns need to be processed be before dropping cash.",
                ], 320);
            }


            $cashDropsQuery = DB::table('cash_drop_transactions')
                ->select( DB::raw('SUM(amount) as total_drops'))
                ->whereBetween('cash_drop_transactions.created_at', [$startDate, $endDate])
                ->where('cashier_id', $cashier->id)
                ->first();

            $cashier_sales_id = WaPosCashSales::where('status', 'Completed')
                ->where('attending_cashier', $cashier->id)
                ->pluck('id')->toArray();
            $cash_sale =  DB::table('wa_pos_cash_sales_payments')
                ->join('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
                ->whereIn('wa_pos_cash_sales_payments.wa_pos_cash_sales_id', $cashier_sales_id)
                ->where('payment_methods.is_cash', true)
                 ->whereBetween('wa_pos_cash_sales_payments.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(wa_pos_cash_sales_payments.amount) as cash_total'))
                ->first();
            $cash_at_hand = ceil($cash_sale->cash_total - $cashDropsQuery->total_drops);

            $lastDrop = CashDropTransaction::latest()->first();
            $last_drop_id = $lastDrop ? $lastDrop->id : 0;
            $drop =  DB::transaction(function () use ($last_drop_id, $cash_at_hand, $request) {

                $amount = ceil($request->amount);
                $balance = 0;
                $ref = $this->generateUniqueCode($last_drop_id + 1);

               return CashDropTransaction::create([
                      'amount' => $amount,
                      'cashier_balance' => $balance,
                      'user_id' => getLoggeduserProfile()->id,
                      'cashier_id' => $request->cashier_id,
                      'reference' => 'DRP-'.$ref,
                     'bank_receipt_number'=>$request->bank_receipt_number ?? null,
                  ]);
            });

            $startDate = $request->from ?? now()->startOfDay();
            $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
            $cash = User::find($request->cashier_id);
            $drop_amount = CashDropTransaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('cashier_id', $cash->id)
                ->sum('amount');

            /*mark last drop alert as used*/
            try {
                $last = DropLimitAlert::where('user_id', $cashier->id)->latest()->first();
                if ($last)
                {
                    $last->used = true;
                    $last->save();
                }
            } catch (\Exception $e) {

            }
            return response()->json([
                'status'=> true,
                'message'=> 'Cash Dropped Successfully',
                'cashier'=> $cash,
                'amount_dropped'=> $drop_amount,
                'drop'=> $drop,
            ], 200);
        }

        return  redirect()->back()->with('Message', 'This Url only accepts Ajax Requests!');
    }

    public function generateUniqueCode($id)
    {
//        do {
//            $number = mt_rand(100000, 999999);
//            $field = 'DRP-'.$number;
//            $exists = CashDropTransaction::where('reference', $field)->exists();
//
//        } while($exists);

        return mt_rand(100000, 999999);
    }

    public function downloadDropReceipt(Request $request, $id)
    {
        $drop = CashDropTransaction::with('user','cashier')->find($id);
        if ($request->ajax())
        {
            return view('admin.cashierManagement.drop-pdf', compact('drop'));
        }

        $pdf = \PDF::loadView('admin.cashierManagement.drop-pdf', compact('drop'));
        return $pdf->download('drop.pdf');
    }

    public function showCashier($id, Request $request)
    {

        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'cashier-management';
        $user = getLoggeduserProfile();
        $paymentMethods = getPaymentmeList();
        $breadcum = [$title => route($pmodule . '.index'), 'Show' => ''];
        $today =$request->date ??  today();

        $cashier = User::find($id);

        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
        $cashDropsQuery = DB::table('cash_drop_transactions')
            ->select( DB::raw('SUM(amount) as total_drops'))
            ->where('cashier_id', $cashier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        $orders = WaPosCashSales::where('attending_cashier', $cashier->id)
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $ids = $orders ->pluck('id');
        $returns_all = WaPosCashSalesItemReturns::whereIn('wa_pos_cash_sales_id', $ids)->with('saleItem','PosCashSale','reasons')->get();

        $cashier_sales_id = $orders->pluck('id')->toArray();

       $cash_sale =  DB::table('wa_pos_cash_sales_payments')
            ->join('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            ->whereIn('wa_pos_cash_sales_payments.wa_pos_cash_sales_id', $cashier_sales_id)
            ->where('payment_methods.is_cash', true)
            ->select(DB::raw('SUM(wa_pos_cash_sales_payments.amount) as cash_total'))
            ->first();

        $returns  = $orders->sum->acceptedReturnsTotal;

        $cash_at_hand = ceil($cashier->cashAtHand());


        $amount_dropped = CashDropTransaction::whereBetween('created_at', [$startDate, $endDate])->where('cashier_id', $id)->sum('amount');
        if ($request->ajax()) {
            $query = CashDropTransaction::query()
                ->with('cashier')
                ->with('user')
                ->where('cashier_id',$id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest();
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2, '.', ',');
                })
                ->editColumn('cashier_balance', function ($row) {
                    return number_format($row->cashier_balance, 2, '.', ',');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s'); // Customize format as needed
                })
                ->addColumn('action', function ($model) {
                    return ' <a href="'. route('cashier-management.downloadDropReceipt', $model->id).'" class="btn btn-sm btn-primary"><i class="fa fa-download"></i></a>';
                })
                ->toJson();
        }
        return view('admin.cashierManagement.show', compact('paymentMethods','amount_dropped','user','title', 'model', 'breadcum', 'pmodule', 'permission', 'cashier','cash_at_hand','orders','returns_all','returns'));

    }


    public function cashierTransaction($hsId, Request $request)
    {
        $id = base64_decode($hsId);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'cashier-management';
        $user = getLoggeduserProfile();
        $paymentMethods = getPaymentmeList();

        $branch = $request->restaurant_id;
        $active_method = $request->payment_method;

        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();

        $cashier = User::find($id);
        if (request()->wantsJson()) {

            $query = WaPosCashSalesPayments::query()
                ->whereNotNull('payment_method_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('parent', function ($q) use ($id) {
                    $q->where('attending_cashier', $id);
                })
                ->when($active_method, function ($q, $active_method) {
                    return $q->where('payment_method_id', $active_method);
                })
                ->with([
                    'parent' => function ($query) use ($id) {
                        $query->where('attending_cashier', $id);
                    },
                    'parent.branch',
                    'parent.user',
                    'tender_entry',
                    'balancing_account',
                    'method'
                ])
                ->latest();

            $sum_tenders = $query->get()->sum('amount');

            return DataTables::eloquent($query)
                ->with('total', function () use ($query) {
                    return number_format($query->sum('amount'), 2,'.',',') ;
                })
                ->addColumn('sale_total', function ($row){
                    return number_format($row->parent->total, 2, '.', ',');
                })
                ->addColumn('reference', function ($row){
                    return $row->tender_entry->reference ?? '-';
                })
                ->editColumn('amount', function($row) {
                    return number_format($row->amount, 2,'.',',');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s');
                })
                ->with([
                    'sum_tender' => number_format($sum_tenders, 2, '.', ','),
                ])
                ->addIndexColumn()
                ->toJson();
        };
        return view('admin.cashierManagement.show', compact('paymentMethods','title', 'model', 'pmodule', 'permission', 'cashier'));

    }

    public function allTransactions(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'cashier-management-show';
        $user = getLoggeduserProfile();
        $breadcum = [$title => route($pmodule . '.index'), 'all' => ''];
        $branches = $this->getRestaurantList();

        $branch = $request->restaurant_id;
        $startDate = $request->from ?? now()->startOfDay();

        if ($request->ajax()) {
            $query = CashDropTransaction::query()
                ->with('cashier')
                ->with('user')
                ->with('cashier.branch')
                ->whereDate('created_at', $startDate)
                ->when($branch, function ($q) use ($branch) {  // Use 'use' for clarity
                    $q->whereHas('cashier', function ($q) use ($branch) {
                        $q->where('restaurant_id', $branch);
                    });
                })
                ->latest();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2, '.', ',');
                })
                ->editColumn('banked', function ($row) {
                    return number_format($row->banked_amount, 2, '.', ',');
                })
                ->editColumn('cashier_balance', function ($row) {
                    return number_format($row->cashier_balance, 2, '.', ',');
                })
                ->editColumn('unbanked', function ($row) {
                    return number_format(($row->amount - $row->banked_amount ), 2, '.', ',');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s');
                })
                ->toJson();
        }
        return view('admin.cashierManagement.AllTransactions', compact('branches','user','title', 'model', 'breadcum', 'pmodule', 'permission'));

    }

    public function declare(Request $request, $id)
    {
       $cashier =  User::find($id);
        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
        $cashDropsQuery = DB::table('cash_drop_transactions')
            ->select( DB::raw('SUM(amount) as total_drops'))
            ->where('cashier_id', $cashier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();
        $orders = WaPosCashSales::where('attending_cashier', $cashier->id)
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $cashier_sales_id = $orders->pluck('id')->toArray();
        $cash_sale =  DB::table('wa_pos_cash_sales_payments')
            ->join('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            ->whereIn('wa_pos_cash_sales_payments.wa_pos_cash_sales_id', $cashier_sales_id)
            ->where('payment_methods.is_cash', true)
            ->select(DB::raw('SUM(wa_pos_cash_sales_payments.amount) as cash_total'))
            ->first();
        $returns  =DB::table("wa_pos_cash_sales_items_return")
            ->select(
                DB::raw("SUM(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) as amount")
            )
            ->join('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
            ->join('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
            ->where('wa_pos_cash_sales.attending_cashier', $cashier->id)
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->where('accepted', 1)
            ->where('wa_pos_cash_sales_items_return.branch_id', $cashier->restaurant_id)
            ->value('amount');

        $cash_at_hand = $cash_sale->cash_total - ($returns+$cashDropsQuery->total_drops);

       if ($cash_at_hand > 0)
       {
           return response()->json([
              'result'=>0,
              'message'=>"Cashier has not dropped all cash. Cash at hand is $cash_at_hand"
           ]);
       }

        $lastdeclared_time = @CashierDeclaration::where('cashier_id', $cashier->id)->latest()->first()->declared_at;

        if($lastdeclared_time != null ){
            $sales = WaPosCashSales::where('status', 'Completed')
                ->where(function($query) use ($cashier) {
                    $query->where('user_id', $cashier->id)
                        ->orWhere('attending_cashier', $cashier->id);
                })
                ->where('created_at', '>', $lastdeclared_time)
                ->count();
            if ($sales == 0)
            {
                return response()->json([
                    'result'=>0,
                    'message'=>'Cashier Has Already Been Declared .'
                ]);
            }
        }
            /*records cashier */
            CashierDeclaration::create([
                'cashier_id'=> $cashier->id,
                'declared_by'=> Auth::id(),
                'declared_at'=> now(),
            ]);
            return response()->json([
                'result'=>1,
                'message'=>'Cashier Declared Successfully.'
            ]);


    }

    public function cashierSales(Request $request)
    {

        $cashier = User::find($request->cashier);
        $today = $request->date ?? carbon::now()->format('Y-m-d');

        $query = DB::table('wa_pos_cash_sales')
            ->select(
                'wa_pos_cash_sales.*',
                DB::raw('SUM(wa_pos_cash_sales_items.total - wa_pos_cash_sales_items.discount_amount) as total_sales')
            )
            ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales.id', '=', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
            ->where('wa_pos_cash_sales.attending_cashier', $cashier->id)
            ->where('wa_pos_cash_sales.status', 'Completed')
            ->whereDate('wa_pos_cash_sales.created_at', $today)
            ->groupBy( 'wa_pos_cash_sales.id');

        $sum_total = $query->get()->sum('total_sales');


        return DataTables::query($query)
            ->addIndexColumn()
            ->with([
                'sum_total' => number_format($sum_total, 2, '.', ','),
            ])
            ->toJson();
    }
    public function cashierReturns(Request $request)
    {

        $cashier = User::find($request->cashier);

        $date = $request->date ?? carbon::now()->format('Y-m-d');
        $query = WaPosCashSalesItemReturns::whereDate('accepted_at', $date)
            ->with('saleItem', 'saleItem.Item','PosCashSale', 'reasons')
            ->whereHas('PosCashSale', function ($q) use ($cashier) {
                $q->where('attending_cashier',$cashier->id);
            });



        $orders = WaPosCashSalesItemReturns::whereDate('accepted_at', $date)
            ->with('PosCashSale')
            ->whereHas('PosCashSale', function ($q) use ($cashier) {
                $q->where('attending_cashier', $cashier->id);
            })
            ->get()
            ->pluck('PosCashSale')
            ->unique();

        $returnsTotal = $orders->sum->acceptedReturnsTotal;

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s');
            })
            ->addColumn('total', function ($row) {
                $cost_of_one = $row->saleItem->total / $row->saleItem->qty;
                return number_format(ceil($cost_of_one * $row->return_quantity), 2);
            })
            ->addColumn('state', function ($row) {
                return $row->accepted ? 'Processed': 'Pending';
            })
            ->with([
                'returnsTotal' => number_format(ceil($returnsTotal),2)
            ])
            ->make(true);
    }

}
