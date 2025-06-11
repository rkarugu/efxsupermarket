<?php

namespace App\Models;

use App\Model\WaGlTran;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPettyCashRequestItemWithdrawal extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function requestItem()
    {
        return $this->belongsTo(WaPettyCashRequestItem::class, 'request_item_id');
    }

    public function glTran()
    {
        return $this->hasOne(WaGlTran::class, 'document_no', 'transaction_no');
    }
}
