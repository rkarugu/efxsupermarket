<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaLocationAndStore;
use App\Model\WaSupplier;
use App\Model\WaUnitOfMeasure;
use App\WaDemand;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaStoreReturn extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'approved' => 'boolean',
        'approved_date' => 'datetime',
        'rejected' => 'boolean',
        'rejected_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(WaLocationAndStore::class, 'location_id');
    }

    public function uom()
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'uom_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function storeReturnItems()
    {
        return $this->hasMany(WaStoreReturnItem::class, 'wa_store_return_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function totalCost()
    {
        return $this->storeReturnItems()->sum('total_cost');
    }

    public function demand()
    {
        return $this->hasOne(WaDemand::class, 'return_document_no', 'rfs_no');
    }
}
