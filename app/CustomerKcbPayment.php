<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerKcbPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tenderEntry()
    {
        return $this->hasOne(WaTenderEntry::class, 'reference', 'mpesa_reference');
    }
}
