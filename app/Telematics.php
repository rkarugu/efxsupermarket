<?php

namespace App;

use Exception;

class Telematics
{

    /**
     * @throws Exception
     */
    public function makeApiCall($endpoint, $data = [], $method = 'GET')
    {
        $url = env('TELEMATICS_URL') . $endpoint;

        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
        }

        $ch = curl_init($url);

        $headers = ['Content-Type: application/json'];

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception("Failed to make API call: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function authenticate()
    {
        return $this->makeApiCall('/api/login', ['email' => 'roy.karugu@gmail.com', 'password' => 'Test@123'], "POST");
    }

    public function getDevices()
    {

        $auth_response = $this->authenticate();
        $user_api_hash = $auth_response["user_api_hash"];
        return $this->makeApiCall('/api/get_devices', ['user_api_hash' => $user_api_hash, 'lang' => 'en']);
    }

    public function getGeneralInformationReport($format = 'json')
    {
        $auth_response = $this->authenticate();
        $user_api_hash = $auth_response["user_api_hash"];
        return $this->makeApiCall('/api/generate_report', [
            'user_api_hash' => $user_api_hash,
            'lang' => 'en',
            'date_from' => '2023-10-27 00:00',
            'date_to' => '2023-10-28 00:00',
            'devices' => [61],
            'format' => $format,
            'type' => 49,
        ]);
    }
}
