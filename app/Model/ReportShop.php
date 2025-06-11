<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportShop extends Model
{
    //


    protected $fillable = [
        'wa_route_customer_id',
        'comments',
        'report_reason_id',
    ];

    public function reason():BelongsTo{
        return $this->belongsTo(ReportReason::class,'report_reason_id');
    }
    public function shop():BelongsTo{
        return $this->belongsTo(WaRouteCustomer::class, 'wa_route_customer_id');
    }
}
