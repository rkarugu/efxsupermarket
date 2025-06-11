<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class OvertimeHours extends Model{
    protected $table = 'wa_overtime_hours';
    public $timestamps = false;

  public function LoanTypeData(){
	return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
   }
}
