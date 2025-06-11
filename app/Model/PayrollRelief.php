<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollRelief extends Model{
    protected $table = 'wa_payroll_relief';
    public $timestamps = false;

  public function ReliefData(){
	return $this->hasOne(Relief::class, 'id', 'relief_id');
   }
}
