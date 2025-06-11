<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Absent extends Model{
    protected $table = 'wa_absent';
    public $timestamps = false;

  public function LoanTypeData(){
	return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
   }
}
