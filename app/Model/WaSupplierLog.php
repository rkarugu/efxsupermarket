<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class WaSupplierLog extends Model
{

    use Sluggable;
    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'supplier_code',
            'onUpdate' => true
        ]];
    }
   
    //protected $appends = ['c_amount','total_amount', 'month_1_amount', 'month_2_amount', 'month_3_amount', 'month_last_amount'];

    protected $fillable = ['supplier_code'];

    public function getPaymentTerm()
    {
        return $this->belongsTo('App\Model\WaPaymentTerm', 'wa_payment_term_id');
    }

    public function paymentTerm()
    {
        return $this->belongsTo('App\Model\WaPaymentTerm', 'wa_payment_term_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Restaurant::class, 'wa_supplier_branches', 'wa_supplier_id', 'restaurant_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'wa_user_suppliers', 'wa_supplier_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(WaInventoryItem::class, 'wa_inventory_item_suppliers', 'wa_supplier_id', 'wa_inventory_item_id');
    }


    public function getAllTrans()
    {
        return $this->hasMany('App\Model\WaSuppTran', 'supplier_no', 'supplier_code');
    }
}
