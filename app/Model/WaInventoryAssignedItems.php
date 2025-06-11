<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;

class WaInventoryAssignedItems extends Model
{
    protected $table = 'wa_inventory_assigned_items';
    protected $guarded = [];

    public function parent_item()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
    public function destinated_item()
    {
        return $this->belongsTo(WaInventoryItem::class,'destination_item_id');
    }
}