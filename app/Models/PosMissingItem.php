<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosMissingItem extends Model
{
    use HasFactory;
    protected $table = 'pos_missing_items';

    public function getRelatedUser()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
    public function getRelatedInventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
