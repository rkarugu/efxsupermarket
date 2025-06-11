<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TurnOverPurchases extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.dashboard.turn-over-purchases');
    }
}
