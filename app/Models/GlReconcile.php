<?php

namespace App\Models;

use App\Model\WaBankAccount;
use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlReconcile extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(WaChartsOfAccount::class,'bank_account_id');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(GlReconcileInterestExpense::class,'gl_reconcile_id');
    }
}
