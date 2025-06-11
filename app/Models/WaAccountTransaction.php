<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\WaChartsOfAccount;
use App\Model\WaPosCashSales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaAccountTransaction extends Model
{
    use HasFactory;

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }

    public function posSale()
    {
        return $this->belongsTo(WaPosCashSales::class,'wa_pos_cash_sale_id');
    }

    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'account_id');
    }
}
