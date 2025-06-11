<?php

namespace App\Models;

use App\Model\PackSize;
use App\Model\User;
use App\Model\WaSupplier;
use App\WaItemSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestNewSku extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function packsize(): BelongsTo
    {
        return $this->belongsTo(PackSize::class, 'pack_size_id', 'id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(WaItemSubCategory::class, 'sub_category_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id', 'id');
    }

    public function approvedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function requestnewskuimages(): HasMany
    {
        return $this->hasMany(RequestNewSkuImage::class);
    }
    
}
