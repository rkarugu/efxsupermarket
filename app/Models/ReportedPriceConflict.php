<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedPriceConflict extends Model
{
    use HasFactory;
    protected $table = 'reported_price_conflicts';

    public  function getRelatedItem(){
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
    public function getRelateduser(){
        return $this->belongsTo(User::class, 'reported_by');
    }
}
