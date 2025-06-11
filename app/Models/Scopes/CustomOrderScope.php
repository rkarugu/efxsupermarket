<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomOrderScope implements Scope
{
    public function __construct(
        protected string $column = 'name',
        protected string $value = 'Other',
    ){
        // 
    }
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderByRaw("
            CASE
                WHEN {$this->column} = '{$this->value}' THEN 1
                WHEN {$this->column} LIKE '{$this->value}%' THEN 2
                ELSE 0
            END,
            {$this->column}
        ");
    }
}
