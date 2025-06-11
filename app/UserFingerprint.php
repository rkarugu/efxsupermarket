<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFingerprint extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function fingerNumber(): Attribute
    {
        return Attribute::make(get: fn($value) => (int)$value);
    }
}
