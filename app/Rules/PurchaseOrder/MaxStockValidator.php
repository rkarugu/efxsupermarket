<?php

namespace App\Rules\PurchaseOrder;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxStockValidator implements ValidationRule
{
    protected $order;

    protected $location;

    public function __construct($order = null)
    {
        $this->order = $order;

        $this->location = is_null($this->order) ? request()->store_location_id : $this->order->wa_location_and_store_id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (request()->boolean('advance_payment')) {
            return;
        }

        $orderItemId = substr($attribute, 14);
        $stockStatus = WaInventoryLocationStockStatus::where([
            'wa_inventory_item_id' => $orderItemId,
            'wa_location_and_stores_id' => $this->location
        ])->first();


        if (is_null($stockStatus)) {
            $fail("The max stock is not set for item");
        }

        $qoh = WaStockMove::where('wa_inventory_item_id', $orderItemId)
            ->where('wa_location_and_store_id', $this->location)
            ->sum('qauntity');

        $qoo = WaPurchaseOrderItem::where(['wa_inventory_item_id' => $orderItemId])
            ->whereHas('getPurchaseOrder', function ($query) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->when($this->order, function ($query) {
                        $query->where('wa_purchase_orders.id', '<>', $this->order->id);
                    })
                    ->where('wa_location_and_store_id', $this->location)
                    ->doesntHave('grns');
            })            
            ->sum('quantity');

        $total = floatval($value) + floatval($qoo) + floatval($qoh);

        if ($total > floatval($stockStatus->max_stock)) {
            $fail("The :attribute + QOO($qoo) + SOH($qoh) exceeds max stock");
        }
    }
}
