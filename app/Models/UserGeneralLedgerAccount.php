<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGeneralLedgerAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts(): BelongsTo
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'account_id', 'id');
    }
}
