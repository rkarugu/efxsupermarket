<?php

namespace App\Services;

use App\Interfaces\MpesaPaymentInterface;
use App\InvoicePayment;
use App\Model\WaInternalRequisition;

class DarajaMpesaPaymentService implements MpesaPaymentInterface
{

    public function initiatePayment(WaInternalRequisition $order, string $msisdn): array
    {
        return [];
    }

    public function fetchPayment(WaInternalRequisition $order, InvoicePayment $payment, string $reference): array
    {
        return [];
    }
}