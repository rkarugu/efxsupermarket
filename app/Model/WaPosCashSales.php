<?php
namespace App\Model;
use App\Enums\PromotionMatrix;
use App\ItemPromotion;
use App\Models\PromotionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;


class WaPosCashSales extends Model
{
    protected $table = 'wa_pos_cash_sales';
    protected $guarded = [];
    protected $appends = ['total'];

    protected $casts= [
      'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(WaPosCashSalesItems::class,'wa_pos_cash_sales_id');
    }
    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function payments()
    {
        return $this->hasMany(WaPosCashSalesPayments::class,'wa_pos_cash_sales_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }



    public function dispatch()
    {
        return $this->hasMany(WaPosCashSalesDispatch::class,'pos_sales_id');
    }

    public function getTotalAttribute()
    {
        return $this->items->sum(function($item) {
            return ceil(($item->qty * $item->selling_price) - $item->discount_amount);
        });
    }


//    public function dispatching(): Attribute
//    {
//        $dispatching = WaPosCashSalesDispatch::where('pos_sales_id', $this->id)->where('status', 'dispatching')->count();
//        if ($dispatching > 0){
//            $state = true;
//        }else{
//            $state = false;
//        }
//        return Attribute::make(
//             get: fn () => $state,
//        );
//    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(WaPosCashSalesItemReturns::class,'wa_pos_cash_sales_id');
    }

    public function getAcceptedReturnsTotalAttribute()
    {
        return $this->returnItems->where('accepted', true)
            ->sum(function($returnItem) {
//                $cost_of_one = $returnItem->saleItem->selling_price / $row->saleItem->qty;
//                return number_format(ceil($cost_of_one * $row->return_quantity), 2);
            return ($returnItem->saleItem->selling_price * $returnItem->return_quantity) - $this->calcDiscount($returnItem);
        });
    }
    public function getPendingReturnsTotalAttribute()
    {
        return $this->returnItems->where('accepted', false)
            ->sum(function($returnItem) {
            return ($returnItem->saleItem->selling_price * $returnItem->return_quantity) - $this->calcDiscount($returnItem);
        });
    }
    public function totalBeforeReturn(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum(function ($item) {

                $quantity = $item->ReturnItems->isNotEmpty() ? $item->original_quantity : $item->qty;
                return ($quantity * $item->selling_price) - $item->discount_amount;
            })
        );
    }

    public function calcDiscount($item)
    {

        $quantity = $item->return_quantity;
        $item_id = $item->saleItem->wa_inventory_item_id;
        $discount = 0;
        $discountDescription = null;
        $discountBand = DB::table('discount_bands')->where('inventory_item_id', $item_id)
            ->where('from_quantity', '<=', $quantity)
            ->where('to_quantity', '>=', $quantity)
            ->first();
        if ($discountBand) {
            $discount = $discountBand->discount_amount * $quantity;
        }else{
        /*check for discount price promotion*/
        $discount = $this->checkPromotion($item_id);
    }
        return $discount;
    }
    public function checkPromotion($item_id)
    {
        $discount = 0;
        $today = Carbon::today();
        $promotion = ItemPromotion::where('inventory_item_id', $item_id)
            ->where('status', 'active')
            ->where(function ($query) use ($today) {
                $query->where('from_date', '<=', $today)
                    ->where('to_date', '>=', $today);
            })
            ->first();

        if ($promotion) {
            /*get promotion type*/
            $promotionType = $promotion->promotion_type_id ? PromotionType::find($promotion->promotion_type_id)->description : null;

            if ($promotionType)
            {

                /*Price Discount*/
                if ($promotionType == PromotionMatrix::PD->value)
                {
                    /*chenge selling price*/
                    $selling_price = $promotion->promotion_price;
                    $current_price = $promotion->current_price;
                    $discount = $current_price - $selling_price;

                }

            }

        }

        return $discount;
    }

    public function getOrderTotalForEsd()
    {
        $total = 0;
        foreach ($this->items as $saleItem) {
            $total += ($saleItem->selling_price * $saleItem->quantity);
        }

        return $total;
    }

    public function attendingCashier()
    {
        return $this->belongsTo(User::class,'attending_cashier');
    }

    public function buyer()
    {
        return $this->belongsTo(WaRouteCustomer::class,'wa_route_customer_id');
    }

    public function getGrossTotalAttribute()
    {
        return $this->items->sum(function($item) {
            return ceil($item->qty * $item->selling_price);
        });
    }
}