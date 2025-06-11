<?php

namespace App\Model;

use App\Models\UpdateBinInventoryUtilityLog;
use App\Models\UpdateItemPriceUtilityLog;
use App\Models\WaCloseBranchEndOfDay;
use App\ParkingListItem;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaLocationAndStore extends Model
{

    use Sluggable;

    protected $guarded = [];

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'location_name',
            'onUpdate' => true
        ]];
    }

    public function getBranchDetail()
    {
        return $this->belongsTo(Restaurant::class, 'wa_branch_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class, 'wa_branch_id');
    }

    public function user()
    {
        return $this->hasOne('App\Model\User', 'wa_location_and_store_id');
    }

    public function stock_moves()
    {
        return $this->hasMany('App\Model\WaStockMove', 'wa_location_and_store_id', 'id');
    }

    public static function getLocationList()
    {
        $location_data = WaLocationAndStore::get();
        $locations = [];
        foreach ($location_data as $key => $row) {
            $locations[$row->id] = "$row->location_name ($row->location_code)";
        }
        return $locations;
    }

    public static function getLocationListByIds($location_ids = [])
    {
        $location_data = WaLocationAndStore::whereIn('id', $location_ids)->get();
        $locations = [];
        foreach ($location_data as $key => $row) {
            $locations[$row->id] = "$row->location_name ($row->location_code)";
        }
        return $locations;
    }

    public function getRoute()
    {
        return Route::select(['route_name'])->find($this->route_id);
    }

    public function parkingListItems(): HasMany
    {
        return $this->hasMany(ParkingListItem::class, 'store_id', 'id');
    }
    public function bin_locations()
    {
        return $this->belongsToMany(WaUnitOfMeasure::class, 'wa_location_store_uom', 'location_id', 'uom_id');
    }

    public function waclosebranchendofdays(): HasMany
    {
        return $this->hasMany(WaCloseBranchEndOfDay::class);
    }

    // Utility relationships start

    public function inventorybins(): HasMany
    {
        return $this->hasMany(UpdateBinInventoryUtilityLog::class, 'id', 'wa_location_and_store_id');
    }

    public function inventoryitemprices(): HasMany
    {
        return $this->hasMany(UpdateItemPriceUtilityLog::class, 'id', 'wa_location_and_store_id');
    }

    // Utility relationships end

}


