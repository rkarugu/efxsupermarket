<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WalletSupplierDocumentProcess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tradeagreement(): BelongsTo
    {
        return $this->belongsTo(TradeAgreement::class, 'trade_agreement_id', 'id');
    }

    public function walletsupplierdocumentprocessfiles(): HasMany
    {
        return $this->hasMany(WalletSupplierDocumentProcessFile::class);
    }

    public function walletsupplierdocumentprocesslog(): HasOne
    {
        return $this->hasOne(WalletSupplierDocumentProcessLog::class);
    }

    public function walletsupplierdocumentprocesslogs(): HasMany
    {
        return $this->hasMany(WalletSupplierDocumentProcessLog::class);
    }

}
