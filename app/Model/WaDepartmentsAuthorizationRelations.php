<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaDepartmentsAuthorizationRelations extends Model
{
    
       protected $fillable = array('user_id', 'wa_department_id');

     /*public function getAccountSection() {
        return $this->belongsTo('App\Model\WaAccountSection', 'wa_account_section_id');
    }

     public function getParentAccountGroup() {
        return $this->belongsTo('App\Model\WaAccountGroup', 'parent_id');
    }*/

    

    

     
}


