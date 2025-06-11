<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SupplierBillingController extends Controller
{
    public function billingsBankDepositsIndex(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'supplier-billing-bank-deposits';
        $title = 'Supplier Billing Bank Deposits';
        $model = 'supplier-billing-bank-deposits';

        if (!can('view', $pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $request_data = null;
        $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
        $wallet_bank_slips_data = $api->postRequest('/api/get-all-wallet-bank-slip-requests', $request_data);

    }
}
