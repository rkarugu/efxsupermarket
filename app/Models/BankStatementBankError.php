<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementBankError extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function statement(): BelongsTo
    {
        return $this->belongsTo(PaymentVerificationBank::class,'payment_verification_bank_id');
    }
}
