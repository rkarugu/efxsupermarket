<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaCloseBranchEndOfDay extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function openedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by', 'id');
    }

    public function closedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    public function walocationandstore(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'wa_location_and_store_id', 'id');
    }

    public function chiefcashierdeclaration()
    {
        return $this->hasOne(ChiefCashierDeclaration::class);
    }
}
