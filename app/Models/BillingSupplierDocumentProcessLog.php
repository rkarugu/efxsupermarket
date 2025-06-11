<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingSupplierDocumentProcessLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function billingsupplierdocumentprocesslog(): BelongsTo
    {
        return $this->belongsTo(BillingSupplierDocumentProcess::class, 'billing_supplier_document_process_id', 'id');
    }
}
