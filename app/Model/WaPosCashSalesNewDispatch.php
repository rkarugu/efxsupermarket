<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPosCashSalesNewDispatch extends Model
{
    protected $table = 'wa_pos_cash_sales_new_dispatch';
    protected $guarded = [];

    public function dispatch_user()
    {
        return $this->belongsTo(User::class,'dispatched_by');
    }
}