<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;
class WaStoreCReceiveItems extends Model
{
   protected $table = 'wa_store_c_receives_items';     

   public function item()
   {
      return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
   }

   public function location()
   {
      return $this->belongsTo(WaLocationAndStore::class,'store_location_id');
   }
}


