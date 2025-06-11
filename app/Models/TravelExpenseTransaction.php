<?php

namespace App\Models;

use App\Model\User;
use App\Model\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelExpenseTransaction extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
}
