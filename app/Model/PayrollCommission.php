<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollCommission extends Model{
    protected $table = 'wa_payroll_commission';
    public $timestamps = false;

  public function CommissionType(){
	return $this->hasOne(Commission::class, 'id', 'commission_id');
   }
}
