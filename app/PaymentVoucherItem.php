<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucherItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function voucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function payable()
    {
        return $this->morphTo('payable');
    }

    public function invoice()
    {
        return $this->belongsTo(WaSupplierInvoice::class, 'payable_id', 'wa_supp_tran_id');
    }

    public function note()
    {
        return $this->belongsTo(FinancialNote::class, 'payable_id', 'wa_supp_tran_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(FinancialNote::class, "wa_supp_tran_id", 'payable_id',)->where('type', 'CREDIT');
    }

    public function debitNotes()
    {
        return $this->hasMany(FinancialNote::class, "wa_supp_tran_id", 'payable_id',)->where('type', 'DEBIT');
    }
}
