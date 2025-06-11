<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeDiscountDemand extends Model
{
    use HasFactory;

    const PENDING = 0;

    const PROCESSED = 1;

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(TradeDiscountDemandItem::class, 'trade_discount_demand_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('processed', self::PENDING);
    }

    public function scopeProcessed(Builder $query)
    {
        return $query->where('processed', self::PROCESSED);
    }

    public function isPending()
    {
        return $this->status == self::PENDING;
    }

    public function isProcessed()
    {
        return $this->status == self::PROCESSED;
    }
}
