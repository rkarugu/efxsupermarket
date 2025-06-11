<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaGlTran extends BaseModel
{
    protected $guarded = [];

    public function getAccountDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'account', 'account_code');
    }

    public function getShiftDetail()
    {
        return $this->belongsTo('App\Model\WaShift', 'shift_id', 'id');
    }


    public function restaurant()
    {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Model\Restaurant','tb_reporting_branch');
    }    

    public function relatedItems()
    {
        $relatedItems = $this->hasMany('App\Model\WaGlTran', 'transaction_no','transaction_no');
        if(request()->get('start-date') && request()->get('end-date'))
        {
            $date1 = request()->get('start-date').' 00:00:00';
            $date2 = request()->get('end-date').' 23:59:59';       
            $relatedItems = $relatedItems->whereDate('trans_date', '>=', $date1)->whereDate('trans_date', '<=', $date2);
        }
        return $relatedItems->with(['restaurant']);
    }    

    public function customer(): BelongsTo
    {
        return $this->belongsTo(WaCustomer::class,'customer_id');
    }
}
