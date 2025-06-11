<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class InspectionsForms extends Model
{
    
   
    protected $table = "inspection_forms";
    public function getRelatedItems() {
        return $this->hasMany('App\Model\InspectionFormItem', 'inspection_form_id');
    }
    

    
    

     
}


