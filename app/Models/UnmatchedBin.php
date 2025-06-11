<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use App\Model\WaUnitOfMeasure;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnmatchedBin extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function bin(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'uom_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'location_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'inventory_id', 'id');
    }
}
