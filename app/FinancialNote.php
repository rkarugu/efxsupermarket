<?php

namespace App;

use App\Model\Restaurant;
use App\Model\WaGlTran;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Models\TradeDiscountDemand;
use App\Models\WaReturnDemand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        // 'note_date' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function location()
    {
        return $this->belongsTo(Restaurant::class, 'location_id');
    }

    public function items()
    {
        return $this->hasMany(FinancialNoteItem::class, 'financial_note_id');
    }

    public function suppTran()
    {
        return $this->hasOne(WaSuppTran::class, 'document_no', 'note_no');
    }

    public function return()
    {
        return $this->hasOne(WaReturnDemand::class, 'credit_note_no', 'note_no');
    }

    public function discount()
    {
        return $this->hasOne(TradeDiscountDemand::class, 'credit_note_no', 'note_no');
    }

    public function hasPayment()
    {
        return $this->suppTran()->whereHas('payments')->exists();
    }

    public function isReturnDocument()
    {
        return $this->return()->exists();
    }

    public function isDiscountDocument()
    {
        return $this->discount()->exists();
    }

    public function glTransactions()
    {
        return $this->hasMany(WaGlTran::class, 'transaction_no', 'note_no');
    }
}
