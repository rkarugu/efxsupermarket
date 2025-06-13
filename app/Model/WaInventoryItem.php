<?php

namespace App\Model;

use App\ItemPromotion;
use App\Models\HamperItem;
use App\Models\WaInventoryItemPrice;
use App\ProductionProcess;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\DiscountBand;
use App\Models\BaseModel;
use App\Models\CompetingBrandItem;
use App\Models\UpdateBinInventoryUtilityLog;
use App\Models\UpdateItemBin;
use App\Models\UpdateItemPriceUtilityLog;
use App\Models\UpdateNewItemInventoryUtilityLog;
use App\Models\WaInventoryItemApprovalStatus;
use App\Models\WaStoreReturnItem;
use App\ReturnedGrn;
use App\SalesmanShiftIssue;
use Illuminate\Support\Facades\Auth;

class WaInventoryItem extends BaseModel
{

    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'title',
            'onUpdate' => true
        ]];
    }

    // protected $fillable = ['supplier_code', 'approval_status'];

    protected $guarded = [];

    public function wainventorylocationstockstatus(): HasOne
    {
        return $this->hasOne(WaInventoryLocationStockStatus::class);
    }

    public function destinated_items()
    {
        return $this->hasMany(WaInventoryAssignedItems::class, 'wa_inventory_item_id');
    }

    public function assignedItem()
    {
        return $this->hasOne(WaInventoryAssignedItems::class, 'wa_inventory_item_id');
    }

    public function inventory_item_suppliers()
    {
        return $this->belongsToMany(WaSupplier::class, 'wa_inventory_item_suppliers', 'wa_inventory_item_id', 'wa_supplier_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(WaSupplier::class, 'wa_inventory_item_suppliers', 'wa_inventory_item_id', 'wa_supplier_id');
    }

    public function bin_locations()
    {
        return $this->hasMany(WaInventoryLocationUom::class, 'inventory_id');
    }

    public function binLocation()
    {
        return $this->hasOne(WaInventoryLocationUom::class, 'inventory_id');
    }

    public function getAssignedItem()
    {
        return $this->hasOne(WaInventoryAssignedItems::class, 'destination_item_id');
    }

    public function getUnitOfMeausureDetail()
    {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }

    public function getTaxesOfItem()
    {
        return $this->belongsTo('App\Model\TaxManager', 'tax_manager_id');
    }

    public function taxManager()
    {
        return $this->belongsTo(TaxManager::class, 'tax_manager_id');
    }

    public function pack_size()
    {
        return $this->belongsTo('App\Model\PackSize', 'pack_size_id');
    }

    public function packSize()
    {
        return $this->belongsTo('App\Model\PackSize', 'pack_size_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'store_location_id');
    }

    public function getInventoryCategoryDetail()
    {
        return $this->belongsTo('App\Model\WaInventoryCategory', 'wa_inventory_category_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\WaInventoryCategory', 'wa_inventory_category_id');
    }

    public function sub_category()
    {
        return $this->belongsTo('App\Model\ItemSubCategories', 'item_sub_category_id');
    }

    public function stockMoves()
    {
        return $this->hasMany(WaStockMove::class, 'wa_inventory_item_id');
    }

    public function itemQuantity($branch = null)
    {
        if (is_null($branch)) {
            return $this->stockMoves()->sum('qauntity');
        }
        return $this->stockMoves()->where('wa_location_and_store_id', $branch)->sum('qauntity');
    }

    public function locationItemQuantity($locationId = null)
    {
        if ($locationId) {
            return $this->stockMoves()->where('wa_location_and_store_id', $locationId)->sum('qauntity');
        }
        return $this->stockMoves()->sum('qauntity');
    }

    public function getAllFromStockMoves()
    {
        return $this->hasMany('App\Model\WaStockMove', 'wa_inventory_item_id', 'id');
    }

    public function getAllFromStockMovesC()
    {
        return $this->hasMany('App\Model\WaStockMoveC', 'stock_id_code', 'stock_id_code');
    }

    public function getstockmoves()
    {
        return $this->belongsTo('App\Model\WaStockMove', 'stock_id_code', 'stock_id_code');
    }

    public function getstockmoves2()
    {
        return $this->belongsTo('App\Model\WaStockMove2', 'stock_id_code', 'stock_id_code');
    }

    public function supplier_data()
    {
        return $this->hasMany(WaInventoryItemSupplierData::class, 'wa_inventory_item_id');
    }

    public function get_supplier_data()
    {
        return $this->hasMany(WaInventoryItemSupplier::class, 'wa_inventory_item_id');
    }


    public function unitofmeasures()
    {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }

    public function bom(): HasMany
    {
        return $this->hasMany(WaInventoryItemRawMaterial::class, 'wa_inventory_item_id', 'id');
    }


    public static function getInventoryItemListData()
    {
        $list_data = WaInventoryItem::select('id', 'title', 'standard_cost')->get()->toArray();
        $list = $data = [];
        foreach ($list_data as $key => $row) {
            $list[$row['id']] = $row['title'];
            $data[$row['id']] = $row;
        }
        return [$list, $data];
    }

    public function processes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductionProcess::class,
            'production_process_wa_inventory_item',
            'wa_inventory_item_id',
            'production_process_id'
        )->withPivot(['step_number', 'duration', 'quality_control_check']);
    }

    /**
     * Returns only items that are made and not bought
     *
     * @param Builder $query
     * @return void
     */
    public function scopeProducible(Builder $query): void
    {
        $query->where('restocking_method', '2');
    }

    public function latestStockMove(): HasOne
    {
        return $this->hasOne(WaStockMove::class, 'stock_id_code', 'stock_id_code')->latestOfMany();
    }

    public function categoryPrices(): HasMany
    {
        return $this->hasMany(WaCategoryItemPrice::class, 'item_id', 'id');
    }

    public function discountBands()
    {
        return $this->hasMany(DiscountBand::class, 'inventory_item_id');
    }

    public function salesmanshiftissues(): HasMany
    {
        return $this->hasMany(SalesmanShiftIssue::class);
    }

    public function getBin($storeId)
    {
        $binEntry = $this->bin_locations()->where('location_id', $storeId)->first();
        if (!$binEntry) {
            return 'Unassigned';
        }

        return (WaUnitOfMeasure::find($binEntry->uom_id))?->title;
    }

    public function getBinData($storeId)
    {
        $binEntry = $this->bin_locations()->where('location_id', $storeId)->first();
        if (!$binEntry) {
            return 0;
        }

        return (WaUnitOfMeasure::find($binEntry->uom_id));
    }

    public function approvalStatus(): HasMany
    {
        return $this->hasMany(WaInventoryItemApprovalStatus::class, 'wa_inventory_items_id')->orderByDesc('created_at');
    }

    public function locationPrices(): HasMany
    {
        return $this->hasMany(WaInventoryItemPrice::class, 'wa_inventory_item_id');
    }

    protected function shopPrice(): Attribute
    {
        return Attribute::make(
            get: function ($attributes) {
                $price = $attributes['selling_price'];

                $userLoc = Auth::user()->wa_location_and_store_id ?? 0;
                if ($userLoc != 0) {
                    $location_price = $this->locationPrices
                        ->where('store_location_id', $userLoc)
                        ->first();

                    if ($location_price && $location_price->selling_price !== null) {
                        if ($location_price->ends_at !== null) {
                            if (!Carbon::now()->isPast($location_price->ends_at)) {
                                $price = $location_price->selling_price;
                            }
                        } else {
                            $price = $location_price->selling_price;
                        }
                    }
                }
                return $price;
            }
        );
    }

    public function hamperItem()
    {
        return $this->hasMany(HamperItem::class, 'hamper_id');
    }

    public function itemPromotions()
    {
        return $this->hasMany(ItemPromotion::class, 'inventory_item_id');
    }

    public function promotions()
    {
        return $this->hasMany(ItemPromotion::class, 'inventory_item_id');
    }

    public function activePromotion()
    {
        return $this->hasOne(ItemPromotion::class, 'inventory_item_id')->latest()->where('status', 'active');
    }

    // Utility relationships start

    public function inventorybins(): HasMany
    {
        return $this->hasMany(UpdateBinInventoryUtilityLog::class, 'id', 'wa_inventory_item_id');
    }

    public function inventoryitemprices(): HasMany
    {
        return $this->hasMany(UpdateItemPriceUtilityLog::class, 'id', 'wa_inventory_item_id');
    }

    public function newiteminventoryutilitylogs(): HasMany
    {
        return $this->hasMany(UpdateNewItemInventoryUtilityLog::class, 'id', 'wa_inventory_item_id');
    }

    public function updateitembins(): HasMany
    {
        return $this->hasMany(UpdateItemBin::class, 'id', 'inventory_item_id');
    }

    // Utility relationships end

    public function pendingStoreReturnItem()
    {
        return $this->hasMany(WaStoreReturnItem::class, 'wa_inventory_item_id')
            ->whereHas('storeReturn', fn($query) => $query->where('approved', false)->where('rejected', false))
            ->select('wa_inventory_item_id', 'quantity');
    }

    public function pendingGrnReturnItem()
    {
        return $this->hasMany(ReturnedGrn::class, 'item_code', 'stock_id_code')
            ->where('approved', false)
            ->where('rejected', false)
            ->select('item_code', 'returned_quantity');
    }
    public function competingBrand()
    {
        return $this->hasOne(CompetingBrandItem::class, 'wa_inventory_item_id');
    }
}
