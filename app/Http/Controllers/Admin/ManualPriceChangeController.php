<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryAssignedItems;
use Illuminate\Support\Facades\DB;
use App\Model\WaInventoryPriceHistory;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplierData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;



class ManualPriceChangeController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
     
        $this->model = 'maintain-items-manual-cost-change';
        $this->title = 'Maintain items';
        $this->pmodule = 'maintain-items';
    }
    public function standardCost(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Standard Cost';
        $model = $this->model;
        if (isset($permission[$pmodule . '___manage-standard-cost']) || $permission == 'superadmin') {          
            $lists = DB::table('wa_inventory_items')->get();
            $breadcum = [$title => route('maintain-items.manual-cost-change'), 'Listing' => ''];
            return view('admin.maintaininvetoryitems.manual_cost_change.standardCost', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function editStandardCost($slug)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___manage-standard-cost']) || $permission == 'superadmin') {
                $row = WaInventoryItem::whereSlug($slug)->first();
                if ($row) {
                    $this->title = 'Standard Cost';
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route('maintain-items.manual-cost-change'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.maintaininvetoryitems.manual_cost_change.edit', compact('title', 'model', 'breadcum', 'row'));
                } else {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function updateStandardCost(Request $request, $slug)
    {
        try {
            $row = WaInventoryItem::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|unique:wa_inventory_items,stock_id_code,' . $row->id,
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {      
                  //update price history
                  $history = new WaInventoryPriceHistory();
                  $history->wa_inventory_item_id = $row->id;
                  $history->old_standard_cost = $row->standard_cost ? (float)$row->standard_cost : (float)$request->standard_cost;
                  $history->standard_cost = $request->standard_cost;
                  $history->old_selling_price = $row->selling_price;
                  $history->selling_price = $request->selling_price ?? $row->selling_price;
                  $history->initiated_by = getLoggeduserProfile()->id;
                  $history->approved_by = getLoggeduserProfile()->id;
                  $history->status = 'Approved';
                  $history->created_at = date('Y-m-d H:i:s');
                  $history->updated_at = date('Y-m-d H:i:s');
                  $history->block_this = False;  
                  $history->save();      

                //update  inventory item
                $row->prev_standard_cost = $row->standard_cost ??  $request->standard_cost;
                $row->standard_cost = $request->standard_cost;
                $row->selling_price = $request->selling_price?? $row->selling_price;
                $row->cost_update_time = date('Y-m-d H:i:s');
                $row->save();

              


                //update purchase data
                $purchaseData = WaInventoryItemSupplierData::latest()->where('wa_inventory_item_id', $row->id)->first();
                if($purchaseData){
                $purchaseData->price = $request->standard_cost;
                $purchaseData->price_effective_from = Carbon::now();
                $purchaseData->save();
                }
                //update  child items cost price
                $childItems  = WaInventoryAssignedItems::where('wa_inventory_item_id', $row->id)->get();
                if($childItems){
                    foreach ($childItems as $child) {
                       $childItem = WaInventoryItem::find($child->destination_item_id);

                        $childHistory = new WaInventoryPriceHistory();
                        $childHistory->wa_inventory_item_id = $childItem->id;
                        $childHistory->old_standard_cost = $childItem->standard_cost ;
                        $childHistory->standard_cost = (double)$request->standard_cost / (double)$child->conversion_factor;;
                        $childHistory->old_selling_price = $childItem->selling_price;
                        $childHistory->selling_price = $childItem->selling_price;
                        $childHistory->initiated_by = getLoggeduserProfile()->id;
                        $childHistory->approved_by = getLoggeduserProfile()->id;
                        $childHistory->status = 'Approved';
                        $childHistory->created_at = date('Y-m-d H:i:s');
                        $childHistory->updated_at = date('Y-m-d H:i:s');
                        $childHistory->block_this = False;  
                        $childHistory->save();   

                       $childItem->prev_standard_cost = $childItem->standard_cost;
                       $childItem->standard_cost = (double)$request->standard_cost / (double)$child->conversion_factor;
                       $childItem->save(); 
                    }

                }

              
                Session::flash('success', 'Cost updated successfully');
                return redirect()->route('maintain-items.manual-cost-change');
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
}
