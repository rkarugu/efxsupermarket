<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\WaPosCashSalesPayments;
use App\Models\BankedDropTransaction;
use App\Models\BankingApproval;
use App\Models\CashDropTransaction;
use App\Models\ChiefCashierDeclaration;
use App\Models\PaymentVerificationBank;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ShortBankingComment;





class PosBankingController extends Controller
{
    protected string $model = 'pos-banking-overview';
    protected string $permissionModule = 'reconciliation';

    public function showOverviewPage(Request $request): View|RedirectResponse
    {

        if (!can('see-overview-pos', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Cash Sales Banking Overview';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];
        $user = Auth::user();
        $permissionModule = $this->permissionModule;

        $branches = Restaurant::latest();
        $branches = Restaurant::latest();
        if (!can('view-all-branches', $this->permissionModule)) {
            $branches = $branches->where('id', $user->restaurant_id);
        }
        $branches = $branches->select('id', 'name')->get();
        $channels = PaymentMethod::select('id', 'title')->get();
        $date = $request->date;
        $selectBranch = $request->branch ?? $user->restaurant_id;

        return view('banking_approval.pos_overview', compact('title', 'model', 'branches', 'breadcrum', 'channels', 'date', 'selectBranch', 'user', 'permissionModule'));
    }

    public function showDailyOverviewPage(): View|RedirectResponse
    {
        if (!can('see-overview-pos', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Cash Sales Banking Overview';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];
        $user  =  Auth::user();


        $branches = Restaurant::latest();
        if (!can('view-all-branches', $this->permissionModule)) {
            $branches = $branches->where('id', $user->restaurant_id);
        }
        $branches = $branches->select('id', 'name')->get();

        return view('banking_approval.pos_daily_overview', compact('title', 'model', 'branches', 'breadcrum', 'user'));
    }

    public function getDailyRecords(Request $request)
    {
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $endDate = Carbon::parse($request->to_date)->endOfDay();
        $branchId = $request->branch_id;
        $yesterday = Carbon::parse($request->date)->subDay()->toDateString();

        $records =  DB::table('wa_pos_cash_sales_items as items')
            ->select(
                DB::raw("(DATE(sales.created_at)) as date"),
                //sales
                DB::raw("SUM((items.qty * items.selling_price)  -  items.discount_amount) as sales"),
                //returns
                DB::raw("(
                        SELECT COALESCE(SUM(r.return_quantity * return_sales.selling_price), 0) 
                        FROM wa_pos_cash_sales_items_return as r
                        JOIN wa_pos_cash_sales_items as return_sales 
                            ON r.wa_pos_cash_sales_item_id = return_sales.id 
                        JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = r.wa_pos_cash_sales_id
                        WHERE 
                            r.accepted = 1
                            AND DATE(r.accepted_at) = DATE(sales.created_at)
                            AND wa_pos_cash_sales.branch_id = $branchId
                            AND wa_pos_cash_sales.status = 'Completed'
                    ) as returns"),

                //expenses
                DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $branchId
                        AND pos_cash_payments.status = 'Disbursed'
                        AND DATE(pos_cash_payments.disbursed_at) = DATE(sales.created_at)
                    ) as expenses"),

                //eazzy
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 13
                    ) as eazzy"),

                //eb_main
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 10
                    ) as eb_main"),

                //vooma
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 12
                    ) as vooma"),

                //kcb_main
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 9
                    ) as kcb_main"),

                //mpesa
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and (csp.payment_method_id = 3 OR csp.payment_method_id = 15)
                    ) as mpesa"),

                //notified CDMs
                DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                    where DATE(cash_drop_transactions.created_at) = DATE(sales.created_at)
                    ) as cdm"),

                //verified
                DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                            join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                            where DATE(csp.created_at) = DATE(sales.created_at) 
                            and csp.verified = true
                    ) as verified"),

                //allocated_cdms
                DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id 
                            and DATE(cd.created_at) = DATE(sales.created_at)
                            join users on cd.cashier_id = users.id and users.restaurant_id = $branchId 
                            where DATE(bd.created_at) > DATE(sales.created_at) and bd.manually_allocated = true
                    ) as allocated_cdms"),

                // allocated cash banking
                DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where DATE(cb.created_at) = DATE(sales.created_at)
                        AND branch_id = $branchId
                    ) as allocated_cb")

            )
            ->leftJoin('wa_pos_cash_sales as sales', 'sales.id', 'items.wa_pos_cash_sales_id')
            ->where('sales.status', 'Completed')
            ->where('sales.branch_id', $branchId)
            ->whereBetween('sales.created_at', [$fromDate, $endDate])
            ->groupBy(DB::raw("DATE(sales.created_at)"))
            ->orderBy('items.created_at', 'ASC')
            ->get()->map(function ($record) use ($branchId) {
                $record->sales = $record->sales ?? 0;
                $record->returns = $record->returns ?? 0;
                $record->expenses = $record->expenses ?? 0;
                $record->net_sales = $record->sales  - $record->returns - $record->expenses;
                $record->eazzy = $record->eazzy ?? 0;
                $record->eb_main = $record->eb_main ?? 0;
                $record->vooma = $record->vooma ?? 0;
                $record->kcb_main = $record->kcb_main ?? 0;
                $record->mpesa = $record->mpesa ?? 0;
                $record->cdm = $record->cdm ?? 0;
                $record->total_bankings = $record->eazzy + $record->eb_main + $record->vooma + $record->kcb_main + $record->mpesa + $record->cdm;
                $record->verified = ($record->verified ?? 0) + ($record->cdm ?? 0);
                $record->sales_variance =  $record->net_sales - $record->verified;
                $record->allocated_cdms = $record->allocated_cdms ?? 0;
                $record->allocated_cb = $record->allocated_cb ?? 0;
                $record->balance = $record->sales_variance - $record->allocated_cdms - $record->allocated_cb;
                $record->branch = $branchId;

                return $record;
            });

        $period = CarbonPeriod::create($fromDate, $endDate);
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');

            $dayIsNotInCollection = !($records->where('date', $day)->first());
            if ($dayIsNotInCollection) {
                $records->push(collect([
                    'date' => $day,
                    'sales' => 0,
                    'returns' => 0,
                    'expenses' => 0,
                    'net_sales' => 0,
                    'eazzy' => 0,
                    'eb_main' => 0,
                    'vooma' => 0,
                    'kcb_main' => 0,
                    'mpesa' => 0,
                    'cdm' => 0,
                    'total_bankings' => 0,
                    'verified' => 0,
                    'sales_variance' => 0,
                    'allocated_cdms' => 0,
                    'allocated_cb' => 0,
                    'balance' => 0,
                    'formatted_sales' => manageAmountFormat(0),
                    'formatted_returns' => manageAmountFormat(0),
                    'formatted_expenses' => manageAmountFormat(0),
                    'formatted_net_sales' => manageAmountFormat(0),
                    'formatted_eazzy' => manageAmountFormat(0),
                    'formatted_eb_main' => manageAmountFormat(0),
                    'formatted_vooma' => manageAmountFormat(0),
                    'formatted_kcb_main' => manageAmountFormat(0),
                    'formatted_mpesa' => manageAmountFormat(0),
                    'formatted_cdm' => manageAmountFormat(0),
                    'formatted_total_bankings' => manageAmountFormat(0),
                    'formatted_verified' => manageAmountFormat(0),
                    'formatted_sales_variance' => manageAmountFormat(0),
                    'formatted_allocated_cdms' => manageAmountFormat(0),
                    'formatted_allocated_cb' => manageAmountFormat(0),
                    'formatted_balance' => manageAmountFormat(0),
                ]));
            }
        }


        $records = $records->map(function ($record) {
            try {
                $record->formatted_sales = manageAmountFormat($record->sales);
                $record->formatted_returns = manageAmountFormat($record->returns);
                $record->formatted_expenses = manageAmountFormat($record->expenses);
                $record->formatted_net_sales = manageAmountFormat($record->net_sales);
                $record->formatted_eazzy = manageAmountFormat($record->eazzy);
                $record->formatted_eb_main = manageAmountFormat($record->eb_main);
                $record->formatted_vooma = manageAmountFormat($record->vooma);
                $record->formatted_kcb_main = manageAmountFormat($record->kcb_main);
                $record->formatted_mpesa = manageAmountFormat($record->mpesa);
                $record->formatted_cdm = manageAmountFormat($record->cdm);
                $record->formatted_total_bankings = manageAmountFormat($record->total_bankings);
                $record->formatted_verified = manageAmountFormat($record->verified);
                $record->formatted_sales_variance = manageAmountFormat($record->sales_variance);
                $record->formatted_allocated_cdms = manageAmountFormat($record->allocated_cdms);
                $record->formatted_allocated_cb = manageAmountFormat($record->allocated_cb);
                $record->formatted_balance = manageAmountFormat($record->balance);
            } catch (\Throwable $th) {
                $record['formatted_sales'] = manageAmountFormat($record['sales']);
            }

            return $record;
        });

        $totalSales = $records->sum('sales');
        $totals = [
            'sales' => manageAmountFormat($totalSales),
            'returns' => manageAmountFormat($records->sum('returns')),
            'expenses' => manageAmountFormat($records->sum('expenses')),
            'net_sales' => manageAmountFormat($records->sum('net_sales')),
            'eazzy' => manageAmountFormat($records->sum('eazzy')),
            'eb_main' => manageAmountFormat($records->sum('eb_main')),
            'vooma' => manageAmountFormat($records->sum('vooma')),
            'kcb_main' => manageAmountFormat($records->sum('kcb_main')),
            'mpesa' => manageAmountFormat($records->sum('mpesa')),
            'cdm' => manageAmountFormat($records->sum('cdm')),
            'total_bankings' => manageAmountFormat($records->sum('total_bankings')),
            'verified' => manageAmountFormat($records->sum('verified')),
            'sales_variance' => manageAmountFormat($records->sum('sales_variance')),
            'allocated_cdms' => manageAmountFormat($records->sum('allocated_cdms')),
            'allocated_cb' => manageAmountFormat($records->sum('allocated_cb')),
            'balance' => manageAmountFormat($records->sum('balance')),
            'total_returns' => manageAmountFormat($records->sum('returns')),
        ];

        return $this->jsonify(['records' => $records, 'totals' => $totals]);
    }


    public function getRecords(Request $request)
    {
        try {
            $date = $request->date;
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();

            $bankingRecord = BankingApproval::whereDate('payment_date', $date)->where('branch_id', $request->branch_id)->where('sales_type', 1)->first();
            if (!$bankingRecord) {
                $bankingRecord = BankingApproval::create([
                    'sales_date' => $date,
                    'branch_id' => $request->branch_id,
                    'payment_date' => $date,
                    'sales_type' => 1
                ]);
            }

            $sales = DB::table('wa_pos_cash_sales_items as items')
                ->select(
                    DB::raw("('$date') as date"),
                    // DB::raw("(sum(items.total)) as cs"),
                    DB::raw("(sum(items.selling_price * items.qty)) as cs"),
                    DB::raw("(sum(items.discount_amount)) as disc"),

                    DB::raw("(select coalesce(sum(selling_price * r.return_quantity), 0) from wa_pos_cash_sales_items_return as r
                        join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                        where r.accepted = 1 and (r.accepted_at between '$fromDate' and '$endDate')) as returns"),

                    //expenses
                    DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $request->branch_id 
                        AND pos_cash_payments.status = 'Disbursed'
                        AND (pos_cash_payments.disbursed_at between '$fromDate' and '$endDate')
                    ) as expenses"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 13) as eazzy"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 10) as eb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 12) as vooma"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 9) as kcb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and (csp.payment_method_id = 3 or csp.payment_method_id = 15)) as mpesa"),

                    DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                        where (created_at between '$fromDate' and '$endDate')) as cdm"),

                    DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                        join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                        where (csp.created_at between '$fromDate' and '$endDate') and csp.verified = true) as verified"),

                    DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and (cd.created_at between '$fromDate' and '$endDate')
                            join users on cd.cashier_id = users.id and users.restaurant_id = $request->branch_id 
                            where bd.created_at > '$fromDate' and bd.manually_allocated = true) as allocated_cdms"),

                    DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where (cb.created_at between '$fromDate' and '$endDate') and branch_id = $request->branch_id) as allocated_cb")
                )
                ->join('wa_pos_cash_sales as sales', 'items.wa_pos_cash_sales_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$fromDate, $endDate])
                ->where('sales.branch_id', $request->branch_id)
                ->where('sales.status', 'Completed')
                ->first();

            $sales->sales = $sales->cs - $sales->disc;
            $sales->net_sales = $sales->sales - $sales->returns - $sales->expenses;
            $sales->total_bankings = $sales->eazzy + $sales->eb_main + $sales->vooma + $sales->kcb_main + $sales->mpesa + $sales->cdm;

            $sales->verified = $sales->verified + $sales->cdm;
            $sales->sales_variance = $sales->net_sales - $sales->verified;
            $sales->balance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            $todaysBalance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;

            $sales->raw_rcts = $sales->total_bankings;
            $sales->raw_verified = $sales->verified;
            $sales->raw_balance = $sales->balance;


            $sales->sales = manageAmountFormat($sales->sales);
            $sales->returns = manageAmountFormat($sales->returns);
            $sales->expenses = manageAmountFormat($sales->expenses);
            $sales->net_sales = manageAmountFormat($sales->net_sales);
            $sales->eazzy = manageAmountFormat($sales->eazzy);
            $sales->eb_main = manageAmountFormat($sales->eb_main);
            $sales->vooma = manageAmountFormat($sales->vooma);
            $sales->kcb_main = manageAmountFormat($sales->kcb_main);
            $sales->mpesa = manageAmountFormat($sales->mpesa);
            $sales->cdm = manageAmountFormat($sales->cdm);
            $sales->total_bankings = manageAmountFormat($sales->total_bankings);
            $sales->verified = manageAmountFormat($sales->verified);
            $sales->sales_variance = manageAmountFormat($sales->sales_variance);
            $sales->allocated_cdms = manageAmountFormat($sales->allocated_cdms);
            $sales->allocated_cb = manageAmountFormat($sales->allocated_cb);
            $sales->balance = manageAmountFormat($sales->balance);

            return $this->jsonify(['sales' => $sales, 'todaysBalance' => $todaysBalance, 'bankingRecord' => $bankingRecord]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getBankingSummary(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $records = DB::table('payment_methods as pm')
                ->select(
                    'pm.title as collection_account',
                    'sweep_account.title as sweep_account',

                    DB::raw("(select sum(csp.amount) from wa_pos_cash_sales_payments as csp 
                    join wa_pos_cash_sales as cs on csp.wa_pos_cash_sales_id = cs.id 
                    join payment_verification_banks as b on csp.bank_statement_id = b.id and date(csp.created_at) = date(b.bank_date)
                    where (cs.created_at between '$fromDate' and '$endDate') 
                    and cs.branch_id = $branchId 
                    and csp.payment_method_id = pm.id 
                    and csp.verified = true 
                    and cs.status = 'Completed') as same_day_collections"),

                    DB::raw("(select sum(csp.amount) from wa_pos_cash_sales_payments as csp 
                    join wa_pos_cash_sales as cs on csp.wa_pos_cash_sales_id = cs.id 
                    join payment_verification_banks as b on csp.bank_statement_id = b.id and date(csp.created_at) != date(b.bank_date)
                    where (cs.created_at between '$fromDate' and '$endDate') 
                    and cs.branch_id = $branchId 
                    and csp.payment_method_id = pm.id 
                    and csp.verified = true 
                    and cs.status = 'Completed') as late_utilizations"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                    join wa_pos_cash_sales_payments as csp on payment_verification_banks.id = csp.bank_statement_id 
                    and date(csp.created_at) != date(payment_verification_banks.bank_date)
                    and payment_verification_banks.amount > 0
                    and (bank_date between '$fromDate' and '$endDate')
                    and payment_verification_banks.payment_method_id = pm.id) as utilized_unknowns"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                    where payment_verification_banks.status = 'Pending' 
                    and payment_verification_banks.amount > 0
                    and (bank_date between '$fromDate' and '$endDate')
                    and payment_verification_banks.payment_method_id = pm.id) as actual_unknowns"),

                    DB::raw("(select sum(amount) from payment_verification_banks 
                    where amount < 0
                    and (bank_date between '$fromDate' and '$endDate')
                    and payment_method_id = pm.id) as sweep_total"),
                )
                ->join('payment_methods as sweep_account', 'pm.sweep_account_id', '=', 'sweep_account.id')
                ->where('pm.branch_id', $request->branch_id)
                ->where('pm.account_type', 1)
                ->get()
                ->map(function ($record) {
                    $record->formatted_same_day_collections = manageAmountFormat($record->same_day_collections);
                    $record->formatted_late_utilizations = manageAmountFormat($record->late_utilizations);

                    $record->total_collection = $record->same_day_collections + $record->late_utilizations;
                    $record->formatted_total_collection = manageAmountFormat($record->total_collection);

                    $record->formatted_utilized_unknowns = manageAmountFormat($record->utilized_unknowns);
                    $record->formatted_actual_unknowns = manageAmountFormat($record->actual_unknowns);

                    $record->total_unknowns = $record->utilized_unknowns + $record->actual_unknowns;
                    $record->formatted_total_unknowns = manageAmountFormat($record->total_unknowns);

                    $record->nominal_total = $record->same_day_collections + $record->total_unknowns;
                    $record->formatted_nominal_total = manageAmountFormat($record->nominal_total);

                    $record->formatted_sweep_total = manageAmountFormat(abs($record->sweep_total));
                    $record->variance = abs($record->sweep_total) - $record->nominal_total;
                    $record->formatted_variance = manageAmountFormat($record->variance);

                    return $record;
                });

            return $this->jsonify(['records' => $records]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function runVerification(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $payments = DB::select("
                        select `csp`.`id` as `payment_id`, `csp`.`payment_reference`, 
                        (select id from payment_verification_banks b 
                        where b.amount = csp.amount and b.status = 'Pending' and b.reference like CONCAT('%',te.reference,'%')
                        LIMIT 1
                        ) as bank_id 
                        from `wa_pos_cash_sales_payments` as `csp` 
                        inner join `wa_tender_entries` as `te` on `csp`.`wa_tender_entry_id` = `te`.`id`  
                        inner join `payment_methods` as `p` on `csp`.`payment_method_id` = `p`.`id` and `p`.`is_cash` = 0 
                        inner join `wa_pos_cash_sales` as `cs` on `csp`.`wa_pos_cash_sales_id` = `cs`.`id` 
                        and (`cs`.`created_at` between '$fromDate' and '$endDate') 
                        and `cs`.`branch_id` = $branchId and `cs`.`status` = 'Completed' 
                        where `csp`.`verified` = 0;
                    ");

            $feedback = [];
            foreach (collect($payments) as $payment) {
                DB::beginTransaction();
                try {
                    if ($payment->bank_id) {
                        WaPosCashSalesPayments::find($payment->payment_id)->update([
                            'verified' => true,
                            'bank_statement_id' => $payment->bank_id
                        ]);

                        PaymentVerificationBank::find($payment->bank_id)->update([
                            'status' => 'Verified'
                        ]);
                    }

                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    $feedback[] = [
                        'payment' => "$payment->id | $payment->payment_reference",
                        'message' => $th->getMessage()
                    ];
                }
            }

            return $this->jsonify($feedback);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getCdms(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $cdms = DB::select("
                select bank_reference,bd.amount,bd.created_at as bd_time,cd.reference as cd_reference,bd.verified from banked_drop_transactions bd 
                join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and (cd.created_at between '$fromDate' and '$endDate')
                join users on cd.cashier_id = users.id and users.restaurant_id = $branchId 
                where bd.created_at > '$fromDate';
            ");

            $cdms = collect($cdms);

            $cdmTotal = $cdms->sum('amount');

            $cdms = $cdms->map(function ($record) {
                $record->amount = manageAmountFormat($record->amount);
                $record->status = $record->verified ? 'Verified' : 'Pending';
                return $record;
            });

            $cdmTotal = manageAmountFormat($cdmTotal);

            return $this->jsonify(['cdms' => $cdms, 'total' => $cdmTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function searchCdmDeposit(Request $request)
    {
        try {
            $statement = DB::table('payment_verification_banks')
                ->where('amount', $request->amount)
                ->where('status', 'Pending')
                ->where('reference', 'like', '%' . $request->reference . '%')
                ->first();

            if (!$statement) {
                return $this->jsonify(['message' => 'A bank record with the searched paramaters was not found'], 422);
            }

            $statement->amount = manageAmountFormat($statement->amount);

            return $this->jsonify($statement);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getDrops(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $drops = DB::table('cash_drop_transactions as cd')
                ->select('cd.id', 'cd.reference', 'cd.amount')
                ->join('users', function ($join) use ($branchId) {
                    $join->on('cd.cashier_id', '=', 'users.id')->where('users.restaurant_id', $branchId);
                })
                ->whereBetween('cd.created_at', [$fromDate, $endDate])
                ->get();

            return $this->jsonify($drops);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function allocateCdmDeposit(Request $request)
    {
        DB::beginTransaction();

        try {
            $drop = CashDropTransaction::find($request->drop_id);
            $bankStatement = PaymentVerificationBank::find($request->bank_id);

            $bankStatement->update(['status' => 'Verified']);

            $banking = BankedDropTransaction::create([
                'cash_drop_transaction_id' => $drop->id,
                'amount' => $bankStatement->amount,
                'bank_reference' => $bankStatement->reference,
                'banked_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'verified' => true,
                'manually_allocated' => true
            ]);

            DB::commit();
            return $this->jsonify([]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getUnknown(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();

            $query = DB::table('payment_verification_banks')
                ->select('bank_date', 'amount', 'channel', 'reference')
                ->where('amount', '>', 0)
                ->where('status', 'Pending')
                ->whereBetween('bank_date', [$fromDate, $endDate]);

            if ($request->channel) {
                $query = $query->where('channel', $request->channel);
            } else {
                $query = $query->whereIn('channel', ['MPESA THIKA COUNTER (4204201)', 'EQUITY THIKA COUNTER (0766110941)', 'VOOMA THIKA COUNTER (6149693)']);
            }

            $unknowns = $query->get();

            $unknownTotal = $unknowns->sum('amount');

            $unknowns = $unknowns->map(function ($record) {
                $record->amount = manageAmountFormat($record->amount);
                return $record;
            });

            $unknownTotal = manageAmountFormat($unknownTotal);

            return $this->jsonify(['records' => $unknowns, 'total' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getSales(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = $request->branch_id;

            $sales = DB::table('wa_pos_cash_sales as cs')
                ->select(
                    'cs.id',
                    'cs.sales_no',
                    'cs.created_at',
                    'cs.is_tablet_sale',
                    'counter_cashiers.name as counter_cashier',
                    'tablet_cashiers.name as tablet_cashier',
                    DB::raw("(select count(*) from wa_pos_cash_sales_items where wa_pos_cash_sales_id = cs.id) as item_count"),
                    DB::raw("(select sum(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty) from wa_pos_cash_sales_items where wa_pos_cash_sales_id = cs.id) as gross_total"),
                    DB::raw("(select sum(discount_amount) from wa_pos_cash_sales_items where wa_pos_cash_sales_id = cs.id) as discount_amount"),
                )
                ->join('users as counter_cashiers', 'cs.attending_cashier', '=', 'counter_cashiers.id')
                ->join('users as tablet_cashiers', 'cs.user_id', '=', 'tablet_cashiers.id')
                ->where('cs.branch_id', $branchId)->where('cs.status', 'Completed')
                ->whereBetween('cs.created_at', [$fromDate, $endDate])
                ->get()->map(function ($record) {
                    $record->net_total = $record->gross_total - $record->discount_amount;
                    $record->type = 'Counter';

                    if ($record->is_tablet_sale) {
                        $record->type = 'Tablet';

                        if ($record->tablet_cashier != $record->counter_cashier) {
                            $record->type = 'Tablet, Counter';
                        }
                    }

                    if (!$record->is_tablet_sale) {
                        $record->tablet_cashier = '-';
                    }

                    if ($record->is_tablet_sale && ($record->counter_cashier == $record->tablet_cashier)) {
                        $record->counter_cashier = '-';
                    }

                    $record->formatted_gross_total = manageAmountFormat($record->gross_total);
                    $record->formatted_net_total = manageAmountFormat($record->net_total);
                    $record->formatted_discount_amount = manageAmountFormat($record->discount_amount);

                    return $record;
                });

            $salesTotal = manageAmountFormat($sales->sum('net_total'));
            $salesGrossTotal = manageAmountFormat($sales->sum('gross_total'));
            $salesDiscountTotal = manageAmountFormat($sales->sum('discount_amount'));

            return $this->jsonify(['records' => $sales, 'total' => $salesTotal, 'gross_total' => $salesGrossTotal, 'discount_total' => $salesDiscountTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getUnverified(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = $request->branch_id;

            $unverified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'pm.title as channel',
                    'te.reference',
                    'cs.sales_no',
                    'users.name as cashier',
                    'csp.amount',
                )
                ->join('wa_pos_cash_sales as cs', function ($join) use ($fromDate, $endDate, $branchId) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'cs.id')
                        ->whereBetween('cs.created_at', [$fromDate, $endDate])
                        ->where('cs.branch_id', $branchId)
                        ->where('cs.status', 'Completed');
                })
                ->join('payment_methods as pm', function ($join) {
                    $join->on('csp.payment_method_id', '=', 'pm.id')
                        ->where('pm.is_cash', false);
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('users', 'cs.attending_cashier', '=', 'users.id')
                ->where('verified', false)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unverifiedTotal = manageAmountFormat($unverified->sum('amount'));

            return $this->jsonify(['records' => $unverified, 'total' => $unverifiedTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getCashBankingRecords(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = $request->branch_id;

            $records = DB::table('chief_cashier_declarations as cb')
                ->select(
                    'cb.*',
                    'statements.channel'
                )
                ->join('payment_verification_banks as statements', 'cb.bank_statement_id', '=', 'statements.id')
                ->whereBetween('cb.created_at', [$fromDate, $endDate])
                ->where('cb.branch_id', $branchId)
                ->where('banked_amount', '>', 0)
                ->get()
                ->map(function ($record) {
                    $record->formatted_banked_amount = manageAmountFormat($record->banked_amount);
                    return $record;
                });


            return $this->jsonify(['records' => $records]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function searchCbDeposit(Request $request)
    {
        try {
            $statement = DB::table('payment_verification_banks')
                ->where('amount', $request->amount)
                ->where('status', 'Pending')
                ->where('reference', 'like', '%' . $request->reference . '%')
                ->first();

            if (!$statement) {
                return $this->jsonify(['message' => 'A bank record with the searched paramaters was not found'], 422);
            }

            $statement->amount = manageAmountFormat($statement->amount);

            return $this->jsonify($statement);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function allocateCbDeposit(Request $request)
    {
        DB::beginTransaction();

        try {
            $bankStatement = PaymentVerificationBank::find($request->bank_id);
            $bankStatement->update(['status' => 'Verified']);

            $cashBankingrecord = ChiefCashierDeclaration::whereDate('created_at', $request->date)
                ->where('branch_id', $request->branch_id)
                ->first();
            if (!$cashBankingrecord) {
                $reference = rand(100000, 999999);
                $cashBankingrecord = ChiefCashierDeclaration::create([
                    'created_at' => Carbon::parse($request->date)->endOfDay(),
                    'updated_at' => Carbon::parse($request->date)->endOfDay(),
                    'branch_id' => $request->branch_id,
                    'reference' => "CBR-$reference",
                    'bank_reference' => $bankStatement->reference,
                    'banked_amount' => $bankStatement->amount,
                    'banking_time' => Carbon::now(),
                    'verified' => true,
                    'manual_allocation' => true,
                    'bank_statement_id' => $bankStatement->id
                ]);
            }

            DB::commit();
            return $this->jsonify([]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getEazzy(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $verified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'te.reference',
                    'wa_pos_cash_sales.sales_no',
                    'csp.amount'
                )
                ->join('wa_pos_cash_sales', function ($join) use ($branchId, $fromDate, $endDate) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                        ->whereBetween('wa_pos_cash_sales.created_at', [$fromDate, $endDate])
                        ->where('wa_pos_cash_sales.branch_id', $branchId)
                        ->where('wa_pos_cash_sales.status', 'Completed');
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('csp.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('csp.verified', true)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getEbMain(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $verified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'te.reference',
                    'wa_pos_cash_sales.sales_no',
                    'csp.amount'
                )
                ->join('wa_pos_cash_sales', function ($join) use ($branchId, $fromDate, $endDate) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                        ->whereBetween('wa_pos_cash_sales.created_at', [$fromDate, $endDate])
                        ->where('wa_pos_cash_sales.branch_id', $branchId)
                        ->where('wa_pos_cash_sales.status', 'Completed');
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('csp.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 2);
                    // ->where('payment_methods.branch_id', $branchId);
                })
                ->where('csp.verified', true)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 2)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getVooma(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $verified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'te.reference',
                    'wa_pos_cash_sales.sales_no',
                    'csp.amount'
                )
                ->join('wa_pos_cash_sales', function ($join) use ($branchId, $fromDate, $endDate) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                        ->whereBetween('wa_pos_cash_sales.created_at', [$fromDate, $endDate])
                        ->where('wa_pos_cash_sales.branch_id', $branchId)
                        ->where('wa_pos_cash_sales.status', 'Completed');
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('csp.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%vooma%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('csp.verified', true)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%vooma%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getKcbMain(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $verified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'te.reference',
                    'wa_pos_cash_sales.sales_no',
                    'csp.amount'
                )
                ->join('wa_pos_cash_sales', function ($join) use ($branchId, $fromDate, $endDate) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                        ->whereBetween('wa_pos_cash_sales.created_at', [$fromDate, $endDate])
                        ->where('wa_pos_cash_sales.branch_id', $branchId)
                        ->where('wa_pos_cash_sales.status', 'Completed');
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('csp.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%COMMERCIAL%')
                        ->where('payment_methods.account_type', 2);
                    // ->where('payment_methods.branch_id', $branchId);
                })
                ->where('csp.verified', true)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%COMMERCIAL%')
                        ->where('payment_methods.account_type', 2)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getMpesa(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $verified = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'csp.created_at',
                    'te.reference',
                    'wa_pos_cash_sales.sales_no',
                    'csp.amount'
                )
                ->join('wa_pos_cash_sales', function ($join) use ($branchId, $fromDate, $endDate) {
                    $join->on('csp.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                        ->whereBetween('wa_pos_cash_sales.created_at', [$fromDate, $endDate])
                        ->where('wa_pos_cash_sales.branch_id', $branchId)
                        ->where('wa_pos_cash_sales.status', 'Completed');
                })
                ->join('wa_tender_entries as te', 'csp.wa_tender_entry_id', '=', 'te.id')
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('csp.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%mpesa%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('csp.verified', true)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%mpesa%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getReturns(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $returns = DB::table('wa_pos_cash_sales_items_return as rtns')
                ->select(
                    'rtns.return_grn',
                    'sales.sales_no',
                    'rtns.return_quantity as return_quantity',
                    'users.name as return_by',
                    DB::raw('(items.selling_price * rtns.return_quantity) as  amount'),
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'items.selling_price as selling_price',
                    'sales.created_at as sale_date',
                    'rtns.created_at as return_date',
                    'cashier.name as sale_cashier',
                )
                ->leftJoin('wa_pos_cash_sales_items as items', 'rtns.wa_pos_cash_sales_item_id', 'items.id')
                ->leftJoin('wa_pos_cash_sales as sales', 'sales.id', 'items.wa_pos_cash_sales_id')
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'items.wa_inventory_item_id')
                ->leftJoin('users', 'users.id', 'rtns.return_by')
                ->leftJoin('users as cashier', 'cashier.id', 'sales.attending_cashier')
                ->where('rtns.accepted', 1)
                ->where('sales.branch_id', $branchId)
                ->whereBetween('rtns.accepted_at', [$fromDate, $endDate])
                ->get()->map(function ($record) {
                    $record->formated_amount = manageAmountFormat($record->amount);
                    $record->selling_price = manageAmountFormat($record->selling_price);
                    return $record;
                });
            $returnsTotal = manageAmountFormat($returns->sum('amount'));

            return $this->jsonify(['returns' => $returns, 'returnsTotal' => $returnsTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function printCashSaleBankingOverview(Request $request)
    {
        try {
            $date = $request->date;
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branch = Restaurant::find($request->branch_id);
            $user = Auth::user();

            $sales = DB::table('wa_pos_cash_sales_items as items')
                ->select(
                    DB::raw("('$date') as date"),
                    DB::raw("(sum(items.selling_price * items.qty)) as cs"),
                    DB::raw("(sum(items.discount_amount)) as disc"),

                    DB::raw("(select coalesce(sum(selling_price * r.return_quantity), 0) from wa_pos_cash_sales_items_return as r
                        join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                        where r.accepted = 1 and (r.accepted_at between '$fromDate' and '$endDate')) as returns"),

                    DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $request->branch_id 
                        AND pos_cash_payments.status = 'Disbursed'
                        AND (pos_cash_payments.disbursed_at between '$fromDate' and '$endDate')
                    ) as expenses"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 13) as eazzy"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 10) as eb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 12) as vooma"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 9) as kcb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and (csp.payment_method_id = 3 or csp.payment_method_id = 15)) as mpesa"),

                    DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                        where (created_at between '$fromDate' and '$endDate')) as cdm"),

                    DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                        join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                        where (csp.created_at between '$fromDate' and '$endDate') and csp.verified = true) as verified"),

                    DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and (cd.created_at between '$fromDate' and '$endDate')
                            join users on cd.cashier_id = users.id and users.restaurant_id = $request->branch_id 
                            where bd.created_at > '$fromDate' and bd.manually_allocated = true) as allocated_cdms"),

                    DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where (cb.created_at between '$fromDate' and '$endDate') and branch_id = $request->branch_id) as allocated_cb")
                )
                ->join('wa_pos_cash_sales as sales', 'items.wa_pos_cash_sales_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$fromDate, $endDate])
                ->where('sales.branch_id', $request->branch_id)
                ->where('sales.status', 'Completed')
                ->first();

            $sales->sales = $sales->cs - $sales->disc;
            $sales->net_sales = $sales->sales - $sales->returns - $sales->expenses;
            $sales->total_bankings = $sales->eazzy + $sales->eb_main + $sales->vooma + $sales->kcb_main + $sales->mpesa + $sales->cdm;
            $sales->verified = $sales->verified + $sales->cdm;
            $sales->sales_variance = $sales->net_sales - $sales->verified;
            $sales->balance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            $sales->sales = manageAmountFormat($sales->sales);
            $sales->returns = manageAmountFormat($sales->returns);
            $sales->expenses = manageAmountFormat($sales->expenses);
            $sales->net_sales = manageAmountFormat($sales->net_sales);
            $sales->eazzy = manageAmountFormat($sales->eazzy);
            $sales->eb_main = manageAmountFormat($sales->eb_main);
            $sales->vooma = manageAmountFormat($sales->vooma);
            $sales->kcb_main = manageAmountFormat($sales->kcb_main);
            $sales->mpesa = manageAmountFormat($sales->mpesa);
            $sales->cdm = manageAmountFormat($sales->cdm);
            $sales->total_bankings = manageAmountFormat($sales->total_bankings);
            $sales->verified = manageAmountFormat($sales->verified);
            $sales->sales_variance = manageAmountFormat($sales->sales_variance);
            $sales->allocated_cdms = manageAmountFormat($sales->allocated_cdms);
            $sales->allocated_cb = manageAmountFormat($sales->allocated_cb);
            $sales->balance = manageAmountFormat($sales->balance);

            $branchId = $request->branch_id;
            $summary = DB::table('payment_methods as pm')
                ->select(
                    'pm.title as collection_account',
                    'sweep_account.title as sweep_account',

                    DB::raw("(select sum(csp.amount) from wa_pos_cash_sales_payments as csp 
                join wa_pos_cash_sales as cs on csp.wa_pos_cash_sales_id = cs.id 
                join payment_verification_banks as b on csp.bank_statement_id = b.id and date(csp.created_at) = date(b.bank_date)
                where (cs.created_at between '$fromDate' and '$endDate') 
                and cs.branch_id = $branchId 
                and csp.payment_method_id = pm.id 
                and csp.verified = true 
                and cs.status = 'Completed') as same_day_collections"),

                    DB::raw("(select sum(csp.amount) from wa_pos_cash_sales_payments as csp 
                join wa_pos_cash_sales as cs on csp.wa_pos_cash_sales_id = cs.id 
                join payment_verification_banks as b on csp.bank_statement_id = b.id and date(csp.created_at) != date(b.bank_date)
                where (cs.created_at between '$fromDate' and '$endDate') 
                and cs.branch_id = $branchId 
                and csp.payment_method_id = pm.id 
                and csp.verified = true 
                and cs.status = 'Completed') as late_utilizations"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                join wa_pos_cash_sales_payments as csp on payment_verification_banks.id = csp.bank_statement_id 
                and date(csp.created_at) != date(payment_verification_banks.bank_date)
                and payment_verification_banks.amount > 0
                and (bank_date between '$fromDate' and '$endDate')
                and payment_verification_banks.payment_method_id = pm.id) as utilized_unknowns"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                where payment_verification_banks.status = 'Pending' 
                and payment_verification_banks.amount > 0
                and (bank_date between '$fromDate' and '$endDate')
                and payment_verification_banks.payment_method_id = pm.id) as actual_unknowns"),

                    DB::raw("(select sum(amount) from payment_verification_banks 
                where amount < 0
                and (bank_date between '$fromDate' and '$endDate')
                and payment_method_id = pm.id) as sweep_total"),
                )
                ->join('payment_methods as sweep_account', 'pm.sweep_account_id', '=', 'sweep_account.id')
                ->where('pm.branch_id', $request->branch_id)
                ->where('pm.account_type', 1)
                ->get()
                ->map(function ($record) {
                    $record->formatted_same_day_collections = manageAmountFormat($record->same_day_collections);
                    $record->formatted_late_utilizations = manageAmountFormat($record->late_utilizations);

                    $record->total_collection = $record->same_day_collections + $record->late_utilizations;
                    $record->formatted_total_collection = manageAmountFormat($record->total_collection);

                    $record->formatted_utilized_unknowns = manageAmountFormat($record->utilized_unknowns);
                    $record->formatted_actual_unknowns = manageAmountFormat($record->actual_unknowns);

                    $record->total_unknowns = $record->utilized_unknowns + $record->actual_unknowns;
                    $record->formatted_total_unknowns = manageAmountFormat($record->total_unknowns);

                    $record->nominal_total = $record->same_day_collections + $record->total_unknowns;
                    $record->formatted_nominal_total = manageAmountFormat($record->nominal_total);

                    $record->formatted_sweep_total = manageAmountFormat(abs($record->sweep_total));
                    $record->variance = abs($record->sweep_total) - $record->nominal_total;
                    $record->formatted_variance = manageAmountFormat($record->variance);

                    return $record;
                });

            $cdmDeposits = DB::select("
                select bank_reference,bd.amount,bd.created_at as bd_time,cd.reference as cd_reference,bd.verified from banked_drop_transactions bd 
                join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and (cd.created_at between '$fromDate' and '$endDate')
                join users on cd.cashier_id = users.id and users.restaurant_id = $branchId 
                where bd.created_at > '$fromDate';
            ");

            $cdmDeposits = collect($cdmDeposits);

            $cdmTotal = manageAmountFormat($cdmDeposits->sum('amount'));

            $cdms = $cdmDeposits->map(function ($record) {
                // $record->amount = manageAmountFormat($record->amount);
                $record->status = $record->verified ? 'Verified' : 'Pending';
                return $record;
            });
            $cashBankings = DB::table('chief_cashier_declarations as cb')
                ->select(
                    'cb.*',
                    'statements.channel'
                )
                ->join('payment_verification_banks as statements', 'cb.bank_statement_id', '=', 'statements.id')
                ->whereBetween('cb.created_at', [$fromDate, $endDate])
                ->where('cb.branch_id', $branchId)
                ->where('banked_amount', '>', 0)
                ->get()
                ->map(function ($record) {
                    $record->formatted_banked_amount = manageAmountFormat($record->banked_amount);
                    return $record;
                });
            $cbTotal = manageAmountFormat($cashBankings->sum('amount'));

            $shortBankings = DB::table('short_bankings_comments as sb')
                ->select(
                    'sb.*',
                    'users.name'
                )
                ->join('users', 'users.id', '=', 'sb.created_by')
                ->whereDate('sb.sales_date', $date)
                ->where('sb.branch_id', $branchId)
                ->get();
            $shortBankingsTotal =  manageAmountFormat($shortBankings->sum('amount'));

            $pdf = Pdf::loadView('banking_approval.cash_banking_overview_pdf', compact('sales', 'user', 'branch', 'date', 'summary', 'cdmDeposits', 'cdmTotal', 'cashBankings', 'cbTotal', 'shortBankings', 'shortBankingsTotal'))->setPaper('a4', 'landscape');

            return $pdf->download('Cash_Sales_banking_overview' . $request->date . '.pdf');
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function allocateShortBanking(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $shortBankingComment = new ShortBankingComment();
            $shortBankingComment->created_by = $user->id;
            $shortBankingComment->amount = $request->amount;
            $shortBankingComment->sales_date = $request->date;
            $shortBankingComment->comment = $request->comment;
            $shortBankingComment->type = $request->type ?? '';
            $shortBankingComment->branch_id = $request->branch_id;
            $shortBankingComment->save();
            DB::commit();
            return response()->json(['message' => 'Payment Initiated Successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['error' => true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
        }
    }

    public function getShortBankingRecords(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->toDateString();
            $branchId = $request->branch_id;

            $records = DB::table('short_bankings_comments as sb')
                ->select(
                    'sb.*',
                    'users.name'
                )
                ->join('users', 'users.id', '=', 'sb.created_by')
                ->whereDate('sb.sales_date', $fromDate)
                ->where('sb.branch_id', $branchId)
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });
            $rawSbTotal = $records->sum('amount');
            $sbTotal = manageAmountFormat($rawSbTotal);

            return $this->jsonify(['records' => $records, 'sbTotal' => $sbTotal, 'rawSbTotal' => $rawSbTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getOpeningBalance(Request $request)
    {
        try {
            $date = $request->date;
            // $fromDate = Carbon::parse($request->date)->toDateString();
            $fromDate = Carbon::now()->toDateString();


            $sales = DB::table('wa_pos_cash_sales_items as items')
                ->select(
                    DB::raw("('$fromDate') as date"),
                    DB::raw("(sum(items.selling_price * items.qty)) as cs"),
                    DB::raw("(sum(items.discount_amount)) as disc"),

                    DB::raw("(select coalesce(sum(selling_price * r.return_quantity), 0) from wa_pos_cash_sales_items_return as r
                        join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                        where r.accepted = 1 and DATE(r.accepted_at) < '$fromDate'
                    ) as returns"),

                    //expenses
                    DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $request->branch_id 
                        AND pos_cash_payments.status = 'Disbursed'
                        AND DATE(pos_cash_payments.disbursed_at) < '$fromDate'
                    ) as expenses"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where DATE(sales.created_at) < '$fromDate'
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 13) as eazzy"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where DATE(sales.created_at) < '$fromDate'
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 10) as eb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where DATE(sales.created_at)  < '$fromDate'
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 12) as vooma"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where DATE(sales.created_at) < '$fromDate'
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 9) as kcb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where DATE(sales.created_at) < '$fromDate'
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and (csp.payment_method_id = 3 or csp.payment_method_id = 15)) as mpesa"),

                    DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                        where DATE(created_at) < '$fromDate'
                        ) as cdm"),

                    DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                        join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                        where DATE(csp.created_at)  < '$fromDate' and csp.verified = true) as verified"),

                    DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and DATE(cd.created_at) < '$fromDate'
                            join users on cd.cashier_id = users.id and users.restaurant_id = $request->branch_id 
                            where  bd.manually_allocated = true) as allocated_cdms"),

                    DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where DATE(cb.created_at) < '$fromDate' and branch_id = $request->branch_id) as allocated_cb")
                )
                ->join('wa_pos_cash_sales as sales', 'items.wa_pos_cash_sales_id', '=', 'sales.id')
                ->whereDate('sales.created_at', '<', $fromDate)
                ->where('sales.branch_id', $request->branch_id)
                ->where('sales.status', 'Completed')
                ->first();

            $sales->sales = $sales->cs - $sales->disc;
            $sales->net_sales = $sales->sales - $sales->returns - $sales->expenses;
            $sales->total_bankings = $sales->eazzy + $sales->eb_main + $sales->vooma + $sales->kcb_main + $sales->mpesa + $sales->cdm;

            $sales->verified = $sales->verified + $sales->cdm;
            $sales->sales_variance = $sales->net_sales - $sales->verified;
            $sales->balance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            $todaysBalance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            $formattedOpeningBalance = manageAmountFormat($todaysBalance);

            return $this->jsonify(['balance' => $todaysBalance, 'formattedOpeningBalance'  => $formattedOpeningBalance]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function shortBankingDetails(Request $request): View|RedirectResponse
    {

        if (!can('see-overview-pos', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }
        $title = 'Cash Sales Banking Overview';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];

        $branches = Restaurant::select('id', 'name')->get();
        $branch = Restaurant::find($request->branch_id);


        return view('banking_approval.short_bankings_comments', compact('title', 'model', 'branches', 'breadcrum', 'branch'));
    }

    public function  getDailyRecordsBalances(Request $request)
    {
        $fromDate = Carbon::now()->toDateString();
        // $endDate = Carbon::parse($request->to_date)->endOfDay();
        $branchId = $request->branch_id;

        $records =  DB::table('wa_pos_cash_sales_items as items')
            ->select(
                DB::raw("(DATE(sales.created_at)) as date"),
                //sales
                DB::raw("SUM((items.qty * items.selling_price)  -  items.discount_amount) as sales"),
                //returns
                DB::raw("(
                        SELECT COALESCE(SUM(r.return_quantity * return_sales.selling_price), 0) 
                        FROM wa_pos_cash_sales_items_return as r
                        JOIN wa_pos_cash_sales_items as return_sales 
                            ON r.wa_pos_cash_sales_item_id = return_sales.id 
                        JOIN wa_pos_cash_sales ON wa_pos_cash_sales.id = r.wa_pos_cash_sales_id
                        WHERE 
                            r.accepted = 1
                            AND DATE(r.accepted_at) = DATE(sales.created_at)
                            AND wa_pos_cash_sales.branch_id = $branchId
                            AND wa_pos_cash_sales.status = 'Completed'
                    ) as returns"),

                //expenses
                DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $branchId
                        AND pos_cash_payments.status = 'Disbursed'
                        AND DATE(pos_cash_payments.disbursed_at) = DATE(sales.created_at)
                    ) as expenses"),

                //eazzy
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 13
                    ) as eazzy"),

                //eb_main
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 10
                    ) as eb_main"),

                //vooma
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 12
                    ) as vooma"),

                //kcb_main
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and csp.payment_method_id = 9
                    ) as kcb_main"),

                //mpesa
                DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales on csp.wa_pos_cash_sales_id = wa_pos_cash_sales.id 
                        where 
                        wa_pos_cash_sales.branch_id = $branchId 
                        AND DATE(wa_pos_cash_sales.created_at) = DATE(sales.created_at)
                        and wa_pos_cash_sales.status = 'Completed'
                        and (csp.payment_method_id = 3 OR csp.payment_method_id = 15)
                    ) as mpesa"),

                //notified CDMs
                DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                    where DATE(cash_drop_transactions.created_at) = DATE(sales.created_at)
                    ) as cdm"),

                //verified
                DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                            join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                            where DATE(csp.created_at) = DATE(sales.created_at) 
                            and csp.verified = true
                    ) as verified"),

                //allocated_cdms
                DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id 
                            and DATE(cd.created_at) = DATE(sales.created_at)
                            join users on cd.cashier_id = users.id and users.restaurant_id = $branchId 
                            where DATE(bd.created_at) > DATE(sales.created_at) and bd.manually_allocated = true
                    ) as allocated_cdms"),

                // allocated cash banking
                DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where DATE(cb.created_at) = DATE(sales.created_at)
                        AND branch_id = $branchId
                    ) as allocated_cb"),


                //Balance Allocations
                DB::raw("(select coalesce(sum(sbc.amount), 0)
                    FROM short_bankings_comments as sbc
                    WHERE sbc.branch_id = $branchId
                    AND DATE(sbc.sales_date) = DATE(sales.created_at)
                ) as short_bankings")


            )
            ->leftJoin('wa_pos_cash_sales as sales', 'sales.id', 'items.wa_pos_cash_sales_id')
            ->where('sales.status', 'Completed')
            ->where('sales.branch_id', $branchId)
            ->whereDate('sales.created_at', '<', $fromDate)
            ->groupBy(DB::raw("DATE(sales.created_at)"))
            ->orderBy('items.created_at', 'ASC')
            ->get()->map(function ($record) use ($branchId) {
                $record->sales = $record->sales ?? 0;
                $record->returns = $record->returns ?? 0;
                $record->expenses = $record->expenses ?? 0;
                $record->net_sales = $record->sales  - $record->returns - $record->expenses;
                $record->eazzy = $record->eazzy ?? 0;
                $record->eb_main = $record->eb_main ?? 0;
                $record->vooma = $record->vooma ?? 0;
                $record->kcb_main = $record->kcb_main ?? 0;
                $record->mpesa = $record->mpesa ?? 0;
                $record->cdm = $record->cdm ?? 0;
                $record->total_bankings = $record->eazzy + $record->eb_main + $record->vooma + $record->kcb_main + $record->mpesa + $record->cdm;
                $record->verified = ($record->verified ?? 0) + ($record->cdm ?? 0);
                $record->sales_variance =  $record->net_sales - $record->verified;
                $record->allocated_cdms = $record->allocated_cdms ?? 0;
                $record->allocated_cb = $record->allocated_cb ?? 0;
                $record->balance = $record->sales_variance - $record->allocated_cdms - $record->allocated_cb;
                $record->branch = $branchId;
                $record->short_bankings = $record->short_bankings ?? 0;
                $record->balance_variance = $record->balance - $record->short_bankings;

                return $record;
            });


        $records = $records->map(function ($record) {
            try {
                $record->formatted_sales = manageAmountFormat($record->sales);
                $record->formatted_returns = manageAmountFormat($record->returns);
                $record->formatted_expenses = manageAmountFormat($record->expenses);
                $record->formatted_net_sales = manageAmountFormat($record->net_sales);
                $record->formatted_eazzy = manageAmountFormat($record->eazzy);
                $record->formatted_eb_main = manageAmountFormat($record->eb_main);
                $record->formatted_vooma = manageAmountFormat($record->vooma);
                $record->formatted_kcb_main = manageAmountFormat($record->kcb_main);
                $record->formatted_mpesa = manageAmountFormat($record->mpesa);
                $record->formatted_cdm = manageAmountFormat($record->cdm);
                $record->formatted_total_bankings = manageAmountFormat($record->total_bankings);
                $record->formatted_verified = manageAmountFormat($record->verified);
                $record->formatted_sales_variance = manageAmountFormat($record->sales_variance);
                $record->formatted_allocated_cdms = manageAmountFormat($record->allocated_cdms);
                $record->formatted_allocated_cb = manageAmountFormat($record->allocated_cb);
                $record->formatted_balance = manageAmountFormat($record->balance);
                $record->formatted_short_bankings = manageAmountFormat($record->short_bankings);
                $record->formatted_balance_variance = manageAmountFormat($record->balance_variance);
            } catch (\Throwable $th) {
                $record['formatted_sales'] = manageAmountFormat($record['sales']);
            }

            return $record;
        });

        $totalSales = $records->sum('sales');
        $totals = [
            'sales' => manageAmountFormat($totalSales),
            'returns' => manageAmountFormat($records->sum('returns')),
            'expenses' => manageAmountFormat($records->sum('expenses')),
            'net_sales' => manageAmountFormat($records->sum('net_sales')),
            'eazzy' => manageAmountFormat($records->sum('eazzy')),
            'eb_main' => manageAmountFormat($records->sum('eb_main')),
            'vooma' => manageAmountFormat($records->sum('vooma')),
            'kcb_main' => manageAmountFormat($records->sum('kcb_main')),
            'mpesa' => manageAmountFormat($records->sum('mpesa')),
            'cdm' => manageAmountFormat($records->sum('cdm')),
            'total_bankings' => manageAmountFormat($records->sum('total_bankings')),
            'verified' => manageAmountFormat($records->sum('verified')),
            'sales_variance' => manageAmountFormat($records->sum('sales_variance')),
            'allocated_cdms' => manageAmountFormat($records->sum('allocated_cdms')),
            'allocated_cb' => manageAmountFormat($records->sum('allocated_cb')),
            'balance' => manageAmountFormat($records->sum('balance')),
            'total_returns' => manageAmountFormat($records->sum('returns')),
            'total_short_bankings' => manageAmountFormat($records->sum('short_bankings')),
            'balance_variance' => manageAmountFormat($records->sum('balance_variance'))

        ];
        return $this->jsonify(['records' => $records, 'totals' => $totals]);
    }

    public function shortBankingDetailsBreakdown(Request $request)
    {
        $date = $request->input('date');
        $branchId = $request->input('branch_id');

        $breakdown = DB::table('short_bankings_comments as sb')
            ->select(
                'sb.*',
                'users.name'
            )
            ->join('users', 'users.id', '=', 'sb.created_by')
            ->whereDate('sb.sales_date', $date)
            ->where('sb.branch_id', $branchId)
            ->get()
            ->map(function ($record) {
                $record->formatted_amount = manageAmountFormat($record->amount);
                return $record;
            });
        $breakdownTotal = $breakdown->sum('amount');

        return response()->json(['breakdown' => $breakdown, 'breakdownTotal' => $breakdownTotal]);
    }

    public function getShortBankingComment(Request $request)
    {
        try {
            $sbComment = DB::table('short_bankings_comments')->where('id', $request->id)->first();
            return response()->json($sbComment);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function editShortBankingComment(Request $request)
    {
        try {
            $shortBankingComment =  ShortBankingComment::find($request->id);
            $shortBankingComment->amount = $request->amount;
            $shortBankingComment->comment = $request->comment;
            $shortBankingComment->type = $request->type ?? '';
            $shortBankingComment->save();
            return response()->json(['message' => 'Record Edited Successfully'], 200);
        } catch (\Throwable $th) {
            return $this->jsonify(['error' => true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
        }
    }
    public function getManualAllocations(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->date)->endOfDay();
            $branchId = $request->branch_id;

            $records = DB::table('wa_pos_cash_sales_payments as csp')
                ->select(
                    'sale.sales_no',
                    'csp.amount',
                    'users.name as cashier',
                    'tender.reference',
                    'tender.channel',
                    'tender.document_no as receipt_no',
                    'csp.remarks as comment'

                )
                ->leftJoin('wa_pos_cash_sales as sale', 'sale.id', 'csp.wa_pos_cash_sales_id')
                ->leftJoin('wa_tender_entries as tender', 'tender.id', 'csp.wa_tender_entry_id')
                ->leftJoin('users', 'users.id', 'sale.attending_cashier')
                ->where('sale.status', 'Completed')
                ->whereBetween('sale.created_at', [$fromDate, $endDate])
                ->where('sale.branch_id', $branchId)
                ->where('csp.remarks', 'like', 'Manual%')
                ->get()->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });
            $manualAllocationsTotal = manageAmountFormat($records->sum('amount'));


            return $this->jsonify(['records' => $records, 'manualAllocationsTotal' => $manualAllocationsTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function approveAndCloseBanking(Request $request)
    {
        try {
            $bankingRecord = BankingApproval::find($request->banking_record_id);
            $bankingRecord = $bankingRecord->update([
                'stage' => 2,
                'closed' => true,
                'closed_by' => $request->user_id
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function completeVerification(Request $request)
    {
        try {
            $bankingRecord = BankingApproval::find($request->banking_record_id);
            $bankingRecord = $bankingRecord->update([
                'stage' => 2,
                'verified' => true,
                'verified_by' => $request->user_id
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
