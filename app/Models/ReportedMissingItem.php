<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedMissingItem extends Model
{
    use HasFactory;
    protected $table = 'reported_missing_items';

    public function getRelatedItem(){
        return $this->belongsTo(WaInventoryItem::class, 'item_id');
    }
    public function getRelatedUser()
    {
        return $this->belongsTo(User::class,'reported_by');
    }
    public function getRelatedStore()
    {
        return $this->belongsTo(WaLocationAndStore::class,'wa_location_and_store_id');
    }
}
