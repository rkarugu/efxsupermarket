<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Models\PettyCashTransaction;
use App\Models\WaPettyCashLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;

class UndisbursedPettyCashController extends Controller
{
    public function undisbursedPettyCash()
    {
        if (!can('view', 'undisbursed-petty-cash')) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Petty Cash Failed Disbursement';
        $model = 'undisbursed-petty-cash';
        $breadcum = [
            'Petty Cash' => '',
            'Failed Disbursement' => ''
        ];

        $request = request();

        $query = PettyCashTransaction::with([
            'user',
            'travelExpenseTransaction.route.branch',
            'child'
        ])
            ->where('initial_approval_status', 'pending')
            ->where('amount', '>', 0)
            ->where('initial_approved_by', null)
            ->where('status', 0)
            ->has('travelExpenseTransaction.route.branch');

        if ($request->filled('branch')) {
            $query->whereHas('travelExpenseTransaction.route.branch', function ($query) use ($request) {
                $query->where('id', $request->branch);
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('user', function ($query) use ($request) {
                $roleId = $request->type == 'order-taking' ? 4 : 6;
                $query->where('role_id', $roleId);
            });
        }

        $query->orderBy('created_at', 'desc');

        $undisbursedPettyCash = $query->get();

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();
        return view('admin.petty_cash_approvals.failed_disbursement', compact('model', 'title', 'breadcum', 'routes', 'undisbursedPettyCash', 'branches'));
    }

    public function approveUndisbursedPettyCash(Request $request)
    {
        try {
            $total = 0;
            $initiated = 0;
            $declinedTransactions = 0;
            $declinedTransactionsAmount = 0;
            $approvalIds = json_decode($request->resend_ids, true);
            foreach ($approvalIds as $approvalId) {
                try {
                    $transaction = PettyCashTransaction::find((int)$approvalId);
                    $amount = $transaction->amount;
                    if ($request->get("resend_$approvalId") == 'on') {

                        // Send money
                        $user = User::find($transaction->user_id);

                        $tokenResponse = $this->authenticatePesaFlow();
                        $token = $tokenResponse['token'];
                        $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $user->phone_number . "$transaction->amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
                        $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
                        $payload = [
                            'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
                            'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
                            'amount' => "$amount",
                            'currency' => 'KES',
                            'party_b' => $user->phone_number,
                            'secure_hash' => $hash,
                            'type' => 'b2c',
                            'notification_url' => env('APP_URL') . '/api/wallet-transactions/pesaflow/callback',
                        ];

                        Log::info("PF B2C Payload: " . json_encode($payload));

                        $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';

                        $response = Http::withToken($token)->post($url, $payload);
                        if (!$response->ok()) {
                            $transaction->update([
                                'final_approval_status' => 'pending',
                                'final_approved_by' => null,
                                'final_approval_time' => null,
                            ]);
                        } else {
                            $transaction->update([
                                'initial_approval_status' => 'approved',
                                'initial_approved_by' => Auth::id(),
                                'initial_approval_time' => Carbon::now(),
                                'final_approval_status' => 'approved',
                                'final_approved_by' => Auth::id(),
                                'final_approval_time' => Carbon::now(),
                                'amount' => $amount,
                                'old_amount' => $amount,
                            ]);
                        }
                        Log::info("PF Response: " . $response->body());

                        $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
                        PettyCashTransaction::create([
                            'user_id' => $user->id,
                            'amount' => (float)$transaction->amount * -1,
                            'document_no' => $documentNumber,
                            'wallet_type' => $transaction->wallet_type,
                            'wallet_type_id' => $transaction->wallet_type_id,
                            'parent_id' => $transaction->id,
                            'reference' => $response->json()['reference'],
                            'narrative' => "Travel deposit to $user->name - $user->phone_number",
                            'call_back_status' => 'pending'
                        ]);

                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                        $initiated += 1;
                    } else {
                        $declinedTransactions++;
                        $declinedTransactionsAmount += (float)$transaction->amount;
                    }
                } catch (Throwable $e) {
                    Log::info("Failed for $transaction->document_no: " . $e->getMessage());
                }
            }

            if ($initiated > 0) {
                $pettyCashLog = WaPettyCashLog::where('created_at', Carbon::now())->first();

                if ($pettyCashLog) {
                    $pettyCashLog->update([
                        'approved_by' => Auth::id(),
                        'approved_time' => Carbon::now(),
                        'declined_transactions' => $declinedTransactions,
                        'declined_amount' => $declinedTransactionsAmount
                    ]);
                }
            }
            return response()->json([
                'message' => 'Petty cash approved successfully.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectUndisbursedPettyCash(Request $request)
    {
        try {
            $rejectIds = json_decode($request->resend_ids, true);
            foreach ($rejectIds as $rejectId) {
                $petty_cash_transactions = PettyCashTransaction::where('id', $rejectId)->first();
                $petty_cash_transactions->status = 1;
                $petty_cash_transactions->save();
            }
            return response()->json([
                'message' => 'Petty cash rejected successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
