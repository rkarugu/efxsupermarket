<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaInternalRequisitionDispatch extends Model
{
    protected $table = 'wa_internal_requisition_dispatch';
    protected $guarded = [];
    public function dispatch_user()
    {
        return $this->belongsTo(User::class,'dispatched_by');
    }
}