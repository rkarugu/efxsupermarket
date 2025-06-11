<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Model\WaStockMove;
use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Model\WaStockBreaking;
use App\Model\WaStockBreakingItem;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\PosStockBreakRequest;
use Illuminate\Support\Facades\Auth;
use App\Model\WaInventoryLocationUom;
use App\Model\WaInventoryAssignedItems;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\DisplayBinUserItemAllocation;
use App\Model\WaLocationAndStore;

class MobileInventoryManagementController extends Controller
{
    public function getUserInventoryList(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'token' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     $error = $this->validationHandle($validator->messages());
        //     return response()->json(['status' => false, 'message' => $error]);
        // }
        $user= JWTAuth::toUser($request->token);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Token mismatch']);
        }
        $items  = DB::table('display_bin_user_item_allocations')
            ->select(
                'wa_inventory_items.id as item_id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                DB::raw("(SELECT SUM(wa_stock_moves.qauntity)
                FROM wa_stock_moves
                WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code
                AND wa_stock_moves.wa_location_and_store_id = display_bin_user_item_allocations.wa_location_and_store_id
                ) AS qoh"),
                DB::raw("(SELECT(wa_unit_of_measures.title)
                FROM wa_unit_of_measures 
                LEFT JOIN wa_inventory_location_uom ON wa_unit_of_measures.id = wa_inventory_location_uom.uom_id
                WHERE wa_inventory_location_uom.inventory_id = wa_inventory_items.id
                AND wa_inventory_location_uom.location_id = '$user->wa_location_and_store_id'
                ) AS bin"),
                'mother.id as mother_id',
                'mother.stock_id_code as mother_code',
                'mother.title as mother_title',
                DB::raw("(SELECT SUM(wa_stock_moves.qauntity)
                FROM wa_stock_moves
                WHERE wa_stock_moves.stock_id_code = mother.stock_id_code
                AND wa_stock_moves.wa_location_and_store_id = display_bin_user_item_allocations.wa_location_and_store_id
                ) AS mother_qoh"),
                DB::raw("(SELECT(wa_unit_of_measures.title)
                    FROM wa_unit_of_measures 
                    LEFT JOIN wa_inventory_location_uom ON wa_unit_of_measures.id = wa_inventory_location_uom.uom_id
                    WHERE wa_inventory_location_uom.inventory_id = mother.id
                    AND wa_inventory_location_uom.location_id = '$user->wa_location_and_store_id'
                ) AS mother_bin"),
                'wa_inventory_assigned_items.conversion_factor',
                DB::raw("(SELECT (pos_stock_break_requests.requested_quantity * wa_inventory_assigned_items.conversion_factor) 
                    FROM pos_stock_break_requests
                    WHERE pos_stock_break_requests.wa_location_and_store_id = '$user->wa_location_and_store_id'
                    AND pos_stock_break_requests.status = 'pending'
                    AND pos_stock_break_requests.item_id = wa_inventory_items.id
                ) AS pending_split_qty"),
                DB::raw("(SELECT (pos_stock_break_requests.updated_at) 
                    FROM pos_stock_break_requests
                    WHERE pos_stock_break_requests.wa_location_and_store_id = '$user->wa_location_and_store_id'
                    AND pos_stock_break_requests.status != 'rejected'
                    AND pos_stock_break_requests.item_id = wa_inventory_items.id
                    ORDER BY pos_stock_break_requests.updated_at DESC
                    LIMIT 1
                ) AS last_split_date"),
            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'display_bin_user_item_allocations.wa_inventory_item_id')
            ->leftJoin('users', 'users.id', 'display_bin_user_item_allocations.user_id')
            ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'display_bin_user_item_allocations.wa_inventory_item_id')
            ->leftJoin('wa_inventory_items as mother', 'mother.id', 'wa_inventory_assigned_items.wa_inventory_item_id')
            ->where('display_bin_user_item_allocations.user_id', $user->id)
            ->whereColumn('display_bin_user_item_allocations.wa_location_and_store_id', 'users.wa_location_and_store_id');
            if($request->search){    
                $items = $items->where('wa_inventory_items.stock_id_codewa_inventory_items.stock_id_code', 'like', '%'.$request->search.'%') 
                        ->orWhere('wa_inventory_items.title', 'like', '%'.$request->search.'%');           
            }
            $items = $items->where('wa_inventory_items.status', 1)
            ->distinct('wa_inventory_items.id')
            ->orderBy('item_id')
            ->get();     

        if ($items->count() == 0) {
            return response()->json(['status' => false, 'message' => 'You have no assigned items'], 400);
        }

        return response()->json(['status' => true, 'items' => $items, 'show_quantity' => true]);
    }
    public function requestSplit(Request $request)
    {
        try{
            $itemIds = $request->input('item_id');
            $itemQuantities = $request->input('item_quantity');
            if (!(count($itemIds) === count($itemQuantities))) {
                return response()->json(['status' => false, 'message' => 'The item_id, item_quantity arrays must have the same length.']);
            }
            $validator = Validator::make($request->all(), [
                // 'token' => 'required',
                'item_id' => 'array|required',
                'item_quantity' => 'array|required',
                'item_id.*' => 'required|exists:wa_inventory_items,id',
                'item_quantity.*' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error], 400);
            }
            $user= JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 401);
            }
            DB::beginTransaction();

            foreach ($request->item_id as $index => $itemId) {


                $inventoryItem = WaInventoryItem::find($itemId);
                
                if(!$inventoryItem) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'item with id '.$itemId.' not found'], 404);
                }
                $childItemBin = WaInventoryLocationUom::where('location_id', $user->wa_location_and_store_id)->where('inventory_id', $itemId)->first()->uom_id;
                $allocation = DisplayBinUserItemAllocation::where('user_id', $user->id)
                    ->where('wa_inventory_item_id', $itemId)
                    ->first();
                if(!$allocation) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => $inventoryItem->title.' has no user allocation'], 404);
                }
                $has_mother  =  WaInventoryAssignedItems::where('destination_item_id', $itemId)->first();
                if(!$has_mother) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => $inventoryItem->title.' has no mother'], 404);
                }
                $existingRequest = PosStockBreakRequest::where('status', 'pending')
                    ->where('item_id', $itemId)
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)
                    ->first();
                if($existingRequest) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'You have a pending request for item'. $inventoryItem->title], 204);
                }
                  //check unreceived dispatches
                  $unreceivedItemDispatches = DB::table('stock_break_dispatch_items')
                  ->leftJoin('stock_break_dispatches', 'stock_break_dispatch_items.dispatch_id',  'stock_break_dispatches.id')
                  ->where('stock_break_dispatch_items.child_item_id', $itemId)
                  ->where('stock_break_dispatches.child_bin_id', $childItemBin)
                  ->where('stock_break_dispatches.received', 0)
                  ->first();
                if($unreceivedItemDispatches) {
                    DB::rollBack();
                    return response()->json(['status' => true, 'message' => 'You have a pending unreceived request for item'. $inventoryItem->title], 204);
                }
                $motherItem = WaInventoryItem::find($has_mother->wa_inventory_item_id);
                $motherQuantity = WaStockMove::where('stock_id_code', $motherItem->stock_id_code)
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity');
                if($request->item_quantity[$index] > $motherQuantity){
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => $inventoryItem->title.' Mother Item Has No Enough Quantity'], 400);
                }
                $stockBreakRequest = new PosStockBreakRequest();
                $stockBreakRequest->item_id = $itemId;
                $stockBreakRequest->child_item_bin = $childItemBin;
                $stockBreakRequest->requested_quantity = $request->item_quantity[$index];
                $stockBreakRequest->mother_item_id = $motherItem->id;
                $stockBreakRequest->mother_item_bin = WaInventoryLocationUom::where('location_id', $user->wa_location_and_store_id)->where('inventory_id', $motherItem->id)->first()->uom_id;
                $stockBreakRequest->requested_by = $user->id;
                $stockBreakRequest->wa_location_and_store_id = $user->wa_location_and_store_id;
                $stockBreakRequest->save();
        }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Stock Split Request Sent Successfully. Visit the stores to collect your items.'], 200);
        }catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['status' => false, 'message' => $e->getMessage()], 500);
        }

    }
    public function splitRequestsIndex(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'display-split-requests';
        $title = 'display-split-requests';
        $model = 'display-split-requests';
        $basePath = 'display-split-requests';
        $stores = WaLocationAndStore::all();
        $user = Auth::user();
        if( $user->role_id == 152){
            $stores = WaLocationAndStore::where('id', $user->wa_location_and_store_id)->get();
        }

        if (!$request->user()?->uom?->isDisplay() && $permission != 'superadmin') {
            Session::flash('warning', 'Page can only be accessed by child bin users.');
            return redirect()->back();
        }
        $store = null;
        if ($permission != 'superadmin') {
            $store  =Auth::user()->wa_location_and_store_id;
        }


        $requests = PosStockBreakRequest::with(['getChild', 'getChildBinDetail', 'getMother','getMotherBinDetail', 'getTransactingStore', 'getInitiatingUser'])
            ->where('status', 'pending');
        if($request->store){
            $requests = $requests->where('wa_location_and_store_id', $request->store);
        }else{
            $requests = $requests->where('wa_location_and_store_id', $user->wa_location_and_store_id);
        }
        
        $requests =  $requests->get()->map(function ($record){
                $record->mother_qoh = WaStockMove::where('wa_inventory_item_id', $record->mother_item_id)
                    ->where('wa_location_and_store_id', $record->wa_location_and_store_id)
                    ->sum('qauntity');
                return $record; 
            });

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('stock-breaking.split-requests'), 'Listing' => ''];
            return view('admin.split_requests.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission','requests', 'stores'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function approveSplitRequests(Request $request)
    {
        $user = Auth::user();
        $selectedRequests = $request->input('selected_requests');
        if ($selectedRequests) {
            DB::beginTransaction();
            $stock_break_number = getCodeWithNumberSeries('STOCKBREAKING');

            $firstRequest = PosStockBreakRequest::find($selectedRequests[0]);

            $head = new WaStockBreaking();
            $head->user_id = $user->id;
            $head->date = Carbon::now()->toDateString();
            $head->time = Carbon::now()->toTimeString();
            $head->pos_stock_break_request_id = $firstRequest;
            $head->breaking_code = $stock_break_number;
            $head->status = 'PROCESSED';
            $head->save();
            foreach ($selectedRequests as $request_id) {
                $splitRequest = PosStockBreakRequest::find($request_id);
                $motherItem = WaInventoryItem::find($splitRequest->mother_item_id);
                $motherItemQuantity = WaStockMove::where('stock_id_code', $motherItem->stock_id_code)
                    ->where('wa_location_and_store_id', $splitRequest->wa_location_and_store_id)->sum('qauntity');
                if($splitRequest->requested_quantity > $motherItemQuantity)
                {
                    continue;
                }
                $breakRelation = WaInventoryAssignedItems::where('wa_inventory_item_id', $splitRequest->mother_item_id)
                    ->where('destination_item_id', $splitRequest->item_id)
                    ->first();
                $line = new WaStockBreakingItem();
                $line->wa_stock_breaking_id = $head->id;
                $line->source_item_id = $motherItem->id;
                $line->source_item_bal_stock = $motherItemQuantity;
                $line->source_qty = $splitRequest->requested_quantity;
                $line->destination_item_id = $splitRequest->item_id;
                $line->conversion_factor = $breakRelation->conversion_factor;
                $line->destination_qty =  $breakRelation->conversion_factor * $splitRequest->requested_quantity;
                $line->save();

                $parentStockMove = new WaStockMove();
                $parentStockMove->user_id = $user->id;
                $parentStockMove->restaurant_id = $user->restaurant_id;
                $parentStockMove->wa_location_and_store_id = $splitRequest->wa_location_and_store_id;
                $parentStockMove->wa_inventory_item_id = $motherItem->id;
                $parentStockMove->standard_cost = $motherItem->standard_cost;
                $parentStockMove->qauntity = $splitRequest->requested_quantity * -1;
                $parentStockMove->new_qoh = $motherItemQuantity - $splitRequest->requested_quantity;
                $parentStockMove->stock_id_code = $motherItem->stock_id_code;
                $parentStockMove->price = $motherItem->selling_price * $splitRequest->requested_quantity;
                $parentStockMove->document_no = $stock_break_number;
                $parentStockMove->refrence = "$stock_break_number - DISPLAY BIN REQUEST";
                $parentStockMove->total_cost = $motherItem->selling_price * $splitRequest->requested_quantity;
                $parentStockMove->selling_price = $motherItem->selling_price;
                $parentStockMove->save();
                $childItem = WaInventoryItem::find($splitRequest->item_id);
                $childItemQuantity = WaStockMove::where('stock_id_code', $childItem->stock_id_code)
                    ->where('wa_location_and_store_id', $splitRequest->wa_location_and_store_id)
                    ->sum('qauntity');
                $destinationQty = $splitRequest->requested_quantity * $breakRelation->conversion_factor;
                

                $childStockMove = new WaStockMove();
                $childStockMove->user_id = $user->id;
                $childStockMove->restaurant_id = $user->restaurant_id;
                $childStockMove->wa_location_and_store_id = $splitRequest->wa_location_and_store_id;
                $childStockMove->wa_inventory_item_id = $splitRequest->item_id;
                $childStockMove->standard_cost = $childItem->standard_cost;
                $childStockMove->qauntity = $destinationQty;
                $childStockMove->new_qoh = $childItemQuantity + $destinationQty;
                $childStockMove->stock_id_code = $childItem->stock_id_code;
                $childStockMove->price = $childItem->selling_price * $destinationQty;
                $childStockMove->total_cost = $childItem->selling_price * $destinationQty;
                $childStockMove->selling_price = $childItem->selling_price;
                $childStockMove->document_no = $stock_break_number;
                $childStockMove->refrence = "$stock_break_number - DISPLAY BIN REQUEST";
                $childStockMove->save();

                $splitRequest->status = 'approved';
                $splitRequest->approved_by = $user->id;
                $splitRequest->approved_at = Carbon::now()->toDateTimeString();
                $splitRequest->save();

            }
            updateUniqueNumberSeries('STOCKBREAKING', $stock_break_number);
            DB::commit();

        }
        return redirect()->back()->with('success', 'Breaks Successful. Items with no enough mother quantities have been skipped.');
    }
    public function rejectSplitRequests(Request $request)
    {
        $user = Auth::user();
        $selectedRequests = $request->input('selected_requests');
        if ($selectedRequests) {
            DB::beginTransaction();
            foreach ($selectedRequests as $request_id) {
                $splitRequest = PosStockBreakRequest::find($request_id);
                $splitRequest->status = 'rejected';
                $splitRequest->approved_by = $user->id;
                $splitRequest->approved_at = Carbon::now()->toDateTimeString();
                $splitRequest->save();
            }
            DB::commit();
        }
        return redirect()->back()->with('success', 'Break Requests Rejected successfully.');
    }
    public function reportMissingItems(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'token' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     $error = $this->validationHandle($validator->messages());
        //     return response()->json(['status' => false, 'message' => $error]);
        // }
        $user= JWTAuth::toUser($request->token);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Token mismatch']);
        }        

    }

    public function requestSplitsWeb(Request $request)
    {
        try {
            $data = $request->validate([
                'item_name.*' => 'required|exists:wa_inventory_items,id',
                'quantity.*' => 'required|integer|min:1',
            ]);
            $user = Auth::user();
            
            DB::beginTransaction();

            foreach ($data['item_name'] as $index => $itemId) {

                $inventoryItem = WaInventoryItem::find($itemId);
                if(!$inventoryItem) {
                    DB::rollBack();
                    return response()->json(['error' => true, 'message' => 'item with id '.$itemId.' not found']);
                }
                $childItemBin = WaInventoryLocationUom::where('location_id', $user->wa_location_and_store_id)->where('inventory_id', $itemId)->first()->uom_id;
                $allocation = DisplayBinUserItemAllocation::where('user_id', $user->id)
                    ->where('wa_inventory_item_id', $itemId)
                    ->first();
                if(!$allocation) {
                    DB::rollBack();
                    return response()->json(['error' => true, 'message' => $inventoryItem->title.' has no user allocation'], 404);
                }
                $has_mother  =  WaInventoryAssignedItems::where('destination_item_id', $itemId)->first();
                if(!$has_mother) {
                    DB::rollBack();
                    return response()->json(['error' => true, 'message' => $inventoryItem->title.' has no mother'], 404);
                }
                $existingRequest = PosStockBreakRequest::where('status', 'pending')
                    ->where('item_id', $itemId)
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)
                    ->first();
                if($existingRequest) {
                    DB::rollBack();
                    return response()->json(['error' => true, 'message' => 'You have a pending request for item'. $inventoryItem->title], 204);
                }
                //check unreceived dispatches
                $unreceivedItemDispatches = DB::table('stock_break_dispatch_items')
                    ->leftJoin('stock_break_dispatches', 'stock_break_dispatch_items.dispatch_id',  'stock_break_dispatches.id')
                    ->where('stock_break_dispatch_items.child_item_id', $itemId)
                    ->where('stock_break_dispatches.child_bin_id', $childItemBin)
                    ->where('stock_break_dispatches.received', 0)
                    ->first();
                if($unreceivedItemDispatches) {
                    DB::rollBack();
                    return response()->json(['error' => true, 'message' => 'You have a pending unreceived request for item'. $inventoryItem->title], 204);
                }
                $motherItem = WaInventoryItem::find($has_mother->wa_inventory_item_id);
                $stockBreakRequest = new PosStockBreakRequest();
                $stockBreakRequest->item_id = $itemId;
                $stockBreakRequest->child_item_bin = $childItemBin;
                $stockBreakRequest->requested_quantity = $data['quantity'][$index];
                $stockBreakRequest->mother_item_id = $motherItem->id;
                $stockBreakRequest->mother_item_bin = WaInventoryLocationUom::where('location_id', $user->wa_location_and_store_id)->where('inventory_id', $motherItem->id)->first()->uom_id;
                $stockBreakRequest->requested_by = $user->id;
                $stockBreakRequest->wa_location_and_store_id = $user->wa_location_and_store_id;
                $stockBreakRequest->save();
        }
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Split request Submitted Successfully!']);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getMessage()]);

        }
      
    }
}
