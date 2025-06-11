<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class WaCategoryItemPrice extends Model
{

	public static function getitemcatprice($itemid,$catid){
		$data = self::where('item_id',$itemid)->where('category_id',$catid)->first();
		if($data){
			return $data->price;
		}else{
			return "";
		}
	}

}
