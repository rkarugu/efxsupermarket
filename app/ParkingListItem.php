<?php

namespace App;

use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingListItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'store_id', 'id');
    }
}
