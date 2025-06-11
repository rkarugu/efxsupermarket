<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaUnitOfMeasure;
use App\Model\DeliveryCentres;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleCenterSmallPackDispatchStatus extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(SaleCenterSmallPackDispatch::class,'dispatch_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'bin_id');
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'dispatcher_id');
    }

    public  function center(): BelongsTo
    {
        return $this->belongsTo(DeliveryCentres::class,'center_id');
    }
}
