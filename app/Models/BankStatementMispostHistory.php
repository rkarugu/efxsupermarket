<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;

class BankStatementMispostHistory extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(PaymentVerificationBank::class, 'payment_verification_bank_id');
    }
}
