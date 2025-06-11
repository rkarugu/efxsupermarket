<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeBillingPlan extends Model
{
    use HasFactory;

    public static function plans(){
        return [
            'monthly'=>'Monthly','yearly'=>'Yearly'
        ];
    }

    public function currency(){
        return $this->belongsTo(\App\Model\WaCurrencyManager::class, 'wa_currency_manager_id');
    }
}
