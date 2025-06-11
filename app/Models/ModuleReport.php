<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function modulereportcategory(): BelongsTo
    {
        return $this->belongsTo(ModuleReportCategory::class, 'module_report_category_id', 'id');
    }
}
