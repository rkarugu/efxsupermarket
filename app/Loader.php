<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\Restaurant;

class Loader extends Model
{
    use HasFactory;
    public function branches()
    {
        return $this->belongsToMany(Restaurant::class, 'user_branches', 'user_id', 'restaurant_id');
    }
}
