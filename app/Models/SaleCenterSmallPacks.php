<?php

namespace App\Models;

use App\Model\User;
use App\Model\Route;
use App\Model\DeliveryCentres;
use App\Model\Restaurant;
use App\Model\WaInternalRequisition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SaleCenterSmallPacks extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(SaleCenterSmallPackItems::class,'sale_center_small_pack_id');
    }

    public  function center(): BelongsTo
    {
        return $this->belongsTo(DeliveryCentres::class,'center_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class,'route_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }

    public function dispatchedPacks(): HasOne
    {
        return $this->hasOne(SaleCenterSmallPackDispatch::class,'sale_center_small_pack_id');
    }

    public function createdBy(): BelongsTo 
    {
        return $this->belongsTo(User::class,'created_by');    
    }

    public function internalRequisition(): HasMany
    {
        return $this->hasMany(WaInternalRequisition::class,'center_small_pack_id');
    }
}
