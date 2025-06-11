<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends BaseModel
{
    use HasFactory;

    protected $guarded=[];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class,'device_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'issued_by');
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(User::class,'issued_to');
    }
}
