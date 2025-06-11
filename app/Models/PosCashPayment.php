<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosCashPayment extends Model
{
    use HasFactory;
    public function initiator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
    public function recipient() : BelongsTo
    {
        return $this->belongsTo(User::class, 'payee');
    }
    public function approvedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function branch() : BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'branch_id');
    }
}
