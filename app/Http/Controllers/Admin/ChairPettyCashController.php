<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use App\Models\TravelExpenseTransaction;
use Illuminate\Support\Facades\DB;

class ChairPettyCashController extends Controller
{

    protected $title;
    protected $model;
    protected $pmodel;

    public function __construct()
    {
        $this->title = 'Petty Cash Reports';
        $this->model = 'chair-petty-cash-report';
        $this->pmodel = 'chair-petty-cash-report';
    }

    public function index()
    {

        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', '0');

        $title = $this->title;
        $model = $this->model;
        $pmodel = $this->pmodel;

        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
        $currentMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');

        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d 00:00:00');
        $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d 23:59:59');

        $previousMonthStart = Carbon::now()->subMonthsNoOverflow(2)->startOfMonth()->format('Y-m-d 00:00:00');
        $previousMonthEnd = Carbon::now()->subMonthsNoOverflow(2)->endOfMonth()->format('Y-m-d 23:59:59');

        $currentYear = Carbon::now()->year;
        $currentMonth = \Carbon\Carbon::now()->month;

        $startDate = Carbon::create($currentYear, 3, 1)->startOfDay();

        $yearStart = Carbon::create($currentYear, 1, 1)->startOfDay();

        $selected_month = request()->input('month', Carbon::now()->format('n'));
        $selected_year = request()->input('year', Carbon::now()->year);

        $selected_month_start = Carbon::create($selected_year, $selected_month, 1)->startOfMonth()->format('Y-m-d 00:00:00');
        $selected_month_end = Carbon::create($selected_year, $selected_month, 1)->endOfMonth()->format('Y-m-d 23:59:59');


        $currentmonthpettycash = [];
        $lastmonthpettycash = [];
        $previousmonthpettycash = [];

        // Current, last and previous months data start

        $previous_month_pettycash = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->get();

        $last_month_pettycash = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->get();

        $current_month_pettycash = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();

        $previous_month_pettycash_amount = $previous_month_pettycash->sum('amount');
        $last_month_pettycash_amount = $last_month_pettycash->sum('amount');
        $current_month_pettycash_amount = $current_month_pettycash->sum('amount');

        // Current, last and previous months data end


        // Year-to-date data start

        $year_to_date_pettycash = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->whereBetween('created_at', [$yearStart, Carbon::now()])
            ->get();

        $year_to_date_pettycash_amount = $year_to_date_pettycash->sum('amount');

        // Year-to-date data end


        // Daily data start
        $total_counts_query = TravelExpenseTransaction::select(
            DB::raw("DATE(travel_expense_transactions.created_at) as date"),
            DB::raw("SUM(CASE WHEN travel_expense_transactions.shift_type = 'order_taking' THEN 1 ELSE 0 END) as order_taking_count"),
            DB::raw("SUM(CASE WHEN travel_expense_transactions.shift_type = 'delivery' THEN 1 ELSE 0 END) as delivery_count")
        );

        $amount_counts_query = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->with('child', 'user');

        // return request()->all();

        if (request()->has('month') && request()->has('year')) {
            $total_counts_query->whereBetween('travel_expense_transactions.created_at', [$selected_month_start, $selected_month_end]);
            $amount_counts_query->whereBetween('created_at', [$selected_month_start, $selected_month_end]);
        } else {
            // $total_counts_query->where('travel_expense_transactions.created_at', '>=', $startDate);
            // $amount_counts_query->where('created_at', '>=', $startDate);
            $total_counts_query->whereBetween('travel_expense_transactions.created_at', [$currentMonthStart, $currentMonthEnd]);
            $amount_counts_query->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
        }

        $total_counts = $total_counts_query->groupBy(DB::raw("DATE(travel_expense_transactions.created_at)"))->get();
        $amount_counts = $amount_counts_query->get()->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function ($transactions) {
            $daily_totals = $transactions->groupBy(function ($transaction) {
                return $transaction?->user?->role_id == 4 ? 'order_taking' : 'delivery';
            })->map(function ($role_transactions) {
                $total_amount = $role_transactions->sum('amount');
                return [
                    'count' => $role_transactions->count(),
                    'total_amount' => $total_amount,
                ];
            });

            return $daily_totals;
        });

        $amount_counts_array = $amount_counts->toArray();
        $merged_data = [];
        $grand_totals = [];

        foreach ($total_counts as $count) {
            $date = $count['date'];

            $order_taking_data = [
                'count' => $count['order_taking_count'],
                'total_amount' => 0,
            ];
            $delivery_data = [
                'count' => $count['delivery_count'],
                'total_amount' => 0,
            ];

            if (isset($amount_counts_array[$date])) {
                if (isset($amount_counts_array[$date]['order_taking'])) {
                    $order_taking_data['total_amount'] = $amount_counts_array[$date]['order_taking']['total_amount'];
                }
                if (isset($amount_counts_array[$date]['delivery'])) {
                    $delivery_data['total_amount'] = $amount_counts_array[$date]['delivery']['total_amount'];
                }
            }

            $merged_data[$date] = [
                'order_taking' => $order_taking_data,
                'delivery' => $delivery_data,
            ];
        }

        // Daily data end


        // Monthly data start

        $monthly_counts = PettyCashTransaction::where('amount', '>', 0)
            ->whereHas('child', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->with('child', 'user')
            ->get()
            ->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m');
            })
            ->map(function ($transactions) {
                $monthly_totals = $transactions->groupBy(function ($transaction) {
                    return $transaction?->user?->role_id == 4 ? 'order_taking' : 'delivery';
                })->map(function ($role_transactions) {
                    $total_amount = $role_transactions->sum('amount');
                    return [
                        'count' => $role_transactions->count(),
                        'total_amount' => $total_amount,
                    ];
                });

                return $monthly_totals;
            });

        // Monthly data end

        return view('admin.page.dashboards.petty_cash_reports.index', compact(
            'title',
            'model',
            'pmodel',
            'previous_month_pettycash_amount',
            'last_month_pettycash_amount',
            'current_month_pettycash_amount',
            'merged_data',
            'grand_totals',
            'monthly_counts',
            'grand_totals',
            'year_to_date_pettycash_amount',
            'selected_month'
        ));
    }
}
