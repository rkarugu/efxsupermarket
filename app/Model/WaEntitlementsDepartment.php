<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaEntitlementsDepartment extends Model{
    protected $table = 'wa_entitlements_department';
    public $timestamps = false;

    public function EmpDataGet2(){
	   return $this->hasOne(Employee::class, 'id', 'emp_id');
   }

    public function LeaveDataGet2(){
	return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
   }
}


