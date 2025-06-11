<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Services\ApiService;
use Illuminate\Support\Facades\Session;

class SupplierImpersonationController extends Controller
{
    protected $model = 'supplier-maintain-suppliers';

    public function show(WaSupplier $supplier)
    {
        if (!can('impersonate', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
        $response = $api->postRequest('/api/impersonation-url', [
            'supplier_code' => $supplier->supplier_code,
        ]);

        if (isset($response['error'])) {
            return redirect()->back()->withErrors('An error occurred reaching the portal');
        }

        if ($response['result'] == 1) {
            return redirect()->away($response['url']);
        }

        return redirect()->back()->withErrors('Supplier impersonation failed');
    }
}
