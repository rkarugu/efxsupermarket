<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReverseSplit extends Model
{
    use HasFactory;
    protected $table = 'reverse_splits';

    public function getMotherItem(){
        return $this->belongsTo(WaInventoryItem::class, 'mother_item_id');
    }
    public function getChildItem(){
        return $this->belongsTo(WaInventoryItem::class, 'child_item_id');
    }
    public function getMotherBin(){
        return $this->belongsTo(WaUnitOfMeasure::class,'mother_item_bin');
    }
    public function getChildBin(){
        return $this->belongsTo(WaUnitOfMeasure::class,'child_item_bin');
    }
}
