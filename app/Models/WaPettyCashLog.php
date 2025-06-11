<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPettyCashLog extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'petty_cash_transaction_ids' => 'json',
        'initiated_time' => 'datetime',
        'approved_time' => 'datetime',
    ];

    protected $appends = [
        'approved_transactions',
        'approved_amount',
        'formatted_petty_cash_type',
        'order_delivery_date'
    ];

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function formattedPettyCashType(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->petty_cash_type == 'travel-order-taking') {
                    return 'Order Taking';
                } else if ($this->petty_cash_type == 'travel-delivery') {
                    return 'Travel Delivery';
                }
            },
        );
    }

    public function approvedTransactions(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->transactions_count - $this->declined_transactions,
        );
    }

    public function approvedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_amount - $this->declined_amount,
        );
    }

    public function orderDeliveryDate(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->petty_cash_transaction_ids[0]) ? PettyCashTransaction::find($this->petty_cash_transaction_ids[0])->created_at->format('Y-m-d') : '',
        );
    }

    public function pettyCashTransactions()
    {
        return PettyCashTransaction::whereIn('id', $this->petty_cash_transaction_ids)->get();
    }


}
