<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponse extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class,'ticket_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(TicketAssignee::class,'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
