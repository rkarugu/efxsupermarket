<?php

namespace App\View\Components;

use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Models\PosStockBreakRequest;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class SplitRequestsComponent extends Component
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
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        $requests = PosStockBreakRequest::with(['getChild', 'getChildBinDetail', 'getMother','getMotherBinDetail', 'getTransactingStore', 'getInitiatingUser'])
            // ->where('status', 'pending')
            ->where('wa_location_and_store_id', $user->wa_location_and_store_id)
            ->where('requested_by', $user->id)
            ->whereDate('created_at', $today)
            ->get()->map(function ($record){
                $record->mother_qoh = WaStockMove::where('wa_inventory_item_id', $record->mother_item_id)
                    ->where('wa_location_and_store_id', $record->wa_location_and_store_id)
                    ->sum('qauntity');
                return $record; 
            });
        $pendingItemsId = PosStockBreakRequest::where('status', 'pending')->where('requested_by', $user->id)->where('wa_location_and_store_id', $user->wa_location_and_store_id)->pluck('item_id')->toArray();
        $inventoryItems = WaInventoryItem::select(
            'wa_inventory_items.*',
            'wa_inventory_assigned_items.conversion_factor',
        )
            ->leftJoin('display_bin_user_item_allocations', 'wa_inventory_items.id', 'display_bin_user_item_allocations.wa_inventory_item_id')
            ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_items.id')
            ->whereNotNull('wa_inventory_assigned_items.destination_item_id')
            ->where('display_bin_user_item_allocations.user_id', $user->id)
            ->whereNotIn('wa_inventory_items.id', $pendingItemsId)
            ->get();
        

        return view('components.split-requests-component', ['inventoryItems'=>$inventoryItems, 'requests'=>$requests]);
    }
}
