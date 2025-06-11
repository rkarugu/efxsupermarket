<?php

namespace App\Rules\PurchaseOrder;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Models\TradeAgreement;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TradeAgreementValidator implements ValidationRule
{
    public function __construct(
        protected $update = false
    ) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tradeAgreement = TradeAgreement::where([
            'wa_supplier_id' => $value
        ])->first();

        if (is_null($tradeAgreement)) {
            $fail("The supplier does not have a trade agreement");
        }

        if (!$tradeAgreement->is_locked) {
            $fail("The supplier trade agreement is not locked");
        }

        // Commenting out portal account check to allow suppliers without portal accounts
        // if (!$tradeAgreement->linked_to_portal && !$this->update) {
        //     $fail("The supplier does not have a portal account");
        // }
    }
}
