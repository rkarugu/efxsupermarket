<?php

namespace App\Models;

use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashType extends Model
{
    protected $table = 'petty_cash_types';

    use HasFactory;
public function chart_of_account()
    {
       return $this->belongsTo(WaChartsOfAccount::class,'wa_chart_of_accounts_id');
    }
}
