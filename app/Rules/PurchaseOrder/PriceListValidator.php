<?php

namespace App\Rules\PurchaseOrder;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PriceListValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $item = WaInventoryItem::find(explode('.', $attribute)[1]);

        if ($value < $item->standard_cost) {
            $fail('The price is less than the standard cost');
        }
    }
}
