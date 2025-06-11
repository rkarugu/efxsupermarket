<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollCustomParameters extends Model{
    protected $table = 'wa_payroll_custom_parameters';
    public $timestamps = false;

  public function CustomParameterData(){
	return $this->hasOne(CustomParameter::class, 'id', 'custom_parameters_id');
   }
}
