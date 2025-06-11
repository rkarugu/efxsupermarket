<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerEquityPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tenderEntry()
    {
        return $this->hasOne(WaTenderEntry::class, 'reference', 'transaction_reference');
    }
}
