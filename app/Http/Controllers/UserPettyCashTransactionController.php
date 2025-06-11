<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\WaAccountingPeriod;
use App\Model\WaBankAccount;
use App\Model\WaBanktran;
use App\Model\WaChartsOfAccount;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Models\PettyCashTransaction;
use App\Models\PettyCashType;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\WaPettyCashLog;

class UserPettyCashTransactionController extends Controller
{
    public function getUserWallets(Request $request): JsonResponse
    {
        try {
            if (!$request->user_id) {
                return $this->jsonify(['message' => 'User ID is required'], 422);
            }

            $pettyCashTransactions = DB::table('petty_cash_transactions')->select('user_id', 'amount', 'wallet_type_id')
                ->where('call_back_status', 'complete')->get();
            $user = User::find($request->user_id);
            $wallets = DB::table('petty_cash_types')->select('id', 'title', 'active', 'slug')->get()->map(function ($row) use ($pettyCashTransactions, $user) {
                $balance = $pettyCashTransactions->where('user_id', $user->id)->where('wallet_type_id', $row->id)->sum('amount') ?? 0;
                $row->balance = manageAmountFormat($balance);
                $row->balance_raw = $balance;
                $row->can_withdraw = $row->slug == 'travel-expense' || $row->active;

                return $row;
            });

            return $this->jsonify(['data' => $wallets], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function withdraw(Request $request): JsonResponse
    {
        return $this->jsonify(['message' => 'This process is currently blocked.'], 422);

        try {
            if (!$request->user_id) {
                return $this->jsonify(['message' => 'User ID is required'], 422);
            }

            if (!$request->wallet_id) {
                return $this->jsonify(['message' => 'Wallet ID is required'], 422);
            }

            if (!$request->amount || ((float)$request->amount < 10)) {
                return $this->jsonify(['message' => 'The provided withdrawal amount is either invalid or below 10 shillings'], 422);
            }

            $user = User::find($request->user_id);
            $walletType = PettyCashType::find($request->wallet_id);
            $walletBalance = DB::table('petty_cash_transactions')->where('user_id', $user->id)->where('wallet_type_id', $walletType->id)
                ->where('call_back_status', 'complete')->sum('amount') ?? 0;
            if ((float)$request->amount > $walletBalance) {
                return $this->jsonify(['message' => 'Withdrawal amount is more than wallet balance'], 422);
            }

            $tokenResponse = $this->authenticatePesaFlow();
            if (!$tokenResponse['success']) {
                return $this->jsonify(['message' => $tokenResponse['message']], 422);
            }

            $token = $tokenResponse['token'];
            $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $user->phone_number . "$request->amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
            $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
            $payload = [
                'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
                'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
                'amount' => "$request->amount",
                'currency' => 'KES',
                'party_b' => $user->phone_number,
                'secure_hash' => $hash,
                'type' => 'b2c',
                'notification_url' => env('APP_URL') . '/api/wallet-transactions/pesaflow/callback',
            ];
            $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';

            $response = Http::withToken($token)->post($url, $payload);
            if (!$response->ok()) {
                return $this->jsonify(['message' => $response->body()], 422);
            }

            $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
            PettyCashTransaction::create([
                'user_id' => $user->id,
                'amount' => (float)$request->amount * -1,
                'document_no' => $documentNumber,
                'wallet_type' => $walletType->title,
                'wallet_type_id' => $walletType->id,
                'parent_id' => 0,
                'reference' => $response->json()['reference'],
                'narrative' => "Wallet withdrawal from user $user->name to $user->phone_number",
                'call_back_status' => 'complete'
            ]);

            updateUniqueNumberSeries('PETTY_CASH', $documentNumber);

            return $this->jsonify(['message' => 'Withdrawal initiated successfully'], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    private function authenticatePesaFlow(): array
    {
        $response = ['success' => true];
        try {
            $payload = [
                'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                'secret' => env('PESAFLOW_B2C_AUTH_SECRET'),
            ];
            $url = env('PESAFLOW_B2C_URL') . '/oauth/generate/token';
            $apiResponse = Http::post($url, $payload);
            if (!$apiResponse->ok()) {
                $response['success'] = false;
                $response['message'] = $apiResponse->body();
            } else {
                $response['token'] = $apiResponse->json()['token'];
            }
        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function receiveCallback(Request $request): void
    {
        Log::info("Callback received from pesaflow b2c");
        Log::info(json_encode($request->all()));
        try {
            $trans = PettyCashTransaction::with('user', 'parent.travelExpenseTransaction.route')->where('reference', $request->reference)->first();
            if ($trans) {
                $trans->update(['call_back_status' => $request->status]);

                $pettyCashLog = WaPettyCashLog::where('created_at', $trans->parent->initial_approval_time)->first();

                if ($request->status == 'complete') {
                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();

                    $narrative = "{$trans->parent->travelExpenseTransaction->route->route_name} / {$trans->user->name} / {$trans->user->phone_number}";


                    $bank_account = WaBankAccount::where('account_code', '988329')->first();
                    $btran = new WaBanktran();
                    $btran->type_number = $series_module->type_number;
                    $btran->document_no = $trans->document_no;
                    $btran->bank_gl_account_code = $bank_account->getGlDetail?->account_code;
                    $btran->reference = $trans->reference;
                    $btran->trans_date = Carbon::now();
                    $btran->wa_payment_method_id = 11; //PETTY CASH
                    $btran->amount = $trans->amount;
                    $btran->wa_curreny_id = 0;
                    $btran->cashier_id = 1;
                    $btran->save();


                    $cr = new WaGlTran();
                    $cr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $cr->grn_type_number = $series_module->type_number;
                    $cr->trans_date = Carbon::now();
                    $cr->restaurant_id = 10; // MAKONGENI;
                    $cr->tb_reporting_branch = 10; // MAKONGENI;
                    $cr->grn_last_used_number = $series_module->last_number_used;
                    $cr->transaction_type = $series_module->description;
                    $cr->transaction_no = $trans->document_no;
                    $cr->narrative = $narrative;
                    $cr->reference = $trans->reference;
                    $cr->account = $bank_account->getGlDetail->account_code;
                    $cr->amount = $trans->amount;
                    $cr->save();


                    $dr = new WaGlTran();
                    $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $dr->grn_type_number = $series_module->type_number;
                    $dr->trans_date = Carbon::now();
                    $dr->restaurant_id = 10;
                    $dr->tb_reporting_branch = 10;
                    $dr->grn_last_used_number = $series_module->last_number_used;
                    $dr->transaction_type = $series_module->description;
                    $dr->transaction_no = $trans->document_no;
                    $dr->narrative = $narrative;
                    $dr->reference = $trans->reference;
                    $dr->account = '56002-038'; // Travel
                    $dr->amount = abs($trans->amount);
                    $dr->save();

                    if ($pettyCashLog) {
                        $pettyCashLog->update([
                            'successful_transactions' => ($pettyCashLog->successful_transactions ?: 0) + 1,
                            'disbursed_amount' => ($pettyCashLog->disbursed_amount ?: 0) + abs($trans->amount)
                        ]);
                    }
                } else {
                    if ($pettyCashLog) {
                        $pettyCashLog->update([
                            'failed_transactions' => ($pettyCashLog->failed_transactions ?: 0) + 1,
                            'pending_amount' => ($pettyCashLog->pending_amount ?: 0) + abs($trans->amount)
                        ]);
                    }
                }
            } else {
                Log::info("Trans reference$request->reference not found");
            }

        } catch (\Throwable $e) {
            Log::info("Call back PF failed");
            Log::info($e->getMessage());
        }
    }
}
