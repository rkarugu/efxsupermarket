<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaEmpExperience extends Model{
    protected $table = 'wa_emp_experience';
    public $timestamps = false;

        public function JobTitile()
    {
        return $this->hasMany('App\Model\JobTitle','id');
    }
}

