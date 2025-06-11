<?php

namespace App;

use App\Model\User;
use App\Model\WaChartsOfAccount;
use App\Model\WaCustomer;
use App\Model\WaPosCashSalesPayments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaTenderEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'trans_date' => 'datetime'
    ];

    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'account_code', 'account_code');
    }

    public function customer()
    {
        return $this->belongsTo(WaCustomer::class, 'customer_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customerequitypayment(): BelongsTo
    {
        return $this->belongsTo(CustomerEquityPayment::class, 'transaction_reference', 'reference');
    }

    public function customerkcbpayment(): BelongsTo
    {
        return $this->belongsTo(CustomerKcbPayment::class, 'mpesa_reference', 'reference');
    }

}
