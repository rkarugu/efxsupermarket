<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class InspectionHistoryItems extends Model
{
    
    protected $table = "inspection_history_items";
    /*public function getRelated() {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'wip_gl_code_id');
    }*/
    

    public function item_type() {
        return $this->belongsTo('App\Model\InspectionItemTypes', 'inspection_type_id');
    }

    public function form_item() {
        return $this->belongsTo('App\Model\InspectionFormItem', 'inspection_item_id');
    }

    public function form() {
        return $this->belongsTo('App\Model\InspectionsForms', 'inspection_item_id');
    }
    

     
}


