<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WaBankAccount extends Model
{
    public function getGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'bank_account_gl_code_id');
    }

    public function bank_trans()
    {
        return $this->belongsTo('App\Model\WaBanktran', 'bank_account_gl_code_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'bank_account_gl_code_id', 'gl_account_id');
    }

    public function scopeMakesPayments($query)
    {
        return $query->whereHas('paymentMethod', function ($query) {
            $query->where('use_for_payments', 1);
        })->where('account_name', 'NOT LIKE', '%wallet%');
    }
}
