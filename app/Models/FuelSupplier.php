<?php

namespace App\Models;

use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelSupplier extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    public function supplierDetails(){
        return $this->hasOne(WaSupplier::class,'id','wa_suppliers_id');
    }
}
