<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaInventoryItemSupplierDataApprovals extends Model
{
    protected $guarded = [];
    protected $table = "wa_inventory_item_supplier_data_approvals";

    public function initiator()
    {
        return $this->belongsTo(User::class,'initiated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function item_data()
    {
        return $this->belongsTo(WaInventoryItemSupplierData::class,'wa_supplier_data_id');
    }
}
