<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetingBrand extends Model
{
    use HasFactory;
    protected $table = 'competing_brands';
    
    public function getRelatedUser()
{
    return $this->belongsTo(User::class,'created_by');
}
public function getRelatedItems()
{
    return $this->hasMany(CompetingBrandItem::class,'competing_brand_id');
}
}
