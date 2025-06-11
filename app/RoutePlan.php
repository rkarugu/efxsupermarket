<?php

namespace App;

use App\Model\DeliveryCentres;
use App\Model\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoutePlan extends Model
{
    //

    protected $fillable = [
        'route_id',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng',
        'total_distance',
        'total_time',
        'total_fuel',
        'start_time',
        'end_time',
    ];


    public function route():BelongsTo{
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function routePlanCenter():HasMany{
        return $this->hasMany(RoutePlanCentre::class);
    }
}
