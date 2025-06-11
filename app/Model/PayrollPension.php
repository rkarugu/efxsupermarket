<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollPension extends Model{
    protected $table = 'wa_payroll_pension';
    public $timestamps = false;

    public function PayrollPensionData(){
	return $this->hasOne(Pension::class, 'id', 'pension_id');
   }
}

