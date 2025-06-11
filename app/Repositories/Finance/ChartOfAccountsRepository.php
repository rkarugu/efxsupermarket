<?php

namespace App\Repositories\Finance;


use App\Interfaces\Finance\ChartOfAccountsInterface;
use App\Model\WaChartsOfAccount;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsRepository implements ChartOfAccountsInterface
{
    
    public function getByAccount($account)
    {
        try {
            return WaChartsOfAccount::where('account_code',$account)->get()->first();
        } catch (\Exception $e) {
            return response('No Account Found', 400);
        }
    }
}
