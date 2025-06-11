<?php

namespace App\Model;
use App\Model\WaInternalRequisitionItem;
use Illuminate\Database\Eloquent\Model;

class SaleOrderReturns extends Model
{
    //

    protected $table = 'sale_order_returns';

    public function wa_internal_requisition_item(){
        return $this->belongsTo(WaInternalRequisitionItem::class, 'wa_internal_requisition_item_id');
    }


    public function reason(){
        return $this->belongsTo(ItemReturnReason::class, 'wa_internal_requisition_item_id');
    }
}
