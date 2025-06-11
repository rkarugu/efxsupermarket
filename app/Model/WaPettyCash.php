<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPettyCash extends Model
{
   protected $table = 'wa_petty_cash';
   protected $guarded = [];

   public function items()
   {
      return $this->hasMany(WaPettyCashItem::class,'wa_petty_cash_id');
   }
   public function user()
   {
      return $this->belongsTo(User::class,'user_id');
   }

   public function payment_method()
   {
      return $this->belongsTo(PaymentMethod::class,'payment_method_id');
   }
   public function bank_account()
   {
      return $this->belongsTo(WaBankAccount::class,'wa_bank_account_id');
   }

   public function approvals(){
      return $this->hasMany(WaPettyCashApprovals::class,'petty_cash_id');
   }
}


