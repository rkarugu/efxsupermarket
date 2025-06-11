<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasualsPayPeriodDetail extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'dates' => 'json',
    ];

    public function casualsPayPeriod()
    {
        return $this->belongsTo(CasualsPayPeriod::class);
    }
    
    public function casual()
    {
        return $this->belongsTo(Casual::class);
    }

    public function casualsPayDisbursements()
    {
        return $this->hasMany(CasualsPayDisbursement::class);
    }

    public function disbursement()
    {
        return $this->hasOne(CasualsPayDisbursement::class);
    }
}
