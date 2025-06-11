<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollMonth extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'name'
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->start_date)->format('F/Y')
        );
    }

    public function payrollMonthDetails()
    {
        return $this->hasMany(PayrollMonthDetail::class);
    }
}
