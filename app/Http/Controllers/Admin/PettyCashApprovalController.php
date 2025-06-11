<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Vehicle;
use Carbon\Carbon;
use App\Model\User;
use App\Model\Route;
use App\SalesmanShift;
use App\Model\WaGlTran;
use App\DeliverySchedule;
use App\Model\WaBanktran;
use App\Model\WaBankAccount;
use Illuminate\Http\Request;
use App\Models\WaPettyCashLog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\Models\PettyCashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use App\Models\TravelExpenseTransaction;
use Exception;

class PettyCashApprovalController extends Controller
{
    public function initialApprovals(Request $request): View|RedirectResponse
    {
        if (!can('initial_approval', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Initial Approval';
        $model = 'initial-petty-cash-approvals';
        $breadcum = [
            'Petty Cash' => '',
            'Initial Approval' => ''
        ];

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();

        $dateAsString = Carbon::yesterday()->toDateString();
        if ($request->date) {
            $dateAsString = Carbon::parse($request->date)->toDateString();
        }

        $salesmanAllocations = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.id',
                'travel_expense_transactions.shift_id',
                'petty_cash_transactions.created_at',
                'petty_cash_transactions.amount',
                'routes.route_name as route',
                'routes.travel_expense',
                'routes.offsite_allowance',
                'users.name as salesman',
                'salesman_shifts.status',
                DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
                // DB::raw("(select count(distinct wa_route_customer_id) from wa_internal_requisitions 
                // where wa_internal_requisitions.route_id = routes.id and 
                // wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id) as met_customers"),
                DB::raw("(select count(distinct salesman_shift_customers.route_customer_id) from salesman_shift_customers
                left join salesman_shifts on salesman_shifts.id = salesman_shift_customers.salesman_shift_id
                where salesman_shifts.route_id = routes.id and salesman_shift_customers.visited = 1 and salesman_shifts.id = travel_expense_transactions.shift_id
                ) as met_customers"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                 join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                 where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
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
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
                as tonnage"),
            )
            ->join('users', function ($join) {
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->join('travel_expense_transactions', function ($join) use ($request) {
                $query = $join->on('petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id');
                if ($request->route) {
                    $query = $query->where('route_id', $request->route);
                }
            })
            // ->leftJoin('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.shift_id')
            ->join('salesman_shifts', 'travel_expense_transactions.shift_id', '=', 'salesman_shifts.id')
            ->join('routes', function ($join) {
                $join->on('travel_expense_transactions.route_id', '=', 'routes.id')->where('routes.restaurant_id', 10);
            })
            ->whereDate('petty_cash_transactions.created_at', '=', $dateAsString)
            // ->whereBetween('petty_cash_transactions.created_at', [$dateAsString.' 00:00:00', $dateAsString.' 23:59:59'])
            ->where('petty_cash_transactions.wallet_type', 'TRAVEL') // Travel expenses
            ->where('petty_cash_transactions.initial_approval_status', 'pending') // Travel expenses
            ->where('petty_cash_transactions.amount', '>', 0) // Allocations
            ->where('petty_cash_transactions.status', 0)
            ->orderBy('created_at', 'asc')
            ->get()->map(function ($record, $request) {
                $record->delivery_number = (DeliverySchedule::where('shift_id', $record->shift_id)->first())?->delivery_number;
                // $orderShiftType = WaInternalRequisition::select('shift_type', DB::raw('count(*) as count'))
                //     ->where('wa_shift_id', $record->shift_id)
                //     ->groupBy('shift_type')
                //     ->pluck('count', 'shift_type');
                $orderShiftType = DB::table('salesman_shift_customers')
                    ->select('salesman_shift_type', DB::raw('count(*) as count'))
                    ->where('salesman_shift_id', $record->shift_id)
                    ->where('visited', 1)
                    ->groupBy('salesman_shift_type')
                    ->pluck('count', 'salesman_shift_type');
                $record->onsitecount = $orderShiftType->get('onsite', 0);
                $record->offsitecount = $orderShiftType->get('offsite', 0);
                // $record->incentiveAmount = ceil(($onsitecount * $routeonsiteallowance) + ($offsitecount * $routeoffsiteallowance));
                return $record;
            });

        // dd($salesmanAllocations);

        $deliveryDate = $request->delivery_date ? Carbon::parse($request->delivery_date)->toDateString() : now()->toDateString();
        $driverAllocations = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.id',
                'delivery_schedules.id as delivery_id',
                'delivery_schedules.shift_id',
                'petty_cash_transactions.created_at',
                'petty_cash_transactions.amount',
                'routes.route_name as route',
                'users.name as salesman',
                'users.id as user_id',
                DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
                DB::raw("(select count(distinct wa_route_customer_id) from wa_internal_requisitions where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id) as met_customers"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                 join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                 where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                 as gross_sales"),
                DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                 from wa_inventory_location_transfer_item_returns
                 join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                 join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                 join wa_internal_requisitions on wa_inventory_location_transfers.transfer_no = wa_internal_requisitions.requisition_no
                 where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                 as returns"),
                DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                as tonnage"),
            )
            ->join('users', function ($join) {
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 6);
            })
            ->join('travel_expense_transactions', function ($join) use ($request) {
                $query = $join->on('petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id');
                if ($request->route) {
                    $query = $query->where('route_id', $request->route);
                }
            })
            ->join('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.id')
            ->join('routes', function ($join) {
                $join->on('travel_expense_transactions.route_id', '=', 'routes.id')->where('routes.restaurant_id', 10);
            })
            ->whereDate('petty_cash_transactions.created_at', '=', $deliveryDate)
            ->where('petty_cash_transactions.wallet_type', 'TRAVEL') // Travel expenses
            ->where('petty_cash_transactions.initial_approval_status', 'pending')
            ->where('petty_cash_transactions.amount', '>', 0) // Allocations
            ->where('petty_cash_transactions.status', 0)
            ->get()->map(function ($record) {
                $deliverySchedule = DeliverySchedule::find($record->delivery_id);
                $record->delivery_number = $deliverySchedule->delivery_number;
                $record->shift_id = $deliverySchedule->shift_id;
                $vehicle = Vehicle::with('model')->find($deliverySchedule->vehicle_id);
                $record->travel_expense = $vehicle?->model?->travel_expense ?? 300;
                $pettycashdriverallocation = PettyCashTransaction::where('user_id', $record->user_id)->first();
                $record->driver_allocation = $pettycashdriverallocation->amount;
                return $record;
            });

        $unVisitedRoutes = DB::table('salesman_shifts')
            ->select(
                'salesman_shifts.created_at',
                'routes.route_name',
                'users.name as salesman',
                'users.phone_number',
                DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
            )
            ->join('routes', function ($join) {
                $join->on('salesman_shifts.route_id', '=', 'routes.id')->where('routes.restaurant_id', 10);
            })
            ->join('users', 'salesman_shifts.salesman_id', '=', 'users.id')
            ->where('salesman_shifts.status', 'not_started')
            ->whereDate('salesman_shifts.created_at', '=', $dateAsString)
            ->get();

        $unVisitedDeliveries = DB::table('delivery_schedules')
            ->select(
                'delivery_schedules.expected_delivery_date as created_at',
                'routes.route_name',
                'users.name as driver',
                'users.phone_number',
                DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
            )
            ->join('routes', function ($join) {
                $join->on('delivery_schedules.route_id', '=', 'routes.id')->where('routes.restaurant_id', 10);
            })
            ->leftJoin('users', 'delivery_schedules.driver_id', '=', 'users.id')
            ->whereNotIn('delivery_schedules.status', ['in_progress', 'finished'])
            ->whereDate('delivery_schedules.expected_delivery_date', '=', $dateAsString)
            ->get();

        $source = 'initial';
        return view('admin.petty_cash_approvals.initial_approval', compact('model', 'title', 'breadcum', 'routes', 'branches', 'salesmanAllocations', 'driverAllocations', 'source', 'unVisitedRoutes', 'unVisitedDeliveries'));
    }

    public function showFinalApprovals(Request $request): View|RedirectResponse
    {

        if (!can('final_approval', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Final Approval';
        $model = 'final-petty-cash-approvals';
        $breadcum = [
            'Petty Cash' => '',
            'Final Approval' => ''
        ];

        $pendingOrderTakingApprovals = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.initial_approval_time as date_time',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw("'order_taking' as type"),
                'petty_cash_transactions.initial_approval_time',
                'users.name as approver',
            )
            ->join('users', 'petty_cash_transactions.initial_approved_by', '=', 'users.id')
            ->join('users as recipients', function ($join) {
                $join->on('petty_cash_transactions.user_id', '=', 'recipients.id')->where('recipients.role_id', 4);
            })
            ->where('petty_cash_transactions.wallet_type', 'TRAVEL') // Travel expenses
            ->where('petty_cash_transactions.initial_approval_status', 'approved')
            ->where('petty_cash_transactions.final_approval_status', 'pending')
            ->whereNot('petty_cash_transactions.status', '1')
            ->where('petty_cash_transactions.amount', '>', 0)
            ->when($request->date, fn ($query) => $query->whereDate('petty_cash_transactions.initial_approval_time', $request->date))
            ->groupBy('date_time')
            ->get();

        $pendingDeliveryApprovals = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.initial_approval_time as date_time',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw("'delivery' as type"),
                'petty_cash_transactions.initial_approval_time',
                'users.name as approver',
            )
            ->join('users', 'petty_cash_transactions.initial_approved_by', '=', 'users.id')
            ->join('users as recipients', function ($join) {
                $join->on('petty_cash_transactions.user_id', '=', 'recipients.id')->where('recipients.role_id', 6);
            })
            ->where('petty_cash_transactions.wallet_type', 'TRAVEL') // Travel expenses
            ->where('petty_cash_transactions.initial_approval_status', 'approved')
            ->where('petty_cash_transactions.final_approval_status', 'pending')
            ->where('petty_cash_transactions.amount', '>', 0)
            ->when($request->date, fn ($query) => $query->whereDate('petty_cash_transactions.initial_approval_time', $request->date))
            ->groupBy('date_time')
            ->get();

        $pendingApprovals = $pendingOrderTakingApprovals->merge($pendingDeliveryApprovals);
        $pendingApprovals = $pendingApprovals->sortBy('date_time');

        $branches = DB::table('restaurants')->select('id', 'name')->get();

        return view('admin.petty_cash_approvals.final_approval', compact('model', 'title', 'breadcum', 'pendingApprovals', 'branches'));
    }

    public function showFinalApprovalLines(Request $request): View|RedirectResponse
    {
        if (!can('final_approval', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Final Approval';
        $model = 'final-petty-cash-approvals';
        $breadcum = [
            'Petty Cash' => '',
            'Final Approval' => ''
        ];

        $salesmanAllocations = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.id',
                'petty_cash_transactions.initial_approved_by',
                'travel_expense_transactions.shift_id',
                'petty_cash_transactions.created_at',
                'petty_cash_transactions.old_amount',
                'petty_cash_transactions.amount',
                'routes.route_name as route',
                'routes.travel_expense',
                'users.name as salesman',
                'users.id as user_id',
                DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
                DB::raw("(select count(distinct wa_route_customer_id) from wa_internal_requisitions where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id) as met_customers"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                 join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                 where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
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
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = travel_expense_transactions.shift_id)
                as tonnage"),
            )
            ->join('users', function ($join) use ($request) {
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', $request->type == 'order_taking' ? 4 : 6);
            })
            ->join('travel_expense_transactions', function ($join) use ($request) {
                $query = $join->on('petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id');
                if ($request->route) {
                    $query = $query->where('route_id', $request->route);
                }
            })
            ->join('routes', 'travel_expense_transactions.route_id', '=', 'routes.id')
            ->where('petty_cash_transactions.initial_approval_time', $request->date)
            ->where('petty_cash_transactions.wallet_type', 'TRAVEL') // Travel expenses
            ->where('petty_cash_transactions.initial_approval_status', 'approved')
            ->where('petty_cash_transactions.final_approval_status', 'pending')
            ->whereNot('petty_cash_transactions.status', '1')
            ->where('petty_cash_transactions.amount', '>', 0) // Allocations
            ->get()->map(function ($record) use ($request) {
                if ($request->type == 'order_taking') {
                    $record->delivery_number = (DeliverySchedule::where('shift_id', $record->shift_id)->first())?->delivery_number;
                } else {
                    $schedule = DeliverySchedule::find($record->shift_id);
                    $record->delivery_number = $schedule->delivery_number;
                    $record->shift_id = $schedule->shift_id;
                    $pettycashdriverallocation = PettyCashTransaction::where('user_id', $record->user_id)->first();
                    $record->driver_allocation = $pettycashdriverallocation->amount;
                }

                return $record;
            });

        if ($request->type == 'order_taking') {
            $unVisitedRoutes = DB::table('salesman_shifts')
                ->select(
                    'salesman_shifts.created_at',
                    'routes.route_name',
                    'users.name as user',
                    'users.phone_number',
                    DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
                )
                ->join('routes', 'salesman_shifts.route_id', '=', 'routes.id')
                ->join('users', function ($join) {
                    $join->on('salesman_shifts.salesman_id', '=', 'users.id')->where('users.restaurant_id', '10');
                })
                ->where('salesman_shifts.status', 'not_started')
                ->whereDate('salesman_shifts.created_at', '=', $request->date)
                ->get();
        } else {
            $unVisitedRoutes = DB::table('delivery_schedules')
                ->select(
                    'delivery_schedules.expected_delivery_date as created_at',
                    'routes.route_name',
                    'users.name as user',
                    'users.phone_number',
                    DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as total_customers"),
                )
                ->join('routes', 'delivery_schedules.route_id', '=', 'routes.id')
                ->join('users', function ($join) {
                    $join->on('delivery_schedules.driver_id', '=', 'users.id')->where('users.restaurant_id', '10');
                })
                ->whereNotIn('delivery_schedules.status', ['finished'])
                ->whereDate('delivery_schedules.expected_delivery_date', '=', $request->date)
                ->get();
        }


        $routes = DB::table('routes')->select('id', 'route_name')->get();

        return view('admin.petty_cash_approvals.final_approval_lines', compact('model', 'title', 'breadcum', 'salesmanAllocations', 'routes', 'unVisitedRoutes'));
    }

    public function approveInitial(Request $request): RedirectResponse
    {
        try {
            $date = now();

            $approvalIds = json_decode($request->approval_ids, true);
            $processedApprovals = [];
            $totalAmount = 0;
            foreach ($approvalIds as $approvalId) {
                if ($request->get("approve_$approvalId") == 'on') {
                    $transaction = PettyCashTransaction::find((int)$approvalId);
                    $amount = $request->get("amount_$approvalId", 0);
                    $transaction->update([
                        'initial_approval_status' => 'approved',
                        'initial_approved_by' => Auth::id(),
                        'initial_approval_time' => $date,
                        'amount' => $amount,
                        'old_amount' => $transaction->amount,
                    ]);

                    $processedApprovals[] = $approvalId;
                    $totalAmount += (float)$amount;
                }
            }

            $user = Auth::user();

            WaPettyCashLog::create([
                'petty_cash_transaction_ids' => $processedApprovals,
                'petty_cash_type' => $request->petty_cash_type,
                'initiated_by' => $user->id,
                'initiated_time' => $date,
                'transactions_count' => count($processedApprovals),
                'total_amount' => $totalAmount
            ]);

            Session::flash('success', 'Petty cash approved successfully.');
            return redirect()->back();
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function approveFinal(Request $request): RedirectResponse
    {
        //        Session::flash('warning', "This process is currently under review and will be updated shortly.");
        //        return redirect()->back();

        $approvalIds = json_decode($request->approval_ids, true);
        
        if ($request->submit != 'Reject') {
            try {
                $total = 0;
                $initiated = 0;
                $declinedTransactions = 0;
                $declinedTransactionsAmount = 0;
                foreach ($approvalIds as $approvalId) {
                    try {
                        $transaction = PettyCashTransaction::find((int)$approvalId);
                        if (($request->get("approve_$approvalId") == 'on')) {
                            $total += 1;
                            $transaction->update([
                                'final_approval_status' => 'approved',
                                'final_approved_by' => Auth::id(),
                                'final_approval_time' => Carbon::now(),
                            ]);
    
                            // Send money
                            $user = User::find($transaction->user_id);
    
                            $tokenResponse = $this->authenticatePesaFlow();
                            $token = $tokenResponse['token'];
                            $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $user->phone_number . "$transaction->amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
                            $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
                            $payload = [
                                'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
                                'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
                                'amount' => "$transaction->amount",
                                'currency' => 'KES',
                                'party_b' => $user->phone_number,
                                'secure_hash' => $hash,
                                'type' => 'b2c',
                                'notification_url' => env('APP_URL') . '/api/wallet-transactions/pesaflow/callback',
                            ];
    
                            Log::info("PF B2C Payload: " . json_encode($payload));
    
                            $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';
    
                            $response = Http::withToken($token)->post($url, $payload);
                            if (!$response->ok()) {
                                $transaction->update([
                                    'final_approval_status' => 'pending',
                                    'final_approved_by' => null,
                                    'final_approval_time' => null,
                                ]);
                            }
                            Log::info("PF Response: " . $response->body());
    
                            $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
                            PettyCashTransaction::create([
                                'user_id' => $user->id,
                                'amount' => (float)$transaction->amount * -1,
                                'document_no' => $documentNumber,
                                'wallet_type' => $transaction->wallet_type,
                                'wallet_type_id' => $transaction->wallet_type_id,
                                'parent_id' => $transaction->id,
                                'reference' => $response->json()['reference'],
                                'narrative' => "Travel deposit to $user->name - $user->phone_number",
                                'call_back_status' => 'pending'
                            ]);
    
                            updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                            $initiated += 1;
                        } else {
                            $declinedTransactions++;
                            $declinedTransactionsAmount += (float)$transaction->amount;
                        }
                    } catch (Throwable $e) {
                        Log::info("Failed for $transaction->document_no: " . $e->getMessage());
                    }
                }
    
                if ($initiated > 0) {
                    $pettyCashLog = WaPettyCashLog::where('created_at', $request->date_time)->first();
    
                    if ($pettyCashLog) {
                        $pettyCashLog->update([
                            'approved_by' => Auth::id(),
                            'approved_time' => now(),
                            'declined_transactions' => $declinedTransactions,
                            'declined_amount' => $declinedTransactionsAmount
                        ]);
                    }
                }
    
                Session::flash('success', "Petty cash approved successfully. $initiated out of $total deposits have been initiated.");
                return redirect()->back();
            } catch (\Throwable $e) {
                Session::flash('warning', $e->getMessage());
                return redirect()->back();
            }
        } else {
            try {
                PettyCashTransaction::whereIn('id', $approvalIds)->update(['status' => '1']);

                Session::flash('success', "Petty cash rejected successfully.");
                
                return redirect()->route('petty-cash-approvals.final');

            } catch (Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    public function resendFailedDeposits(Request $request): RedirectResponse
    {
        $resendIds = json_decode($request->resend_ids, true);
        
        if (isset($request->expunge)) {
            $totalExpunged = 0;
            
            DB::beginTransaction();
            try {
                foreach ($resendIds as $resendId) {
                    if (($request->get("resend_$resendId") == 'on')) {
                        $pettyCashTransaction = PettyCashTransaction::find($resendId);
    
                        if ($pettyCashTransaction) {
                            $pettyCashTransaction->update(['expunged' => true]);
    
                            $totalExpunged++;
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                
                Session::flash('danger', $e->getMessage());
                return redirect()->back();
            }

            Session::flash('success', "$totalExpunged failed deposits expunged");
            return redirect()->back();
        } else {
            try {
                $pfTokenPayload = [
                    'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                    'secret' => env('PESAFLOW_B2C_AUTH_SECRET'),
                ];
    
                $tokenUrl = env('PESAFLOW_B2C_URL') . '/oauth/generate/token';
    
                $apiResponse = Http::post($tokenUrl, $pfTokenPayload);
                if (!$apiResponse->ok()) {
                    Session::flash('error', 'PF Token error');
    
                    return redirect()->back();
                }
    
                $token = $apiResponse->json()['token'];
                $statusUrl = env('PESAFLOW_B2C_URL') . '/payment/withdrawal/status';
    
                $total = 0;
                $initiated = 0;
                $updated = 0;
                foreach ($resendIds as $resendId) {
                    DB::beginTransaction();
                    try {
                        $transaction = PettyCashTransaction::with('user', 'parent.travelExpenseTransaction.route')->find((int)$resendId);
                        $parentTransaction = $transaction->parent;
                        if (($request->get("resend_$resendId") == 'on')) {
                            $total += 1;
    
                            $reference = $transaction->reference;
                            // Recheck status
                            $statusPayload = [
                                'trx_ref' => $reference
                            ];
    
                            $response = Http::withToken($token)->post($statusUrl, $statusPayload);
                            if ($response->ok()) {
                                $responsePayload = json_decode($response->body(), true);
    
                                $status = $responsePayload['status'];
    
                                $transaction->update(['call_back_status' => $status]);
    
                                if ($status == 'complete') {
                                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                                    $series_module = WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();
    
                                    $narrative = "{$transaction->parent->travelExpenseTransaction->route->route_name} / {$transaction->user->name} / {$transaction->user->phone_number}";
    
    
                                    $bank_account = WaBankAccount::where('account_code', '988329')->first();
                                    $btran = new WaBanktran();
                                    $btran->type_number = $series_module->type_number;
                                    $btran->document_no = $transaction->document_no;
                                    $btran->bank_gl_account_code = $bank_account->getGlDetail?->account_code;
                                    $btran->reference = $reference;
                                    $btran->trans_date = Carbon::now();
                                    $btran->wa_payment_method_id = 11; //PETTY CASH
                                    $btran->amount = $transaction->amount;
                                    $btran->wa_curreny_id = 0;
                                    $btran->cashier_id = 1;
                                    $btran->save();
    
    
                                    $cr = new WaGlTran();
                                    $cr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                                    $cr->grn_type_number = $series_module->type_number;
                                    $cr->trans_date = Carbon::now();
                                    $cr->restaurant_id = 10; // MAKONGENI;
                                    $cr->tb_reporting_branch = 10; // MAKONGENI;
                                    $cr->grn_last_used_number = $series_module->last_number_used;
                                    $cr->transaction_type = $series_module->description;
                                    $cr->transaction_no = $transaction->document_no;
                                    $cr->narrative = $narrative;
                                    $cr->reference = $transaction->reference;
                                    $cr->account = $bank_account->getGlDetail->account_code;
                                    $cr->amount = $transaction->amount;
                                    $cr->save();
    
    
                                    $dr = new WaGlTran();
                                    $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                                    $dr->grn_type_number = $series_module->type_number;
                                    $dr->trans_date = Carbon::now();
                                    $dr->restaurant_id = 10;
                                    $dr->tb_reporting_branch = 10;
                                    $dr->grn_last_used_number = $series_module->last_number_used;
                                    $dr->transaction_type = $series_module->description;
                                    $dr->transaction_no = $transaction->document_no;
                                    $dr->narrative = $narrative;
                                    $dr->reference = $reference;
                                    $dr->account = '56002-038'; // Travel
                                    $dr->amount = abs($transaction->amount);
                                    $dr->save();
    
                                    $pettyCashLog = WaPettyCashLog::where('created_at', $transaction->parent->initial_approval_time)->first();
    
                                    if ($pettyCashLog) {
                                        $pettyCashLog->update([
                                            'successful_transactions' => ($pettyCashLog->successful_transactions ?: 0) + 1,
                                            'disbursed_amount' => ($pettyCashLog->disbursed_amount ?: 0) + abs($transaction->amount),
                                            'failed_transactions' => ($pettyCashLog->failed_transactions ?: 0) - 1,
                                            'pending_amount' => ($pettyCashLog->pending_amount ?: 0) - abs($transaction->amount)
                                        ]);
                                    }
    
                                    $updated++;
                                } else {
                                    // Send money
                                    $user = User::find($transaction->user_id);
    
                                    $amount = abs($transaction->amount);
                                    $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $user->phone_number . "$amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
                                    $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
                                    $payload = [
                                        'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
                                        'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
                                        'amount' => "$amount",
                                        'currency' => 'KES',
                                        'party_b' => $user->phone_number,
                                        'secure_hash' => $hash,
                                        'type' => 'b2c',
                                        'notification_url' => env('APP_URL') . '/api/wallet-transactions/pesaflow/callback',
                                    ];
    
                                    Log::info("PF B2C Payload: " . json_encode($payload));
    
                                    $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';
    
                                    $response = Http::withToken($token)->post($url, $payload);
                                    Log::info("PF Response: " . $response->body());
                                    if ($response->ok()) {
                                        $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
    
                                        PettyCashTransaction::create([
                                            'user_id' => $user->id,
                                            'amount' => (float)$amount * -1,
                                            'document_no' => $documentNumber,
                                            'wallet_type' => $transaction->wallet_type,
                                            'wallet_type_id' => $transaction->wallet_type_id,
                                            'parent_id' => $parentTransaction->id,
                                            'reference' => $response->json()['reference'],
                                            'narrative' => "Travel deposit to $user->name - $user->phone_number",
                                            'call_back_status' => 'pending'
                                        ]);
    
                                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
    
                                        $transaction->delete();
                                        $initiated += 1;
                                    }
                                }
    
    
                            } else {
                                Log::info("Recheck failed for $reference with " . $response->body());
                            }
                        }
    
                        DB::commit();
                    } catch (Throwable $e) {
                        DB::rollback();
                        Log::info("Failed for $transaction->document_no: " . $e->getMessage());
                    }
                }
    
                Session::flash('success', "$updated requests updated, $initiated requests resent.");
                return redirect()->back();
            } catch (\Throwable $e) {
                Session::flash('warning', $e->getMessage());
                return redirect()->back();
            }
        }
    }

    private function authenticatePesaFlow(): array
    {
        $response = ['success' => true];
        try {
            $payload = [
                'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                'secret' => env('PESAFLOW_B2C_AUTH_SECRET'),
            ];
            $url = env('PESAFLOW_B2C_URL') . '/oauth/generate/token';
            $apiResponse = Http::post($url, $payload);
            Log::info("PF Token response  " . $apiResponse->body());
            if (!$apiResponse->ok()) {
                $response['success'] = false;
                $response['message'] = $apiResponse->body();
            } else {
                $response['token'] = $apiResponse->json()['token'];
            }
        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            Log::info("Failed to get PF Token, citing " . $e->getMessage());
        }

        return $response;
    }

    public function showSuccessfulAllocations(Request $request): View|RedirectResponse
    {
        if (!can('successful_allocations', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $successfulAllocations = $this->getAllocations($request, 'successful');

        $title = 'Petty Cash Successful Allocations';
        $model = 'successful-petty-cash-allocations';
        $breadcum = [
            'Petty Cash' => '',
            'Allocations' => ''
        ];

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();
        return view('admin.petty_cash_approvals.successful_allocations', compact('model', 'title', 'breadcum', 'routes', 'successfulAllocations', 'branches'));
    }

    public function exportSuccessfulAllocations(Request $request)
    {
        $successfulAllocations = $this->getAllocations($request, 'successful');

        $export_array = [];
        foreach ($successfulAllocations as $successfulAllocation) {
            $export_array[] = [
                Carbon::parse($successfulAllocation->date)->format('d-m-Y H:i:s'),
                $successfulAllocation->role == 4 ? 'Order Taking' : 'Delivery',
                $successfulAllocation->branch,
                $successfulAllocation->route,
                $successfulAllocation->user,
                $successfulAllocation->phone_number,
                $successfulAllocation->reference,
                number_format($successfulAllocation->amount, 2),
            ];
        }

        $export_array[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            'Grand Total',
            number_format($successfulAllocations->sum('amount'), 2)
        ];

        $report_name = 'successful_petty_cash_allocations_' . date('Y_m_d_H_i_A');

        return ExcelDownloadService::download(
            $report_name,
            collect($export_array),
            [
                'Date',
                'Petty Cash Type',
                'Branch',
                'Route',
                'Recipient',
                'Phone Number',
                'Reference',
                'Amount',
            ]
        );
    }

    public function showFailedDeposits(Request $request): View|RedirectResponse
    {
        if (!can('failed_deposits', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $failedDeposits = $this->getAllocations($request, 'failed');

        $title = 'Petty Cash Failed Deposits';
        $model = 'failed-petty-cash-deposits';
        $breadcum = [
            'Petty Cash' => '',
            'Failed Deposits' => ''
        ];

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();
        return view('admin.petty_cash_approvals.failed_deposits', compact('model', 'title', 'breadcum', 'routes', 'failedDeposits', 'branches'));
    }

    public function showRejectedDeposits(Request $request): View|RedirectResponse
    {
        if (!can('rejected_deposits', 'petty-cash-approvals')) {
            return returnAccessDeniedPage();
        }

        $title = 'Petty Cash Rejected Deposits';
        $model = 'rejected-petty-cash-deposits';
        $breadcum = [
            'Petty Cash' => '',
            'Rejected Deposits' => ''
        ];

        // $start = Carbon::parse($request->start_date)->startOfDay();
        // $end = Carbon::parse($request->end_date)->endOfDay();

        $rejectedDeposits = PettyCashTransaction::with('user', 'travelExpenseTransaction.route.branch')
            ->whereHas('user', function ($query) use ($request) {
                $query->when($request->type == 'order-taking', fn ($query) => $query->where('role_id', 4))
                    ->when($request->type == 'delivery', fn ($query) => $query->where('role_id', 6));
            })
            ->whereHas('travelExpenseTransaction.route.branch', fn ($query) => $query->when($request->branch, fn ($query) => $query->where('id', $request->branch)))
            ->where('status', '1')
            // ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get()
            ->map(function ($pettyCashTransaction) {
                if ($pettyCashTransaction->user->role_id == 4) {
                    $shiftDate = SalesmanShift::find($pettyCashTransaction?->travelExpenseTransaction?->shift_id)?->created_at ?? '';
                } else {
                    $shiftDate = DeliverySchedule::find($pettyCashTransaction?->travelExpenseTransaction?->shift_id)?->actual_delivery_date ?? '';
                }

                return (object)[
                    'id' => $pettyCashTransaction->id,
                    'reference' => $pettyCashTransaction->reference,
                    'date' => $pettyCashTransaction->created_at,
                    'user' => $pettyCashTransaction->user->name,
                    'role' => $pettyCashTransaction->user->role_id,
                    'phone_number' => $pettyCashTransaction->user->phone_number,
                    'route' => $pettyCashTransaction->travelExpenseTransaction->route->route_name,
                    'shift_date' => $shiftDate,
                    'branch' => $pettyCashTransaction->travelExpenseTransaction->route->branch->name,
                    'amount' => abs($pettyCashTransaction->amount),
                    'call_back_status' => $pettyCashTransaction->call_back_status
                ];
            });

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();

        return view('admin.petty_cash_approvals.rejected_deposits', compact('model', 'title', 'breadcum', 'routes', 'rejectedDeposits', 'branches'));
    }

    public function showExpungedDeposits(Request $request): View|RedirectResponse
    {
        if (!can('expunged_deposits', 'petty-cash-approvals')) {
            return returnAccessDeniedPage();
        }

        $title = 'Petty Cash Expunged Deposits';
        $model = 'expunged-petty-cash-deposits';
        $breadcum = [
            'Petty Cash' => '',
            'Expunged Deposits' => ''
        ];

        // $start = Carbon::parse($request->start_date)->startOfDay();
        // $end = Carbon::parse($request->end_date)->endOfDay();

        $expungedDeposits =  PettyCashTransaction::withoutGlobalScope('expunged')
            ->with('user', 'parent.travelExpenseTransaction.route.branch')
            ->whereHas('user', function ($query) use ($request) {
                $query->when($request->type == 'order-taking', fn ($query) => $query->where('role_id', 4))
                    ->when($request->type == 'delivery', fn ($query) => $query->where('role_id', 6));
            })
            ->whereHas('parent.travelExpenseTransaction.route.branch', fn ($query) => $query->when($request->branch, fn ($query) => $query->where('id', $request->branch)))
            ->where('amount', '<', 0)
            ->where('expunged', true)
            // ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get()
            ->map(function ($pettyCashTransaction) {
                if ($pettyCashTransaction->user->role_id == 4) {
                    $shiftDate = SalesmanShift::find($pettyCashTransaction?->parent?->travelExpenseTransaction?->shift_id)?->created_at ?? '';
                } else {
                    $shiftDate = DeliverySchedule::find($pettyCashTransaction?->parent?->travelExpenseTransaction?->shift_id)?->actual_delivery_date ?? '';
                }

                return (object)[
                    'id' => $pettyCashTransaction->id,
                    'reference' => $pettyCashTransaction->reference,
                    'date' => $pettyCashTransaction->created_at,
                    'user' => $pettyCashTransaction->user->name,
                    'role' => $pettyCashTransaction->user->role_id,
                    'phone_number' => $pettyCashTransaction->user->phone_number,
                    'route' => $pettyCashTransaction->parent->travelExpenseTransaction->route->route_name,
                    'shift_date' => $shiftDate,
                    'branch' => $pettyCashTransaction->parent->travelExpenseTransaction->route->branch->name,
                    'amount' => abs($pettyCashTransaction->amount),
                    'call_back_status' => $pettyCashTransaction->call_back_status
                ];
            });

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();

        return view('admin.petty_cash_approvals.expunged_deposits', compact('model', 'title', 'breadcum', 'routes', 'expungedDeposits', 'branches'));
    }

    public function showSummaryLog(Request $request): View|RedirectResponse
    {
        if (!can('logs', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Summary Log';
        $model = 'petty-cash-summary-log';
        $breadcum = [
            'Petty Cash' => '',
            'Summary Log' => ''
        ];

        [$dates, $logs] = $this->getLogData($request);

        $groupedLogs = $this->getSummaryLogData($logs);

        return view('admin.petty_cash_approvals.summary-log', compact('model', 'title', 'breadcum', 'groupedLogs', 'dates'));
    }

    public function printSummaryLog(Request $request)
    {
        if (!can('logs', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        [$dates, $logs] = $this->getLogData($request);

        $groupedLogs = $this->getSummaryLogData($logs);

        $filters = $request->all();

        $pdf = PDF::loadView('admin.petty_cash_approvals.summary-log-print', compact('groupedLogs', 'dates', 'filters'));

        return $pdf->stream("petty_cash_summary_log_{date('Y_m_d_H_i_s)}.pdf");
    }

    public function getSummaryLogData($logs)
    {
        $groupedLogs = collect([]);
        $logs->groupBy(fn ($log) => $log->approved_time?->format('Y-m-d'))
            ->map(function ($approvedTimeGroup, $approvedTime) use ($groupedLogs) {
                return $approvedTimeGroup->groupBy('petty_cash_type')
                    ->map(function ($pettyCashTypeGroup, $pettyCashType) use ($approvedTime, $groupedLogs) {
                        $groupedLogs->push(
                            (object)[
                                'approved_date' => $approvedTime,
                                'petty_cash_type' => $pettyCashType,
                                'approved_transactions' => $pettyCashTypeGroup->sum('approved_transactions'),
                                'approved_amount' => $pettyCashTypeGroup->sum('approved_amount'),
                                'disbursed_amount' => $pettyCashTypeGroup->sum('disbursed_amount'),
                                'failed_amount' => $pettyCashTypeGroup->sum('pending_amount'),
                            ]
                        );
                    });
            });

        return $groupedLogs;
    }

    public function showDetailedLog(Request $request): View|RedirectResponse
    {
        if (!can('logs', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Detailed Log';
        $model = 'petty-cash-detailed-log';
        $breadcum = [
            'Petty Cash' => '',
            'Detailed Log' => ''
        ];

        [$dates, $logs] = $this->getLogData($request);

        return view('admin.petty_cash_approvals.detailed-log', compact('model', 'title', 'breadcum', 'logs', 'dates'));
    }

    public function getLogData(Request $request)
    {
        $startDate =  ($request->start_date ? Carbon::parse($request->start_date) : now()->subWeek())->startOfDay();
        $endDate =  ($request->end_date ? Carbon::parse($request->end_date) : now())->endOfDay();

        $logs = WaPettyCashLog::with('initiatedBy', 'approvedBy')
            ->when($request->type, fn ($query) => $query->where('petty_cash_type', $request->type))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $dates = [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')];

        return [$dates, $logs];
    }

    public function showSummaryLogTransactions(Request $request): View|RedirectResponse
    {
        if (!can('logs', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Summary Logs';
        $model = 'petty-cash-summary-logs';
        $breadcum = [
            'Petty Cash' => '',
            'Summary Log Transactions' => ''
        ];

        $logs = WaPettyCashLog::where('petty_cash_type', $request->type)->whereDate('approved_time', $request->date)->get();

        if (!$logs->count()) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $transactionIds = [];
        foreach ($logs as $log) {
            $transactionIds = array_merge($transactionIds, $log->petty_cash_transaction_ids);
        }


        $transactions = PettyCashTransaction::with('user', 'child')->whereIn('id', $transactionIds)->get();

        return view('admin.petty_cash_approvals.log-transactions', compact('model', 'title', 'breadcum', 'transactions'));
    }

    public function showDetailedLogTransactions(Request $request, $id): View|RedirectResponse
    {
        if (!can('logs', 'petty-cash-approvals')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Logs';
        $model = 'petty-cash-logs';
        $breadcum = [
            'Petty Cash' => '',
            'Log Transactions' => ''
        ];

        $log = WaPettyCashLog::find($id);

        if (!$log) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $transactions = PettyCashTransaction::with('user', 'child')->whereIn('id', $log->petty_cash_transaction_ids)->get();

        return view('admin.petty_cash_approvals.log-transactions', compact('model', 'title', 'breadcum', 'transactions'));
    }

    public function getAllocations(Request $request, string $callBackStatus)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        if ($request->start_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
        }

        if ($request->end_date) {
            $end = Carbon::parse($request->end_date)->endOfDay();
        }

        return PettyCashTransaction::with('user', 'parent.travelExpenseTransaction.route.branch')
            ->whereHas('user', function ($query) use ($request) {
                $query->when($request->type == 'order-taking', fn ($query) => $query->where('role_id', 4))
                    ->when($request->type == 'delivery', fn ($query) => $query->where('role_id', 6));
            })
            ->whereHas('parent.travelExpenseTransaction.route.branch', fn ($query) => $query->when($request->branch, fn ($query) => $query->where('id', $request->branch)))
            ->where('amount', '<', 0)
            ->when($callBackStatus == 'successful', fn ($query) => $query->where('call_back_status', 'complete'))
            ->when($callBackStatus == 'failed', fn ($query) => $query->whereNot('call_back_status', 'complete'))
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get()
            ->map(function ($pettyCashTransaction) {
                if ($pettyCashTransaction->user->role_id == 4) {
                    $shiftDate = SalesmanShift::find($pettyCashTransaction?->parent?->travelExpenseTransaction?->shift_id)?->created_at ?? '';
                } else {
                    $shiftDate = DeliverySchedule::find($pettyCashTransaction?->parent?->travelExpenseTransaction?->shift_id)?->actual_delivery_date ?? '';
                }

                return (object)[
                    'id' => $pettyCashTransaction->id,
                    'reference' => $pettyCashTransaction->reference,
                    'date' => $pettyCashTransaction->created_at,
                    'user' => $pettyCashTransaction->user->name,
                    'role' => $pettyCashTransaction->user->role_id,
                    'phone_number' => $pettyCashTransaction->user->phone_number,
                    'route' => $pettyCashTransaction->parent->travelExpenseTransaction->route->route_name,
                    'shift_date' => $shiftDate,
                    'branch' => $pettyCashTransaction->parent->travelExpenseTransaction->route->branch->name,
                    'amount' => abs($pettyCashTransaction->amount),
                    'old_amount' => abs($pettyCashTransaction->old_amount),
                    'call_back_status' => $pettyCashTransaction->call_back_status
                ];
            });
    }

    public function recalculateIncetives()
    {
        $salesmanAllocations = request()->input('salesmanAllocations', []);
        $driverAllocations = request()->input('driverAllocations', []);
        // dd($salesmanAllocations);
        if (!empty($salesmanAllocations)) {
            foreach ($salesmanAllocations as $salesmanAllocation) {
                $total_incentive_amount = 0;
                $calculatedrouteonsiteallowance = 0;
                $calculatedrouteoffsiteallowance = 0;
                $routedata = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                $routeid = $routedata->route_id;
                $incentiveTotal = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                $incentiveTotalAmount = $incentiveTotal->amount;
                $route = Route::where('id', $routeid)->withCount('waRouteCustomer')->first();
                $shiftdata = DB::table('salesman_shift_customers')
                    ->where('salesman_shift_id', $salesmanAllocation['shift_id'])
                    ->where('visited', '1')
                    ->orderBy('updated_at', 'asc')
                    ->first();

                $offsitecount = floatval($salesmanAllocation['offsitecount']);
                $onsitecount = floatval($salesmanAllocation['onsitecount']);

                // $offsitecount = DB::table('salesman_shift_customers')
                //     ->where('salesman_shift_id', $salesmanAllocation['shift_id'])->where('visited', 1)
                //     ->where('salesman_shift_type', 'offsite')->count();
                // $onsitecount = DB::table('salesman_shift_customers')
                //     ->where('salesman_shift_id', $salesmanAllocation['shift_id'])->where('visited', 1)
                //     ->where('salesman_shift_type', 'onsite')->count();


                // $onsitecount = floatval($onsitecount);
                // $offsitecount = floatval($offsitecount);


                if ($offsitecount > 0 && $onsitecount > 0) {
                    $total_incentive_amount = $route->offsite_allowance + $route->travel_expense;
                } else if ($offsitecount <= 0 && $onsitecount > 0) {
                    $total_incentive_amount = $route->travel_expense;
                } else if ($offsitecount > 0 && $onsitecount <= 0) {
                    $total_incentive_amount = $route->offsite_allowance;
                }

                if ($shiftdata->salesman_shift_type == 'onsite') {
                    $calculatedIncentiveAmount = 0;
                    $total_customers = $route->wa_route_customer_count;
                    $customers_visited = $total_customers - $onsitecount;

                    if (floatval($total_customers) > 0) {
                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($total_customers);
                    } else {
                        $calculatedrouteonsiteallowance = 0;
                    }

                    if (floatval($customers_visited) > 0) {
                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($customers_visited);
                    } else {
                        $calculatedrouteoffsiteallowance = 0;
                    }

                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                    $calculatedoffsiteamount = floatval($offsitecount * $calculatedrouteoffsiteallowance);
                    $calculatedIncentiveAmount = ceil($calculatedonsiteamount + $calculatedoffsiteamount);
                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                        if (floatval($calculatedIncentiveAmount) > $incentiveTotalAmount) {
                            $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                            $incentive->amount = $total_incentive_amount;
                            $incentive->save();
                            $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                            $pettycashtransaction->amount = $incentive->amount;
                            $pettycashtransaction->save();
                        } else {
                            $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                            $incentive->amount = $calculatedIncentiveAmount;
                            $incentive->save();
                            $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                            $pettycashtransaction->amount = $incentive->amount;
                            $pettycashtransaction->save();
                        }
                    } else {
                        $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                        $incentive->amount = $total_incentive_amount;
                        $incentive->save();
                        $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                        $pettycashtransaction->amount = $incentive->amount;
                        $pettycashtransaction->save();
                    }
                } else if ($shiftdata->salesman_shift_type == 'offsite') {
                    $calculatedIncentiveAmount = 0;
                    $total_customers = $route->wa_route_customer_count;
                    $customers_visited = $total_customers - $offsitecount;

                    if (floatval($customers_visited) > 0) {
                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($customers_visited);
                    } else {
                        $calculatedrouteonsiteallowance = 0;
                    }

                    if (floatval($total_customers) > 0) {
                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($total_customers);
                    } else {
                        $calculatedrouteoffsiteallowance = 0;
                    }

                    $calculatedoffsiteallowance = floatval($calculatedrouteoffsiteallowance) * floatval($offsitecount);
                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                    $calculatedIncentiveAmount = ceil($calculatedoffsiteallowance + $calculatedonsiteamount);
                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                        if (floatval($calculatedIncentiveAmount) > $incentiveTotalAmount) {
                            $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                            $incentive->amount = $total_incentive_amount;
                            $incentive->save();
                            $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                            $pettycashtransaction->amount = $incentive->amount;
                            $pettycashtransaction->save();
                        } else {
                            $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                            $incentive->amount = $calculatedIncentiveAmount;
                            $incentive->save();
                            $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                            $pettycashtransaction->amount = $incentive->amount;
                            $pettycashtransaction->save();
                        }
                    } else {
                        $incentive = TravelExpenseTransaction::where('shift_id', $salesmanAllocation['shift_id'])->where('shift_type', 'order_taking')->first();
                        $incentive->amount = $total_incentive_amount;
                        $incentive->save();
                        $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                        $pettycashtransaction->amount = $incentive->amount;
                        $pettycashtransaction->save();
                    }
                }
            }
        }

        if (!empty($driverAllocations)) {
            foreach ($driverAllocations as $driverAllocation) {
                $deliveryshedule = DeliverySchedule::where('shift_id', $driverAllocation['shift_id'])->with('driver')->first();
                $vehicle = Vehicle::latest()->with('model')->where('driver_id', $deliveryshedule->driver_id)->first();
                $incentive = TravelExpenseTransaction::where('user_id', $deliveryshedule->driver_id)->first();
                $incentive->amount = $vehicle->model?->travel_expense ?? 0;
                $pettycashtransaction = PettyCashTransaction::where('parent_id', $incentive->id)->where('user_id', $incentive->user_id)->first();
                $pettycashtransaction->amount = $incentive->amount;
                $pettycashtransaction->save();
            }
        }
    }

}
