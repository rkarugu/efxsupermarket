<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaLpoPortalReqApprovalItem extends Model{
    protected $table = 'wa_lpo_portal_req_approval_items';
    public $timestamps = false;

    public function inventory_item(){
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id','id');
    }

    public function OrderItem(){
        return $this->belongsTo(WaPurchaseOrderItem::class,'order_item_id');
    }
}
