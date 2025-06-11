<?php

namespace App\View\Components\Dashboard;

use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Model\WaUserSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class SupplierBalances extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.dashboard.supplier-balances');
    }
}
