<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;
class WaSupremeStoreReceiveItems extends Model
{
   protected $table = 'wa_supreme_store_receives_items';     

   public function item()
   {
      return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
   }

   public function location()
   {
      return $this->belongsTo(WaLocationAndStore::class,'store_location_id');
   }
}


