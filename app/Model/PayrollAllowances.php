<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollAllowances extends Model{
    protected $table = 'wa_payroll_allowances';
    public $timestamps = false;

  public function AllowanceData(){
	return $this->hasOne(Allowance::class, 'id', 'allowance_id');
   }
}
