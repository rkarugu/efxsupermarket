<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaUserSupplier extends Model{
    


    protected $table = "wa_user_suppliers";
    
    public function user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function supplier() {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }

    
}


