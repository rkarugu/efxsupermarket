<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeAgreement extends Model
{
    use HasFactory;

    public function supplier()
    {
        return $this->belongsTo(\App\Model\WaSupplier::class, 'wa_supplier_id');
    }

    public function discounts()
    {
        return $this->hasMany(TradeAgreementDiscount::class, 'trade_agreements_id');
    }

    public function offers()
    {
        return $this->hasMany(TradeProductOffer::class, 'trade_agreements_id');
    }

    public function billing_charges(){
        return $this->hasMany(TradeBillingPlan::class,'trade_agreement_id');
    }
}
