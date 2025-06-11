<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class NWaInternalReqPermissionDemo extends Model
{
    
   
	  public function getInternalPurchase() {
        return $this->belongsTo('App\Model\NWaInternalRequisitionDemo', 'wa_internal_requisition_id');
    }

    public function getInternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    

    

     
}


