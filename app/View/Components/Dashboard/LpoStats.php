<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LpoStats extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.dashboard.lpo-stats');
    }
}
