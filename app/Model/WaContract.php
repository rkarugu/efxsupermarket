<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\EmploymentType;



class WaContract extends Model{
    protected $table = 'wa_contract';
    public $timestamps = false;

     public function getEmploymentType(){
    	return $this->hasOne(EmploymentType::class, 'id', 'emp_type');
    }
}
