<?php

namespace App\View\Components\ItemCentre;

use App\Model\WaSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PriceChangeHistory extends Component
{
    public function __construct(
        protected int $itemId
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.item-centre.price-change-history', [
            'suppliers' => WaSupplier::whereHas('products', function($query){
                $query->where('wa_inventory_items.id', $this->itemId);
            })->get(),
            'item' => $this->itemId,
        ]);
    }
}
