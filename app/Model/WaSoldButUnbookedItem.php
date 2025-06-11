<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaSoldButUnbookedItem extends Model {
    
    public function getAssociatedOrder() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }
    public function getAssociatedOrderedItem() {
        return $this->belongsTo('App\Model\OrderedItem', 'ordered_item_id');
    }
    
    public function getAssociatedInventoryItem() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }
    
}
