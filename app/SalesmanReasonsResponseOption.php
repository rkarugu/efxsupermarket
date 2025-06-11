<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesmanReasonsResponseOption extends Model
{
    use HasFactory;

    public function optionReason(): BelongsTo {
        return  $this->belongsTo(SalesmanReportingReasonOption::class, 'reporting_reason_options_id', 'id');
    }
}
