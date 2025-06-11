<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtorsReportController extends Controller
{
    public function index()
    {
        if (!can('debtors-report', 'sales-and-receivables-reports')) {
            return returnAccessDeniedPage();
        }

        $title = 'Debtors Report';
        $model = 'sales-and-receivables-reports';
        $breadcrum = ['Sales & Receivables' => '', 'Reports' => '', 'Debtors Report' => ''];

        $branches = Restaurant::select('id', 'name')->get();

        return view('sales_and_receivable_reports.debtors_report', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function generate(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->from_date)->endOfDay();

            $yFromDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $yToDate = Carbon::parse($request->from_date)->subDay()->endOfDay();

            // $ySalesQuery = DB::table('wa_internal_requisition_items')
            //     ->select(
            //         'routes.id as route_id',
            //         DB::raw("(sum(total_cost_with_vat)) as total")
            //     )
            //     ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            //     ->join('routes', function ($join) use ($request) {
            //         $join->on('wa_internal_requisitions.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
            //     })
            //     ->whereBetween('wa_internal_requisition_items.created_at', [$yFromDate, $yToDate])
            //     ->groupBy('route_id')
            //     ->get();
            $ySalesQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$yFromDate, $yToDate])
                ->where('document_no', 'like', 'INV%')
                ->whereNotNull('wa_sales_invoice_id')
                // ->where('reference', 'like', '%- INV%')
                ->groupBy('route_id')
                ->get();

            $todaySalesQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('reference', 'like', '%- INV%')
                ->groupBy('route_id')
                ->get();

            $closingBalancesQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereDate('wa_debtor_trans.trans_date', '<', $fromDate)
                ->groupBy('route_id')
                ->get();

            // $todaySalesQuery = DB::table('wa_internal_requisition_items')
            //     ->select(
            //         'routes.id as route_id',
            //         DB::raw("(sum(total_cost_with_vat)) as total")
            //     )
            //     ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            //     ->join('routes', function ($join) use ($request) {
            //         $join->on('wa_internal_requisitions.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
            //     })
            //     ->whereBetween('wa_internal_requisition_items.created_at', [$fromDate, $toDate])
            //     ->groupBy('route_id')
            //     ->get();

            $todayPaymentsQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('document_no', 'like', 'RCT%')
                ->groupBy('route_id')
                ->get();

            $todayReturnsQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('wa_debtor_trans.amount', '<', 0)
                ->where('document_no', 'like', 'RTN%')
                ->groupBy('route_id')
                ->get();

            $todayDiscountReturnsQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('document_no', 'like', 'RTN%')
                ->where('amount', '>', 0)
                ->groupBy('route_id')
                ->get();

            $todayDiscountReturnsRepostsQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('document_no', 'like', 'INV%')
                ->whereNull('wa_sales_invoice_id')
                // ->where('reference', 'not like', '%- INV%')
                ->groupBy('route_id')
                ->get();

            $todayFraudQuery = DB::table('wa_debtor_trans')
                ->select(
                    'routes.id as route_id',
                    DB::raw("(sum(amount)) as total")
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('document_no', 'like', 'FJ%')
                ->groupBy('route_id')
                ->get();

            // $todayCreditsQuery = DB::table('wa_debtor_trans')
            // ->select(
            //     'routes.id as route_id',
            //     DB::raw("(sum(amount)) as total")
            // )
            // ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            // ->join('routes', function ($join) use ($request) {
            //     $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
            // })
            // ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
            // ->where('wa_debtor_trans.amount', '<', 0)
            // ->groupBy('route_id')
            // ->get();

            // $todayDebitsQuery = DB::table('wa_debtor_trans')
            //     ->select(
            //         'routes.id as route_id',
            //         DB::raw("(sum(amount)) as total")
            //     )
            //     ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            //     ->join('routes', function ($join) use ($request) {
            //         $join->on('wa_customers.route_id', '=', 'routes.id')->where('routes.restaurant_id', $request->branch_id)->where('is_physical_route', 1);
            //     })
            //     ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
            //     ->where('wa_debtor_trans.amount', '>', 0)
            //     ->groupBy('route_id')
            //     ->get();

            $routes = DB::table('wa_customers')
                ->select('wa_customers.id as customer_id', 'routes.id as route_id', 'routes.route_name')
                ->join('routes', function ($join) use ($request) {
                    $join->on('wa_customers.route_id', '=', 'routes.id')->where('restaurant_id', $request->branch_id)->where('is_physical_route', 1);
                })
                ->get();

            $report = [];

            $totalYSales = 0;
            $totalBf = 0;
            $totalTodaySales = 0;
            $totalTotal = 0;
            $totalCredits = 0;
            $totalPayments = 0;
            $totalReturns = 0;
            $totalDiscountReturns = 0;
            $totalCf = 0;
            $totalFraud = 0;

            foreach ($routes as $route) {
                $ySales = $ySalesQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $totalYSales += $ySales;

                $openingBalance = $closingBalancesQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $bf = $openingBalance - $ySales;
                $totalBf += $bf;

                $totalBalance = $bf + $ySales;
                $totalTotal += $totalBalance;

                $todayPayments = $todayPaymentsQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $totalPayments += $todayPayments;

                $todayReturns = $todayReturnsQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $totalReturns += $todayReturns;

                $todayFraud = $todayFraudQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $totalFraud+= $todayFraud;

                $todayCredits = $todayPayments + $todayReturns;
                $totalCredits += $todayCredits;

                $todayDiscountReturns = $todayDiscountReturnsQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $todayDiscountReturnReposts = $todayDiscountReturnsRepostsQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $todayDiscountReturns = $todayDiscountReturns - abs($todayDiscountReturnReposts);
                $totalDiscountReturns += $todayDiscountReturns;

                $todaySales = $todaySalesQuery->where('route_id', $route->route_id)->first()?->total ?? 0;
                $totalTodaySales += $todaySales;

                $cf = $totalBalance - abs($todayCredits) + $todayDiscountReturns - abs($todayFraud);
                $totalCf += $cf;

                $record = [
                    'route_id' => $route->route_id,
                    'account_id' => $route->customer_id,
                    'account_name' => $route->route_name,
                    'bf' => manageAmountFormat($bf),
                    'ysales' => manageAmountFormat($ySales),
                    'total_balance' => manageAmountFormat($totalBalance),
                    'payments' => manageAmountFormat($todayPayments),
                    'rtns' => manageAmountFormat($todayReturns),
                    'today_credits' => manageAmountFormat($todayCredits),
                    'discount_returns' => manageAmountFormat($todayDiscountReturns),
                    'today_sales' => manageAmountFormat($todaySales),
                    'fraud' => manageAmountFormat($todayFraud),
                    'cf' => manageAmountFormat($cf),


                ];

                $report[] = $record;
            }

            $report[] = [
                'route_id' => 0,
                'account_id' => 0,
                'account_name' => 'TOTALS',
                'bf' => manageAmountFormat($totalBf),
                'ysales' => manageAmountFormat($totalYSales),
                'total_balance' => manageAmountFormat($totalTotal),
                'payments' => manageAmountFormat($totalPayments),
                'rtns' => manageAmountFormat($totalReturns),
                'today_credits' => manageAmountFormat($totalCredits),
                'discount_returns' => manageAmountFormat($totalDiscountReturns),
                'today_sales' => manageAmountFormat($totalTodaySales),
                'fraud' => manageAmountFormat($totalFraud),
                'cf' => manageAmountFormat($totalCf),
            ];

            if ($request->intent == 'pdf') {
                $pdf = Pdf::loadView('sales_and_receivable_reports.debtors_report_pdf', ['report' => $report, 'day' => $request->from_date]);

                return $pdf->download("debtors_report_{$request->from_date}.pdf");
            }

            return $this->jsonify($report);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
