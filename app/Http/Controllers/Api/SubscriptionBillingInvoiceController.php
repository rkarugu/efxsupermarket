<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\WaNumerSeriesCode;
use Illuminate\Http\Request;

class SubscriptionBillingInvoiceController extends Controller
{
    public function generateSubscriptionInvoice(Request $request, $reference)
    {
        $series_module = getCodeWithNumberSeries('SUBSCRIPTION_INVOICE');
        updateUniqueNumberSeries('SUBSCRIPTION_INVOICE', $series_module);
        return response()->json([
            'series_module' => $series_module
        ]);
    }
}
