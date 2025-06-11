<?php

namespace App\Services;

use App\Interfaces\MpesaPaymentInterface;
use App\InvoicePayment;
use App\Model\WaInternalRequisition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesaFlowMpesaPaymentService implements MpesaPaymentInterface
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('PESAFLOW_BASE_URL') ?? '';
    }

    public function fetchPayment( $order, InvoicePayment $payment, string $reference): array
    {
        $response = ['success' => 1, 'message' => 'success'];

        try {
            $payload = [
                'invoice_number' => $payment->truncated_order_no,
                'receipt' => "$reference",
            ];

            $response['payload'] = $payload;

            $apiResponse = Http::post("$this->baseUrl/api/payment/fetchNotification", $payload);
            if (!$apiResponse->ok()) {
                $response['success'] = false;
            }

            $response['message'] = $apiResponse->body();
        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function initiatePayment( $order, string $msisdn): array
    {
        $response = ['success' => true, 'message' => 'success'];

        try {
            $invoicePaymentRecord = DB::table('invoice_payments')
                ->where('payable_id', $order->id)
                ->where('payable_type', get_class($order))
                ->orderBy('created_at', 'DESC')
                ->first();

            $payload = [
                'apiClientID' => env('PESAFLOW_API_CLIENT_ID'),
                'serviceID' => '140',
                'billRefNumber' => $invoicePaymentRecord->order_no,
                'billDesc' => $invoicePaymentRecord->order_no,
                'clientMSISDN' => "$msisdn",
                'clientIDNumber' => "$msisdn",
                'clientName' => '',
                'clientEmail' => '',
                'notificationURL' => env('APP_URL') . '/api/customer-payments/pesaflow/callback',
//                'notificationURL' => 'https://gold-hornets-cut.loca.lt/api/customer-payments/pesaflow/callback',
                'callBackURLOnSuccess' => '',
                'currency' => 'KES',
                'amountExpected' => $invoicePaymentRecord->invoice_amount,
                'format' => 'json',
                'sendSTK' => 'true',
                'secureHash' => $this->generateSecureHash($msisdn, $invoicePaymentRecord)
            ];

            $apiResponse = Http::post("$this->baseUrl/PaymentAPI/invoice/checkout", $payload);
            if (!$apiResponse->ok()) {
                $response['success'] = false;
            }

            $response['message'] = $apiResponse->body();
            $response['payload'] = $payload;
        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    private function generateSecureHash(string $msisdn, $invoicePaymentRecord): string
    {
        $api_client_id = env('PESAFLOW_API_CLIENT_ID');
        $service_id = '140';
        $id_number = "$msisdn";
        $currency = "KES";
        $client_invoice_ref = $invoicePaymentRecord->order_no;
        $desc = "$invoicePaymentRecord->order_no";
        $name = "";
        $secret = env('PESAFLOW_SECRET');
        $key = env('PESAFLOW_KEY');
        $amount_expected = $invoicePaymentRecord->invoice_amount;

        $data_string = "$api_client_id" . "$amount_expected" . "$service_id" . "$id_number" . "$currency" . "$client_invoice_ref" . "$desc" . "$name" . "$secret";
        return base64_encode(hash_hmac('sha256', $data_string, $key));
    }

}