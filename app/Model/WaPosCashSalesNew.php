<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPosCashSalesNew extends Model
{
    protected $table = 'wa_pos_cash_sales_new';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(WaPosCashSalesNewItems::class,'wa_pos_cash_sales_id');
    }
    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}