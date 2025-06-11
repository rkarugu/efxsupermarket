<?php

namespace App;

use App\Model\WaInventoryCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaItemSubCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function image(): Attribute
    {
        $appUrl = env('APP_URL');
        return Attribute::make(get: fn($value) => $value ? "$appUrl/$value" : "$appUrl/placeholder.png");
    }

    public function category()
    {
        return $this->belongsToMany(WaInventoryCategory::class, 'wa_inventory_category_sub_category_relation', 'sub_category_id', 'category_id');
    }
}
