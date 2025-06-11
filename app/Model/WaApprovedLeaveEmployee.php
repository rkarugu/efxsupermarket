<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaApprovedLeaveEmployee extends Model{
    protected $table = '';
    public $timestamps = false;

  public function EmpData(){
	return $this->hasOne(Employee::class, 'id', 'emp_id');
   }

   public function AppUserData(){
	return $this->hasOne(User::class, 'id', 'approved_by');
   }

   public function LeaveData(){
	return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
   }

    public function DCUserData(){
	return $this->hasOne(User::class, 'id', 'reject_by');
   }

}
