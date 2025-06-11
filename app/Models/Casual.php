<?php

namespace App\Models;

use App\Model\Restaurant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Casual extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'full_name'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "$this->first_name $this->middle_name $this->last_name"
        );
    }

    public function active(): Attribute
    {
        return Attribute::make(
            set: fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }
}
