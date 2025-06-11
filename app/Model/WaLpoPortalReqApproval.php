<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaLpoPortalReqApproval extends Model{
    protected $table = 'wa_lpo_portal_req_approval';

    public function purchaseOrder(){
        return $this->belongsTo(WaPurchaseOrder::class,'wa_purchase_order_id','id');
    }

    public function getRelatedItem(){
        return $this->hasMany(WaLpoPortalReqApprovalItem::class,'wa_lpo_portal_req_approval_id','id');
    }
}
