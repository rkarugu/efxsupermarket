<?php

namespace App\Model;
use App\Model\PayrollWaPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Employee extends Model{
    protected $table = 'wa_employee';
    public $timestamps = false;

   protected $guarded = [];

  protected $appends = [
    'name'
  ];

 public function DepData(){
	   return $this->hasOne(WaDepartment::class, 'id', 'department_id');
   }

  public function BankData(){
	   return $this->hasOne(Bank::class, 'id', 'bank_id');
   }

  public function branch()
  {
    return $this->belongsTo(Restaurant::class);
  }
   
  public function JobTitle()
  {
    return $this->hasOne(JobTitle::class, 'id', 'job_title');
  }

  public function employmentType()
  {
    return $this->belongsTo(EmploymentType::class);
  }

   public function JobData(){
	   return $this->hasOne(JobGroup::class, 'id', 'job_group_id');
   }

   public function PayrollWaPaymentData(){
    return $this->belongsTo('App\Model\PayrollWaPayment',  'id', 'emp_id');
   }

   public function PayrollAllowancesData(){
    return $this->belongsTo('App\Model\PayrollAllowances',  'id', 'emp_id');
   }

   public function PayrollCommissionData(){
    return $this->belongsTo('App\Model\PayrollCommission',  'id', 'emp_id');
   }

   public function PayrollCustomParametersData(){
    return $this->belongsTo('App\Model\PayrollCustomParameters',  'id', 'emp_id');
   }

  public function gender()
  {
    return $this->belongsTo(Gender::class);
  }

  public function department()
  {
    return $this->belongsTo(WaDepartment::class);
  }
   
  public function name(): Attribute
  {
    return Attribute::make(
      get: fn () => "$this->first_name $this->middle_name $this->last_name"
    );
  }
}


