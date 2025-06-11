<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\Log;

class Pesaflow
{

  /**
   * @throws Exception
   */
  public function makeApiCall($endpoint, $data = [], $method = 'GET') {
    $url = $endpoint;

    $ch = curl_init($url);


    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $response = curl_exec($ch);

    if ($response === false) {
      throw new Exception("Failed to make API call: " . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response);
  }

  public function initiate($amount, $clientIDNumber, $clientMSISDN, $clientEmail, $billRefNumber, $billDesc, $clientNames ){
    $apiClientID = env('PESAFLOW_CLIENT_ID');
    $serviceID = env('PESAFLOW_SERVICE_ID');
    $currency = env('PESAFLOW_CURRENCY');
    $secret = env('PESAFLOW_SECRET');
    $key = env('PESAFLOW_KEY');
    $url = env('PESAFLOW_URL');
    $callbackUrl = env('PESAFLOW_CALLBACK_URL');
    $notificationUrl = env('PESAFLOW_NOTIFICATION_URL');

    $data_string = $apiClientID . $amount . $serviceID . $clientIDNumber . $currency . $billRefNumber . $billDesc . $clientNames . $secret;
    $hash = hash_hmac('sha256', $data_string, $key);
    $secureHash = base64_encode($hash);

    $invoice_no_hash = EncryptionHelper::encrypt($billRefNumber);
    $postDataArray = [
      "apiClientID" => $apiClientID,
      "secureHash" => $secureHash,
      "billDesc" => $billDesc,
      "billRefNumber" => $billRefNumber,
      "currency" => $currency,
      "serviceID" => $serviceID,
      "clientMSISDN" => $clientMSISDN,
      "clientName" => $clientNames,
      "clientEmail" => $clientEmail,
      "clientIDNumber" => $clientIDNumber,
      "callBackURLOnSuccess" => $callbackUrl .'/'. $invoice_no_hash,
      "notificationURL" => $notificationUrl .'/'. $invoice_no_hash,
      "amountExpected" => $amount,
      "format" => "json"
    ];

    Log::info("PAYLOAD", $postDataArray);

    $data = http_build_query($postDataArray);

    return $this->makeApiCall($url, $data,"POST");
  }

}
