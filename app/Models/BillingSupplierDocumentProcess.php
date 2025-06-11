<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillingSupplierDocumentProcess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tradeagreement(): BelongsTo
    {
        return $this->belongsTo(TradeAgreement::class, 'trade_agreement_id', 'id');
    }

    public function billingsupplierdocumentprocessfiles(): HasMany
    {
        return $this->hasMany(BillingSupplierDocumentProcessFile::class);
    }

    public function billingsupplierdocumentprocesslog(): HasOne
    {
        return $this->hasOne(BillingSupplierDocumentProcessLog::class);
    }

    public function billingsupplierdocumentprocesslogs(): HasMany
    {
        return $this->hasMany(BillingSupplierDocumentProcessLog::class);
    }
}
