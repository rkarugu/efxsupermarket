<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesmanReportingReason extends Model
{
    use HasFactory;
    public $guarded=[];
    public function  salesReportingReasons(){
        return $this->hasMany(SalesmanReportingReasonOption::class, 'reporting_reason_id');
    }
}
