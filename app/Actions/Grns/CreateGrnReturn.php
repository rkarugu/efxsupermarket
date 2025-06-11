<?php

namespace App\Actions\Grns;

use App\Model\User;
use App\ReturnedGrn;

class CreateGrnReturn
{
    public function create(array $lineItems, User $user)
    {
        
        $returnNumber = getCodeWithNumberSeries('RETURN');

        foreach ($lineItems as $lineItem) {
            if ((int)$lineItem['quantity'] > 0) {
                ReturnedGrn::create([
                    'return_number' => $returnNumber,
                    'grn_id' => $lineItem['id'],
                    'grn_number' => $lineItem['grn_number'],
                    'wa_supplier_id' => $lineItem['supplier_id'],
                    'item_code' => $lineItem['item_code'],
                    'returned_quantity' => $lineItem['quantity'],
                    'initiated_by' => $user->id,
                    'reason' => $lineItem['reason'],
                ]);
            }
        }

        updateUniqueNumberSeries('RETURN', $returnNumber);
        
        return $returnNumber;
    }
}
