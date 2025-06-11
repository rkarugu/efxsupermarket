<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','wa_demand_id','start_time','end_time','active','deeds-demand'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
