<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionMispostHistory extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(WaCustomer::class, 'wa_customer_id');
    }

    public function debtorTrans(): BelongsTo
    {
        return $this->belongsTo(WaDebtorTran::class, 'wa_debtor_trans_id');
    }
}
