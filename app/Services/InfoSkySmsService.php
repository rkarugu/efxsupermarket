<?php

namespace App\Services;

use App\Interfaces\SmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

class InfoSkySmsService implements SmsService
{
    function sendOtp(string $msg, string $phoneNumber): void
    {
        try {
            $payload = [
                "sender" => env("KANINI_SMS_SENDER_ID_2"),
                "message" => $msg,
                "phone" => $phoneNumber
            ];

            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => env('KANINI_SMS_TOKEN'),
            ])->post('https://bulk.infosky.co.ke/api/v1/send-sms', $payload);

            if (!$apiResponse->ok()) {
                Log::info($apiResponse->body());
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    function sendMessage(string $msg, string $phoneNumber): void
    {
        try {
            $payload = [
                "sender" => env("KANINI_SMS_SENDER_ID"),
                "message" => $msg,
                "phone" => $phoneNumber
            ];

            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => env('KANINI_SMS_TOKEN'),
            ])->post('https://bulk.infosky.co.ke/api/v1/send-sms', $payload);

            if (!$apiResponse->ok()) {
                Log::info($apiResponse->body());
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }


    public function sendMessageOld(string $msg, array $phoneNumber): void
    {
        try {
            $payload = [
                "acc_no" => env("KANINI_SMS_ACC_NO"),
                "api_key" => env("KANINI_SMS_API_KEY"),
                "sender_id" => env("KANINI_SMS_SENDER_ID"),
                "message" => $msg,
                "msisdn" => $phoneNumber,
                "dlr_url" => "",
                "linkID" => ""
            ];

            $apiResponse = Http::post("https://isms.infosky.co.ke/sms2/api/v1/send-sms", $payload);
            if (!$apiResponse->ok()) {
                Log::info($apiResponse->body());
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function checkSmsBalance()
    {
        $acc_no = env("KANINI_SMS_ACC_NO");
        $api_key = env("KANINI_SMS_API_KEY");
        try {
            $apiResponse = Http::get("http://isms.infosky.co.ke/sms2/api/v1/account/balance?acc_no=$acc_no&api_key=$api_key");
            if (!$apiResponse->ok()) {
                Log::info($apiResponse->body());
            }
        } catch (\Throwable $th) {
            // throw $th;
            Log::info($th->getMessage());

        }
    }



    function sendMessageResponse(string $msg, string $phoneNumber,$issn)
    {
        try {
            $payload = [
                "sender" => $issn,
                "message" => $msg,
                "phone" => $phoneNumber
            ];

            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => env('KANINI_SMS_TOKEN'),
            ])->post('https://bulk.infosky.co.ke/api/v1/send-sms', $payload);

            if (!$apiResponse->ok()) {
                return $apiResponse->body();
            }
            return 1;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}