<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollMonthDetailEarning extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function payrollMonthDetail()
    {
        return $this->belongsTo(PayrollMonthDetail::class);
    }

    public function earning()
    {
        return $this->belongsTo(Earning::class);
    }
}
