<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryCategory;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTakeUserAssignment extends Model
{
    use HasFactory;
    protected $table = 'stock_take_user_assignment';
    public function storeKeeper(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function assistant(){
        return $this->hasMany(StockTakeUserAssignmentAssignee::class,'stock_take_user_assignment_id');
    }
    public function uom(){
        return $this->belongsTo(WaUnitOfMeasure::class,'uom_id');
    }
    public function store_location(){
        return $this->belongsTo(WaLocationAndStore::class,'wa_location_and_store_id');
    }
    public function getAssignedCategoriesAttribute()
    {
        $categories = explode(',', $this->category_ids);
        $catNames = [];
        foreach($categories as $catId){
            $catName = WaInventoryCategory::find($catId)->category_description; 
            $catNames[] = $catName;
        }
        return implode(', ', $catNames);
    }


}
