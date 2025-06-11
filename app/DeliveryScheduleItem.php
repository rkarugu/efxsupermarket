<?php

namespace App;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryScheduleItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['tonnage'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DeliverySchedule::class, 'delivery_schedule_id', 'id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }

    public function tonnage(): Attribute
    {
        $itemTonnage = $this->inventoryItem->net_weight ?? 0;
        $totalTonnage = 0;
        try {
            $totalTonnage = (float)$itemTonnage * $this->received_quantity;
        } catch (\Throwable $e) {
            // pass
        }

        return Attribute::make(
            get: fn () => $totalTonnage
        );
    }
}
