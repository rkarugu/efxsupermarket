<?php

namespace App\Models;

use App\PaymentVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBankFileItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function voucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function bankFile()
    {
        return $this->belongsTo(WaBankFile::class, 'wa_bank_file_id');
    }
}
