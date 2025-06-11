<?php

namespace App;

use App\Model\Restaurant;
use App\Models\FuelSupplier;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelStation extends Model
{
    protected $guarded = [];

    use Sluggable;
    use SluggableScopeHelpers;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'branch_id', 'id');
    }
    public function fuelSupplier(): BelongsTo
    {
        return $this->belongsTo(FuelSupplier::class, 'fuel_supplier_id');
    }
}
