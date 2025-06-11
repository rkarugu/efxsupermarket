<?php

namespace App\Services;

use App\InvoicePayment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function successCallback(array $payload)
    {

        try {
            $paymentRecord = InvoicePayment::latest()->where('order_no', $payload['client_invoice_ref'])->first();

            if ($paymentRecord->payable_type == 'App\Model\WaPosCashSales')
            {
                $PosService = new PosPaymentService();
                $PosService->paymentCallback($payload, $paymentRecord);
            }
            else{
                $paymentRecord->update([
                    'payment_invoice_no' => $payload['client_invoice_ref'],
                    'payment_channel' => $payload['payment_channel'],
                    'payment_reference' => $payload['payment_reference'],
                    'paid_amount' => $payload['paid_amount'],
                    'paying_number' => $payload['paying_number'],
                    'payment_date' => $payload['payment_date'],
                    'status' => 'settled',
                ]);
            }

        } catch (\Throwable $e) {
            Log::info("Mpesa callback update failed");
            Log::error($e->getMessage(), $e->getTrace());
        }
    }
}