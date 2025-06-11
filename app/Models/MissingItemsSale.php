<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaRouteCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\SalesmanShift;

class MissingItemsSale extends BaseModel
{
    use HasFactory;
    protected $table = 'missing_items_sales';

    public function getRelatedItem(){
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
    public function getRelatedShift(){
        return $this->belongsTo(SalesmanShift::class,'shift_id');
    }
    public function getRelatedSalesman(){
        return $this->belongsTo(User::class,'salesman_id');
    }
    public function getRelatedRouteCustomer(){
        return $this->belongsTo(WaRouteCustomer::class,'wa_route_customer_id');
    }
}
