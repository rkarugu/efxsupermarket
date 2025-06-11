<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaAuditBill extends Model
{
     public $timestamps = false;

 public static function saveActivity($data){
	 $addActivity			 = new WaAuditBill();
	 $addActivity->bill_id	 = $data['bill_id'];
	 $addActivity->trans_type	 = $data['trans_type'];
	 $addActivity->user_id	 = $data['user_id'];
	 $addActivity->order_id	 = $data['order_id'];
	 $addActivity->table_no	 = $data['table_no'];
	 $addActivity->old_bill_id	 = $data['old_bill_id'];
	 $addActivity->old_table_no	 = $data['old_table_no'];
	 $addActivity->receipt_id	 = $data['receipt_id'];
	 $addActivity->save();
 }
    
}


