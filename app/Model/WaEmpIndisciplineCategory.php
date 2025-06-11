<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\IndisciplineCategory;
use App\Model\IndisciplineAction;

class WaEmpIndisciplineCategory extends Model{
    protected $table = 'wa_emp_Indiscipline_category';
    public $timestamps = false;


    public function getIndisciplineCategory(){
    	return $this->hasOne(IndisciplineCategory::class, 'id', 'indiscipline_category_id');
    }

    public function getIndisciplineAction(){
    	return $this->hasOne(IndisciplineAction::class, 'id', 'action_id');
    }
}


