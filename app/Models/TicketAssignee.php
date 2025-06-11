<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssignee extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class,'assignee_id');
    }
    
}
