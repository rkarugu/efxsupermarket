<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Earning extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_recurring' => 'boolean',
        'is_reliefable' => 'boolean',
        'system_reserved' => 'boolean',
    ];
}
