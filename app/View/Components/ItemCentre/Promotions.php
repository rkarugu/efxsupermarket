<?php

namespace App\View\Components\ItemCentre;

use App\ItemPromotion;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Promotions extends Component
{
    public function __construct(
        public int $itemId
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.item-centre.promotions', [
            'promotions' => ItemPromotion::with('promotionType')->where('inventory_item_id', $this->itemId)
                ->get(),
            'can_create' => !ItemPromotion::where('inventory_item_id', $this->itemId)
                ->where('status', 'active')->exists()
        ]);
    }
}
