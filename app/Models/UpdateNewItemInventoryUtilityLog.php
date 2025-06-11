<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateNewItemInventoryUtilityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function initiatedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by', 'id');
    }

    public function approvedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }
}
