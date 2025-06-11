<?php

namespace App\View\Components\ItemCentre;

use Closure;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;

class ShopPrices extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public int $itemId)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $authuser = Auth::user();
        // $authuser = Auth::user()->wa_location_and_store_id;
        $controller = new Controller();
        $permission = $controller->mypermissionsforAModule();

        $item = WaInventoryItem::with('locationPrices')
            ->where('id', $this->itemId)
            ->with('locationPrices.location')
            ->first();

        return view('components.item-centre.shop-prices', [
            'item' => $item,
            'permission' => $permission,
            'authuser' => $authuser,
        ]);

        // return view('components.item-centre.shop-prices',[
        //     'item'=>WaInventoryItem::with('locationPrices')
        //         ->where('id', $this->itemId)
        //         ->with('locationPrices.location')->first()
        // ]);
    }
}
