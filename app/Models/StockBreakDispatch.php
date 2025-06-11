<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockBreakDispatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'dispatch_number',
    ];

    public function dispatchNumber(): Attribute
    {
        $number = match (true) {
            $this->id < 10 => '00000',
            ($this->id >= 10) && ($this->id < 100) => '0000',
            ($this->id >= 100) && ($this->id < 1000) => '000',
            ($this->id >= 1000) && ($this->id < 10000) => '00',
            ($this->id >= 10000) && ($this->id < 100000) => '0',
            default => '',
        };

        return Attribute::make(get: fn() => "SBD-" . $number . "$this->id");
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockBreakDispatchItem::class, 'dispatch_id', 'id');
    }
}
