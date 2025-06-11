<?php

namespace App\Models;

use App\Model\Route;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmallPackDriverDispatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(SmallPackDriverDispatchItems::class,'small_pack_driver_dispatch_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class,'route_id');
    }
}
