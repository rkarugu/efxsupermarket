<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesaFlowDisbursementService
{
    protected $token;

    /**
     * @throws \Exception
     */
    public function __construct() {
        $this->token = $this->generateToken();
    }

    public function generateToken() {
        try {
            $response = Http::post(
                env('PESAFLOW_B2C_URL') . '/oauth/generate/token',
                [
                    'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                    'secret' => env('PESAFLOW_B2C_AUTH_SECRET')
                ]
            );

            if (!$response->ok()) {
                throw new \Exception('Failed to generate token');
            }

           return $this->token = $response->json()['token'];

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function getTransactionStatus(string $transactionReference) {
        try {
            $response = Http::withToken($this->token)
                ->post(
                    env('PESAFLOW_B2C_URL') . '/payment/withdrawal/status',
                    [
                        'trx_ref' => $transactionReference
                    ]
                );

            if (!$response->ok()) {
                throw new \Exception('Failed to get transaction status');
            }

            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function initiateWithdrawal($phoneNumber, $amount, $callBack)
    {
        try {
            $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $phoneNumber . "$amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
            $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
            $payload = [
                'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
                'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
                'amount' => "$amount",
                'currency' => 'KES',
                'party_b' => $phoneNumber,
                'secure_hash' => $hash,
                'type' => 'b2c',
                'notification_url' => $callBack,
            ];

            Log::info("PF B2C Payload: " . json_encode($payload));

            $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';

            $response = Http::withToken($this->token)
                ->post($url, $payload);

            Log::info("PF Response: " . $response->body());

            if (!$response->ok()) {
                throw new \Exception('Failed to initiate Disbursement');
            }

            return $response;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}