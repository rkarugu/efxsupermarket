<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspendedTransaction extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'suspended_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'resolved_by');
    }

    public function customerDetail(): BelongsTo
    {
        return $this->belongsTo(WaCustomer::class, 'wa_customer_id');
    }

    public function editedCustomerDetail(): BelongsTo
    {
        return $this->belongsTo(WaCustomer::class, 'edited_wa_customer_id');
    }
}
