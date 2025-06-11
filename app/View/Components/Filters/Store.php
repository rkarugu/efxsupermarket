<?php

namespace App\View\Components\Filters;

use App\Model\WaLocationAndStore;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Store extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.filters.store', [
            'stores' => WaLocationAndStore::get(),
        ]);
    }
}
