<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetingBrandItem extends Model
{
    use HasFactory;
    protected $table = 'competing_brand_items';

    public function getCompetingBrand()
    {
        return $this->belongsTo(CompetingBrand::class,'competing_brand_id');
    }
    public function getRelatedItem()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
}
