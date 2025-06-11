<?php

namespace App\Http\Controllers\Api;

use Ably\Log;
use App\Http\Controllers\Controller;
use App\Model\WaStockMove;
use App\Models\Disbursement;
use App\Models\SalesmanSupplierIncentiveEarning;
use App\Services\DarajaDisbursementService;
use App\Services\PesaFlowDisbursementService;
use App\Services\PesaFlowMpesaPaymentService;
use App\Services\SupplierIncentiveCalculator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SupplierIncentivesController extends Controller
{
    public function activeIncentives()
    {
        $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
        $inputs['supplier_email'] = '';
        return $api->get_active_incentives(
            $inputs
        );
    }

    public function salesmanIncentives(Request  $request)
    {

        /*get active */

        $user = JWTAuth::toUser($request->token);
//        $user = User::find(1);
        $start = now()->startOfMonth();
        $end = now();
        $search = $request->search ?? '';
        $incentives = DB::table('salesman_supplier_incentive_earnings as ssi')
            ->join('wa_suppliers as s', 's.supplier_code', '=', 'ssi.supplier_code')
            ->join('wa_inventory_items as ii', 'ii.stock_id_code', '=', 'ssi.stock_id_code')
            ->select(
                'ssi.stock_id_code',
                'ssi.quantity',
                'ssi.incentive as scheme_name',
                'ssi.created_at',
                'ssi.target',
                'ssi.reward',
                'ii.title as product',
                's.name as supplier',
                DB::raw('ssi.quantity * ssi.reward AS earning')
            )
            ->where('ssi.user_id', $user->id)
            ->whereBetween('ssi.created_at', [$start, $end])
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('s.name', 'like', "%{$search}%")
                        ->orWhere('ii.title', 'like', "%{$search}%");
                });
            })
            ->orderBy('ssi.created_at')
            ->cursorPaginate();


        $total_earnings = DB::table('salesman_supplier_incentive_earnings as ssi')
            ->select(
                DB::raw('SUM(ssi.quantity * ssi.reward) AS total_earning')
            )
            ->where('ssi.user_id', $user->id)
            ->whereBetween('ssi.created_at',[$start,$end])
            ->first();


        $response = [
            'incentives' => $incentives,
            'total_earnings' => $total_earnings->total_earning ?? 0,
        ];

        return response()->json($response);
    }

    public function process(Request $request)
    {
        $payments =  $request->payments;
        $source = $request->source;
        /*get all salesmen and amounts*/

        foreach ($payments as $payment)
        {
            /*create a disbursement*/
            \Illuminate\Support\Facades\Log::info('B2C recording disbursement');

            $msisdn = '254'. substr($payment['account_number'], -9);
            $disbursement = Disbursement::create([
                'phone_number' => $msisdn,
                'amount' => ceil((int)$payment['amount_out']),
                'originator_conversation_id' => Str::uuid()->toString(),
                'disbursement_paybill' => env('DARAJA_B2C_ACCOUNT'),
                'narration' => $payment['description'],
                'source' => $source,
                'source_wallet_id' => $payment['identifier'],
            ]);

            $disbursement->update([
                'document_no' => Disbursement::buildDocumentNumber($disbursement->id)
            ]);

            /*call PesaFlow*/
            $pesaflow = new PesaFlowDisbursementService();
            $callback = env('APP_URL') . "/api/disbursements/pesaflow/$disbursement->id/callback";
            $pesaflow->initiateWithdrawal($msisdn, ceil((int)$payment['amount_out']),$callback);

            /*daraja*/
//            $darajaService  = new DarajaDisbursementService();
//            $darajaService->disburse($payment['account_number'], $payment['amount_out'], $payment['description'], $source, $payment['identifier']);

        }

        return response()->json('Received For Processing');


    }

    public static function processCallback($disbursement)
    {
        $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
        $res =
        [
            'source_wallet_id'=>$disbursement->source_wallet_id,
            'reference'=>$disbursement->receipt_no,
            'status'=> $disbursement->payment_status,
            'payment_failure_reason'=> $disbursement->payment_failure_reason,
            'time'=> now()
        ];

      $api->get_salesman_incentives_callback($res);

    }
}
