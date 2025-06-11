<?php

namespace App\Models;

use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBillItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'account_id');
    }
}
