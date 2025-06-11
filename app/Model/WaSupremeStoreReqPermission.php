<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaSupremeStoreReqPermission extends Model
{
    protected $table = "wa_supreme_store_req_permissions";
    
   
	public function getInternalPurchase() {
        return $this->belongsTo('App\Model\WaSupremeStoreRequisition', 'wa_supreme_store_requisitions_id');
    }

    public function getInternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    

    

     
}


