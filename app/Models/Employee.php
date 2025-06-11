<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\WaDepartment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'inclusive_of_house_allowance' => 'boolean',
        'is_line_manager' => 'boolean',
        'is_draft' => 'boolean',
        'eligible_for_overtime' => 'boolean',
    ];

    protected $appends = [
        'full_name'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->employee_no = 'EMP' . Str::padLeft($model->id, 6, '0');
            $model->save();
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope('draft', function (Builder $builder) {
            $builder->whereNot('is_draft', true);
        });
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function salutation()
    {
        return $this->belongsTo(Salutation::class);
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class);
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function department()
    {
        return $this->belongsTo(WaDepartment::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function employeeBankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }

    public function primaryBankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class)
            ->where('primary', true);
    }

    public function educationHistories()
    {
        return $this->hasMany(EmployeeEducationHistory::class);
    }

    public function professionalHistories()
    {
        return $this->hasMany(EmployeeProfessionalHistory::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(EmployeeBeneficiary::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function currentContract()
    {
        return $this->hasOne(EmployeeContract::class)->latest();
    }

    // MUTATORS AND ACCESSORS
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "$this->first_name $this->middle_name $this->last_name"
        );
    }

    public function inclusiveOfHouseAllowance(): Attribute
    {
        return Attribute::make(
            set: fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }

    public function eligibleForOvertime(): Attribute
    {
        return Attribute::make(
            set: fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }

    public function isLineManager(): Attribute
    {
        return Attribute::make(
            set: fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }

    public function isDraft(): Attribute
    {
        return Attribute::make(
            set: fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }

    // SCOPES
    public function scopeLineManagers(Builder $query)
    {
        return $query->where('is_line_manager', true);
    }
}
