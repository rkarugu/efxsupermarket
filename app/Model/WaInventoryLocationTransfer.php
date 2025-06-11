<?php

namespace App\Model;

use App\SalesmanShift;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaInventoryLocationTransfer extends Model
{
    protected $guarded = [];

    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'transfer_no',
            'onUpdate' => true
        ]];
    }

    public function getrelatedEmployee()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function getRelatedItem()
    {
        return $this->hasMany('App\Model\WaInventoryLocationTransferItem', 'wa_inventory_location_transfer_id');
    }

    public function getRelatedItem_ForReturn()
    {
        return $this->hasMany('App\Model\WaInventoryLocationTransferItem', 'wa_inventory_location_transfer_id');
    }

    public function getBranch()
    {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function getDepartment()
    {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }

    public function fromStoreDetail()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'from_store_location_id');
    }

    public function toStoreDetail()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'to_store_location_id');
    }


    public function get_customer()
    {
        return $this->belongsTo('App\Model\WaCustomer', 'customer_id');
    }


    public function get_requisition()
    {
        return $this->belongsTo('App\Model\WaInternalRequisition', 'transfer_no', 'requisition_no');
    }

    public function getDiscount()
    {
        return $this->getRelatedItem()->sum('discount_amount');
    }

    public function getTotalWithDiscount()
    {
        return $this->getRelatedItem()->sum('total_cost_with_vat');
    }


    public function returns(): HasMany
    {
        return  $this->hasMany(WaInventoryLocationItemReturn::class,'wa_inventory_location_transfer_id');
    }


    public function getTotalReturns()
    {
       $accepted_returns =  $this->returns->where('return_status', true);
       return $accepted_returns;
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class,'shift_id');
    }
}


