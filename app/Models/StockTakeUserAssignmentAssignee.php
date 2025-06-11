<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class StockTakeUserAssignmentAssignee extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function assistant(){
        return $this->belongsTo(User::class,'user_id');
    }
    
}
