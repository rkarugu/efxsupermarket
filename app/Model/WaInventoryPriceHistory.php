<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaInventoryPriceHistory extends Model
{
    protected $table = 'wa_inventory_item_price_history';
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(\App\Model\WaInventoryItem::class, 'wa_inventory_item_id');
    }
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'initiated_by');
    }
    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }
}
