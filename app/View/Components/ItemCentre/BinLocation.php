<?php

namespace App\View\Components\ItemCentre;

use Closure;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use Illuminate\View\Component;
use App\Model\WaLocationAndStore;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BinLocation extends Component
{
    public function __construct(
        protected int $itemId
    ) {}

    public function render(): View|Closure|string
    {
        $authuser = Auth::user();
        // $authuser = Auth::user()->wa_location_and_store_id;
        $controller = new Controller();
        $permission = $controller->mypermissionsforAModule();
        
        $item = WaInventoryItem::find($this->itemId);
        $bins = WaLocationAndStore::with(['bin_locations'])->select([
            'wa_location_and_stores.*',
            'wa_inventory_location_uom.uom_id',
        ])->where('is_physical_store', '1')
            ->leftJoin('wa_inventory_location_uom', function ($e) use ($item) {
                $e->on('wa_location_and_stores.id', '=', 'wa_inventory_location_uom.location_id')
                    ->where('wa_inventory_location_uom.inventory_id', $item->id);
            })->groupBy('wa_location_and_stores.id')
            ->get();

        return view('components.item-centre.bin-location', [
            'item' => $item,
            'bins' => $bins,
            'permission' => $permission,
            'authuser' => $authuser,
        ]);
    }
}
