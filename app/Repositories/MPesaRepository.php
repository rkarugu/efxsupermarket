<?php

namespace App\Repositories;

use Carbon\Carbon;

class MPesaRepository
{
    public function create(array $data, string $operationType, $status)
    {
        if ($operationType === 'stk_callback_success') {
            $checker = ['operation_type' => 'stk_callback_success', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif($operationType === 'stkpush') {
            $checker = ['operation_type' => 'stkpush', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'simulate_payment') {
            $checker = ['operation_type' => 'simulate_payment', 'conversation_id' => $data['ConversationID']];
        } elseif ($operationType === 'simulate_payment_success') {
            $checker = ['operation_type' => 'simulate_payment_success', 'transaction_id' => $data['TransID']];
        } elseif ($operationType === 'stk_callback_timeout') {
            $checker = ['operation_type' => 'stk_callback_timeout', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'stk_callback_invalid_prompt') {
            $checker = ['operation_type' => 'stk_callback_invalid_prompt', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'stk_callback_cancelled') {
            $checker = ['operation_type' => 'stk_callback_cancelled', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'stk_callback_unable_to_lock_subscriber') {
            $checker = ['operation_type' => 'stk_callback_unable_to_lock_subscriber', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'stkpush_query') {
            $checker = ['operation_type' => 'stkpush_query', 'checkout_request_id' => $data['CheckoutRequestID']];
        } elseif ($operationType === 'stk_callback_failed') {
            $checker = ['operation_type' => 'stk_callback_failed', 'checkout_request_id' => $data['CheckoutRequestID']];
        } else {
            throw new \InvalidArgumentException('Invalid Operation Type passed');
        }

        $mpesaOperation = $this->model->operations()->firstOrCreate(
            $checker,
            [
                'gateway_app_id' => $this->model->gateway_app_id,
                'currency' => 'KES',
                'status' => $status,
                'operation_type' => $operationType,
                'merchant_request_id' => $data['MerchantRequestID']  ?? null,
                'checkout_request_id' => $data['CheckoutRequestID'] ?? null,
                'response_code' => $data['ResponseCode'] ?? null,
                'response_description' => $data['ResponseDescription'] ?? null,
                'result_code' => $data['ResultCode'] ?? null,
                'result_description' => $data['ResultDesc'] ?? null,
                'customer_message' => $data['CustomerMessage'] ?? null,
                'conversation_id' => $data['ConversationID'] ?? null,
                'originator_conversation_id' => $data['OriginatorCoversationID'] ?? null,
                'transaction_type' => $data['TransactionType'] ?? null,
                'transaction_id' => $data['TransID'] ?? null,
                'transaction_time' => isset($data['TransTime']) ? Carbon::createFromFormat('YmdHis', $data['TransTime'], 'Africa/Nairobi') : null,
                'business_short_code' => $data['BusinessShortCode'] ?? null,
                'bill_reference_number' => $data['BillRefNumber'] ?? null,
                'invoice_number' => $data['InvoiceNumber'] ?? null,
                'org_account_balance' => $data['OrgAccountBalance'] ?? null,
                'third_party_transaction_id' => $data['ThirdPartyTransID'] ?? null,
                'msisdn' => $data['MSISDN'] ?? null,
                'phone_number' => $data['PhoneNumber'] ?? null,
                'first_name' => $data['FirstName'] ?? null,
                'middle_name' => $data['MiddleName'] ?? null,
                'last_name' => $data['LastName'] ?? null,
                'transaction_amount' => $data['TransAmount'] ?? null,
                'amount' => $data['Amount'] ?? null,
                'mpesa_receipt_number' => $data['MpesaReceiptNumber'] ?? null,
                'balance' => $data['Balance'] ?? null,
                'transaction_date' => isset($data['TransactionDate']) ? Carbon::createFromFormat('YmdHis', $data['TransactionDate'], 'Africa/Nairobi') : null,
            ]
        );

        return $mpesaOperation;
    }
}