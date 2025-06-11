<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentType extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'required_during_onboarding' => 'boolean',
        'system_reserved' => 'boolean',
    ];

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
