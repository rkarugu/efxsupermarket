<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class OrderDeliverySlots extends Model{
    protected $table = 'order_delivery_slots';
    public $timestamps = false;

    public function branch(){
        return $this->belongsTo(Restaurant::class,'branch_id');
    }
}
