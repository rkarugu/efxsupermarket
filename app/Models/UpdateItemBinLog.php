<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateItemBinLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function previousbin(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'previous_uom_id', 'id');
    }

    public function newbin(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'new_uom_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'inventory_item_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'location_id', 'id');
    }
}
