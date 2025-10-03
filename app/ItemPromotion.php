<?php

namespace App;

use App\Models\PromotionGroup;
use App\Models\PromotionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\WaInventoryItem;
use App\Model\User;


class ItemPromotion extends Model
{
    use HasFactory;
    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'inventory_item_id');
    }
    public function promotionItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'promotion_item_id');
    }
    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function promotionType()
    {
        return $this->belongsTo(PromotionType::class,'promotion_type_id')->withDefault([
            'name'=>'N/A'
        ]);
    }

    public function promotionGroup()
    {
        return $this->belongsTo(PromotionGroup::class,'promotion_group_id')->withDefault([
            'name'=>'N/A'
        ]);
    }
}
