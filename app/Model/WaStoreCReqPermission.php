<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStoreCReqPermission extends Model
{
    protected $table = "wa_store_c_req_permissions";
    
   
	public function getInternalPurchase() {
        return $this->belongsTo('App\Model\WaStoreCRequisition', 'wa_store_c_requisitions_id');
    }

    public function getInternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    

    

     
}


