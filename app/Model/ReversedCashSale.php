<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ReversedCashSale extends Model
{
//     public $timestamps = false;

 public static function saveCashSales($data){
	 $addActivity			 = new ReversedCashSale();
	 $addActivity->cash_sale_id	 = $data['cash_sales_id'];
	 $addActivity->cash_sale_item_id	 = $data['cash_sale_item_id'];
	 $addActivity->user_id	 = $data['user_id'];
	 $addActivity->save();
 }
    
}


