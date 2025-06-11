<?php

namespace App;

use App\Model\WaInventoryItem;
use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingListItemDispatch extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function listItem(){
        return $this->belongsTo(WaInventoryItem::class, 'inventory_items_id');
    }
    public function userReceived(){
        return $this->belongsTo(User::class, 'receiving_person_id');
    }
    public function dispatchShift(){
        return $this->belongsTo(SalesmanShift::class, 'shift_id');
    }

}
