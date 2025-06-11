<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\PettyCashRequestController;
use App\Http\Controllers\Api\SupplierIncentivesController;
use App\Models\Disbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PesaflowDisbursementController extends controller
{
    public function receiveResultCallback(Request $request, $id){
        $response  = json_encode($request->all());
        Log::info($id);
        Log::info($response);

        $disbursement = Disbursement::find($id);
        /*update the disbursement*/
        if ($disbursement) {

            $status = $request->status;

            $disbursement->callback_status = $status;
            $disbursement->payment_status = $status;

            if ($status == 'successful') {
                $disbursement->receipt_no = Str::random(8);
            } else {
                $disbursement->payment_failure_reason =$response;
            }

            $disbursement->save();
            SupplierIncentivesController::processCallback($disbursement);

        } else {
            Log::info("Disbursement not found with ID: $id");
        }


    }
}