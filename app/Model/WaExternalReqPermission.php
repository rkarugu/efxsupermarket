<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaExternalReqPermission extends Model
{
    
   
	  public function getExternalPurchase() {
        return $this->belongsTo('App\Model\WaExternalRequisition', 'wa_external_requisition_id');
    }

    public function getExternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    

    

     
}


