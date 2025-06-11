<?php

namespace App;

use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesmanShiftStoreDispatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(SalesmanShiftStoreDispatchItem::class, 'dispatch_id', 'id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class, 'shift_id', 'id');
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'bin_location_id', 'id');
    }
    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_id', 'id');
    }
    
}
