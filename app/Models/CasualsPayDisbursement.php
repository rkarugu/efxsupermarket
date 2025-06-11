<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasualsPayDisbursement extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expunged' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('expunged', function (Builder $builder) {
            $builder->whereNot('expunged', true);
        });
    }
    
    public function casualsPayPeriodDetail()
    {
        return $this->belongsTo(CasualsPayPeriodDetail::class);
    }
}
