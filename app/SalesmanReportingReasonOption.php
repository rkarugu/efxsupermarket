<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesmanReportingReasonOption extends Model
{
    use HasFactory;

    public function reportingOptionName(): BelongsTo
    {
        return $this->belongsTo(SalesmanReportingReason::class, 'reporting_reason_id', 'id');
    }
}
