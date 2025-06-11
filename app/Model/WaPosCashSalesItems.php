<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPosCashSalesItems extends Model
{
    protected $table = 'wa_pos_cash_sales_items';
    protected $guarded = [];

    protected $casts = [
        'return_date'=> 'datetime'
    ];


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
        return $this->belongsTo(WaPosCashSales::Class,'wa_pos_cash_sales_id');
    }
    public function dispatch_details()
    {
        return $this->hasMany(WaPosCashSalesDispatch::Class,'pos_sales_item_id');
    }
    public function tax_manager()
    {
        return $this->belongsTo(TaxManager::Class,'tax_manager_id');
    }
    public function dispatch()
    {
        return $this->hasOne(WaPosCashSalesDispatch::class,'pos_sales_item_id');
    }

    public function returnItem()
    {
        return $this->hasOne(WaPosCashSalesItemReturns::class,'wa_pos_cash_sales_item_id');
    }
    public function returnItems()
    {
        return $this->hasMany(WaPosCashSalesItemReturns::class,'wa_pos_cash_sales_item_id');
    }
}