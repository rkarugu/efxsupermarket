<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deduction extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        // 'has_brackets' => 'boolean',
        // 'is_statutory' => 'boolean',
        'is_recurring' => 'boolean',
        'is_reliefable' => 'boolean',
        'system_reserved' => 'boolean',
    ];

    // public function brackets()
    // {
    //     return $this->hasMany(DeductionBracket::class, 'deduction_id')
    //         ->orderBy('from');
    // }
}
