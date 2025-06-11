<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaInventoryItemSupplierData;
use App\Model\WaInventoryItemSupplierDataApprovals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryItemSupplierDataController extends Controller
{
    public function removeMissingItems()
    {
        try {
            $deleted1 = 0;
            $deleted2 = 0;
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $itemSuppliers = DB::table('wa_inventory_item_suppliers')->get();
            foreach ($itemSuppliers as $itemSupplier) {
                if (!(DB::table('wa_inventory_items')->where('id', $itemSupplier->wa_inventory_item_id)->first())) {
                    WaInventoryItemSupplier::find($itemSupplier->id)->delete();
                    $deleted1 += 1;
                }
            }

            $itemSupplierData = DB::table('wa_inventory_item_supplier_data')->get();
            foreach ($itemSupplierData as $datum) {
                if (!(DB::table('wa_inventory_items')->where('id', $datum->wa_inventory_item_id)->first())) {
                    WaInventoryItemSupplierDataApprovals::where('wa_supplier_data_id', $datum->id)->first()?->delete();
                    WaInventoryItemSupplierData::find($datum->id)->delete();
                    $deleted2 += 1;
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return $this->jsonify(['deleted1' => $deleted1, 'deleted2' => $deleted2], 200);
        } catch(\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
