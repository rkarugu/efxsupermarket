<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStoreCRequisition extends Model
{
    protected $table = "wa_store_c_requisitions";
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'requisition_no',
            'onUpdate'=>true
        ]];
    }

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaStoreCRequisitionItem', 'wa_store_c_requisitions_id');
    }

    public function getBranch() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

     public function getDepartment() {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }

     public function getrelatedEmployee() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }


      public function getRelatedAuthorizationPermissions() {
         return $this->hasMany('App\Model\WaStoreCReqPermission', 'wa_store_c_requisition_id')->orderBy('approve_level','asc');
    }
    
    public function getRelatedFromLocationAndStore() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
    
    public function getRelatedToLocationAndStore() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'to_store_id');
    }
    

    

     
}


