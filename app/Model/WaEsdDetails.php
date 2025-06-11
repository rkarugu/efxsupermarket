<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Model\WaInternalRequisition;


class WaEsdDetails extends Model
{

    
    /*public function getInvoice()
    {
        return $this->belongsTo('App\Model\WaInventoryItem', 'invoice_number','invoice_number');
    }*/
    
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(WaInternalRequisition::class, 'invoice_number', 'requisition_no');
    }
    public function inventoryLocationTransfer(): BelongsTo
    {
        return $this->belongsTo(WaInventoryLocationTransfer::class, 'invoice_number','transfer_no');
    }


    
}
