<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesmanShiftReopenRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getShift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class, 'shift_id', 'id');
    }
}
