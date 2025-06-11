<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(TicketAssignee::class,'ticket_id');
    }

    public function current_assignee(): HasOne
    {
        return $this->hasOne(TicketAssignee::class,'ticket_id')->latest();
    }

    public function status(): HasMany
    {
        return $this->hasMany(TicketStatus::class, 'ticket_id');
    }

    public function current_status()
    {
        return $this->hasOne(TicketStatus::class, 'ticket_id')->latest();
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class,'ticket_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }
}
