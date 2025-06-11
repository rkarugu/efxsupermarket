<?php

namespace App;

use App\Model\User;
use App\Model\WaGrn;
use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnedGrn extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'approved' => 'boolean',
        'approved_date' => 'datetime',
        'rejected' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id');
    }

    public function grn()
    {
        return $this->belongsTo(WaGrn::class, 'grn_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'item_code', 'stock_id_code');
    }

    public function totalCost()
    {
        return self::with('grn')->where('return_number', $this->return_number)->get()
            ->sum(function ($return) {
                return (float)$return->grn?->qty_received * (float)json_decode($return->grn?->invoice_info)?->order_price;
            });
    }
}
