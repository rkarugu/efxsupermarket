<?php

namespace App\Imports;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Models\DisplayBinUserItemAllocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkUserItemAllocationImport implements ToCollection, WithHeadingRow
{
    protected $rows;
    protected $duplicates;
    public function __construct()
    {
        $this->rows = collect();
        $this->duplicates = collect();
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        foreach ($rows as $row) {
            $row = $row->toArray();
            $row = array_change_key_case($row, CASE_LOWER);
            $item = WaInventoryItem::where('stock_id_code', $row['item_code'])->where('status', 1)->first();
            $user = User::find($row['user_id']);
            if(!$user || !$user->wa_unit_of_measures_id || !$user->wa_location_and_store_id){
                $this->duplicates->push($row);
                continue;
            }
            if($item && $user){
                $exists = DisplayBinUserItemAllocation::where('wa_location_and_store_id', $user->wa_location_and_store_id)
                ->where('bin_id', $user->wa_unit_of_measures_id)
                ->where('wa_inventory_item_id', $item->id)
                ->exists();
            if ($exists) {
                DisplayBinUserItemAllocation::where('wa_location_and_store_id', $user->wa_location_and_store_id)
                ->where('bin_id', $user->wa_unit_of_measures_id)
                ->where('wa_inventory_item_id', $item->id)->delete();
                $this->rows->push(new DisplayBinUserItemAllocation([
                    'user_id' => $user->id,
                    'wa_location_and_store_id' => $user->wa_location_and_store_id,
                    'bin_id' => $user->wa_unit_of_measures_id,
                    'wa_inventory_item_id' => $item->id,
                ]));
            } else {
                $this->rows->push(new DisplayBinUserItemAllocation([
                    'user_id' => $user->id,
                    'wa_location_and_store_id' => $user->wa_location_and_store_id,
                    'bin_id' => $user->wa_unit_of_measures_id,
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
