<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestNewSkuImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function requestnewsku(): BelongsTo
    {
        return $this->belongsTo(RequestNewSku::class, 'request_new_sku_id', 'id');
    }
}
