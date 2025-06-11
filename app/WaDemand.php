<?php

namespace App;

use App\Model\User;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaDemand extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'processed' => 'boolean',
        'approved' => 'boolean',
        'merged' => 'boolean',
        'merged_from' => 'json',
    ];

    public function getDemandItem()
    {
        return $this->hasMany('App\WaDemandItem', 'wa_demand_id');
    }

    public function getSupplier()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function demandItems()
    {
        return $this->hasMany(WaDemandItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id');
    }
}
