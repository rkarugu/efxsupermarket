<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Model\VehicleType;



class VehicleModel extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(VehicleSupplier::class,'suppliers');
    }
}
