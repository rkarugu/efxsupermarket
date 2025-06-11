<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaSalesOrderItems extends Model
{
    protected $table = 'wa_sales_orders_items';
    protected $guarded = [];
    public function item()
    {
        return $this->belongsTo(WaInventoryItem::class,'stock_code_id');
    }
}