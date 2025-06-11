<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Model\User;
use App\Model\WaDebtorTran;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentVerificationSystem extends BaseModel
{
    use HasFactory;

    public function verificationRange(): BelongsTo
    {
        return $this->belongsTo(PaymentVerification::class, 'payment_verification_id');
    }

    public function bankVerification(): HasOne
    {
        return $this->hasOne(PaymentVerificationBank::class,'verification_system_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'verified_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(WaDebtorTran::class, 'debtor_id');
    }


}
