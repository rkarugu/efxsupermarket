<?php

namespace App\Repositories\Finance;


use App\Interfaces\Finance\GeneralLedgerInterface;
use App\Model\WaGlTran;
use Illuminate\Support\Facades\DB;

class GeneralLedgerRepository implements GeneralLedgerInterface
{
    
    public function getTrialBalanceAccountData($account)
    {
        try {
            $data = DB::table('wa_gl_trans')
                    ->leftJoin('restaurants','restaurants.id','wa_gl_trans.tb_reporting_branch')
                    ->when(request('branch'), function ($query, $branch) {
                        return $query->where('wa_gl_trans.restaurant_id', $branch);
                    })
                    ->when(request('transaction_type'), function ($query, $transactionType) {
                        return $query->where('wa_gl_trans.transaction_no','like', $transactionType.'%');
                    })
                    ->where('wa_gl_trans.account',$account)
                    ->when(request('search'), function ($query, $search) {
                        return $query->where('wa_gl_trans.narrative', 'like', '%' . $search . '%')
                                ->orWhere('wa_gl_trans.transaction_no', 'like', '%' . $search . '%')
                                ->orWhere('wa_gl_trans.transaction_type', 'like', '%' . $search . '%');
                    });
                    if(request()->filled('start-date') && request()->filled('end-date')){
                        $data->whereBetween('wa_gl_trans.trans_date', [request()->input('start-date').' 00:00:00', request()->input('end-date').' 23:59:59']);
                    }
                   return $data->select(
                        'wa_gl_trans.trans_date as created_at',
                        'restaurants.name as branch_name',
                        'wa_gl_trans.narrative',
                        'wa_gl_trans.reference',
                        'wa_gl_trans.transaction_type',
                        'wa_gl_trans.transaction_no',
                        'wa_gl_trans.amount'
                        )
                    ->get();
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
    
    public function getTrialBalanceAccountDataPaginate($account)
    {
        try {
            $data = DB::table('wa_gl_trans')
                    ->leftJoin('restaurants','restaurants.id','wa_gl_trans.tb_reporting_branch')
                    ->when(request('start-date'), function ($query, $date) {
                        return $query->where('wa_gl_trans.created_at', '>=', $date);
                    })
                    ->when(request('end-date'), function ($query, $date) {
                        return $query->where('wa_gl_trans.created_at', '<=', $date);
                    })
                    ->when(request('branch'), function ($query, $branch) {
                        return $query->where('wa_gl_trans.restaurant_id', $branch);
                    })
                    ->when(request('transaction_type'), function ($query, $transactionType) {
                        return $query->where('wa_gl_trans.transaction_no','like', $transactionType.'%');
                    })
                    ->where('wa_gl_trans.account',$account);
                    return $data->select(
                        'wa_gl_trans.created_at',
                        'restaurants.name as branch_name',
                        'wa_gl_trans.narrative',
                        'wa_gl_trans.reference',
                        'wa_gl_trans.transaction_type',
                        'wa_gl_trans.transaction_no',
                        'wa_gl_trans.amount'
                        )
                    ->paginate(request('showing') ?? 10)
                    ->withQueryString();
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    public function getTrialBalanceAccountDataGroupTransaction($account)
    {
        try {
            $transaction = DB::table('wa_gl_trans')
                    ->leftJoin('restaurants','restaurants.id','wa_gl_trans.tb_reporting_branch')

                    ->where('wa_gl_trans.account',$account)
                    ->when(request('search'), function ($query, $search) {
                        return $query->where('wa_gl_trans.narrative', 'like', '%' . $search . '%')
                                ->orWhere('wa_gl_trans.transaction_no', 'like', '%' . $search . '%')
                                ->orWhere('wa_gl_trans.transaction_type', 'like', '%' . $search . '%');
                    })
                    ->when(request('branch'), function ($query, $branch) {
                        return $query->where('wa_gl_trans.restaurant_id', $branch);
                    })
                    ->when(request('transaction_type'), function ($query, $transactionType) {
                        return $query->where('wa_gl_trans.transaction_no','like', $transactionType.'%');
                    });
                    if(request()->filled('start-date') && request()->filled('end-date')){
                        $transaction->whereBetween('wa_gl_trans.created_at', [request()->input('start-date').' 00:00:00', request()->input('end-date').' 23:59:59']);
                    }
                    $transaction = $transaction->select(
                        'wa_gl_trans.created_at as date',
                        'wa_gl_trans.transaction_no',
                        'wa_gl_trans.transaction_type',
                        DB::RAW("COALESCE(sum(wa_gl_trans.amount),0) as total_amount"),
                    )
                    ->groupBy('wa_gl_trans.transaction_no')
                    ->get();
            return response($transaction, 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }


}