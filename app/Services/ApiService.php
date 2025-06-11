<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl; //env('SUPPLIER_PORTAL_LPO_API');
    }

    public function postRequest($endpoint, $data)
    {
        // Set a short timeout to prevent long waits if supplier portal is down
        $maxAttempts = 1; // Only try once - don't retry
        $timeout = 3; // 3 seconds timeout
        
        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => 'Bearer '.env('SUPPLIER_ISR_TOKEN')
                ])
                ->post($this->baseUrl . $endpoint, $data);
    
            if ($response->successful()) {
                return $response->json();
            } else {
                // Log the error but return a structured error response
                \Illuminate\Support\Facades\Log::warning(
                    'Supplier Portal API call failed', 
                    [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]
                );
                
                return [
                    'error' => 'Request failed',
                    'status' => $response->status(),
                    'message' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            // Log the exception
            \Illuminate\Support\Facades\Log::error(
                'Supplier Portal API call exception', 
                [
                    'endpoint' => $endpoint,
                    'exception' => $e->getMessage()
                ]
            );
            
            // Return a structured error response
            return [
                'error' => 'Connection failed',
                'status' => 0,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function get_discount_list($data){
        return $this->postRequest('/api/supplier/discount-list',$data);
    }

    public function get_supplier_portal_logs($data){
        return $this->postRequest('/api/supplier-logs',$data);
    }

    public function get_supplier_staff($data){
        return $this->postRequest('/api/supplier/get-staff-list',$data);
    }

    public function update_supplier_staff_details($data){
        return $this->postRequest('/api/supplier/update-staff',$data);
    }

    public function suspend_supplier($data){
        return $this->postRequest('/api/supplier/suspend',$data);
    }

    public function get_portal_suppliers($owner){
        return $this->postRequest('/api/supplier/list/'.$owner,[]);
    }
    
    public function get_portal_supplier_details($owner, $data){
        return $this->postRequest('/api/supplier/get/'.$owner,$data);
    }

    public function get_active_incentives($data){
        return $this->postRequest('/api/incentives/active',$data);
    }

    public function get_salesman_incentives(array $inputs)
    {
        return $this->postRequest('/api/incentives/salesman-earning',$inputs);
    }

    public function get_salesman_incentives_callback(array $inputs)
    {
        return $this->postRequest('/api/incentives/salesman-earning/process/callback',$inputs);
    }

    public function get_incentive_by_product($data)
    {
        return $this->postRequest('/api/incentives/incentive-by-product',$data);
    }
}
