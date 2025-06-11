<?php

namespace App\Services;

use App\InvoicePayment;
use App\Models\MpesaOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    public function auth()
    {
        $url = env('MPESA_AUTH_URL').'/oauth/v1/generate?grant_type=client_credentials';
        $consumer_key = env('MPESA_CONSUMER_KEY');
        $consumer_secret = env('MPESA_CONSUMER_SECRET');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);
        return json_decode($curl_response)->access_token;

    }

    public function stkPush($phone, InvoicePayment $payment)
    {
        $msisdn = '254'. substr($phone, -9);
        $url = env('MPESA_URL').'/stkpush/v1/processrequest';
        $callBackUrl = env('MPESA_STK_CALLBACK_URL').'/api/mpesa/callback';
        $shortCode = env('MPESA_SHORT_CODE');
        $shortCodePassword = env('MPESA_PASSWORD');

//        Log::info("||||| Call back: $callBackUrl |||||");


        $time= now()->format('YmdHis');
        $base64Password = base64_encode($shortCode . $shortCodePassword . $time);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->auth(),
            'Content-Type' => 'application/json',
        ])->post($url, [
            'BusinessShortCode' => $shortCode,
            'Password' => $base64Password,
            'Timestamp' => $time,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => ceil($payment->invoice_amount) ,
            'PartyA' => $msisdn,
            'PartyB' => $shortCode,
            'PhoneNumber' => $msisdn,
            'CallBackURL' => $callBackUrl,
            'AccountReference' =>$payment->order_no,
            'TransactionDesc' => 'desc',
        ]);


        if ($response->failed()) {
            return 'Payment Initiation failed';
        } else {
            $response_array = (array)json_decode($response);
             MpesaOperation::create([
                'amount' =>   ceil($payment->invoice_amount) ,
                'invoice_payment_id' =>  $payment->id,
                'phone_number' =>  '254'. substr($phone, -9),
                'merchant_request_id' =>  $response_array['MerchantRequestID'],
                'checkout_request_id' =>  $response_array['CheckoutRequestID'],
                'response_code' =>  $response_array['ResponseCode'],
                'response_description' =>  $response_array['ResponseDescription'],
                'customer_message' =>  $response_array['CustomerMessage'],
            ]);
            return $response_array;
        }

    }
    public function callBack(Request $request)
    {
//        Log::info('STK Callback: '.json_encode($request->all()));
        try {
            $reponseContent = json_decode($request->getContent(), true);
            $stkCallback = $reponseContent['Body']['stkCallback'];
            $operationType = $this->getOperationType($stkCallback['ResultCode']);
            $mpesaOperation = MpesaOperation::where('merchant_request_id', $stkCallback['MerchantRequestID'])->first();

            if ($operationType['status'] != 'valid')
            {
                Log::info('STk failed');
                return [
                    'status'=>0,
                    'operation'=>$mpesaOperation,
                    'data'=>$operationType
                ];
            } else
            {
                $CallbackMetadata = $this->formatRequestData($reponseContent['Body']['stkCallback']['CallbackMetadata']['Item']);
                if ($CallbackMetadata)
                {
                    /*update the mpesa operation record*/
                    $mpesaOperation->update([
                        'result_code' => $stkCallback['ResultCode'],
                        'result_description' => $stkCallback['ResultDesc'],
                        'transaction_amount' => $CallbackMetadata['Amount'],
                        'mpesa_receipt_number' => $CallbackMetadata['MpesaReceiptNumber'],
                        'transaction_date' => $CallbackMetadata['TransactionDate'],
                        'phone_number' => $CallbackMetadata['PhoneNumber'],
                    ]);

                    return [
                        'status'=>1,
                        'data'=> $mpesaOperation->refresh()
                    ];
                }
            }

        } catch (\Exception $ex) {
            Log::error('Error Processing Mpesa Daraja callback |||||||||||||| ' . $ex->getMessage());
            return [
                'status'=>0,
                'data'=>[]
            ];
        }
    }
    public function stkQuery($checkoutRequestID)
    {
        $shortCode = env('MPESA_SHORT_CODE');
        $shortCodePassword = env('MPESA_PASSWORD');
        $url = env('MPESA_URL').'/stkpushquery/v1/query';

        $time= now()->format('YmdHis');
        $base64Password = base64_encode($shortCode . $shortCodePassword . $time);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->auth(),
            'Content-Type' => 'application/json',
        ])->post($url, [
            'BusinessShortCode' => $shortCode,
            'Password' => $base64Password,
            'Timestamp' => $time,
            'CheckoutRequestID' => $checkoutRequestID,
        ]);

        /**/
        $data = $response->json();

        $mpesaOperation = MpesaOperation::where('checkout_request_id', $checkoutRequestID)->first();
        $mpesaOperation->update([
            'result_code' => $data['ResultCode'],
            'result_description' => $data['ResultDesc'],
        ]);
        return [
            'status'=>1,
            'data'=> $data
        ];

    }

    /**
     * Method to format the array received from safaricom
     * @param $data
     * @return array
     */
    public function formatRequestData($data)
    {

        $result = [];

        foreach ($data as $item) {
            $value = isset($item['Value']) ? $item['Value'] : null;
            $result[$item['Name']] = $value;
        }
        return  $result;

    }

    public function getOperationType(int $resultCode)
    {
        switch ($resultCode) {
            case 0:
                $data = [
                    'operation_type' => 'stk_callback_success',
                    'status' => 'valid',
                ];
                return $data;
                break;
            case 1001:
                $data = [
                    'operation_type' => 'stk_callback_unable_to_lock_subscriber',
                    'status' => 'cancelled',
                ];
                return $data;
                break;
            case 1032:
                $data = [
                    'operation_type' => 'stk_callback_cancelled',
                    'status' => 'cancelled',
                ];
                return false;
                break;
            case 1037:
            case 1036:
                $data = [
                    'operation_type' => 'stk_callback_timeout',
                    'status' => 'cancelled',
                ];
                return $data;
                break;
            case 1101:
                $data = [
                    'operation_type' => 'stk_callback_invalid_prompt',
                    'status' => 'cancelled',
                ];
                return $data;
                break;
            default:
                $data = [
                    'operation_type' => 'stk_callback_failed',
                    'status' => 'cancelled',
                ];
                return $data;
        }
    }

}