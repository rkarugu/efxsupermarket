<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmallPackDriverDispatchItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function driverDispatch(): BelongsTo
    {
        return $this->belongsTo(SmallPackDriverDispatch::class,'small_pack_driver_dispatch_id')->orderBy('created_at', 'desc');
    }

    public function storeDispatch(): BelongsTo
    {
        return $this->belongsTo(SaleCenterSmallPackDispatch::class,'sale_center_small_pack_dispatch_id')->orderBy('created_at', 'desc');
    }
}
