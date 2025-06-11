<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PayrollLoanType extends Model{
    protected $table = 'wa_payroll_loan_type';
    public $timestamps = false;

  public function LoanTypeData(){
	return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
   }
}
