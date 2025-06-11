<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class LeaveReversal extends Model{
    protected $table = 'wa_leave_reversal';
    public $timestamps = false;

     public function ReCallsLeave(){
	   return $this->hasOne(User::class, 'id', 'reversal_by');
   }  

   public function AssignLeave(){
	   return $this->hasOne(AssignLeave::class, 'id', 'assign_leave_id');
   }

   public function EmpData(){
    return $this->belongsTo(Employee::class, 'emp_id', 'id');
   }

   public function LeaveDataGet2(){
	return $this->hasOne(LeaveType::class, 'id', 'leave_id');
   } 
}




