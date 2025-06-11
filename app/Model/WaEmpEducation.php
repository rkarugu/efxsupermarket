<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaEmpEducation extends Model{
    protected $table = 'wa_emp_education';
    public $timestamps = false;


    public function JobGradeID(){
    	return $this->hasOne(JobGrade::class, 'id', 'job_grade_id');
    }
    
    public function JobEductionData(){
    	return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }
}


