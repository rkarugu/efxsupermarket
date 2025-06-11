<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeviceSimCard extends BaseModel
{
    use HasFactory;

    protected $guarded=[];

    public function device(): HasOne
    {
        return $this->hasOne(Device::class,'simcard_id');
    }
}
