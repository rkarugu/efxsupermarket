<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SaleCenterSmallPackDispatch extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function saleCenter(): BelongsTo
    {
        return $this->belongsTo(SaleCenterSmallPacks::class,'sale_center_small_pack_id');
    }

    public function items(): HasMany 
    {
        return $this->hasMany(SaleCenterSmallPackDispatchItems::class,'dispatch_id')->orderBy('is_received', 'asc');
    }

    public function status(): HasMany 
    {
        return $this->hasMany(SaleCenterSmallPackDispatchStatus::class,'dispatch_id');
    } 

    public function createdBy(): BelongsTo 
    {
        return $this->belongsTo(User::class,'created_by');
    } 

    public function driverDispatch(): HasOne
    {
        return $this->hasOne(SmallPackDriverDispatchItems::class,'sale_center_small_pack_dispatch_id');
    }
}
