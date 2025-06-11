<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerBalancesReportController extends Controller
{
    public function generate(Request $request)
    {
        $title = 'Customer Balance Report';
        $model = 'customer-balances-report';
        $breadcum = ['Sales & Receivable Reports' => '', $title => ''];
        $user = getLoggeduserProfile();

        $branches = DB::table('restaurants')->select('id', 'name')->get();

        $records = [];
        if ($request->intent) {
        //    $routes = DB::table('routes')->where('is_physical_route', 1)->where('restaurant_id', $request->branch_id)->get();
            // $routes = DB::table('routes')->where('restaurant_id', $request->branch_id)->get();
            // foreach ($routes as $route) {
            //     if ($route) {
            //         $customer = WaCustomer::where('route_id', $route->id)->first();
            //         if ($customer) {
            //             $balanceBf = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '<', $request->date)
            //             ->sum('amount');

            //             $debits = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)
            //                 ->where('amount', '>', 0)
            //                 ->sum('amount');

            //             $credits = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)
            //                 ->where('amount', '<', 0)
            //                 ->sum('amount');

            //             $lastTrans = WaDebtorTran::latest()->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)->first();

            //             $records[] = [
            //                 'customer' => $customer->customer_name,
            //                 'balance_bf' => $balanceBf,
            //                 'debits' => $debits,
            //                 'credits' => $credits,
            //                 'last_trans_time' => $lastTrans ? Carbon::parse($lastTrans->created_at)->format('d/m/Y H:i:s') : '-',
            //                 'pd_cheques' => 0,
            //                 'balance' => ($balanceBf + $debits) + $credits,
            //             ];
            //         }
                    
            //     }
                
            // }

            $customers = WaCustomer::join('routes', 'routes.id', 'wa_customers.route_id')
                        ->select('wa_customers.id','wa_customers.customer_name')
                        ->when($request->filled('branch_id'), function ($query) use ($request) {
                            $query->where('restaurant_id', $request->branch_id);
                        })->get();
            
            foreach ($customers as $customer) {
                        $balanceBf = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '<', $request->date)
                        ->sum('amount');

                        $debits = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)
                            ->where('amount', '>', 0)
                            ->sum('amount');

                        $credits = DB::table('wa_debtor_trans')->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)
                            ->where('amount', '<', 0)
                            ->sum('amount');

                        $lastTrans = WaDebtorTran::latest()->where('wa_customer_id', $customer->id)->whereDate('created_at', '=', $request->date)->first();

                        $records[] = [
                            'customer' => $customer->customer_name,
                            'balance_bf' => $balanceBf,
                            'debits' => $debits,
                            'credits' => $credits,
                            'last_trans_time' => $lastTrans ? Carbon::parse($lastTrans->created_at)->format('d/m/Y H:i:s') : '-',
                            'pd_cheques' => 0,
                            'balance' => ($balanceBf + $debits) + $credits,
                        ];   
            }

            $records = collect($records)->sortBy('balance', descending: true)->all();

            if ($request->intent == 'PDF') {
                $branch = Restaurant::find($request->branch_id);
                $date = Carbon::parse($request->date)->format('d/m/Y');
                $report_name = "CUSTOMER_BALANCE_REPORT-$branch->name-" . $date;
                $pdf = PDF::loadView('admin.salesreceiablesreports.customer_balances_pdf', compact('records', 'branch', 'date'));
                return $pdf->download($report_name . '.pdf');
            }
        }

        return view('admin.salesreceiablesreports.customer_balances', compact('title', 'model', 'breadcum', 'branches', 'records'));
    }
}
