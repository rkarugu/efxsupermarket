<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaSupplier;
use App\ReturnedGrn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaReturnDemand extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'return_type'
    ];

    protected $casts = [
        'processed' => 'boolean',
        'approve' => 'boolean'
    ];
    
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

    public function returnDemandItems()
    {
        return $this->hasMany(WaReturnDemandItem::class);
    }

    public function storeReturn()
    {
        return $this->belongsTo(WaStoreReturn::class, 'return_document_no', 'rfs_no');
    }

    public function returnedGrns()
    {
        return $this->hasMany(ReturnedGrn::class, 'return_number', 'return_document_no');
    }

    public function totalDemandCost()
    {
        return $this->returnDemandItems()->sum('demand_cost');
    }

    protected function returnType(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->return_document_no) {
                    return substr($this->return_document_no, 0, 3) == 'RTN' ? 'from grn' : 'from store';
                } else {
                    return null;
                }
            },
        );
    }
}
