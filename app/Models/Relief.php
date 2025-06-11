<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Relief extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'system_reserved' => 'boolean'
    ];

    // public function earning()
    // {
    //     return $this->belongsTo(Earning::class);
    // }

    // public function deduction()
    // {
    //     return $this->belongsTo(Deduction::class);
    // }
}
