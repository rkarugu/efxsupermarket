<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaDebtorTran;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockDebtorTran extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(StockDebtor::class,'stock_debtors_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockDebtorTranItem::class,'stock_debtor_trans_id')->orderBy('created_at','desc');
    }

    public function itemsNotProcessed(): HasMany
    {
        return $this->hasMany(StockDebtorTranItem::class,'stock_debtor_trans_id')->where('is_processed',0)->orderBy('created_at','desc');
    }

    public function nonDebtor(): BelongsTo
    {
        return $this->belongsTo(User::class,'stock_non_debtor_id');
    }

    public function bankStatement(): HasOne
    {
        return $this->hasOne(PaymentVerificationBank::class,'stock_non_debtor_id');
    }
}
