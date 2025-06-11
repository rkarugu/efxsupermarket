<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;
class WaSupremeStoreReceive extends Model
{
   public $table = 'wa_supreme_store_receives';     

   public function items()
   {
      return $this->hasMany(WaSupremeStoreReceiveItems::class,'wa_supreme_store_receive_id');
   }
   public function user()
   {
      return $this->belongsTo(User::class,'user_id');
   }
}


