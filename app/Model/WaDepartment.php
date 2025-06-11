<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaDepartment extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'department_name',
            'onUpdate'=>true
        ]];
    }

    public function getManyRelativeAuthorizations() {
        return $this->hasMany('App\Model\WaDepartmentsAuthorizationRelations', 'wa_department_id');
    }

      public function getManyExternalRelativeAuthorizations() {
        return $this->hasMany('App\Model\WaDepartmentExternalAuthorization', 'wa_department_id');
    }

     public function getManyPurchaseRelativeAuthorizations() {
        return $this->hasMany('App\Model\WaPurchaseOrderAuthorization', 'wa_department_id');
    }
 
     public function getAssociateBranch() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    

    

     
}


