<?php

namespace App\Models;

use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class GlReconcileInterestExpense extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(WaChartsOfAccount::class,'chart_of_account_id');
    }

    public function statement(): MorphOne
    {
        return $this->morphOne(GlReconStatement::class, 'matched');
    }
}
