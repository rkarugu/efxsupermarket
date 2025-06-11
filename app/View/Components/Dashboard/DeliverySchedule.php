<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeliverySchedule extends Component
{    public function render(): View|Closure|string
    {
        return view('components.dashboard.delivery-schedule');
    }
}
