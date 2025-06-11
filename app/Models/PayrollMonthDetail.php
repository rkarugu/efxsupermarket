<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollMonthDetail extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function payrollMonth()
    {
        return $this->belongsTo(PayrollMonth::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function earnings()
    {
        return $this->hasMany(PayrollMonthDetailEarning::class);
    }

    public function deductions()
    {
        return $this->hasMany(PayrollMonthDetailDeduction::class);
    }
}
