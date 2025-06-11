<?php

namespace App\Models;

use App\Model\WaDebtorTran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentVerificationBank extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function verificationRange(): BelongsTo
    {
        return $this->belongsTo(PaymentVerification::class, 'payment_verification_id');
    }

    public function verificationSystem(): BelongsTo
    {
        return $this->belongsTo(PaymentVerificationSystem::class, 'verification_system_id');
    }

    public function matchedDebtor(): BelongsTo
    {
        return $this->belongsTo(WaDebtorTran::class, 'matched_debtors_id');
    }

    public function debtors(): HasOne {
        return $this->hasOne(WaDebtorTran::class, 'bank_statement_id');
    }

    public function bankError(): HasMany
    {
        return $this->hasMany(BankStatementBankError::class,'payment_verification_bank_id');
    }

    public function stockDebtor(): BelongsTo
    {
        return $this->belongsTo(StockDebtorTran::class,'stock_debtor_tran_id');
    }
}
