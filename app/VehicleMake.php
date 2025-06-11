<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleMake extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class);
    }
}
