<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaReceivePurchaseOrderItem extends Model
{
    protected $table = "wa_receive_purchase_order_items";
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(WaPurchaseOrderItem::class,'wa_purchase_order_item_id');
    }
}
