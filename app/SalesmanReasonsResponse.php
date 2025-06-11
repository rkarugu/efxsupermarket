<?php

namespace App;

use App\Model\WaRouteCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesmanReasonsResponse extends Model
{
    use HasFactory;

    public function response_options(): HasMany
    {
        return $this->hasMany(SalesmanReasonsResponseOption::class, 'reasons_responses_id', 'id');
    }

    public function responseCustomer(): BelongsTo
    {
        return $this->belongsTo(WaRouteCustomer::class, 'wa_route_customer_id', 'id');
    }

    public function responseSalesmanShift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class, 'shift_id', 'id');
    }
}
