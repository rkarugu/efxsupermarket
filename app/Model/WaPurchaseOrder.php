<?php

namespace App\Model;

use App\Models\AdvancePayment;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaPurchaseOrder extends Model
{
    use Sluggable;

    protected $guarded = [];

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'purchase_no',
            'onUpdate' => true
        ]];
    }

    public function getRelatedItem()
    {
        return $this->hasMany('App\Model\WaPurchaseOrderItem', 'wa_purchase_order_id');
    }

    public function reception()
    {
        return $this->hasOne('App\Model\WaReceivePurchaseOrder', 'wa_purchase_order_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(WaPurchaseOrderItem::class, 'wa_purchase_order_id');
    }

    public function getRelatedItem_with_grn()
    {
        return $this->hasMany('App\Model\WaPurchaseOrderItem', 'wa_purchase_order_id')->join('wa_grns', function ($e) {
            $e->on('wa_grns.wa_purchase_order_item_id', '=', 'wa_purchase_order_items.id');
        })->select(
            [
                'wa_purchase_order_items.*',
                'wa_grns.qty_received'
            ]
        );
    }
    public function getRelatedInventoryItem()
    {
        return $this->belongsTo('App\Model\WaPurchaseOrderItem', 'wa_purchase_order_id');
    }

    public function getBranch()
    {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function getSupplierUomDetail()
    {
        return $this->belongsTo('App\Model\SupplierUom', 'supplier_uom_id');
    }

    public function getDepartment()
    {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }

    public function department()
    {
        return $this->belongsTo(WaDepartment::class, 'wa_department_id');
    }

    public function getStoreLocation()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

    public function storeLocation()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

    public function uom()
    {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measures_id');
    }

    public function getSupplier()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }



    public function getrelatedEmployee()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\User', 'employee_id');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle', 'vehicle_id');
    }


    public function getRelatedAuthorizationPermissions()
    {
        return $this->hasMany('App\Model\WaPurchaseOrderPermission', 'wa_purchase_order_id')->orderBy('approve_level', 'asc');
    }

    public function grn()
    {
        return $this->hasOne('App\Model\WaGrn', 'wa_purchase_order_id');
    }

    public function grns()
    {
        return $this->hasMany('App\Model\WaGrn', 'wa_purchase_order_id');
    }

    public function getRelatedGrn()
    {
        return $this->hasOne('App\Model\WaGrn', 'wa_purchase_order_id');
    }

    public function getRelatedGlTran()
    {
        return $this->hasMany('App\Model\WaGlTran', 'wa_purchase_order_id');
    }

    public function getRelatedStockMoves()
    {
        return $this->hasMany('App\Model\WaStockMove', 'wa_purchase_order_id');
    }

    public function getSuppTran()
    {
        return $this->hasOne('App\Model\WaSuppTran', 'wa_purchase_order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Model\WaSuppTran', 'wa_purchase_order_id', 'id');
    }

    public function advance()
    {
        return $this->hasOne(AdvancePayment::class,  'wa_purchase_order_id');
    }

    public function children()
    {
        return $this->hasMany(WaPurchaseOrder::class,  'mother_lpo');
    }

    public function mother()
    {
        return $this->belongsTo(WaPurchaseOrder::class,  'mother_lpo');
    }
}
