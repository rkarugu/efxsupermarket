<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaSalaryReview extends Model{
    protected $table = 'wa_salary_review';
    public $timestamps = false;

  public function LoanTypeData(){
	return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
   }
}
