<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductionProcess extends Model
{
    protected $guarded = [];

    /**
     * Returns only active records
     *
     * @param Builder $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', '=', 'active');
    }
}
