<?php

namespace App\Models;

use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestedOrder extends Model
{
    use HasFactory;

    public function getSupplier(){
        return $this->belongsTo(WaSupplier::class,'wa_supplier_id');
    }

    public function items(){
        return $this->hasMany(SuggestedOrderItem::class, 'suggested_order_id');
    }
}
