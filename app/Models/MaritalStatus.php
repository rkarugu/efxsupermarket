<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaritalStatus extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
