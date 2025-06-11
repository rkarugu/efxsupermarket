<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;

class WaMergedPayments extends Model
{
    
    protected $table = "wa_merged_payments";

    protected $guarded = [];

	 public function getAccountDetail() {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'payment_account','account_code');
    }
    
	 public function getShiftDetail() {
        return $this->belongsTo('App\Model\WaShift', 'shift_id','id');
    }
    
    
	//  public function restaurant() {
    //     return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    // }
    

     
}


