<?php

namespace App\Models;

use App\Model\Route;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteRepresentatives extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class,'route_id');
    }
}
