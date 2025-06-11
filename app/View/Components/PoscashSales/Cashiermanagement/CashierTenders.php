<?php

namespace App\View\Components\PoscashSales\Cashiermanagement;

use App\User;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class CashierTenders extends Component
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
        $paymentMethods = getPaymentmeList();
        return view('components.poscash-sales.cashiermanagement.cashier-tenders',compact('paymentMethods'));
    }
}
