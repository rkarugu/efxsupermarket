<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class InspectionFormItem extends Model{
    
   
	protected $table = 'inspection_forms_items';


	public function item_type() {
        return $this->belongsTo('App\Model\InspectionItemTypes', 'inspection_from_type_id');
    }
}


