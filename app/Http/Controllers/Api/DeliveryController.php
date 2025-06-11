<?php

namespace App\Http\Controllers\Api;

use App\Model\ItemReturnReason;
use App\Model\Route;
use App\Model\SaleOrderReturns;
use App\Model\WaAccountingPeriod;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaNumerSeriesCode;
use App\Model\WaRouteCustomer;
use App\Model\WaShift;
use App\Model\WaStockMove;
use App\Services\MappingService;
use App\User;
use App\WaInventoryLocationTransferItemReturn;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Model\BeerKegCategory;
use App\Model\BeerAndKegCategoryRelation;
use App\Model\BeerItemsAndCategoryRelation;
use App\Model\BeerDeliveryItem;
use App\Model\DeliveryOrder;
use App\Model\DeliveryOrderItem;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationItemReturn;
use App\Models\ReturnReason;

class DeliveryController extends Controller
{

    private $uploadsfolder;

    public function __construct()
    {

        $this->uploadsfolder = asset('uploads/');
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }


    public function receiveItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'array',
            'item_id.*' => 'required|exists:wa_internal_requisition_items,id',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }
        $user = JWTAuth::toUser($request->token);
        $route = Route::where('id', @$user->route)->first()->route_name;

        $updateOrders = WaInternalRequisition::whereIn('id', $request->item_id)->update(['items_received' => true]);

        $orders = WaInternalRequisition::whereIn('id', $request->item_id)->pluck('id');

        WaInternalRequisitionItem::whereIn('wa_internal_requisition_id', $orders)->update(['driver_item_received' => true]);


        return response()->json(['status' => true, 'message' => 'Items received successfully', 'route' => $route]);
    }


    public function itemReturnReasons(Request $request)
    {
        // $reasons = ItemReturnReason::all();
        $reasons = ReturnReason::all();
        foreach ($reasons as $reason) {
            $reason->name = $reason->reason;
        }
        return response()->json(['reasons' => $reasons]);
    }


    // public function returnItems(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'item_id' => 'required',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         "reason_id" => "required",
    //         "comment" => "required",
    //         "quantity" => "required|min:1"
    //     ]);

    //     if ($validator->fails()) {
    //         $error = $this->validationHandle($validator->messages());
    //         return response()->json(['status' => false, 'message' => $error], 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $requisitionItem = WaInternalRequisitionItem::find($request->item_id);
    //         if (!$requisitionItem) {
    //             return response()->json(['status' => false, 'message' => 'Invalid item submitted for return.'], 422);
    //         }

    //         $transferItem = WaInventoryLocationTransferItem::where('wa_internal_requisition_item_id', $requisitionItem->id)->first();
    //         if (!$transferItem) {
    //             return response()->json(['status' => false, 'message' => 'Invalid item submitted for return.'], 422);
    //         }

    //         $returnQty = (float)$request->quantity;
    //         if ($returnQty < 1) {
    //             return $this->jsonify(['message' => "Return quantity must be more than 1."], 422);
    //         }

    //         $existingReturnQty = WaInventoryLocationTransferItemReturn::latest()->where('wa_inventory_location_transfer_item_id', $transferItem->id)->sum('return_quantity');
    //         $itemExceedsAllowedQty = $returnQty > ((float)$transferItem->quantity - ($existingReturnQty ?? 0));
    //         if ($itemExceedsAllowedQty) {
    //             return $this->jsonify(['message' => "Entered quantity exceeds allowed return quantity."], 422);
    //         }

    //         $user = \Tymon\JWTAuth\Facades\JWTAuth::toUser($request->token);
    //         $returnNumber = WaInventoryLocationTransferItemReturn::latest()->where('wa_inventory_location_transfer_id', $transferItem->wa_inventory_location_transfer_id)
    //             ->first()?->return_number;
    //         $updateReturnSeries = false;
    //         if (!$returnNumber) {
    //             $returnNumber = getCodeWithNumberSeries('RETURN');
    //             $updateReturnSeries = true;
    //         }

    //         $return = WaInventoryLocationTransferItemReturn::create([
    //             'return_number' => $returnNumber,
    //             'wa_inventory_location_transfer_item_id' => $transferItem->id,
    //             'wa_inventory_location_transfer_id' => $transferItem->wa_inventory_location_transfer_id,
    //             'return_by' => $user->id,
    //             'return_date' => Carbon::now(),
    //             'return_quantity' => $returnQty,
    //         ]);

    //         if ($updateReturnSeries) {
    //             updateUniqueNumberSeries('RETURN', $returnNumber);
    //         }

    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Items returned successfully',]);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return $this->jsonify(['message' => $e->getMessage()], 500);
    //     }
    // }


    public function returnItems(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.item_id' => 'required',
            'items.*.quantity' => 'required|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reason_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        DB::beginTransaction();
        try {
            $user = \Tymon\JWTAuth\Facades\JWTAuth::toUser($request->token);
            //sum of shift returns
            $returnedAmount = 0;
            $returnedDiscount = 0;
            $recalculatedDiscount = 0;
            $transfer = null;
            foreach ($request->items as $item) {
                $requisitionItem = WaInternalRequisitionItem::find($item['item_id']);
                if (!$requisitionItem) {
                    return response()->json(['status' => false, 'message' => 'Invalid item submitted for return.'], 422);
                }

                $transferItem = WaInventoryLocationTransferItem::with('getInventoryItemDetail')->where('wa_internal_requisition_item_id', $requisitionItem->id)->first();

                if (!$transferItem) {
                    return response()->json(['status' => false, 'message' => 'Invalid item submitted for return.'], 422);
                }
                $order = WaInternalRequisition::find($requisitionItem->wa_internal_requisition_id);
                $transfer = WaInventoryLocationTransfer::with('get_requisition')->find($transferItem->wa_inventory_location_transfer_id);

                //check existing returns
                // $existingReturns = WaInventoryLocationTransferItemReturn::latest()
                //     ->where('wa_inventory_location_transfer_item_id', $transferItem->id)
                //     ->where('wa_inventory_location_transfer_id', $transfer->id)
                //     ->where('status', 'received')
                //     ->whereNot('received_quantity', 0)
                //     ->first();
                // if($existingReturns){
                //     $item = WaInventoryItem::find($transferItem->wa_inventory_item_id);
                //     return response()->json(['status' => false, 'message' => 'Returns have already been done on invoice item.'.$item->title], 422);
                // }

                $pendingExistingReturns = WaInventoryLocationTransferItemReturn::latest()
                ->where('wa_inventory_location_transfer_item_id', $transferItem->id)
                ->where('wa_inventory_location_transfer_id', $transfer->id)
                ->first();
                if($pendingExistingReturns){
                    $item = WaInventoryItem::find($transferItem->wa_inventory_item_id);
                    return response()->json(['status' => false, 'message' => 'Returns have already been initiated on invoice item.'.$item->title], 422);
                }
                $customer = WaRouteCustomer::with('route')->where('id',$order->wa_route_customer_id)->first();
                if (!$customer)
                {
                    return response()->json(['status' => false, 'message' => 'Invalid Order  submitted for return. Customer with this order  not found'], 422);
                }
                // $distance = MappingService::getTheaterDistanceBetweenTwoPoints($request->latitude, $request->longitude, $customer->lat, $customer->lng);
                // $prox_dist = $customer->route ->salesman_proximity;
                // if ($distance > $prox_dist )
                // {
                //     return response()->json(['status' => false, 'message' => "You are Outside the allowed Delivery Distance ($distance) from the shop."], 422);
                // }

                $returnQty = (float)$item['quantity'];
                if ($returnQty < 1) {
                    return $this->jsonify(['message' => 'Return quantity must be more than 1.'], 422);
                }
                $returns = DB::table('wa_inventory_location_transfer_item_returns')
                    ->select(DB::raw('sum(wa_inventory_location_transfer_item_returns.return_quantity * wa_inventory_location_transfer_items.selling_price) as total'), )
                    ->join('wa_inventory_location_transfers','wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
                    ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
                    ->where('wa_inventory_location_transfers.shift_id', $transfer->shift_id)
                    ->get();

                if($customer){
                    $getDiffInHours = Carbon::now()->diffInMinutes($transfer->created_at) / 60;
                    if($getDiffInHours > 48){
                        //return done past 48hrs  since invoice was created
                        $status = 3;
                    }elseif(((int)$returns->sum('total') + ((int)($transferItem->selling_price * $returnQty))) > 100000 ){
                        $status = 2;

                    }else{
                        if((int)$returns->sum('total') < 100000){
                            $rtn_amount =(int)($transferItem->selling_price * $returnQty );
                            $remaining_bal = 10000 - (int)$returns->sum('total');
                            //check limits
                            if((int)$remaining_bal > 0 ){
                                if((int)$rtn_amount > (int)$remaining_bal){
                                    $status = 0;
                                }
                                if((int)$rtn_amount < (int)$remaining_bal ||
                                    (int)$rtn_amount == (int)$remaining_bal){
                                    //approver 1
                                    $status = 1;
                                }

                            }
                            else{
                                $status = 0;
                            }
                        }else{
                            //apprrover 2
                            $status = 2;
                        }
                    }
                }

                $existingReturnQty = WaInventoryLocationTransferItemReturn::latest()->where('wa_inventory_location_transfer_item_id', $transferItem->id)->sum('return_quantity');
                $itemExceedsAllowedQty = $returnQty > ((float)$transferItem->quantity - ($existingReturnQty ?? 0));
                if ($itemExceedsAllowedQty) {
                    return $this->jsonify(['message' => 'Entered quantity exceeds allowed return quantity.'], 422);
                }

                $user = \Tymon\JWTAuth\Facades\JWTAuth::toUser($request->token);
                $returnNumber = WaInventoryLocationTransferItemReturn::latest()->where('wa_inventory_location_transfer_id', $transferItem->wa_inventory_location_transfer_id)
                    ->first()?->return_number;
                $updateReturnSeries = false;
                if (!$returnNumber) {
                    $returnNumber = getCodeWithNumberSeries('RETURN');
                    $updateReturnSeries = true;
                }
                if($request->reason_id){
                    $returnReason = ReturnReason::find($request->reason_id)->reason;

                }

                $return = WaInventoryLocationTransferItemReturn::create([
                    'return_number' => $returnNumber,
                    'wa_inventory_location_transfer_item_id' => $transferItem->id,
                    'wa_inventory_location_transfer_id' => $transferItem->wa_inventory_location_transfer_id,
                    'return_by' => $user->id,
                    'return_date' => Carbon::now(),
                    'return_quantity' => $returnQty,
                    'return_status' => $status,
                    'return_reason' => $returnReason ?? '',
                ]);

                if ($updateReturnSeries) {
                    updateUniqueNumberSeries('RETURN', $returnNumber);
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Items returned successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }


    public function deliverItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'array',
            'item_id.*' => 'required|exists:wa_internal_requisition_items,id',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }
        $user = JWTAuth::toUser($request->token);
        $route = Route::where('id', @$user->route)->first()->route_name;
        // $item = WaInternalRequisitionItem::where("id", $request->item_id)->first();
        $itemIds = $request->item_id;
        $shiftlist = WaShift::where('status', 'open')->where('salesman_id', $user->id)->first();

        WaInternalRequisitionItem::whereIn('id', $request->item_id)->update(['delivered' => true]);


        return response()->json(['status' => true, 'message' => 'Items received successfully', 'route' => $route]);


        foreach ($itemIds as $key => $value) {
            $order = WaInternalRequisition::where("id", $item->wa_internal_requisition_id)->first();
            $order->wa_delivery_shift_id = $shiftlist->id;
            $order->save();
        }


        return response()->json(['status' => true, 'message' => 'Items Delivered successfully', 'route' => $route]);
    }


    public function orderDelivered(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }
        $user = JWTAuth::toUser($request->token);
        $route = Route::where('id', @$user->route)->first()->route_name;
        // $item = WaInternalRequisitionItem::where("id", $request->item_id)->first();
        // $itemIds = $request->item_id;
        // $shiftlist = WaShift::where('status', 'open')->where('salesman_id', $user->id)->first();

        WaInternalRequisition::where('id', $request->item_id)
            ->update(['is_delivered' => true, 'status' => 'DELIVERED']);

        return response()->json(['status' => true, 'message' => 'Items Delivered successfully', 'route' => $route]);
    }


    public function getDeliverySubMajorGroup()
    {
        $lists = BeerKegCategory::whereLevel(1)->orderBy('display_order', 'asc')->get();
        $menulist = [];
        foreach ($lists as $list) {
            $inner_array = [];
            $inner_array['submajorgroup_id'] = $list->id;
            $inner_array['pic'] = $this->uploadsfolder . '/beerandkeg/' . $list->image;
            $inner_array['pic_thumb'] = $this->uploadsfolder . '/beerandkeg/' . $list->image;
            $inner_array['title'] = strtoupper($list->name);
            $menulist[] = $inner_array;
        }
        $response_array = ['status' => true, 'message' => 'Sub major groups list', 'data' => $menulist];
        return response()->json($response_array);
    }

    public function getDeliveryFamilyGroups(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'submajorgroup_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {
            $lists = BeerKegCategory::whereId($request->submajorgroup_id)->first();
            $menulist = [];
            if ($lists && count($lists->getManyRelativeChilds) > 0) {
                foreach ($lists->getManyRelativeChilds as $list) {
                    $inner_array = [];
                    $inner_array['family_group_id'] = $list->getRelativeCategorysData->id;
                    $inner_array['pic'] = $this->uploadsfolder . '/beerandkeg/' . $list->getRelativeCategorysData->image;
                    $inner_array['pic_thumb'] = $this->uploadsfolder . '/beerandkeg/' . $list->getRelativeCategorysData->image;
                    $inner_array['title'] = strtoupper($list->getRelativeCategorysData->name);
                    $inner_array['is_have_sub_family'] = (int)$list->getRelativeCategorysData->is_have_another_layout;
                    $menulist[] = $inner_array;
                }
            }
            $response_array = ['status' => true, 'message' => 'Family groups list', 'data' => $menulist];
            return response()->json($response_array);
        }
    }

    public function getDeliverySubFamilyGroups(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'family_group_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {
            $lists = BeerKegCategory::whereId($request->family_group_id)->first();
            $menulist = [];
            if ($lists && count($lists->getManyRelativeChilds) > 0) {
                foreach ($lists->getManyRelativeChilds as $list) {
                    $inner_array = [];
                    $inner_array['sub_family_group_id'] = $list->getRelativeCategorysData->id;
                    $inner_array['pic'] = $this->uploadsfolder . '/beerandkeg/' . $list->getRelativeCategorysData->image;
                    $inner_array['pic_thumb'] = $this->uploadsfolder . '/beerandkeg/' . $list->getRelativeCategorysData->image;
                    $inner_array['title'] = strtoupper($list->getRelativeCategorysData->name);
                    $menulist[] = $inner_array;
                }
            }
            $response_array = ['status' => true, 'message' => 'Sub Family groups list', 'data' => $menulist];
            return response()->json($response_array);
        }
    }

    public function getDeliveryAppetizer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'family_or_sub_family_group_id' => 'required',
            // 'restaurant_id' => 'required',   
        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());

            return response()->json(['status' => false, 'message' => $error]);
        } else {

            $beer_keg_category_id = $request->family_or_sub_family_group_id;
            $all_items = BeerItemsAndCategoryRelation::where('beer_keg_category_id', $beer_keg_category_id)->get();
            $response_array = ['status' => true, 'message' => 'list response'];
            $sMCounter = 0;
            $items = [];

            foreach ($all_items as $itemList) {
                if ($itemList->getRelativeitemDetail->is_available_in_stock == '1') {

                    $item_pic_url = $itemList->getRelativeitemDetail->image ? $this->uploadsfolder . '/beerandkeg/' . $itemList->getRelativeitemDetail->image : $this->uploadsfolder . '/item_none.png';
                    $item_pic_url_thumb = $itemList->getRelativeitemDetail->image ? $this->uploadsfolder . '/beerandkeg/thumb/' . $itemList->getRelativeitemDetail->image : $this->uploadsfolder . '/item_none.png';


                    $inner_array = [
                        'title' => ucfirst($itemList->getRelativeitemDetail->name),
                        'appetizer_id' => $itemList->getRelativeitemDetail->id,
                        'pic_url' => $item_pic_url,
                        'pic_thumb_url' => $item_pic_url_thumb,

                        'description' => $itemList->getRelativeitemDetail->description,
                        'price' => (string)$itemList->getRelativeitemDetail->price,

                    ];
                    $items[] = $inner_array;

                    $sMCounter++;
                }
            }
            sort($items);
            $response_array['menu_list'] = $items;
            return response()->json($response_array);
        }
    }

    public function getDeliveryAppetizerdetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'restaurant_id' => 'required',
            'appetizer_id' => 'required',

        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {
            $item = BeerDeliveryItem::whereId($request->appetizer_id)->first();
            $taxation = $this->getTaxationForItem($item);
            $item_pic_url = $item->image ? $this->uploadsfolder . '/beerandkeg/' . $item->image : $this->uploadsfolder . '/item_none.png';
            $item_pic_url_thumb = $item->image ? $this->uploadsfolder . '/beerandkeg/thumb/' . $item->image : $this->uploadsfolder . '/item_none.png';
            $item_arr = [
                'appetizer_id' => $item->id,
                'pic_url' => $item_pic_url,
                'pic_thumb_url' => $item_pic_url_thumb,
                'title' => ucfirst($item->name),
                'description' => $item->description,
                'price' => (string)$item->price,
                'item_charges' => $taxation
            ];


            return response()->json([
                'status' => true,
                'message' => 'Appetizerdetail response',
                'detail' => $item_arr
            ]);
        }
    }


    public function getCheckoutForBeerDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'total_price' => 'required',
            'address' => 'required',

            'checkout_json' => 'required'

        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        } else {
            $json = json_decode($request->checkout_json);

            $new_order = new DeliveryOrder();
            $new_order->user_id = $request->user_id;
            $new_order->final_comment = isset($request->final_comment) ? $request->final_comment : '';
            $new_order->order_final_price = $request->total_price;
            $new_order->address = $request->address;
            $new_order->slug = rand(99, 999) . strtotime(date('Y-m-d h:i:s'));
            if (isset($json->order_charges)) {
                $new_order->order_charges = json_encode($json->order_charges);

            }


            $new_order->save();

            $order_id = $new_order->id;

            if (isset($json->Appetizerdata) && count($json->Appetizerdata) > 0) {

                $this->storeDeliveryItemForOrder($json->Appetizerdata, $order_id);
            }
            return response()->json(['status' => true, 'message' => 'Your order added successfully.', 'delivery_order_id' => $new_order->id]);

        }
    }


    public function storeDeliveryItemForOrder($items_array, $order_id)
    {
        //OrderedItem

        $items_array_for_insert = [];
        foreach ($items_array as $item) {
            $inner_array = [
                'beer_delivery_item_id' => $item->appetizer_id,
                'price' => $item->price,

                'item_title' => $item->title,
                'item_comment' => isset($item->comment) ? $item->comment : '',
                'item_quantity' => $item->quantity,
                'delivery_order_id' => $order_id,

                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
                'item_charges' => isset($item->item_charges) ? json_encode($item->item_charges) : null,

            ];


            $items_array_for_insert[] = $inner_array;
        }
        DeliveryOrderItem::insert($items_array_for_insert);
    }

}