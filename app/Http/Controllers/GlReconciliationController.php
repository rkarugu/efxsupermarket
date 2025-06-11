<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\WaBankAccount;
use App\Models\BankOpeningBalance;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlReconciliationController extends Controller
{
    protected string $model = 'gl-recon';
    protected string $permissionModule = 'reconciliation';

    public function showOverviewPage(): View|RedirectResponse
    {
        if (!can('gl-reconciliation', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'GL Reconciliation';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];

        $branches = Restaurant::select('id', 'name')->get();
        $channels = PaymentMethod::select('id', 'title')->where('use_for_receipts', true)->where('use_as_channel', true)->get();

        return view('gl_recon.overview', compact('title', 'model', 'branches', 'breadcrum', 'channels'));
    }

    public function getOpeningBalances(): JsonResponse
    {
        try {
            $bankAccounts = WaBankAccount::select('wa_bank_accounts.*')
                ->join('payment_methods', function ($join) {
                    $join->on('bank_account_gl_code_id', '=', 'gl_account_id')->where('use_as_channel', true);
                })
                ->get();

            foreach ($bankAccounts as $key => $account) {
                $existingBalanceRecord = BankOpeningBalance::where('bank_id', $account->id)->first();
                if (!$existingBalanceRecord) {
                    $existingBalanceRecord = BankOpeningBalance::create([
                        'bank_id' => $account->id
                    ]);
                }
            }

            $bankAccounts = WaBankAccount::select('wa_bank_accounts.id', 'account_name', 'bank_opening_balances.id as balance_id', 'bank_opening_balances.opening_balance')
                ->join('payment_methods', function ($join) {
                    $join->on('bank_account_gl_code_id', '=', 'gl_account_id')->where('use_as_channel', true);
                })
                ->join('bank_opening_balances', 'wa_bank_accounts.id', '=', 'bank_opening_balances.bank_id')
                ->get();

            return $this->jsonify($bankAccounts);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function updateOpeningBalances(Request $request)
    {
        try {
            $balances = json_decode($request->balances, true);
            foreach ($balances as $updateRecord) {
                BankOpeningBalance::find($updateRecord['balance_id'])->update(['opening_balance' => (float)$updateRecord['opening_balance']]);
            }

            return $this->jsonify([]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getRecords(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->from_date)->endOfDay();
            $branchId = $request->branch_id;
            $channel = $request->channel;

            $bankDebitsQuery = DB::select("
                select payment_method_id,sum(abs(amount)) as channel_total from payment_verification_banks 
                where bank_date between '$fromDate' and '$toDate' and type = 'debit' group by payment_method_id
            ");

            $systemDebitsQuery = DB::select("
                select payment_methods.id as payment_method_id, sum(abs(amount)) as channel_total
                from wa_banktrans
                join wa_charts_of_accounts on account_code = bank_gl_account_code
                join wa_bank_accounts on wa_charts_of_accounts.id = wa_bank_accounts.bank_account_gl_code_id
                join payment_methods on wa_bank_accounts.bank_account_gl_code_id = payment_methods.gl_account_id
                where trans_date between '$fromDate' and '$toDate'
                and amount < 0
                group by payment_method_id
            ");

            $bankCreditsQuery = DB::select("
                select payment_method_id,sum(abs(amount)) as channel_total from payment_verification_banks 
                where bank_date between '$fromDate' and '$toDate' and type = 'credit' group by payment_method_id
            ");

            $systemCreditsQuery = DB::select("
                select payment_methods.id as payment_method_id, sum(abs(amount)) as channel_total
                from wa_banktrans
                join wa_charts_of_accounts on account_code = bank_gl_account_code
                join wa_bank_accounts on wa_charts_of_accounts.id = wa_bank_accounts.bank_account_gl_code_id
                join payment_methods on wa_bank_accounts.bank_account_gl_code_id = payment_methods.gl_account_id
                where trans_date between '$fromDate' and '$toDate'
                and amount > 0
                group by payment_method_id
            ");

            $channels = WaBankAccount::select('wa_bank_accounts.account_name', 'payment_methods.id as payment_method_id', 'bank_opening_balances.opening_balance')
                ->join('payment_methods', function ($join) {
                    $join->on('bank_account_gl_code_id', '=', 'gl_account_id')->where('use_as_channel', true);
                })
                ->join('bank_opening_balances', 'wa_bank_accounts.id', '=', 'bank_opening_balances.bank_id')
                ->get();

            $records = [];
            foreach ($channels as $channel) {
                $date = Carbon::parse($request->from_date)->toDateString();
                $yesterday = Carbon::parse($request->from_date)->subDay()->endOfDay();
                $bankDebits = collect($bankDebitsQuery)->where('payment_method_id', $channel->payment_method_id)->first()?->channel_total ?? 0;
                $systemDebits = collect($systemDebitsQuery)->where('payment_method_id', $channel->payment_method_id)->first()?->channel_total ?? 0;

                $bankCredits = collect($bankCreditsQuery)->where('payment_method_id', $channel->payment_method_id)->first()?->channel_total ?? 0;
                $systemCredits = collect($systemCreditsQuery)->where('payment_method_id', $channel->payment_method_id)->first()?->channel_total ?? 0;

                $openingBalance = $channel->opening_balance;
                if ($date != '2024-03-07') {
                    $openingBalanceQuery = DB::select("
                        select sum(amount) as total
                        from wa_banktrans
                        join wa_charts_of_accounts on account_code = bank_gl_account_code
                        join wa_bank_accounts on wa_charts_of_accounts.id = wa_bank_accounts.bank_account_gl_code_id
                        join payment_methods on wa_bank_accounts.bank_account_gl_code_id = payment_methods.gl_account_id
                        where trans_date between '2024-03-07 00:00:00' and '$yesterday' 
                        and payment_methods.id = $channel->payment_method_id
                    ");

                    $openingBalance = collect($openingBalanceQuery)->sum('total');
                }

                $records[] = [
                    'date' => $date,
                    'channel' => $channel->account_name,
                    'opening_balance' => $openingBalance,
                    'bank_debits' => $bankDebits,
                    'system_debits' => $systemDebits,
                    'debits_variance' => abs($systemDebits - $bankDebits),
                    'bank_credits' => $bankCredits,
                    'system_credits' => $systemCredits,
                    'credits_variance' => abs($systemCredits - $bankCredits),
                    'closing_balance' => $openingBalance + $bankCredits - $bankDebits
                ];
            }

            $records = collect($records)->map(function ($record) {
                $record['opening_balance'] = manageAmountFormat($record['opening_balance']);
                $record['bank_debits'] = manageAmountFormat($record['bank_debits']);
                $record['system_debits'] = manageAmountFormat($record['system_debits']);
                $record['debits_variance'] = manageAmountFormat($record['debits_variance']);
                $record['bank_credits'] = manageAmountFormat($record['bank_credits']);
                $record['system_credits'] = manageAmountFormat($record['system_credits']);
                $record['credits_variance'] = manageAmountFormat($record['credits_variance']);
                $record['closing_balance'] = manageAmountFormat($record['closing_balance']);

                return $record;
            });

            return $this->jsonify($records);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
