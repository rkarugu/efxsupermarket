<?php

namespace App\View\Components;

use App\Model\WaInventoryItem;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class MissingItemsTab extends Component
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
        $missingItems  =  DB::table('reported_missing_items')
            ->select(
                'reported_missing_items.created_at',
                'users.name',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.selling_price',
                DB::raw('SUM(reported_missing_items.quantity) as quantity'),
                'reported_missing_items.as_at_quantity',
                DB::raw("(SELECT (wa_grns.created_at) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as last_purchase_date"),
                DB::raw("(SELECT (wa_internal_requisition_items.created_at)
                    FROM wa_internal_requisition_items
                    WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                    ORDER BY wa_internal_requisition_items.created_at DESC
                    LIMIT 1
                ) AS last_sale_date"),
                DB::raw("(SELECT (wa_suppliers.name) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    LEFT JOIN wa_suppliers ON wa_grns.wa_supplier_id = wa_suppliers.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as supplier"),

            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'reported_missing_items.item_id')
            ->leftJoin('users', 'users.id', 'reported_missing_items.reported_by')
            ->where('reported_missing_items.reported_by', $user->id)
            ->whereDate('reported_missing_items.created_at', $today)
            ->groupBy('reported_missing_items.item_id', 'reported_missing_items.reported_by', DB::raw('DATE(reported_missing_items.created_at)'));
            
       
        $missingItems = $missingItems->get();
        $inventoryItems = WaInventoryItem::all();
      
        return view('components.missing-items-tab', ['missingItems'  => $missingItems, 'inventoryItemsMissing' => $inventoryItems]);
    }
}
