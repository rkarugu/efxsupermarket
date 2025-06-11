<?php

namespace App\Services;

use App\Interfaces\SmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirTouchSmsService implements SmsService
{

    public function sendMessage(string $msg, string $phoneNumber): void
    {
        try {
            $api_key = env("AIRTOUCH_SMS_API_KEY");
            $issn = env("AIRTOUCH_ISSN");
            $username = env("AIRTOUCH_USERNAME");
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '254' . substr($phoneNumber, 1);
            }
            if (substr($phoneNumber, 0, 1) === '7') {
                $phoneNumber = '254' . $phoneNumber;
            }
            if (substr($phoneNumber, 0, 1) === '1') {
                $phoneNumber = '254' . $phoneNumber;
            }

            // $apiResponse = Http::get("https://client.airtouch.co.ke:9012/sms/api/?issn=$issn&msisdn=$phoneNumber&text=$msg&username=$username&password=$api_key");
            $apiResponse = Http::get("https://client.airtouch.co.ke:9012/sms/api/", [
                'issn' => $issn,
                'msisdn' => $phoneNumber,
                'text' => $msg,
                'username' => $username,
                'password' => $api_key,
            ]);

            if (!$apiResponse->ok()) {
                Log::info($apiResponse->body());
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function sendOtp(string $msg, string $phoneNumber): void
    {
        $this->sendMessage($msg, $phoneNumber);
    }

    public function sendMessageResponse(string $msg, string $phoneNumber)
    {
        try {
            $api_key = env("AIRTOUCH_SMS_API_KEY");
            $issn = env("AIRTOUCH_ISSN");
            $username = env("AIRTOUCH_USERNAME");
        
            $apiResponse = Http::get("https://client.airtouch.co.ke:9012/sms/api/", [
                'issn' => $issn,
                'msisdn' => $phoneNumber,
                'text' => $msg,
                'username' => $username,
                'password' => $api_key,
            ]);

            if (!$apiResponse->ok()) {
                return $apiResponse->body();
            }
            return 1;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}