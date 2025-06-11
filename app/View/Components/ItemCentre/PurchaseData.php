<?php

namespace App\View\Components\ItemCentre;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaInventoryItemSupplierData;
use App\Model\WaSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PurchaseData extends Component
{
    public function __construct(
        protected int $itemId
    ) {
    }

    public function render(): View|Closure|string
    {
        $inventoryItem = WaInventoryItem::findOrFail($this->itemId);

        $item_suppliers = WaInventoryItemSupplierData::where('wa_inventory_item_id', $this->itemId)
            ->orderBy('id', 'DESC')
            ->get();

        $suppliers = WaSupplier::whereHas('products', function ($query) {
            $query->where('wa_inventory_items.id', $this->itemId);
        })->get();

        return view('components.item-centre.purchase-data', [
            'inventoryItem' => $inventoryItem,
            'item_suppliers' => $item_suppliers,
            'suppliers' => $suppliers,
        ]);
    }
}
