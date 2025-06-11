<?php

namespace App;

use App\Model\WaInventoryItem;
use App\Models\ResolvedSalesmanReportedIssue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesmanShiftIssue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wainventoryitem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'inventory_item_id', 'id');
    }

    public function resolvedsalesmanreportedissue(): HasOne
    {
        return $this->hasOne(ResolvedSalesmanReportedIssue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesman_id', 'id');
    }
}
