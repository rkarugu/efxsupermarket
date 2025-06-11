<?php

namespace App\Models;

use App\Model\WaUnitOfMeasure;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaLocationStoreUom extends Model
{
    use HasFactory;
    protected $table = "wa_location_store_uom";

    protected $guarded = [];

    public function unit_of_measure()
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'uom_id');
    }

    public function locationStore()
    {
        return $this->belongsTo(WaLocationAndStore::class, 'location_id');
    }
}
