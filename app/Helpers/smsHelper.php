<?php

use App\SmsAccount;
use App\SmsMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('sendSms')) {
    /**
     * Check InfoSky account balance
     *
     * @param string $accountNo
     * @param string $apiKey
     * @return boolean
     */
    function checkAccountBalance(string $accountNo, string $apiKey): bool
    {
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
        ])->get('https://isms.infosky.co.ke/sms2/api/v1/account/balance', [
            'acc_no' => $accountNo,
            'api_key' => $apiKey
        ]);

        if ($response->successful()) {
            if ($response['status'] == 1) {
                SmsAccount::where([
                    'account_number' => $accountNo
                ])->update([
                    'account_balance' => $response['balance']['amount'],
                    'sms_units' => $response['balance']['sms_units']
                ]);
                return true;
            } else {
                Log::info('Unable to get sms account balance');
                return false;
            }
        }
        return false;
    }
}

if (!function_exists('sendSms')) {
    /**
     * Send text message using Infosky API
     *
     * @param string $message
     * @param array| string $msisdns
     * @return bool
     */
    function sendSms(array|string $msisdns, string $message): bool
    {
        if (Str::length($message) == 0) {
            throw new Exception('No message provided!');
        }

        $validatedMsisdns = validateMsisdns($msisdns);
        try {
            $smsAccount = SmsAccount::where('active', true)->first();
            if ($smsAccount) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                    ->post('http://isms.infosky.co.ke/sms2/api/v1/send-sms', [
                        'acc_no' => $smsAccount->account_number,
                        'api_key' => $smsAccount->api_key,
                        'sender_id' => $smsAccount->sender_id,
                        'message' => $message,
                        'msisdn' => $validatedMsisdns,
                        // 'dlr_url' => 'https://15ea-197-248-229-25.ngrok-free.app/api/sms-delivery-status',
                        'dlr_url' => env('APP_URL') . '/api/sms-delivery-status',
                        'linkID' => '',
                    ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    foreach ($responseData as $entry) {
                        $status = $entry['status'];
                        $description = $entry['description'];
                        switch ($status) {
                            case 1:
                                // Success/Queued successfully
                                $msisdn = $entry['msisdn'];
                                $smsId = $entry['sms_id'];
                                SmsMessage::create([
                                    'sms_account_id' => $smsAccount->id,
                                    'message' => $message,
                                    'msisdn' => $msisdn,
                                    'sms_external_id' => $smsId,
                                    'status' => $description
                                ]);
                                checkAccountBalance($smsAccount->account_number, $smsAccount->api_key);
                                break;

                            case 2:
                                // Insufficient Credit
                                Log::info('Insufficient Credit');
                                break;

                            case 3:
                                // Invalid MSISDN
                                $invalidMsisdn = $entry['msisdn'];
                                break;

                            default:
                                Log::info("Unhandled status code: $status\n");
                                break;
                        }
                    }

                    return true;
                }
                return false;
            }
            throw new Exception('No active SMS account found!');
        } catch (Throwable $th) {
            Log::alert('Error sending SMS message: ' . $th->getMessage());
            throw $th;
        }
    }
}

function validateMsisdns($msisdns)
{
    if (is_string($msisdns)) {
        $msisdns = [$msisdns];
    }

    foreach ($msisdns as $msisdn) {
        if (!is_numeric($msisdn)) {
            throw new Exception("Invalid MSISDN format: $msisdn");
        }
    }

    return $msisdns;
}

if (!function_exists('deliveryCodeReference')) {
    /**
     * Convert delivery status code to readable text
     *
     * @param int|string $code
     * @return string
     */
    function deliveryCodeReference(int|string $code): string
    {
        if ($code == 0) {
            return 'Sent';
        } elseif ($code == 1) {
            return 'MessageWaiting';
        } elseif ($code == 2) {
            return 'DeliveredToNetwork';
        } elseif ($code == 3) {
            return 'DeliveredToTerminal';
        } elseif ($code == 4) {
            return 'DeliveryNotificationNotSupported';
        } elseif ($code == 5) {
            return 'DeliveryUncertain';
        } elseif ($code == 6) {
            return 'Insufficient_Balance';
        } elseif ($code == 7) {
            return 'Invalid_Linkid';
        } elseif ($code == 8) {
            return 'DeliveryImpossible';
        } elseif ($code == 9) {
            return 'Unknown';
        } elseif ($code == 10) {
            return 'Insufficient_BulkSMS_Credit';
        } elseif ($code == 11) {
            return 'UnknownSubscriber';
        } elseif ($code == 12) {
            return 'UnknownBaseStation';
        } elseif ($code == 13) {
            return 'UnknownMSC';
        } elseif ($code == 14) {
            return 'UnidentifiedSubscriber';
        } elseif ($code == 15) {
            return 'AbsentSubscriberSM';
        } elseif ($code == 16) {
            return 'UnknownEquipment';
        } elseif ($code == 17) {
            return 'RoamingNotAllowed';
        } elseif ($code == 18) {
            return 'IllegalSubscriber';
        } elseif ($code == 19) {
            return 'BearerServiceNotProvisioned';
        } elseif ($code == 20) {
            return 'TeleserviceNotProvisioned';
        } elseif ($code == 21) {
            return 'IllegalEquipment';
        } elseif ($code == 22) {
            return 'CallBarred';
        } elseif ($code == 23) {
            return 'ForwardingViolation';
        } elseif ($code == 24) {
            return 'CUG-Reject';
        } elseif ($code == 25) {
            return 'IllegalSS-Operation';
        } elseif ($code == 26) {
            return 'SS-ErrorStatus';
        } elseif ($code == 27) {
            return 'SS-NotAvailable';
        } elseif ($code == 28) {
            return 'SS-SubscriptionViolation';
        } elseif ($code == 29) {
            return 'SS-Incompatibility';
        } elseif ($code == 30) {
            return 'FacilityNotSupported';
        } elseif ($code == 31) {
            return 'InvalidTargetBaseStation';
        } elseif ($code == 32) {
            return 'NoRadioResourceAvailable';
        } elseif ($code == 33) {
            return 'NoHandoverNumberAvailable';
        } elseif ($code == 34) {
            return 'SubsequentHandoverFailure';
        } elseif ($code == 35) {
            return 'AbsentSubscriber';
        } elseif ($code == 36) {
            return 'SubscriberBusyForMT-SMS';
        } elseif ($code == 37) {
            return 'SM-DeliveryFailure';
        } elseif ($code == 38) {
            return 'MessageWaitingListFull';
        } elseif ($code == 39) {
            return 'SystemFailure';
        } elseif ($code == 40) {
            return 'DataMissing';
        } elseif ($code == 41) {
            return 'UnexpectedDataValue';
        } elseif ($code == 42) {
            return 'PW-RegistrationFailure';
        } elseif ($code == 43) {
            return 'NegativePW-Check';
        } elseif ($code == 44) {
            return 'NoRoamingNumberAvailable';
        } elseif ($code == 45) {
            return 'TracingBufferFull';
        } elseif ($code == 46) {
            return 'NumberOfPW-AttemptsViolation';
        } elseif ($code == 47) {
            return 'NumberChanged';
        } elseif ($code == 48) {
            return 'UnknownAlphabet';
        } elseif ($code == 49) {
            return 'USSD-Busy';
        } elseif ($code == 50) {
            return 'OK';
        } elseif ($code == 51) {
            return 'UserInBlacklist';
        } elseif ($code == 52) {
            return 'UserAbnormalState';
        } elseif ($code == 53) {
            return 'UserIsSuspended';
        } elseif ($code == 54) {
            return 'NotSFCUser';
        } elseif ($code == 55) {
            return 'UserNotSubscribed';
        } elseif ($code == 56) {
            return 'UserNotExist';
        } else {
            return 'Unknown Delivery Status Code';
        }
    }
}
