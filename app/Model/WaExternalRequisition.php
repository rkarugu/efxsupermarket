<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaExternalRequisition extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'purchase_no',
            'onUpdate'=>true
        ]];
    }
     protected $guarded = [];

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaExternalRequisitionItem', 'wa_external_requisition_id');
    }

    public function externalRequisitionItems() {
        return $this->hasMany(WaExternalRequisitionItem::class, 'wa_external_requisition_id');
    }

     public function getBranch() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function branch() {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function projects() {
        return $this->belongsTo(Projects::class, 'project_id');
    }
     public function getDepartment() {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }

    public function department() {
        return $this->belongsTo(WaDepartment::class, 'wa_department_id');
    }

     public function getrelatedEmployee() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }


      public function getRelatedAuthorizationPermissions() {
         return $this->hasMany('App\Model\WaExternalReqPermission', 'wa_external_requisition_id')->orderBy('approve_level','asc');
    }
    
    public function supplier() {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }
    public function unit_of_measure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measures_id');
    }

    public function bin() {
        return $this->belongsTo(WaUnitOfMeasure::class, 'wa_unit_of_measures_id');
    }

    public function store_location() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_store_location_id');
    }

    public function priority_level() {
        return $this->belongsTo('App\Model\WaPriorityLevel', 'wa_priority_level_id');
    }
    

     
}


