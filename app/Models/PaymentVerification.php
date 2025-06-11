<?php

namespace App\Models;

use App\Enums\Status\PaymentVerification as StatusPaymentVerification;
use App\Model\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentVerification extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'branch_id');
    }

    public function systemVerification(): HasMany
    {
        return $this->hasMany(PaymentVerificationSystem::class, 'payment_verification_id');
    }

    public function bankVerification(): HasMany
    {
        return $this->hasMany(PaymentVerificationBank::class, 'payment_verification_id');
    }

    public function systemVerificationVerified()
    {
        return $this->hasMany(PaymentVerificationSystem::class, 'payment_verification_id')->where('status', StatusPaymentVerification::Verified->class);
    }

    public function getVerifying()
    {
        return $this->systemVerification()->where('status', '!=', StatusPaymentVerification::Pending->value)->get();
    }

    public function getMissingBank()
    {
        return $this->bankVerification()->where('status', StatusPaymentVerification::Pending->value)->get();
    }

    public function getMissingSystem()
    {
        return $this->systemVerification()->where('status', StatusPaymentVerification::Pending->value)->get();
    }

    public function isProcessing()
    {
        return $this->status == StatusPaymentVerification::Processing->value;
    }
}
