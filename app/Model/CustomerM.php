<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class CustomerM extends Model
{
    protected $table = 'wa_customers';
    public function route_customers()
    {
        return $this->hasMany(WaRouteCustomer::class,'route_id','route_id');
    }


    public function route()
    {
        return $this->belongsTo(Route::class,'route_id');
    }
}   