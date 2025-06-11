<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class NWaInventoryLocationTransfer extends Model
{


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
        return $this->hasMany('App\Model\NWaInventoryLocationTransferItem', 'wa_inventory_location_transfer_id');
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
}
