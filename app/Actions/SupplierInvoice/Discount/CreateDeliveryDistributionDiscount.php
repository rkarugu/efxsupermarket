<?php

namespace App\Actions\SupplierInvoice\Discount;

use App\Model\User;
use App\Model\WaPurchaseOrder;
use App\Models\TradeAgreement;
use App\Models\TradeAgreementDiscount;
use App\Models\TradeDiscount;

class CreateDeliveryDistributionDiscount
{
    public function create(WaPurchaseOrder $order, string $invoiceNumber, User $user)
    {
        $tradeAgreement = TradeAgreement::where([
            'wa_supplier_id' => $order->wa_supplier_id,
            'is_locked' => 1
        ])->first();

        if (is_null($tradeAgreement)) {
            return;
        }

        $discount = TradeAgreementDiscount::where([
            'discount_type' => 'Distribution Discount on Delivery',
            'trade_agreements_id' => $tradeAgreement->id,
        ])->first();

        if (is_null($discount)) {
            return;
        }

        $options = json_decode($discount->other_options);

        $discounts = [];
        $totalDiscount = 0;

        foreach ($order->purchaseOrderItems as $item) {
            if (!$discountOption = $this->getItemDiscount($item->inventoryItem, $options, $order->storeLocation)) {
                continue;
            }

            $discounts[] = [
                'item_code' => $discountOption->stock,
                'discount_type' => 'Value',
                'discount_value' => $discountOption->discount,
                'item_cost' => $item->order_price,
                'item_quantity' => $item->quantity,
                'amount' => $discountAmount = round($item->quantity * $discountOption->discount, 2)
            ];

            $totalDiscount += $discountAmount;
        }

        $tradeDiscount = TradeDiscount::create([
            'supplier_id' => $order->wa_supplier_id,
            'trade_agreement_discount_id' => $discount->id,
            'supplier_invoice_number' => $invoiceNumber,
            'invoice_date' => now(),
            'description' => 'DELIVERY DISTRIBUTION DISCOUNT - ' . $order->purchase_no,
            'amount' => $totalDiscount,
            'prepared_by' => $user->id,
        ]);

        foreach ($discounts as $discount) {
            $tradeDiscount->items()->create($discount);
        }

        return $tradeDiscount;
    }

    public function getItemDiscount($item, $options, $location)
    {
        if ($options->discount_target_type == 'All') {
            $items = collect($options->location_discounts[0]->discount);

            return $items->where('stock', $item->stock_id_code)->first();
        }

        $locationDiscounts = collect($options->location_discounts);
        $locationDiscount = $locationDiscounts->where('location', $location->location_name . " ($location->location_code)")
            ->first();

        if (is_null($locationDiscount)) {
            return 0;
        }

        $items = collect($locationDiscount->discount);

        return $items->where('stock', $item->stock_id_code)->first();
    }
}
