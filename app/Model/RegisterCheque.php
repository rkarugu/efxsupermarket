<?php

namespace App\Model;

use App\Models\ChequeBank;
use Illuminate\Database\Eloquent\Model;

class RegisterCheque extends Model
{
    protected $table = 'register_cheque';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function depositer()
    {
        return $this->belongsTo(User::class,'deposited_by');
    }
    public function salesman()
    {
        return $this->belongsTo(WaLocationAndStore::class,'salesman_id');
    }

    public function customer()
    {
        return $this->belongsTo(WaCustomer::class,'wa_customer_id');
    }

    public function bank()
    {
        return $this->belongsTo(ChequeBank::class,'bank_deposited')->withDefault([
            'bank'=>''
        ]);
    }
}
