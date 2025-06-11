<?php

namespace App\View\Components\ItemCentre;

use Closure;
use App\Models\RoutePricing;
use App\Model\WaInventoryItem;
use Illuminate\View\Component;
use App\Model\WaLocationAndStore;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoutePricingComponent extends Component
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
        $model = 'maintain-items';
        $title = 'Maintain items';
        $pmodule = 'maintain-items';
        $basePath = 'admin.route_pricing';

        $authuser = Auth::user();
        $authuserLocation = Auth::user()->wa_location_and_store_id;
        $controller = new Controller();
        $permission = $controller->mypermissionsforAModule();

        $restaurant_id = WaLocationAndStore::where('id', $authuserLocation)->pluck('wa_branch_id');

        $routePricingQuery = RoutePricing::latest()
            ->where('wa_inventory_item_id', $this->itemId);

        if ($authuser->role_id != 1 && !isset($permission['maintain-items___view-all-stocks'])) {
            $routePricingQuery->where('restaurant_id', $restaurant_id);
        }

        $routePricing = $routePricingQuery->get();

        $inventoryItem = WaInventoryItem::find($this->itemId);

        return view('components.item-centre.route-pricing-component', [
            'routePricing' => $routePricing,
            'inventoryItem' => $inventoryItem,
            'model' => $model,
            'title' => $title,
            'pmodule' => $pmodule,
            'basePath' => $basePath,
        ]);
    }
}
