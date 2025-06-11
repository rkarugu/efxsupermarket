<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class NWaInventoryLocationTransferItem extends Model
{
    public function getInventoryItemDetail()
    {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function getTransferLocation()
    {
        return $this->belongsTo('App\Model\NWaInventoryLocationTransfer', 'wa_inventory_location_transfer_id');
    }
}
