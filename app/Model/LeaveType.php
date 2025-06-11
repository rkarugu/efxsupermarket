<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model{
    protected $table = 'leave_type';
    public $timestamps = false;

 //    public function LoanEntriesData(){
	// return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
 //   }
}


