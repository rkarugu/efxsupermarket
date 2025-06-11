<?php

namespace App\Services;

use App\Model\WaExternalRequisition; 
use App\Model\WaExternalRequisitionItem; 
use App\SalesmanShift;
use App\SalesmanShiftCustomer;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class LpoService
{
    static public function mergeLpos($selectedIds, $mergeId): void
{
    $selectedLpos = WaExternalRequisition::whereIn('id', $selectedIds)->pluck('id')->toArray();
    $selectedLposNo = WaExternalRequisition::whereIn('id', $selectedIds)->pluck('purchase_no')->toArray();
    $mergeLpo = WaExternalRequisition::findOrFail($mergeId);

    DB::transaction(function () use ($selectedLpos, $mergeLpo, $selectedLposNo) {
        // Update merge_no with selectedLposNo
        $mergeLpo->update([
            'merge_no' => implode(',', $selectedLposNo) 
        ]);

        $selectedItems = WaExternalRequisitionItem::whereIn('wa_external_requisition_id', $selectedLpos)->get();

        foreach ($selectedItems as $item) {
            // Check if the item already exists in the merged requisition
            $existingItem = WaExternalRequisitionItem::where('wa_external_requisition_id', $mergeLpo->id)
                ->where('wa_inventory_item_id', $item->wa_inventory_item_id)
                ->first();

            if ($existingItem) {
                // If it exists, update the quantity
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity,
                    'total_cost' => $existingItem->total_cost + $item->total_cost,
                    'vat_amount' => $existingItem->vat_amount + $item->vat_amount,
                    'total_cost_with_vat' => $existingItem->total_cost_with_vat + $item->total_cost_with_vat
                ]);
            } else {
                // If it does not exist, create a new record
                WaExternalRequisitionItem::create([
                    'wa_external_requisition_id' => $mergeLpo->id, 
                    'wa_inventory_item_id' => $item->wa_inventory_item_id,
                    'quantity' => $item->quantity,
                    'standard_cost' => $item->standard_cost,
                    'total_cost' => $item->total_cost,
                    'vat_rate' => $item->vat_rate,
                    'total_cost_with_vat' => $item->total_cost_with_vat,
                    'vat_amount' => $item->vat_amount,
                    'note' => $item->note,
                    'is_resolved' => $item->is_resolved,
                    'item_no' => $item->item_no,
                    'gl_code_id' => $item->gl_code_id,
                    'unit_of_measure_id' => $item->unit_of_measure_id,
                ]);
            }
        }

         WaExternalRequisition::whereIn('id', $selectedLpos)->update(['is_hide' => 'Yes']);

    });
}


}


          