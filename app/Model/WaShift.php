<?php

namespace App\Model;

use App\SalesmanShift;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Input;

class WaShift extends Model
{
	public function getSalesManDetail(){
		return $this->belongsTo('App\Model\User','salesman_id');
	}
	public function getDeliveryNoteDetail(){
		return $this->belongsTo('App\Model\WaInventoryLocationTransfer','delivery_note');
	}
	public function order():HasMany{
		return $this->hasMany(WaInternalRequisition::class);
	}

    public function salesmanShift(): HasOne
    {
        return $this->hasOne(SalesmanShift::class, 'shift_id', 'id');
    }
}
