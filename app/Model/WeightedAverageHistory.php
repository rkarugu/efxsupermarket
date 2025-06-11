<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WeightedAverageHistory extends Model{
    


    protected $table = "weighted_average_history";
    
    public function getPurchaseOrder() {
        return $this->belongsTo('App\Model\WaPurchaseOrder', 'purchase_order_id');
    }

    public function getPurchaseOrderItem() {
        return $this->belongsTo('App\Model\WaPurchaseOrderItem', 'purchase_order_item_id');
    }

    
}


