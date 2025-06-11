<?php

namespace App\View\Components\PoscashSales\Cashiermanagement;

use App\Model\PaymentMethod;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesItemReturns;
use App\Model\WaPosCashSalesPayments;
use App\PaymentProvider;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class CashierDeclaration extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public User $cashier)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $today =$request->date ??  today();


        $total_drops = DB::table('cash_drop_transactions')
            ->where('cashier_id', $this->cashier->id)
            ->whereDate('created_at', $today)
            ->sum('amount');


        $start = today()->startOfDay();
        $end = today()->endOfDay();
      $payments =   WaPosCashSalesPayments::select(
            'payment_methods.id as method_id',
            'payment_methods.title as payment_method',
            DB::raw('SUM(wa_pos_cash_sales_payments.amount) as total_amount')
        )
            ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', '=', 'wa_pos_cash_sales_payments.wa_pos_cash_sales_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'wa_pos_cash_sales_payments.payment_method_id')
            ->where('wa_pos_cash_sales.attending_cashier', $this->cashier->id)
            ->where('wa_pos_cash_sales.status', 'Completed')
            ->where('payment_methods.use_in_pos', true)
            ->where('payment_methods.is_cash', false)
            ->whereBetween(DB::raw('DATE(wa_pos_cash_sales.created_at)'), [$start, $end])
            ->groupBy('payment_methods.id', 'payment_methods.title')
            ->orderBy('payment_methods.title')
            ->get()
          ->mapWithKeys(function($payment) {
              return [$payment->method_id => $payment->total_amount];
          })
          ->toArray();

        $payMethods = PaymentMethod::where('use_in_pos', true)->where('is_cash', false)->get();

        $all_orders = WaPosCashSales::where('attending_cashier', $this->cashier->id)
            ->where('status', 'Completed')
            ->whereDate('created_at', today())
            ->get();





        $orders = WaPosCashSalesItemReturns::whereDate('accepted_at', $today)
            ->with('PosCashSale')
            ->whereHas('PosCashSale', function ($q) {
                $q->where('attending_cashier',  $this->cashier->id);
            })
            ->get()
            ->pluck('PosCashSale')
            ->unique();
        $total_returns = $orders->sum->acceptedReturnsTotal;

        $totalCash = DB::table('wa_pos_cash_sales_payments')
            ->join('payment_methods', 'wa_pos_cash_sales_payments.payment_method_id', '=', 'payment_methods.id')
            ->whereIn('wa_pos_cash_sales_payments.wa_pos_cash_sales_id', $orders->pluck('id'))
            ->where('payment_methods.is_cash', true)
            ->sum('wa_pos_cash_sales_payments.amount');


        $paymentIds = PaymentMethod::where('use_in_pos', true)
            ->where('is_cash', true)
            ->pluck('id')
            ->toArray();
        $cash_orders = WaPosCashSalesPayments::with('PosCashSale')
            ->whereHas('PosCashSale', function ($q) use ($today) {
                $q->where('attending_cashier', $this->cashier->id)
                    ->whereDate('created_at', $today)
                    ->where('status', 'Completed');
            })
            ->whereIn('payment_method_id', $paymentIds);
        $cashSales = $cash_orders->sum('amount');

        $total_sales =ceil( $all_orders->sum('totalBeforeReturn'));
        $returns = ceil($total_returns);
        $inv = 0;
        $sales = $total_sales - $returns;
        $petty_cash = 0;
//        $net_amount = $cashSales - ($returns + $total_drops);
         $net_amount = $this->cashier->cashAtHand();
        return view('components.poscash-sales.cashiermanagement.cashier-declaration', compact(
            'net_amount',
            'total_sales',
            'totalCash',
            'returns',
            'inv',
            'sales',
            'petty_cash',
            'total_drops',
            'payMethods',
            'payments'
        ));
    }
}
