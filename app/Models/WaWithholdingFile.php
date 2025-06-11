<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaBankAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaWithholdingFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(WaBankAccount::class, 'wa_bank_account_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function items()
    {
        return $this->hasMany(WaWithholdingFileItem::class, 'wa_withholding_file_id');
    }

    public function payment()
    {
        return $this->hasOne(WithholdingPaymentVoucher::class, 'withholding_file_id');
    }
}
