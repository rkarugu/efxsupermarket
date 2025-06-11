<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaInventoryItemApprovalStatus extends BaseModel
{
    use HasFactory;

    protected $fillable = ['wa_inventory_items_id','approval_by','status','changes','user_id','approval_date','new_data'];
    
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_items_id');
    }

    public function approvalBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_by');
    }
}
