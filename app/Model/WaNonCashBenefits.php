<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaNonCashBenefits extends Model{
    protected $table = 'wa_non_cash_benefits';
    public $timestamps = false;

     public function NonCashBenfitData(){
	return $this->hasOne(NonCashBenfit::class, 'id', 'non_cash_benefits_id');
   }
}


