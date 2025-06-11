<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaEmpNext extends Model{
    protected $table = 'wa_emp_next_kin';
    public $timestamps = false;

     public function getNextKin(){
    	return $this->hasOne(JobTitle::class, 'id', 'job_title_id');
    }
}

