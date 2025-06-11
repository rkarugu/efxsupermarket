<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OffsiteShiftRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getShift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class, 'shift_id', 'id');
    }
}
