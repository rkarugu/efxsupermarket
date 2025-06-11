<?php

namespace App\Interfaces\Finance;

interface GeneralLedgerInterface
{
    public function getTrialBalanceAccountData($account);
    public function getTrialBalanceAccountDataPaginate($account);
    public function getTrialBalanceAccountDataGroupTransaction($account);
}
