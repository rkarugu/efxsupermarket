<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaBankAccount;
use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingPaymentVoucher extends Model
{
    use HasFactory;

    const PENDING = 0;

    const APPROVED = 1;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date'
    ];

    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::APPROVED);
    }

    public function isPending()
    {
        return $this->status == self::PENDING;
    }

    public function isApproved()
    {
        return $this->status == self::APPROVED;
    }

    public function withholdingFile()
    {
        return $this->belongsTo(WaWithholdingFile::class, 'withholding_file_id');
    }

    public function withholdingGlAccount()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'withholding_account_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(WaBankAccount::class, 'wa_bank_account_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
