<?php

namespace App\Rules\PurchaseOrder;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Services\ApiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContactsValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $supplier = WaSupplier::find($value);
            
            // Check if SUPPLIER_PORTAL_URI is defined
            $portalUri = env('SUPPLIER_PORTAL_URI');
            if (empty($portalUri)) {
                // If supplier portal URI is not configured, don't fail validation
                // Just log a warning and continue
                \Illuminate\Support\Facades\Log::warning('SUPPLIER_PORTAL_URI not configured. Skipping supplier contact validation.');
                return;
            }
            
            // Add a timeout to the API request to prevent long hangs
            $api = new ApiService($portalUri);
            $response = $api->postRequest('/api/lpo-contacts', [
                'supplier_code' => $supplier->supplier_code,
                'supplier_email' => $supplier->email,
            ]);

            // Only validate if we get a proper response
            if (isset($response['error'])) {
                // Log error but don't fail validation
                \Illuminate\Support\Facades\Log::warning('Supplier portal connection issue: ' . ($response['error'] ?? 'Unknown error'));
                return;
            }

            if (isset($response['result']) && $response['result'] == -1) {
                $fail("The Supplier has not setup an LPO recipient email on the Supplier Portal. Kindly notify the supplier to update this, in order to get LPO notifications");
            }
        } catch (\Exception $e) {
            // Log the exception but don't fail validation
            \Illuminate\Support\Facades\Log::error('Exception in ContactsValidation: ' . $e->getMessage());
            return;
        }
    }
}
