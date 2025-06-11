<?php

namespace App\Services;

use App\Models\Disbursement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DarajaDisbursementService
{
    static public function getAuthToken(): array
    {
        $response = [
            'successful' => true,
            'token' => null
        ];

        try {
            $url = env('DARAJA_B2C_BASE_URL') . '/oauth/v1/generate';
            $tokenResponse = Http::withBasicAuth(env('DARAJA_B2C_CONSUMER_KEY'), env('DARAJA_B2C_CONSUMER_SECRET'))->get($url, [
                'grant_type' => 'client_credentials'
            ]);

            if (!$tokenResponse->ok()) {
                $response['successful'] = false;
            } else {
                $response['token'] = $tokenResponse->json()['access_token'];
            }
        } catch (\Throwable $th) {
            $response['successful'] = false;
        }

        return $response;
    }

    static public function disburse(string $phoneNumber, float $amount, string $narration, $source = null, $source_wallet_id = null, $parentType = null,  $parentId = null, $userId = null)
    {
        Log::info('B2C recording disbursement');
        $disbursement = Disbursement::create([
            'parent_id' => $parentId,
            'parent_type' => $parentType,
            'user_id' => $userId,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'originator_conversation_id' => Str::uuid()->toString(),
            'disbursement_paybill' => env('DARAJA_B2C_ACCOUNT'),
            'narration' => $narration,
            'source' => $source,
            'source_wallet_id' => $source_wallet_id,
        ]);

        $disbursement->update([
            'document_no' => Disbursement::buildDocumentNumber($disbursement->id)
        ]);
        $tokenResponse = self::getAuthToken();
        if (!$tokenResponse['successful']) {
            $disbursement->update([
                'request_status' => 'failed',
                'request_failure_reason' => 'Request failed to get auth token'
            ]);
            Log::error('Request failed to get auth token for B2C');
            return false;
        }

        $token = $tokenResponse['token'];

        try {
            $url = env('DARAJA_B2C_BASE_URL') . '/mpesa/b2c/v3/paymentrequest';
            $msisdn = '254'. substr($disbursement->phone_number, -9);
            $payload = [
                'OriginatorConversationID' => $disbursement->originator_conversation_id,
                'InitiatorName' => env('DARAJA_B2C_INITIATOR_NAME'),
                'SecurityCredential' => env('DARAJA_B2C_SECURITY_CREDENTIAL'),
                'CommandID' => 'BusinessPayment',
                'Amount' =>  ceil((int)$amount),
                'PartyA' => $disbursement->disbursement_paybill,
                'PartyB' => $msisdn,
                'Remarks' => $disbursement->narration,
                'QueueTimeOutURL' => env('APP_URL') . "/api/disbursements/daraja/$disbursement->id/timeout-callback",
                'ResultURL' => env('APP_URL') . "/api/disbursements/daraja/$disbursement->id/callback",
                'Occassion' => 'PETTY CASH',
            ];

            $response = Http::withToken($token)->post($url, $payload);
            $responsePayload = $response->json();
            if (!$response->ok()) {
                Log::error('B2C request  failed for disbursement '. $responsePayload);
                $disbursement->update([
                    'request_status' => 'failed',
                    'request_failure_reason' => $responsePayload['errorMessage']
                ]);

                return false;
            }

            $disbursement->update([
                'request_status' => 'successful',
                'request_conversation_id' => $responsePayload['OriginatorConversationID']
            ]);
            return true;
        } catch (\Throwable $th) {
            $disbursement->update([
                'request_status' => 'failed',
                'request_failure_reason' => 'Server error - ' . $th->getMessage()
            ]);
            Log::error('B2C >>>>>>> Server error - ' . $th->getMessage());
            return false;
        }
    }
}
