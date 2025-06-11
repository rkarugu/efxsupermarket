<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaBankAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBankFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'fileUrl'
    ];

    public function account()
    {
        return $this->belongsTo(WaBankAccount::class, 'wa_bank_account_id');
    }

    public function items()
    {
        return $this->hasMany(WaBankFileItem::class, 'wa_bank_file_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function withholdingFileItem()
    {
        return $this->hasOne(WaWithholdingFileItem::class, 'wa_bank_file_id');
    }

    public function scopePendingWithholding(Builder $query)
    {
        return $query->whereDoesntHave('withholdingFileItem');
    }

    public function getFileUrlAttribute()
    {
        return route('bank-files.download', $this->id);
    }

    public function supportingDocumentRequired()
    {
        return $this->items->where('amount', '>', '999999')->count();
    }

    public function getWithholdingAmount()
    {
        return $this->items->sum(function ($item) {
            return $item->voucher->withholding_amount;
        });
    }
}
