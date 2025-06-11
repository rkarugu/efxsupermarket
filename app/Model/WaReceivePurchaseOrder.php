<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaReceivePurchaseOrder extends Model
{
    protected $table = "wa_receive_purchase_orders";
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(WaPurchaseOrder::class,'wa_purchase_order_id');
    }
    public function child_items()
    {
        return $this->hasMany(WaReceivePurchaseOrderItem::class,'wa_receive_purchase_order_id');
    }
    public function uom(){
        return $this->belongsTo(WaUnitOfMeasure::class,'wa_unit_of_measures_id');
    }
    public function getStoreLocation(){
        return $this->belongsTo(WaLocationAndStore::class,'wa_location_and_store_id');
    }
    public function initiator()
    {
        return $this->belongsTo(User::class,'initiated_by');
    }
    public function return_initiator()
    {
        return $this->belongsTo(User::class,'returned_by');
    }
    public function confirmer()
    {
        return $this->belongsTo(User::class,'confirmed_by');
    }
    public function processor()
    {
        return $this->belongsTo(User::class,'processed_by');
    }
}
