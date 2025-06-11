<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaPettyCashItem extends Model
{
   protected $table = 'wa_petty_cash_items';
   protected $guarded = [];
   public function chart_of_account()
   {
      return $this->belongsTo(WaChartsOfAccount::class,'wa_charts_of_account_id');
   }
   public function parent()
   {
      return $this->belongsTo(WaPettyCash::class,'wa_petty_cash_id');
   }
   public function branch()
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }
}


