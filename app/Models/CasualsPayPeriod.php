<?php

namespace App\Models;

use App\Model\User;
use App\Model\Restaurant;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasualsPayPeriod extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'name'
    ];

    protected $casts = [
        'initial_approval' => 'boolean',
        'final_approval' => 'boolean',
        'initial_approval_date' => 'datetime',
        'final_approval_date' => 'datetime',
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->start_date)->format('F/Y')
        );
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function casualsPayPeriodDetails()
    {
        return $this->hasMany(CasualsPayPeriodDetail::class);
    }

    public function initialApprover()
    {
        return $this->belongsTo(User::class, 'initial_approver');
    }

    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approver');
    }

    public function casuals()
    {
        return $this->hasManyThrough(Casual::class, CasualsPayPeriodDetail::class, 'casuals_pay_period_id', 'id', 'id', 'casual_id');
    }
}
