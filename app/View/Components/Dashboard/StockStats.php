<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StockStats extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.dashboard.stock-stats', [
            'from' => now()->subDays(30)->toDateString(),
            'to' => now()->toDateString(),
        ]);
    }    
}
