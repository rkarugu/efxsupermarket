<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollMonthDetailDeduction extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function payrollMonthDetail()
    {
        return $this->belongsTo(PayrollMonthDetail::class);
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }
}
