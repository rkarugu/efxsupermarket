<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    
   
     
    public function user(){
        return $this->belongsTo('App\Model\User','id');
    }
    public function order(){
        return $this->belongsTo('App\Model\Order','order_id','id')->select('id','order_final_price');
    }
}


