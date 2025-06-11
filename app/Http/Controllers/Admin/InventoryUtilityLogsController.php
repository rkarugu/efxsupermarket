<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UpdateBinInventoryUtilityLog;
use App\Models\UpdateItemBin;
use App\Models\UpdateItemBinLog;
use App\Models\UpdateItemPriceUtilityLog;
use App\Models\UpdateNewItemInventoryUtilityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryUtilityLogsController extends Controller
{
    protected $model;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'inventory-utility-logs';
    }

    public function index(Request $request)
    {
        $title = 'Update Item Prices';
        $model = $this->model;

        $locationId = $request->input('location');
        $binId = $request->input('bin');
        $initiatedById = $request->input('initiatedby');
        $approvedById = $request->input('approvedby');

        $query = UpdateBinInventoryUtilityLog::with('initiatedby', 'approvedby', 'item', 'bin', 'location');
        $query_2 = UpdateNewItemInventoryUtilityLog::with('initiatedby', 'approvedby', 'item');
        $query_3 = UpdateItemPriceUtilityLog::with('initiatedby', 'item', 'location', 'locationitemprice');
        $query_4 = UpdateItemBinLog::with('user', 'item', 'newbin', 'previousbin', 'location')->get();

        if ($locationId) {
            $query->where('wa_location_and_store_id', $locationId);
        }
        if ($binId) {
            $query->where('wa_unit_of_measure_id', $binId);
        }
        if ($initiatedById) {
            $query->where('initiated_by', $initiatedById);
        }
        if ($approvedById) {
            $query->where('approved_by', $approvedById);
        }

        $inventoryutilitylogs = $query->get();
        $inventoryupdateitems = $query_2->get();
        $inventoryupdateitemprices = $query_3->get();

        $initiatedby = $inventoryutilitylogs->pluck('initiatedby')->unique('id');
        $approvedby = $inventoryutilitylogs->pluck('approvedby')->unique('id');
        $locations = $inventoryutilitylogs->pluck('location')->unique('id');
        $locations_2 = $inventoryupdateitems->pluck('location')->unique('id');
        $locations_3 = $inventoryupdateitemprices->pluck('location')->unique('id');
        $bins = $inventoryutilitylogs->pluck('bin')->unique('id');

        $initiatedby_2 = $inventoryupdateitems->pluck('initiatedby')->unique('id');
        $approvedby_2 = $inventoryupdateitems->pluck('approvedby')->unique('id');

        $initiatedby_3 = $inventoryupdateitemprices->pluck('initiatedby')->unique('id');
        $approvedby_3 = $inventoryupdateitemprices->pluck('approvedby')->unique('id');

        $statuses = $inventoryupdateitems->pluck('item.approval_status')->unique();

        $inventoryitemcounts = DB::table('update_new_item_inventory_utility_logs')
            ->select('wa_inventory_item_id', DB::raw('count(*) as count'))
            ->groupBy('wa_inventory_item_id')
            ->having('count', '>', 1)
            ->pluck('count', 'wa_inventory_item_id');

        foreach ($inventoryupdateitems as $log) {
            $log->duplicate = $inventoryitemcounts->has($log->wa_inventory_item_id);
        }

        if ($request->ajax()) {
            return response()->json([
                'inventoryutilitylogs' => $inventoryutilitylogs
            ]);
        }

        return view('admin.utility.inventory.inventory-utility-logs', compact(
            'title',
            'model',
            'inventoryutilitylogs',
            'inventoryupdateitemprices',
            'inventoryupdateitems',
            'initiatedby',
            'approvedby',
            'initiatedby_2',
            'approvedby_2',
            'initiatedby_3',
            'approvedby_3',
            'locations',
            'locations_2',
            'locations_3',
            'bins',
            'statuses',
            'query_4'
        ));
    }
}
