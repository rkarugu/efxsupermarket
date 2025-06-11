<?php

namespace App\View\Components\Dashboard;

use App\Model\WaGrn;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaStockMove;
use App\Model\WaUserSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class PurchasesVsSales extends Component
{
    protected $months = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ];

    public function render(): View|Closure|string
    {
        return view('components.dashboard.purchases-vs-sales', [
            'months' => collect($this->months)
        ]);
    }
}
