<?php

namespace App\Models;

use App\Models\Scopes\CustomOrderScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Relationship extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'system_reserved' => 'boolean'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CustomOrderScope);
    }

    public function employeeEmergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    public function employeeBeneficiaries()
    {
        return $this->hasMany(EmployeeBeneficiary::class);
    }
}
