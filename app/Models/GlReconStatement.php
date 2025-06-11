<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GlReconStatement extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function matched(): MorphTo
    {
        return $this->morphTo();
    }

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(PaymentVerificationBank::class,'bank_id');
    }
}
