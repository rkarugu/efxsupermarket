<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPosCashSalesNewItems extends Model
{
    protected $table = 'wa_pos_cash_sales_new_items';
    protected $guarded = [];


    public function item()
    {
        return $this->belongsTo(WaInventoryItem::Class,'wa_inventory_item_id');
    }

    public function location()
    {
        return $this->belongsTo(WaLocationAndStore::Class,'store_location_id');
    }

    public function stock_moves()
    {
        return $this->HasMany(WaStockMove::Class,'wa_inventory_item_id','wa_inventory_item_id');
    }

    public function dispatch_by()
    {
        return $this->belongsTo(User::class,'dispatched_by');
    }
    public function returned_by()
    {
        return $this->belongsTo(User::class,'return_by');
    }
    public function parent()
    {
        return $this->belongsTo(WaPosCashSalesNew::Class,'wa_pos_cash_sales_id');
    }
    public function dispatch_details()
    {
        return $this->hasMany(WaPosCashSalesNewDispatch::Class,'pos_sales_item_id');
    }
    public function tax_manager()
    {
        return $this->belongsTo(TaxManager::Class,'tax_manager_id');
    }
}