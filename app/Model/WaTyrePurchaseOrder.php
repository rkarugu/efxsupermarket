<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaTyrePurchaseOrder extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'purchase_no',
            'onUpdate'=>true
        ]];
    }
    

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaTyrePurchaseOrderItem', 'wa_tyre_purchase_order_id');
    }
     public function getRelatedInventoryItem() {
         return $this->belongsTo('App\Model\WaTyrePurchaseOrderItem', 'wa_tyre_purchase_order_id');
    }

     public function getBranch() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function getSupplierUomDetail() {
        return $this->belongsTo('App\Model\SupplierUom', 'supplier_uom_id');
    }

     public function getDepartment() {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }
    public function project() {
        return $this->belongsTo(Projects::class, 'project_id');
    }
      public function getStoreLocation() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

     public function getSupplier() {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }

    

     public function getrelatedEmployee() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }


      public function getRelatedAuthorizationPermissions() {
         return $this->hasMany('App\Model\WaTyrePurchaseOrderPermission', 'wa_tyre_purchase_order_id')->orderBy('approve_level','asc');
    }



     public function getRelatedGrn() {
         return $this->hasMany('App\Model\WaGrn', 'wa_tyre_purchase_order_id')->where('return_status','Not Returned');
    }
    public function getRelatedGrnAll() {
            return $this->hasMany('App\Model\WaGrn', 'wa_tyre_purchase_order_id');
    }
    public function getRelatedGrnReturned() {
        return $this->hasMany('App\Model\WaGrn', 'wa_tyre_purchase_order_id')->where('return_status','Returned');
    }
     public function getRelatedGlTran() {
         return $this->hasMany('App\Model\WaGlTran', 'wa_tyre_purchase_order_id');
    }

    public function getRelatedStockMoves() {
         return $this->hasMany('App\Model\WaStockMove', 'wa_tyre_purchase_order_id');
    }
    
     public function getSuppTran() {
        return $this->hasOne('App\Model\WaSuppTran', 'wa_tyre_purchase_order_id');
    }

    

     
}


