<?php

namespace App\Imports;

use App\Model\WaInventoryItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\DisplayBinUserItemAllocation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserItemAllocation implements ToCollection, WithHeadingRow
{
    protected $userId;
    protected $binId;
    protected $locationId;
    protected $rows;
    protected $duplicates;

    public function __construct($userId, $binId, $locationId)
    {
        $this->userId = $userId;
        $this->binId = $binId;
        $this->locationId = $locationId;
        $this->rows = collect();
        $this->duplicates = collect();
    }
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        foreach ($rows as $row) {
            $row = $row->toArray();
            $row = array_change_key_case($row, CASE_LOWER);
            $item = WaInventoryItem::where('stock_id_code', $row['item_code'])->where('status', 1)->first();
            if($item){
                $exists = DisplayBinUserItemAllocation::where('wa_location_and_store_id', $this->locationId)
                ->where('bin_id', $this->binId)
                ->where('wa_inventory_item_id', $item->id)
                ->exists();
            if ($exists) {
                DisplayBinUserItemAllocation::where('wa_location_and_store_id', $this->locationId)
                ->where('bin_id', $this->binId)
                ->where('wa_inventory_item_id', $item->id)->delete();
                // $this->duplicates->push($row);
                $this->rows->push(new DisplayBinUserItemAllocation([
                    'user_id' => $this->userId,
                    'wa_location_and_store_id' => $this->locationId,
                    'bin_id' => $this->binId,
                    'wa_inventory_item_id' => $item->id,
                ]));
            } else {
                $this->rows->push(new DisplayBinUserItemAllocation([
                    'user_id' => $this->userId,
                    'wa_location_and_store_id' => $this->locationId,
                    'bin_id' => $this->binId,
                    'wa_inventory_item_id' => $item->id,
                ]));
            }
            }else{
                $this->duplicates->push($row);
            }
          
        }

        if ($this->duplicates->isEmpty()) {
            foreach ($this->rows as $allocation) {
                $allocation->save();
                DB::commit();
            }
        }else{
            DB::rollBack();
        }
    }
    public function getDuplicates()
    {
        return $this->duplicates;
    }
}
