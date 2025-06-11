<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalancesWaStockCheckFreezeItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'wa_inventory_item_id','wa_location_and_store_id'
    ];
    
    public function getAssociateItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }
    
    public function getUnitOfMeausureDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }
}
