<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;
class WaStoreCReceive extends Model
{
   public $table = 'wa_store_c_receives';     

   public function items()
   {
      return $this->hasMany(WaStoreCReceiveItems::class,'wa_store_c_receive_id');
   }
   public function user()
   {
      return $this->belongsTo(User::class,'user_id');
   }
}


