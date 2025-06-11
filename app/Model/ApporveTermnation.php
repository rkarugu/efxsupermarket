<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ApporveTermnation extends Model{
    protected $table = 'wa_approve_termination';
    public $timestamps = false;

      public function DataGet3(){
    	return $this->hasOne(TerminationTypes::class, 'id', 'type_of_termination');
    }
}



