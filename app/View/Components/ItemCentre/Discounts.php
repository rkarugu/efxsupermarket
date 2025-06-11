<?php

namespace App\View\Components\ItemCentre;

use App\DiscountBand;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Discounts extends Component
{

    public function __construct(
        public int $itemId
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.item-centre.discounts', [
            'discountBands' => DiscountBand::where('inventory_item_id', $this->itemId)
                ->get()
        ]);
    }
}
