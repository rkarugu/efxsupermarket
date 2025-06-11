<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SeparationTermnation extends Model{
    protected $table = 'wa_separation_termnation';
    public $timestamps = false;


    public function DataGet(){
    	return $this->hasOne(TerminationTypes::class, 'id', 'type_of_termination');
    }
}

