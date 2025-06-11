<?php

namespace App\Services;

use App\Model\User;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesItemReturns;
use App\Models\CashDropTransaction;
use App\Models\DropLimitAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashierManagementService
{
    public function dropCash(User $cashier, User $chiefCashier, int $amount)
    {
        $startDate =  now()->startOfDay();
        $endDate =   now();


            $cth = $cashier->cashAtHand();
            if ($cth < 1)
            {
                return [
                    'status'=> false,
                    'message'=> "You cannot Drop 0",
                ];
            }
            /*get pending Returns*/
            $pending_returns = WaPosCashSalesItemReturns::with('PosCashSale')
                ->whereHas('PosCashSale', function ($q) use ($cashier) {
                    $q->where('attending_cashier',$cashier->id);
                })
                ->whereDate('return_date', today())
                ->whereNull('accepted_at')->count();

            if ($pending_returns > 0) {
                return [
                    'status'=> false,
                    'message'=> "Cashier has $pending_returns Pending Return. The returns need to be processed be before dropping cash.",
                ];
            }

            $drop =  DB::transaction(function () use ($amount, $cashier, $chiefCashier) {

                $amount = ceil($amount);
                $balance = 0;
                $ref = mt_rand(100000, 999999);

                return CashDropTransaction::create([
                    'amount' => $amount,
                    'cashier_balance' => $balance,
                    'user_id' => $chiefCashier->id,
                    'cashier_id' => $cashier->id,
                    'reference' => 'DRP-'.$ref,
                ]);
            });

            if ($drop)
            {
                $drop_amount = CashDropTransaction::whereBetween('created_at', [$startDate, $endDate])
                    ->where('cashier_id', $cashier->id)
                    ->sum('amount');

                /*mark last drop alert as used*/
                try {
                    $last = DropLimitAlert::where('user_id', $cashier->id)->latest()->first();
                    if ($last)
                    {
                        $last->used = true;
                        $last->save();
                    }
                } catch (\Exception $e) {

                }
                return  [
                    'status'=> true,
                    'message'=> 'Cash Dropped Successfully',
                    'cashier'=> $cashier->id,
                    'amount_dropped'=> $drop_amount,
                    'drop'=> $drop,
                ];
            }

        return [
            'status'=> false,
            'message'=> 'Error Dropping cash.',
        ];

    }

    private function generateUniqueCode()
    {
        return mt_rand(100000, 999999);
    }
}