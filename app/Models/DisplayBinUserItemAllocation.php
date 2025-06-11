<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class DisplayBinUserItemAllocation extends BaseModel
{
    use HasFactory;
    protected $table = 'display_bin_user_item_allocations';
    protected $fillable = ['user_id', 'wa_location_and_store_id', 'bin_id', 'wa_inventory_item_id'];

    public function getRelatedUser(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getRelatedStore(){
        return $this->belongsTo(WaLocationAndStore::class, 'wa_location_and_store_id');
    }
    public function getRelatedBin(){
        return $this->belongsTo(WaUnitOfMeasure::class, 'bin_id');
    }
    public function getRelatedItem(){
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
