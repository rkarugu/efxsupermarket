<?php

namespace App\Interfaces;

use App\InvoicePayment;
use App\Model\WaInternalRequisition;

interface MpesaPaymentInterface
{
    public function initiatePayment($order, string $msisdn): array;

    public function fetchPayment($order, InvoicePayment $payment, string $reference): array;
}