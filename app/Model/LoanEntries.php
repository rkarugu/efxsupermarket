<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class LoanEntries extends Model{
    protected $table = 'wa_loan_entries';
    public $timestamps = false;

    public function LoanEntriesData(){
	return $this->hasOne(LoanType::class, 'id', 'loan_type_id');
   }
}

