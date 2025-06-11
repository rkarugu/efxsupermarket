<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteReport extends Model
{
    //

    protected $fillable = [
        'route_id',
        'report_reason_id',
        'comments',
        'image',
    ];


    public function reason() : BelongsTo {
        
        return $this->belongsTo(ReportReason::class, 'report_reason_id');
    }

    public function route() : BelongsTo {
        
        return $this->belongsTo(Route::class, 'route_id');
    }
}
