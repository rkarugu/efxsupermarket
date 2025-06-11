<?php

namespace App\Http\Controllers;

use App\SmsAccount;
use App\SmsMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    /**
     * Store SMS Account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeSMSAccount(Request $request): JsonResponse
    {
        $request->validate([
            'account_number' => 'required|unique:sms_accounts,account_number',
            'sender_id' => 'required|unique:sms_accounts,sender_id',
            'api_key' => 'required'
        ]);

        $smsAccount = SmsAccount::create([
            'account_number' => $request->account_number,
            'sender_id' => $request->sender_id,
            'api_key' => $request->api_key,
        ]);

        return response()->json(
            [
                'success' => true,
                'message' => 'SMS Account saved successfully!',
                'data' => [
                    'account' => $smsAccount
                ]
            ],
            200,
        );
    }

    /**
     * Update sent messages delivery status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleSMSDeliveryStatus(Request $request): JsonResponse
    {
        logger($request->all());
        $request->validate([
            'sms_id' => 'required|string',
            'msisdn' => 'required|string',
            'status' => 'required|integer',
        ]);

        $smsId = $request->input('sms_id');
        $msisdn = $request->input('msisdn');
        $status = $request->input('status');

        $smsMessage = SmsMessage::where([
            'sms_external_id' => $smsId,
            'msisdn' => $msisdn,
        ])->first();

        $smsMessage->status = deliveryCodeReference($status);
        $smsMessage->save();
        return response()->json('', 200);
    }
}
