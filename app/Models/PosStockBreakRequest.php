<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosStockBreakRequest extends Model
{
    use HasFactory;
    protected $table =  'pos_stock_break_requests';

    public  function getChild()
    {
        return $this->belongsTo(WaInventoryItem::class, 'item_id');
    }

    public function getChildBinDetail()
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'child_item_bin');
    }
    public function getMother()
    {
        return $this->belongsTo(WaInventoryItem::class,'mother_item_id');
    }
    public function getMotherBinDetail()
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'mother_item_bin');
    }

    public function getTransactingStore()
    {
        return $this->belongsTo(WaLocationAndStore::class,'wa_location_and_store_id');

    }
    public function getInitiatingUser()
    {
        return $this->belongsTo(User::class,'requested_by');
    }

}
