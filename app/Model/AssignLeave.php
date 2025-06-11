<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class AssignLeave extends Model{
    protected $table = 'wa_assign_leave';
    public $timestamps = false;

    public function EmpDataGet2(){
	   return $this->hasOne(Employee::class, 'id', 'emp_id');
   }

   public function EmpData(){
	   return $this->hasOne(Employee::class, 'id', 'emp_id');
   }

    public function LeaveDataGet2(){
	return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
   } 

   public function UData(){
	return $this->hasOne(User::class, 'id', 'approved_by');
   }

   public function UMangerData(){
	return $this->hasOne(User::class, 'id', 'manage_approve_id');
   }

   public function RejectData(){
	return $this->hasOne(User::class, 'id', 'reject_id');
   }
}


