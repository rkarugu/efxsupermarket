<?php

namespace App\View\Components\Dashboard;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaStockMove;
use App\Model\WaUserSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class TopStockValue extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.dashboard.top-stock-value');
    }
}
