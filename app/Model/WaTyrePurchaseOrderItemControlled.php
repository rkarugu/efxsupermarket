<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaTyrePurchaseOrderItemControlled extends Model
{
    protected $guarded = [];

    public function getStatusArrayAttribute(){
        return ['New','Approved'];
    }
        
}
