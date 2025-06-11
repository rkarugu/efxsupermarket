<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletSupplierDocumentProcessLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function walletsupplierdocumentprocesslog(): BelongsTo
    {
        return $this->belongsTo(WalletSupplierDocumentProcess::class, 'wallet_supplier_document_process_id', 'id');
    }
}
