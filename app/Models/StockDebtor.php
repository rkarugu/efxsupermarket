<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockDebtor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class,'employee_id');
    }

    public function stockDebtorTrans(): HasMany
    {
        return $this->hasMany(StockDebtorTran::class,'stock_debtors_id');
    }

    public function stockDebtorTranItems(): HasMany
    {
        return $this->hasMany(StockDebtorTranItem::class,'stock_debtors_id');
    }

    public function getCurrentBalance(){
        return DB::table('stock_debtor_trans')->where('stock_debtors_id',$this->id)->sum('total');
    }
}
