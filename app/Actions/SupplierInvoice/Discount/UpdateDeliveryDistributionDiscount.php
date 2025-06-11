<?php

namespace App\Actions\SupplierInvoice\Discount;

use App\Model\User;
use App\Models\TradeDiscount;
use App\Models\TradeDiscountItem;
use App\WaSupplierInvoice;

class UpdateDeliveryDistributionDiscount
{
    public function update(WaSupplierInvoice $invoice)
    {
        $discount = TradeDiscount::where('supplier_invoice_number', $invoice->invoice_number)->first();
        if (is_null($discount)) {
            return;
        }

        $discount->update([
            'invoice_id' => $invoice->id,
            'invoice_date' => $invoice->supplier_invoice_date,
            'invoice_amount' => $invoice->amount,
        ]);

        foreach ($invoice->items as $item) {
            $discountItem = TradeDiscountItem::where('item_code', $item->code)->first();
            $discountItem->update([
                'invoice_item_id' => $discountItem->id,
            ]);
        }
    }
}
