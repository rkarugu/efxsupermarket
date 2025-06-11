<?php

namespace App\Models;

use App\PaymentVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaPaymentMode extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function cheque()
    {
        return self::where('mode', 'CHEQUE')->first();
    }

    public function vouchers()
    {
        return $this->hasMany(PaymentVoucher::class, 'wa_payment_mode_id');
    }
}
