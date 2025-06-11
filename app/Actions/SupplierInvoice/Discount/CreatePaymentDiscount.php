<?php

namespace App\Actions\SupplierInvoice\Discount;

use App\Models\TradeAgreement;
use App\Models\TradeAgreementDiscount;
use App\Models\TradeDiscount;
use App\PaymentVoucher;
use Illuminate\Support\Carbon;

class CreatePaymentDiscount
{
    public function create(PaymentVoucher $voucher)
    {
        $tradeAgreement = TradeAgreement::where([
            'wa_supplier_id' => $voucher->wa_supplier_id,
            'is_locked' => 1
        ])->first();

        if (is_null($tradeAgreement)) {
            return;
        }

        $paymentDiscount = TradeAgreementDiscount::where([
            'discount_type' => 'Payment Discount',
            'trade_agreements_id' => $tradeAgreement->id,
        ])->first();        

        if (is_null($paymentDiscount)) {
            return;
        }

        $options = json_decode($paymentDiscount->other_options, true);       
        
        foreach ($voucher->voucherItems as $item) {
            $invoice = $item->invoice;
            $days = now()->diffInDays(Carbon::parse($invoice->supplier_invoice_date));
            
            $discount = $this->getApplicableDiscount($days, $options);
           
            if ($discount == 0) {
                continue;
            }
            
            TradeDiscount::create([
                'supplier_id' => $invoice->supplier_id,
                'invoice_id' => $invoice->id,
                'trade_agreement_discount_id' => $paymentDiscount->id,
                'supplier_invoice_number' => $invoice->supplier_invoice_number,
                'invoice_date' => $invoice->supplier_invoice_date,
                'invoice_amount' => $invoice->amount,
                'description' =>  "DISCOUNT after $days days at $discount%",
                'amount' => ($invoice->amount * $discount) / 100,
                'prepared_by' => $invoice->prepared_by,
            ]);
        }
    }

    protected function getApplicableDiscount($days, $discounts)
    {
        if ($days <= 3) {
            return $discounts['three_days'];
        } elseif ($days <= 7) {
            return $discounts['seventh_days'];
        } elseif ($days <= 14) {
            return $discounts['fourteen_days'];
        } elseif ($days <= 21) {
            return $discounts['twenty_one_days'];
        } elseif ($days <= 30) {
            return $discounts['thirty_days'];
        } else {
            return 0;
        }
    }
}
