<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class DispatchLoadedProducts extends Model
{
	
	protected $table='dispatch_loaded_products';
	
	public function getSalesMan(){
		return $this->belongsTo('App\Model\User','salesman_id');
	}

	public function getShift(){
		return $this->belongsTo('App\Model\WaShift','shift_id');
	}

	public function getStoreLocation(){
		return $this->belongsTo('App\Model\WaLocationAndStore','store_location_id');
	}

	public function getInventoryItem(){
		return $this->belongsTo('App\Model\WaInventoryItem','inventory_item_id');
	}
	
}
