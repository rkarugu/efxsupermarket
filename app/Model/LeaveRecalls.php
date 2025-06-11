<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class LeaveRecalls extends Model{
    protected $table = 'wa_recalls_leave';
    public $timestamps = false;

     public function ReCallsLeave(){
	   return $this->hasOne(User::class, 'id', 'recalled_by');
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




