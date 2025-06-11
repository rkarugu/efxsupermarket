<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteMasterPlan extends Model
{
    protected $guarded = [];

    public function segments(): HasMany
    {
        return $this->hasMany(RouteMasterPlanSegment::class, 'route_master_plan_id', 'id');
    }
}
