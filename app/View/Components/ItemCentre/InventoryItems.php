<?php

namespace App\View\Components\ItemCentre;

use App\Model\WaInventoryItem;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InventoryItems extends Component
{
    public function __construct(
        protected int $itemId
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.item-centre.inventory-items', [
            'item' => WaInventoryItem::with(['destinated_items.destinated_item'])
                ->where('id', $this->itemId)->first()
        ]);
    }
}
