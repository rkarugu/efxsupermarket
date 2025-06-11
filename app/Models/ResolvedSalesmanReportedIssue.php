<?php

namespace App\Models;

use App\SalesmanShiftIssue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResolvedSalesmanReportedIssue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function salesmanshiftissue(): HasOne
    {
        return $this->hasOne(SalesmanShiftIssue::class);
    }
}
