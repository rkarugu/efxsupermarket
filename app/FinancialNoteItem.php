<?php

namespace App;

use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialNoteItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'account_id');
    }
}
