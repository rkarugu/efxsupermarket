<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPosCashSalesDispatch extends Model
{
    protected $table = 'wa_pos_cash_sales_dispatch';
    protected $guarded = [];

    public function dispatch_user()
    {
        return $this->belongsTo(User::class,'dispatched_by');
    }

    public function cashSaleItem()
    {
        return $this->belongsTo(WaPosCashSalesItems::class,'pos_sales_item_id');
    }

    public function bin()
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'wa_unit_of_measure_id');
    }

}