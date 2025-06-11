<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Model\User;

class DeviceRepair extends BaseModel
{
    use HasFactory;

    protected $guarded=[];

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class,'device_id');
    }

    public function chargeTo(): BelongsTo
    {
        return $this->belongsTo(User::class,'charged_user');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'completed_by');
    }
}
