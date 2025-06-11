<?php

namespace App\Models;

use App\Model\WaSuppTran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePaymentAllocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function advancePayment()
    {
        return $this->belongsTo(AdvancePayment::class, 'advance_payment_id');
    }

    public function transaction()
    {
        return $this->belongsTo(WaSuppTran::class, 'wa_supp_trans_id');
    }
}
