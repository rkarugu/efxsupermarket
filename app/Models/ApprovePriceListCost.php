<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovePriceListCost extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'inventory_item_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id', 'id');
    }

    public function trade(): BelongsTo
    {
        return $this->belongsTo(TradeAgreement::class, 'trade_agreement_id', 'id');
    }
}
