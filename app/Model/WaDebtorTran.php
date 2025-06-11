<?php

namespace App\Model;

use App\CustomerEquityPayment;
use App\CustomerKcbPayment;
use App\Models\BaseModel;
use App\Models\PaymentVerificationBank;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\PaymentVerificationSystem;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\GlReconStatement;

class WaDebtorTran extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'trans_date' => 'datetime'
    ];

    public function myInvoice()
    {
        return $this->belongsTo('App\Model\WaSalesInvoice', 'wa_sales_invoice_id');
    }

    public function customerDetail()
    {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }

    public function userDetail()
    {
        return $this->belongsTo('App\Model\User', 'salesman_user_id');
    }

    public function paid_user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function customerequitypayment(): BelongsTo
    {
        return $this->belongsTo(CustomerEquityPayment::class, 'transaction_reference', 'reference');
    }

    public function customerkcbpayment(): BelongsTo
    {
        return $this->belongsTo(CustomerKcbPayment::class, 'mpesa_reference', 'reference');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'salesman_id');
    }

    public function systemVerification(): HasOne
    {
        return $this->hasOne(PaymentVerificationSystem::class, 'debtor_id');
    }

    public function branchMain(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'branch_id');
    }

    public function verificationBank()
    {
        return $this->hasOne(PaymentVerificationBank::class, 'matched_debtors_id');
    }

    public function glMatched(): MorphOne
    {
        return $this->morphOne(GlReconStatement::class, 'matched');
    }
}
