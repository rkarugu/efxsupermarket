<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaInventoryItemSupplierData;
use App\Model\WaInventoryPriceHistory;
use App\Models\ApprovePriceListCost;
use App\Models\PriceChangeHistoryLogSupplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApprovePriceListChangeController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'price-list-cost-change';
        $this->pmodel = 'price-list-cost-change';
        $this->title = 'Price List Cost Change';
        $this->pmodule = 'utility';
    }

    public function index()
    {

        if (!can('view', $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $data = ApprovePriceListCost::where('status', 'Pending')->with('user', 'item', 'supplier', 'trade')->get();

        return view('admin.utility.price_list_cost_change', compact('title', 'model', 'pmodule', 'permission', 'data'));
    }

    public function confirmPriceChange(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids)) {
            return response()->json([
                'result' => -1,
                'message' => 'Invalid IDs provided'
            ]);
        }

        try {
            foreach ($ids as $id) {
                $priceListCost = ApprovePriceListCost::find($id);

                $inventory_item = WaInventoryItemSupplier::where('wa_inventory_item_id', $priceListCost->inventory_item_id)
                    ->where('wa_supplier_id', $priceListCost->supplier_id)->first();

                if ($priceListCost) {
                    $priceListCost->approved_by = auth()->user()->id;
                    $priceListCost->status = 'Confirmed';
                    $priceListCost->save();

                    $item = WaInventoryItem::where('id', $inventory_item->wa_inventory_item_id)->first();
                    $price_history = WaInventoryPriceHistory::where('wa_inventory_item_id', $inventory_item->wa_inventory_item_id)->first();

                    $history = new WaInventoryPriceHistory();
                    $history->wa_inventory_item_id = $item->id;
                    $history->old_price_list_cost = $item->price_list_cost;
                    $history->price_list_cost = $priceListCost->price_list_cost ?? $item->price_list_cost;
                    $history->old_standard_cost = $price_history->standard_cost;
                    $history->standard_cost = $item->standard_cost;
                    $history->old_selling_price = $price_history->selling_price;
                    $history->selling_price = $item->selling_price?? $price_history->selling_price;
                    $history->initiated_by = getLoggeduserProfile()->id;
                    $history->approved_by = getLoggeduserProfile()->id;
                    $history->status = 'Approved';
                    $history->created_at = date('Y-m-d H:i:s');
                    $history->updated_at = date('Y-m-d H:i:s');
                    $history->block_this = False;
                    $history->save();

                    $purchaseData = WaInventoryItemSupplierData::latest()->where('wa_inventory_item_id', $item->id)->first();
                    if ($purchaseData) {
                        $purchaseData->price_list_cost = $request->standard_cost;
                        $purchaseData->price_list_cost_effective_from = Carbon::now();
                        $purchaseData->save();
                    }

                    $item->price_list_cost = $priceListCost->price_list_cost;
                    $item->save();

                    $childItems  = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventory_item->wa_inventory_item_id)->get();
                    if ($childItems) {
                        foreach ($childItems as $child) {
                            $childItem = WaInventoryItem::find($child->destination_item_id);
                            $history = new WaInventoryPriceHistory();
                            $history->wa_inventory_item_id = $child->id;
                            $history->old_price_list_cost = $item->price_list_cost;
                            $history->price_list_cost = $priceListCost->price_list_cost ?? $item->price_list_cost;
                            $history->old_standard_cost = $child->standard_cost;
                            $history->standard_cost = (double)$price_history->standard_cost / (double)$child->conversion_factor;;
                            $history->old_selling_price = $child->selling_price;
                            $history->selling_price = $child->selling_price;
                            $history->initiated_by = getLoggeduserProfile()->id;
                            $history->approved_by = getLoggeduserProfile()->id;
                            $history->status = 'Approved';
                            $history->created_at = date('Y-m-d H:i:s');
                            $history->updated_at = date('Y-m-d H:i:s');
                            $history->block_this = False;
                            $history->save();

                            $childItem->old_price_list_cost = $child->price_list_cost;
                            $childItem->standard_cost = (float)$priceListCost->price_list_cost / (float)$child->conversion_factor;
                            $childItem->save();

                            $price_change_history_log = new PriceChangeHistoryLogSupplier();
                            $price_change_history_log->wa_supplier_id = $priceListCost->supplier_id;
                            $price_change_history_log->wa_inventory_item_id = $child->id;
                            $price_change_history_log->status = 'Approved';
                            $price_change_history_log->save();
                        }
                    }

                    $price_change_history_log = new PriceChangeHistoryLogSupplier();
                    $price_change_history_log->wa_supplier_id = $priceListCost->supplier_id;
                    $price_change_history_log->wa_inventory_item_id = $inventory_item->wa_inventory_item_id;
                    $price_change_history_log->status = 'Approved';
                    $price_change_history_log->save();
                }
            }

            return response()->json([
                'result' => 1,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => -1,
                'message' => $e->getMessage()
            ]);
        }
    }
}
