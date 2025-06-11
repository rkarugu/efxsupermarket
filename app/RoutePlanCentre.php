<?php

namespace App;

use App\Model\DeliveryCentres;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutePlanCentre extends Model
{
    //

    protected $fillable = [
        'route_plan_id',
        'delivery_centre_id',
        'duration',
    ];

    public function plan():BelongsTo{
        return $this->belongsTo(RoutePlan::class, 'route_plan_id');
    }

    public function centre():BelongsTo{
        return $this->belongsTo(DeliveryCentres::class, 'delivery_centre_id');
    }
}
