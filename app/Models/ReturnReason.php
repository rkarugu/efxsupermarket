<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnReason extends Model
{
    use HasFactory;
    protected $table  = 'return_reasons';

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
