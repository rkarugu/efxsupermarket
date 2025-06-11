<?php

namespace App;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaSupplierInvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'code', 'stock_id_code');
    }
}
