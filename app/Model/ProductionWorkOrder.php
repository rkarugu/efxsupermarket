<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class ProductionWorkOrder extends Model
{
    protected $fillable = [
        'wa_inventory_item_id',
        'production_quantity',
        'production_plant_id',
        'description',
        'current_step_number',
        'status',
        'created_at',
        'updated_at',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }

    public function getOrderReference(): string
    {
        // TODO: Upon upgrading, use match statement for efficiency and readability.

        $prefix = "";
        switch (true) {
            case $this->id < 10:
                $prefix = "00000";
                break;

            case ($this->id >= 10 && $this->id < 100):
                $prefix = "0000";
                break;

            case($this->id >= 100 && $this->id < 1000):
                $prefix = "000";
                break;

            case($this->id >= 1000 && $this->id < 10000):
                $prefix = "00";
                break;

            case($this->id >= 10000 && $this->id < 100000):
                $prefix = "0";
                break;

            default:
                break;
        }

        return "WO-$prefix$this->id";
    }

    public function getStatus(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getDisplayProductionQuantity(): string
    {
        $packSizeName = "{$this->inventoryItem->pack_size->title}";
        if ((float)$this->production_quantity != 1) {
            $packSizeName = $packSizeName . "s";
        }

        return "$this->production_quantity " . ucfirst(strtolower($packSizeName));
    }

    public function getBomAvailability(): bool
    {
        $allBomItemsAreInStock = true;
        foreach ($this->inventoryItem->bom as $bomItem) {
            $rawMaterial = $bomItem->raw_material();
            if (!$rawMaterial->getstockmoves || (((float)Arr::get($rawMaterial->getstockmoves, 'qauntity')) < 1)) {
                $allBomItemsAreInStock = false;
                break;
            }
        }

        return $allBomItemsAreInStock;
    }
}
