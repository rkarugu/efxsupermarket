<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiefCashierDeclaration extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function EOD()
    {
        return $this->belongsTo(WaCloseBranchEndOfDay::class,'wa_close_branch_end_of_day_id');
    }
}
