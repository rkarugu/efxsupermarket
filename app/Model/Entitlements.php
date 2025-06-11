<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Entitlements extends Model{
    protected $table = 'wa_entitlements';
    public $timestamps = false;



  public function EmpDataGet(){
	return $this->hasOne(Employee::class, 'id', 'employee_id');
   }

   public function LeaveDataGet(){
	return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
   }


   
}


