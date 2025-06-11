<?php

namespace App\View\Components;

use App\Model\WaInventoryItem;
use App\Models\ReportedPriceConflict;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class PriceConflictTab extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $today = Carbon::now()->toDateString();
        $user = Auth::user();
        $reportedConflictItems = ReportedPriceConflict::select('reported_price_conflicts.*', 'users.name', 'wa_inventory_items.stock_id_code', 'wa_inventory_items.title')
        ->leftJoin('users', 'users.id', 'reported_price_conflicts.reported_by')
        ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'reported_price_conflicts.wa_inventory_item_id')
        ->whereDate('reported_price_conflicts.created_at', $today)
        ->where('reported_price_conflicts.reported_by', $user->id)
        ->get();
        $inventoryItems = WaInventoryItem::all();
        return view('components.price-conflict-tab', ['reportedConflictItems' => $reportedConflictItems, 'inventoryItems' => $inventoryItems]);
    }
}
