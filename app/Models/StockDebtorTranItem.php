<?php

namespace App\Models;

use App\Model\WaDebtorTran;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockDebtorTranItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(StockDebtor::class,'stock_debtors_id');
    }

    public function debtorTran(): BelongsTo
    {
        return $this->belongsTo(StockDebtorTran::class,'stock_debtor_trans_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class,'inventory_item_id');
    }

    public function waDebtorTran(): BelongsTo
    {
        return $this->belongsTo(WaDebtorTran::class,'inventory_item_id');
    }

    public function stockMoves(): BelongsTo
    {
        return $this->belongsTo(WaStockMove::class,'stock_moves_id');
    }

    public function stockCountVariation(): BelongsTo
    {
        return $this->belongsTo(WaStockCountVariation::class,'stock_count_variation_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'uom_id');
    }
}
