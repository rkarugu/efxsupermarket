<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeDiscountDemandItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function demand()
    {
        return $this->belongsTo(TradeDiscountDemand::class, 'trade_discount_demand_id');
    }

    public function discount()
    {
        return $this->belongsTo(TradeDiscount::class, 'trade_discount_id');
    }
}
