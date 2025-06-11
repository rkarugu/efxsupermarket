<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaTyrePurchaseOrderPermission extends Model{
    
    
    


	public function getTyrePurchaseOrder() {
        return $this->belongsTo('App\Model\WaTyrePurchaseOrder', 'wa_tyre_purchase_order_id');
    }

    public function getExternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    

    

     
}


