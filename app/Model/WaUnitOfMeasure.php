<?php
namespace App\Model;

use App\Models\UpdateBinInventoryUtilityLog;
use App\Models\UpdateItemBin;
use App\Models\WaLocationStoreUom;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaUnitOfMeasure extends Model
{
    
    use Sluggable;

    protected $guarded = [];
    
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'title',
            'onUpdate'=>true
        ]];
    }

    public function getinventoryitems()
    {
        return $this->hasMany('App\Model\WaInventoryItem', 'wa_unit_of_measure_id');
    }

    public function get_uom_linked()
    {
        return $this->hasMany('App\Model\WaInventoryLocationUom', 'uom_id');
    }
    public function get_uom_location()
    {
        return $this->belongsTo(WaLocationStoreUom::class, 'uom_id');
    }

    public function location()
    {
        return $this->hasOne(WaLocationStoreUom::class, 'uom_id')->latest();
    }

    // Utility relationships start

    public function inventorybins(): HasMany
    {
        return $this->hasMany(UpdateBinInventoryUtilityLog::class, 'id', 'wa_unit_of_measure_id');
    }

    // Utility relationships end

    public function isDisplay()
    {
        return $this->is_display ? true : false;
    }

}


