<?php

namespace App\Model;

use App\InvoicePayment;
use App\Models\SaleCenterSmallPacks;
use App\SalesmanShift;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class WaInternalRequisition extends Model
{
    protected $guarded = [];

    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'requisition_no',
            'onUpdate' => true
        ]];
    }

    //create the  unique payment  code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->payment_code = $model->generateUniqueCode(); 
        });
    }

    public function generateUniqueCode()
    {
        do {
            $newCode = $this->generateRandomCode();
        } while ($this->codeExists($newCode));

        return $newCode;
    }

    protected function generateRandomCode()
    {
        $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        return substr(str_shuffle(str_repeat($characters, 8)), 0, 8);
    }

    protected function codeExists($code)
    {
        return self::where('payment_code', $code)->exists();
    }

    public function getRelatedItem()
    {
        return $this->hasMany('App\Model\WaInternalRequisitionItem', 'wa_internal_requisition_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(WaCustomer::class, 'customer_id', 'id');
    }


    public function getRouteCustomer()
    {
        return $this->belongsTo('App\Model\WaRouteCustomer', 'wa_route_customer_id');
    }

    public function getBranch()
    {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function getShiftInfo()
    {
        return $this->belongsTo(SalesmanShift::class, 'requisition_no', 'shift_id');
    }

    public function getDepartment()
    {
        return $this->belongsTo('App\Model\WaDepartment', 'wa_department_id');
    }

    public function getrelatedEmployee()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }


    public function getRelatedAuthorizationPermissions()
    {
        return $this->hasMany('App\Model\WaInternalReqPermission', 'wa_internal_requisition_id')->orderBy('approve_level', 'asc');
    }

    public function getRelatedFromLocationAndStore()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

    public function getRelatedToLocationAndStore()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'to_store_id');
    }


    public function shift(): BelongsTo
    {
        return $this->belongsTo(SalesmanShift::class, 'wa_shift_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    // Also Raw order total
    public function getOrderTotalForEsd()
    {
        $total = 0;
        foreach ($this->getRelatedItem as $saleItem) {
            $total += ($saleItem->selling_price * $saleItem->quantity);
        }

        return $total;
        // return ($this->getRelatedItem()->sum('total_cost_with_vat') + $this->getRelatedItem()->sum('discount'));
    }

    public function getOrderTotalWithoutDiscount()
    {
        return ($this->getRelatedItem()->sum('total_cost_with_vat') + $this->getRelatedItem()->sum('discount'));
    }

    public function getOrderTotalForReceipt() // With discount
    {
        return $this->getRelatedItem()->sum('total_cost_with_vat');
    }

    public function getTotalWithAllReturns()
    {
        $orderTotalWithDiscount = DB::table('wa_internal_requisition_items')->where('wa_internal_requisition_id', $this->id)->sum('total_cost_with_vat');
        return $orderTotalWithDiscount - $this->getTotalReturns();
    }

    public function getFinalTotal()
    {
        $orderTotalWithDiscount = DB::table('wa_internal_requisition_items')->where('wa_internal_requisition_id', $this->id)->sum('total_cost_with_vat');
        return $orderTotalWithDiscount - $this->getReceivedReturns();
    }

    public function getTotalReturns()
    {
        $returnRecords = DB::table('wa_inventory_location_transfer_item_returns')
            ->select(
                DB::raw('sum(wa_inventory_location_transfer_item_returns.return_quantity * wa_inventory_location_transfer_items.selling_price) as total'),
            )
            ->join('wa_inventory_location_transfers', function ($join) {
                $join->on('wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
                    ->where('wa_inventory_location_transfers.transfer_no', $this->requisition_no);
            })
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->get();

        return $returnRecords->sum('total');
    }

    public function getReceivedReturns()
    {
        $returnRecords = DB::table('wa_inventory_location_transfer_item_returns')
            ->select(
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) as total'),
            )
            ->join('wa_inventory_location_transfers', function ($join) {
                $join->on('wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
                    ->where('wa_inventory_location_transfers.transfer_no', $this->requisition_no);
            })
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->get();

        return $returnRecords->sum('total');
    }

    public function getTotalDiscount()
    {
        return $this->getRelatedItem()->sum('discount');
    }

    // TODO: Redundant
    public function getOrderTotal()
    {
        return $this->getRelatedItem()->sum('total_cost_with_vat');
    }

    // TODO: Redundant
    public function getOrderTotalWithDiscount()
    {
        return $this->getOrderTotal();
    }

    // TODO: Redundant
    public function getRealCost()
    {
        return $this->getOrderTotalForReceipt();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class, 'order_id', 'id');
    }
    public function esd_details(){
        return $this->hasOne(WaEsdDetails::class, 'invoice_number','requisition_no');
    }

    public function stockMoves(): HasMany
    {
        return  $this->hasMany(WaStockMove::class,'wa_internal_requisition_id');
    }
    public function debtorTrans(): HasOne
    {
        return  $this->hasOne(WaDebtorTran::class,'wa_sales_invoice_id','id');
    }

    public function smallPacks() : BelongsTo 
    {
        return $this->belongsTo(SaleCenterSmallPacks::class,'center_small_pack_id');    
    }

    public function totalDebtors()
    {
    //    return  WaDebtorTran::where('wa_sales_invoice_id', $this->id)->sum('amount');
       return  WaDebtorTran::where('document_no', $this->requisition_no)->sum('amount');
    }

    public function items()
    {
        return $this->hasMany(WaInternalRequisitionItem::class,'wa_internal_requisition_id');
    }
}
