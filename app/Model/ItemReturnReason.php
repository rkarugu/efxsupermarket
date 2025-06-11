<?php


namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemReturnReason extends Model
{
    //

    protected $table = 'item_return_reasons';

    public function orderReturns():HasMany{
        return $this->hasMany(SaleOrderReturns::class);
    }
}
