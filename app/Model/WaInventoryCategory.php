<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaInventoryCategory extends Model
{

    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'category_description',
            'onUpdate' => true
        ]];
    }

    protected $fillable = ['category_code'];

    public function getStockTypecategory()
    {
        return $this->belongsTo('App\Model\WaStockTypeCategory', 'wa_stock_type_category_id');
    }

    public function getStockFamilyGroup()
    {
        return $this->belongsTo('App\Model\WaStockFamilyGroup', 'wa_stock_family_group_id');
    }

    public function getStockGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'stock_gl_code_id');
    }


    public function getAdjustGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'stock_adjustments_gl_code_id');
    }

    public function getIssueGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'internal_stock_issues_gl_code_id');
    }

    public function getPricevarianceGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'price_variance_gl_code_id');
    }

    public function getusageGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'usage_variance_gl_code_id');
    }

    public function getWIPGlDetail()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'wip_gl_code_id');
    }

    public function getinventoryitems()
    {
        return $this->hasMany('App\Model\WaInventoryItem', 'wa_inventory_category_id');
    }

    public function getinventoryitemshowroomstock()
    {
        return $this->hasMany('App\Model\WaInventoryItem', 'wa_inventory_category_id')->where('showroom_stock', '1');
    }

    public function sub_categories()
    {
        return $this->belongsToMany(ItemSubCategories::class, 'wa_inventory_category_sub_category_relation', 'category_id', 'sub_category_id');
    }

    public function image(): Attribute
    {
        $appUrl = env('APP_URL');
        return Attribute::make(get: fn($value) => $value ? "$appUrl/$value" : "$appUrl/placeholder.png");
    }
}


