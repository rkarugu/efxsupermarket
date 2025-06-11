<?php

namespace App\Http\Controllers\Admin;

use App\CustomerEquityPayment;
use App\CustomerKcbPayment;
use App\Http\Controllers\Controller;
use App\Model\NWaInventoryLocationTransfer;
use App\Model\RegisterCheque;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaLocationAndStore;
use App\Model\WaPosCashSales;
use App\Model\WaStockMove;
use App\Models\CashDropTransaction;
use App\Models\ChiefCashierDeclaration;
use App\Models\PosStockBreakRequest;
use App\Models\WaCloseBranchEndOfDay;
use App\PaymentProvider;
use App\WaTenderEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EndOfDayUtilityController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    protected $formattedDate;
    protected $previousDayDate;
    protected $startDate;
    protected $endDate;

    public function __construct()
    {
        $this->model = 'end_of_day_utility';
        $this->title = 'EOD Utility';
        $this->pmodule = 'end-of-day-utility';
        $this->formattedDate = today()->format('Y-m-d 00:00:00');
        $this->previousDayDate = Carbon::now()->subDay()->format('Y-m-d 00:00:00');
        $this->startDate = today()->format('Y-m-d 0000:00');
        $this->endDate = today()->format('Y-m-d 23:59:59');
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Utility';
        $model = $this->model;

        $branches = Restaurant::select('id', 'name as location_name')->get();
        $store = Auth::user()->wa_location_and_store_id;
        if (Auth::user()->is_hq_user)
        {
            $store = null;
        }
        $branchescloseddata = WaCloseBranchEndOfDay::with('openedby', 'closedby', 'walocationandstore','chiefcashierdeclaration')
            ->when($store, function ($q) use ($store) {
               return $q->where('wa_location_and_store_id', $store);
            })
            ->latest()->get();

        if (isset($permission[$pmodule . '___detailed']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.salesreceiablesreports.end_of_day_utility', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'branchescloseddata'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function loadEndOfDayPage(Request $request)
    {

        $previousdate = $this->previousDayDate;

        $date = $request ->date ? : today();
//        dd($date);
        if (Auth::user()->is_hq_user)
        {
            $branch = $request->select_branch ?? Auth::user()->restaurant_id;
        }else{
            $branch = Auth::user()->restaurant_id;
        }


        /*check f all cashiers have dropped cash*/
        $data = $this->cashvsdrops($branch, $date);
        $cash = $data->total_cash;
        $drops  = $data->total_drops;

        if ($cash != $drops) {
            return  redirect()->back()->with('warning','Some cashiers have not been declared. They still have some cash not dropped. Declare them before proceeding.');
        }

        $data  = $this->getData($branch, $date);




        $model = $this->model;
        $title = 'End of Day Process';
        $breadcum = ['End of Day' => '', 'End of Day Process' => ''];

//        return view('admin.salesreceiablesreports.end_of_day_process', compact('title', 'model', 'breadcum', 'previousdate','branch'));
        return view('admin.salesreceiablesreports.end_of_day', compact('title', 'model', 'breadcum', 'previousdate','branch','data','date'));

    }


    public function processBranchData()
    {

        $formattedDate = today()->format('Y-m-d 00:00:00');
        try {
            $branch = WaLocationAndStore::find(intval(request()->branch_id));
            if ($branch === null) {
                throw new \Exception('Branch not available for now');
            }
            $branchdata = WaCloseBranchEndOfDay::where('opened_date', $formattedDate)
                ->where('wa_location_and_store_id', request()->wa_branch_id)
                ->with('openedby', 'closedby', 'walocationandstore')
                ->first();
            $formattedFormatDate = Carbon::parse($formattedDate)->format('d-m-Y');
            if ($branchdata === null) {
                throw new \Exception('Data not available for branch ' . $branch->name . ' for date ' . $formattedFormatDate . '. Create a new end of day.');
            }
            return ['status' => 200, 'branchdata' => $branchdata];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function processBranchAccountsDetails()
    {

        $this->processBranchData();
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $branch = WaLocationAndStore::find(intval(request()->branch_id));

        $data = WaCustomer::select([
            'wa_customers.customer_name as name',
            'wa_customers.customer_code as customer_code',
            DB::RAW('(SELECT SUM(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                (LOWER(wa_tender_entries.channel) LIKE \'%Eazzy%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Equity%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Eazy%\')
                AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Eazzy'),

            DB::RAW('(SELECT SUM(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                (LOWER(wa_tender_entries.channel) LIKE \'%Vooma%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Kcb%\')
                AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Vooma'),

            DB::RAW('(SELECT SUM(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                LOWER(wa_tender_entries.channel) LIKE \'%Ussd%\'
                AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Ussd'),

            DB::RAW('(SELECT COUNT(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                (LOWER(wa_tender_entries.channel) LIKE \'%Eazzy%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Equity%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Eazy%\')
                AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Eazzy_count'),

            DB::RAW('(SELECT COUNT(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                (LOWER(wa_tender_entries.channel) LIKE \'%Vooma%\' OR LOWER(wa_tender_entries.channel) LIKE \'%Kcb%\')
                AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Vooma_count'),

            DB::RAW('(SELECT COUNT(wa_tender_entries.amount)
                FROM wa_tender_entries 
                WHERE wa_tender_entries.customer_id = wa_customers.id AND 
                LOWER(wa_tender_entries.channel) LIKE \'%Ussd%\'
              AND (DATE(wa_tender_entries.trans_date)  = "' . $startDate . '")) as Kcb_count'),

        ])->get();

        $tenderentries = WaTenderEntry::select('amount')->whereBetween('trans_date', [$startDate, $endDate])->get();
        $tenderentriestotals = $tenderentries->sum('amount');

        $totalsumeazzy = $data->sum('Eazzy');
        $totalsumvooma = $data->sum('Vooma');
        $totalsumussd = $data->sum('Ussd');

        $totaleodsums = $totalsumeazzy + $totalsumvooma + $totalsumussd;

        return ['totaleodsums' => $totaleodsums, 'tenderentriestotals' => $tenderentriestotals, 'status' => 200];
    }

    public function processPendingInterBranchTransferDetails(Request $request)
    {
        $formattedDate = $this->formattedDate;
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        try {

            $branch  = WaLocationAndStore::find($request->get('branch_id'));
            if ($branch === null) {
                throw new \Exception('Branch not available for now');
            }

            $inwardspendinginterbranchtransfers = NWaInventoryLocationTransfer::where('from_store_location_id', $request->branch_id)
                ->where('transfer_date', $formattedDate)
                ->where('status', 'PENDING')
                ->with('getRelatedItem.getInventoryItemDetail')->get();
            if ($inwardspendinginterbranchtransfers->count() === 0) {
                $inwardspendinginterbranchtransfers = NWaInventoryLocationTransfer::where('from_store_location_id', $request->branch_id)
                    ->where('status', 'PENDING')
                    ->with('getRelatedItem.getInventoryItemDetail')->orderBy('transfer_date', 'desc')->get();
            }

            $outwardspendinginterbranchtransfers = NWaInventoryLocationTransfer::where('from_store_location_id', '!=', $request->branch_id)
                ->where('transfer_date', $formattedDate)
                ->where('status', 'PENDING')
                ->with('getRelatedItem.getInventoryItemDetail')->get();

            if ($outwardspendinginterbranchtransfers->count() === 0) {
                $outwardspendinginterbranchtransfers = NWaInventoryLocationTransfer::where('from_store_location_id', '!=', $request->branch_id)
                    ->where('status', 'PENDING')
                    ->with('getRelatedItem.getInventoryItemDetail')->orderBy('transfer_date', 'desc')->get();
            }

            return [
                'inwardspendinginterbranchtransfers' => $inwardspendinginterbranchtransfers,
                'outwardspendinginterbranchtransfers' => $outwardspendinginterbranchtransfers,
                'status' => 200
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processSalesVsStockMovement(Request $request)
    {
        $total = 0;
        $datetousestart = Carbon::createFromFormat('d-m-Y', '14-04-2024')->format('Y-m-d 00:00:00');
        $datetouseend = Carbon::createFromFormat('d-m-Y', '14-04-2024')->format('Y-m-d 23:59:59');
        $tenderentries = WaTenderEntry::select('amount')->whereBetween('trans_date', [$datetousestart, $datetouseend])->get();
        $tenderentriestotals = $tenderentries->sum('amount');

        // $wastockmoves = WaStockMove::query()
        //     ->select([
        //         'wa_inventory_item_id',
        //         DB::raw('SUM(price) as total_sales')
        //     ])
        //     ->where('wa_location_and_store_id', 46)
        //     ->where(function ($query) {
        //         $query->where('document_no', 'like', 'INV-%');
        //     })
        //     ->whereBetween('created_at', [$datetousestart, $datetouseend])
        //     ->get();
        //     $totalwastockmovessales = $wastockmoves->sum('total_sales');
        //     return $totalwastockmovessales;

        $wastockmoves = WaStockMove::select('price')
            ->where('wa_location_and_store_id', 46)
            ->where('document_no', 'LIKE', 'INV-%')
            ->whereBetween('created_at', [$datetousestart, $datetouseend])->get();

        $wastockmovesreturns = WaStockMove::select('price')
            ->where('wa_location_and_store_id', 46)
            ->where('document_no', 'LIKE', 'RTN-%')
            ->whereBetween('created_at', [$datetousestart, $datetouseend])->get();

        $total = $wastockmoves->sum('price');
        $totalreturns = $wastockmovesreturns->sum('price');
        return $totalreturns;
    }

    public function processIncompleteBranchTransactionsDetails(Request $request)
    {
        try {
            $branch = WaLocationAndStore::find(intval($request->branch_id));
            if ($branch === null) {
                throw new \Exception('Branch not available for now');
            }
            $wadebtortran = WaDebtorTran::where('salesman_id', $branch->wa_branch_id)->first();

            $watenderentry = WaTenderEntry::with('customerequitypayment', 'customerkcbpayment')->latest()->take(10)->get();
            return $watenderentry;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function closeBranch(Request $request)
    {
//        return response()->json([
//            'status'=> false,
//            'message'=> 'To Be Enabled',
//        ], 500);

        try {

            $data = $request->data;
            $date = Carbon::parse($request->date);
            $store =  WaLocationAndStore::where('wa_branch_id',request()->branch_id)->first();
//            dd($date);

            $closeddatebranch = WaCloseBranchEndOfDay::where('wa_location_and_store_id', $store->id)
                ->whereDate('opened_date', $date)
                ->where('status', 'Closed')
                ->first();

            if ($closeddatebranch !== null) {
                return response()->json([
                    'status'=> false,
                    'message'=> 'Branch already closed for ' . $date->format('Y-m-d') ,
                ], 500);
            }

            if ($data['splits'] !=0 )
            {
                return response()->json([
                    'status'=> false,
                    'message'=> 'There are pending Splits',
                ], 500);
            }
            if ($data['returns_count'] !=0 )
            {
                return response()->json([
                    'status'=> false,
                    'message'=> 'There are pending Returns',
                ], 500);
            }
            if (!$data['stockVsPay'])
            {
                return response()->json([
                    'status'=> false,
                    'message'=> 'Stock vs Sales vs Payments do not balance',
                ], 500);
            }


            /* ensure all cashiers have dropped their  cash */

            $branch = request()->branch_id;
            $data = $this->cashvsdrops($branch, $date);

            $drops  = $data->total_drops;
            $banked  = $data->drops_banked;
            $unbanked = $data->unbanked_cash;

            /*create record for chief cashier for unbanked drops*/
            $reference  = strtoupper(base_convert(md5($branch.$date->format('Ymd')), 16, 36));

            $waclosebranchendofday = WaCloseBranchEndOfDay::create([
                'wa_location_and_store_id' => $store->id,
                'opened_by' => getLoggeduserProfile()->id,
                'closed_by' => getLoggeduserProfile()->id,
                'opened_date' => $date,
                'closed_date' => Carbon::now(),
                'closed_time' => Carbon::now(),
                'opened_time' => Carbon::now(),
                'status' => 'Closed'
            ]);
            if ($unbanked != 0)
            {
                $cash_drop =   ChiefCashierDeclaration::create([
                    'reference' => 'CBR-'. substr($reference, 0, 6),
                    'user_id' => $data->user_id,
                    'branch_id' => $branch,
                    'total_drop' => $drops,
                    'banked_drops' =>$banked,
                    'un_banked_drop' => $unbanked,
                    'cleared_date' =>today(),
                    'wa_close_branch_end_of_day_id'=>$waclosebranchendofday->id
                ]);
                return [
                    'waclosebranchendofday' => $waclosebranchendofday,
                    'cash_receipt_url' => route('cashier-management.downloadCashReceipt', $cash_drop->id),
                    'status' => 200
                ];
            }

            return [
                'waclosebranchendofday' => $waclosebranchendofday,
                'status' => 200
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadCashReceipt(Request $request, $id)
    {
        $drop = ChiefCashierDeclaration::with('user')->find($id);
        if ($request->ajax())
        {
            return view('admin.cashierManagement.chief-cashier-cash-pdf', compact('drop'));
        }

        $pdf = \PDF::loadView('admin.cashierManagement.chief-cashier-cash-pdf', compact('drop'));
        return $pdf->download('drop.pdf');
    }
    public function cashvsdrops($branch, $date)
    {
        $payMethods = PaymentProvider::whereHas('paymentMethods', function($query) {
            $query->where('use_in_pos', true);
        })->get();

        $today = $date;
        $cashDropsQuery = DB::table('cash_drop_transactions')
            ->select(
                'cashier_id',
                'user_id',
                DB::raw('SUM(amount) as total_drops'),
                DB::raw('SUM(banked_amount) as drops_banked'),

            )
            ->whereDate('created_at', $today);

        $orders = WaPosCashSales::where('status', 'Completed')
            ->whereDate('created_at', $today)
            ->get();

        $query = DB::table('users')
            ->select(
                'cash_drops.user_id',
                DB::raw("sum(wa_pos_cash_sales_payments.amount) as total_sales"),
                DB::raw("sum(wa_pos_cash_sales.change) as total_change"),
                DB::raw("IFNULL(cash_drops.total_drops, 0) as total_drops"),
                DB::raw("IFNULL(cash_drops.drops_banked, 0) as drops_banked"),
                DB::raw("IFNULL(cash_drops.total_drops - cash_drops.drops_banked , 0) as unbanked_cash"),
                DB::raw("SUM(CASE WHEN payment_methods.is_cash = true THEN wa_pos_cash_sales_payments.amount ELSE 0 END) as total_cash")
            )
            ->join('wa_pos_cash_sales', 'users.id', '=', 'wa_pos_cash_sales.attending_cashier')
            ->leftJoin('wa_pos_cash_sales_payments', 'wa_pos_cash_sales.id', '=', 'wa_pos_cash_sales_payments.wa_pos_cash_sales_id')
            ->leftJoinSub($cashDropsQuery, 'cash_drops', 'users.id', '=', 'cash_drops.cashier_id')
            ->leftJoin('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('payment_providers', 'payment_methods.payment_provider_id', '=', 'payment_providers.id')
            ->leftJoin('restaurants', 'users.restaurant_id', '=', 'restaurants.id')
            ->whereDate('wa_pos_cash_sales.created_at', $today)
            ->where('users.restaurant_id', $branch)
            ->first();
        return $query;

    }

    public function getData($branch, $date)
    {

        $orders = WaPosCashSales::where('branch_id', $branch)
            ->with('items')
            ->where('status', 'Completed')
            ->whereDate('paid_at', $date)
            ->get();
        $idsString = $orders->pluck('id')->toArray();
        /*get returns */

        /*Get Moves*/
        $stockMovesReturns =  DB::table('wa_stock_moves')
            ->select(DB::raw("SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price) as total_stock_value"))
            ->whereDate('wa_stock_moves.created_at', $date)
            ->where('wa_stock_moves.restaurant_id', $branch)
            ->whereIn('wa_stock_moves.wa_pos_cash_sales_id', $idsString)
            ->where('document_no', 'like', 'RTN%')
            ->first();
        $stockMovesSales =  DB::table('wa_stock_moves')
            ->select(DB::raw("SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price) as total_stock_value"))
            ->whereDate('wa_stock_moves.created_at', $date)
            ->where('wa_stock_moves.restaurant_id', $branch)
            ->where('document_no', 'like', 'CIV%')
            ->first();

        $stockMoves = (($stockMovesSales->total_stock_value * -1) - $stockMovesReturns->total_stock_value );

        /*user location transfers too*/
        $total_sales = $orders->sum->gross_total;
        $accepted_returns = $orders->sum->acceptedReturnsTotal;
        $pending_returns = $orders->sum->pendingReturnsTotal;

        /*get Payments*/
        $payments = DB::table('wa_pos_cash_sales_payments')
            ->whereDate('created_at', today())
            ->where('branch_id', $branch)->sum('amount');

        /*check splits*/
        $store  = WaLocationAndStore::where('wa_branch_id', $branch)->first();
        $pending_splits =   PosStockBreakRequest::where('status','pending')->where('wa_location_and_store_id',$store->id)->get();


        /*return*/
        $pendingReturnsCount = DB::table('wa_pos_cash_sales_items_return')
            ->selectRaw('COUNT(wa_pos_cash_sales_items_return.return_quantity * wa_pos_cash_sales_items.selling_price) AS pending_returns_count')
            ->leftJoin('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', '=', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
            ->where('wa_pos_cash_sales_items_return.accepted', '0')
            ->whereIn('wa_pos_cash_sales_items_return.wa_pos_cash_sales_id',$idsString)
            ->whereDate('wa_pos_cash_sales_items_return.updated_at', $date)
            ->first();

        /*check unbaked cheque*/
        $cheques = RegisterCheque::whereDate('cheque_date', today())->where('status', 'Registered')->get();

        return [
           'payments'=>$payments,
           'stock'=> $stockMoves,
           'sales'=> $total_sales,
           'splits' => $pending_splits->count(),
           'returns_amount'=>$pending_returns,
           'returns_count'=>$pending_returns,
           'stockVsPay'=>($payments == $stockMoves) && ($stockMoves == $total_sales),
           'unbaked_cheque'=> $cheques->count()
        ];
    }
}
