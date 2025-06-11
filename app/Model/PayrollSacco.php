<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollSacco extends Model{
    protected $table = 'wa_payroll_sacco';
    public $timestamps = false;

  public function SaccoData(){
	return $this->hasOne(Sacco::class, 'id', 'sacco_id');
   }
}

