<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\PettyCashRequestController;
use App\Http\Controllers\Api\SupplierIncentivesController;
use App\Http\Controllers\Controller;
use App\Models\Disbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isFalse;

class DarajaDisbursementController extends Controller
{
    public function receiveTimeoutCallback(Request $request, $id)
    {}

    public function receiveResultCallback(Request $request, $id)
    {
        $request = json_encode($request->all());
        Log::info($id);
        Log::info($request);

        $disbursement = Disbursement::find($id);

        SupplierIncentivesController::processCallback($disbursement);

//        $result = json_decode($request, true)['Result'];
//        $reference = $result['TransactionID'];
//
//        if ($disbursement) {
//            $status = $result['ResultCode'] == '0' ? 'successful' : 'failed';
//
//            $disbursement->callback_status = $status;
//            $disbursement->payment_status = $status;
//
//            if ($status == 'successful') {
//                $disbursement->receipt_no = $reference;
//            } else {
//                $disbursement->payment_failure_reason = $result['ResultDesc'];
//            }
//
//            $disbursement->save();
//
//            if ($disbursement->source_wallet_id) {
//                /* this is a supplier request so sent response to supplier portal*/
//                SupplierIncentivesController::processCallback($disbursement);
//            }else{
//                (new PettyCashRequestController)->disbursementCallback($disbursement->parent_id, $status, $reference);
//            }
//
//
//
//        } else {
//            Log::info("Disbursement not found with ID: $id");
//        }
    }
    public function receiveResultCallback1(Request $request, $id)
    {
        $request = json_encode($request->all());
        Log::info($id);
        Log::info($request);

        $disbursement = Disbursement::find($id);
        $result = json_decode($request, true)['Result'];
        $reference = $result['TransactionID'];

        if ($disbursement) {
            $status = $result['ResultCode'] == '0' ? 'successful' : 'failed';

            $disbursement->callback_status = $status;
            $disbursement->payment_status = $status;

            if ($status == 'successful') {
                $disbursement->receipt_no = $reference;
            } else {
                $disbursement->payment_failure_reason = $result['ResultDesc'];
            }

            $disbursement->save();

            if ($disbursement->source_wallet_id) {
                /* this is a supplier request so sent response to supplier portal*/
                SupplierIncentivesController::processCallback($disbursement);
            }else{
                (new PettyCashRequestController)->disbursementCallback($disbursement->parent_id, $status, $reference);
            }



        } else {
            Log::info("Disbursement not found with ID: $id");
        }

    }
}
