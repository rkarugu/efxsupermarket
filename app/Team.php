<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function members(): HasMany
    {
    return $this->hasMany(TeamMember::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(TeamRoute::class);
    }
}
