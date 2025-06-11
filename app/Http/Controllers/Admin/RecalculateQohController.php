<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecalculateQohController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'recalculate-qoh';
        $this->title = 'Recalculate QOH';
        $this->pmodule = 'recalculate-qoh';
    }

    public function recalculateQoh(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $items = DB::table('wa_inventory_items')
            ->select('wa_inventory_items.stock_id_code', 'wa_inventory_items.id', 'wa_inventory_items.title', 'wa_inventory_items.description')
            ->get();
        $locations = WaLocationAndStore::get();
        $lists = [];
        return view('admin.utility.recalculate_qoh', compact('title', 'model', 'pmodule', 'permission', 'items', 'locations', 'lists'));
    }

    public function processItemStockMovesData($stockidcode, $locationid, Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $formurl = "recalculate-qoh";
        $location = WaLocationAndStore::query();
        $locations = WaLocationAndStore::get();
        $user = getLoggeduserProfile();
        if ($user->role_id != 1) {
            $location = $location->where('wa_branch_id', $user->restaurant_id);
        }
        $location = $location->get();

        $items = DB::table('wa_inventory_items')
            ->select('wa_inventory_items.stock_id_code', 'wa_inventory_items.id', 'wa_inventory_items.title', 'wa_inventory_items.description')
            ->get();

        $lists = WaStockMove::with(['getRelatedUser', 'getLocationOfStore'])->select([
            '*',
            \DB::RAW('
            (CASE WHEN grn_type_number = 4 THEN (SELECT wa_pos_cash_sales_items.selling_price FROM wa_pos_cash_sales_items where wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_stock_moves.wa_pos_cash_sales_id AND wa_stock_moves.wa_inventory_item_id = wa_pos_cash_sales_items.wa_inventory_item_id LIMIT 1)
            WHEN grn_type_number = 51 THEN (SELECT wa_internal_requisition_items.selling_price FROM wa_internal_requisition_items where wa_internal_requisition_items.wa_internal_requisition_id = wa_stock_moves.wa_internal_requisition_id AND wa_stock_moves.wa_inventory_item_id = wa_internal_requisition_items.wa_inventory_item_id LIMIT 1)
            ELSE selling_price END
            ) as selling_price
            ')
        ])->where(function ($w) use ($locationid) {
            if ($locationid) {
                $w->where('wa_location_and_store_id', $locationid);
            }
        });

        if ($locationid) {
            $lists = $lists->where('stock_id_code', $stockidcode)->orderBy('id', 'asc')->get();
        }

        return [
            'location' => $location,
            'title' => $title,
            'lists' => $lists,
            'model' => $model,
            'pmodule' => $pmodule,
            'permission' => $permission,
            'stockidcode' => $stockidcode,
            'formurl' => $formurl,
            'items' => $items,
            'locations' => $locations
        ];
    }

    public function recalculateNewQoh($stockidcode)
    {
        try {
            $records = DB::table('wa_stock_moves')->where('stock_id_code', $stockidcode)->orderBy('created_at')->select('id', 'qauntity', 'new_qoh')->get();
            $prevQoH = $records->first()->new_qoh;
            foreach ($records as $key => $record) {
                if($key == 0){
                    continue;
                }
                $newQoH = $record->qauntity + $prevQoH;
                $prevQoH = $newQoH;
                WaStockMove::find($record->id)->update(['new_qoh' => $newQoH]);
                $prevQoH = $newQoH;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
