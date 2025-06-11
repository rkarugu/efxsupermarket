<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryManShift extends Model
{
    protected $guarded = [];

    public function shiftCustomers(): HasMany
    {
        return $this->hasMany(DeliveryManShiftCustomer::class, 'deliveryman_shift_id', 'id');
    }
}
