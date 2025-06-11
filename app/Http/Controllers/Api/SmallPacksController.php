<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\Model\WaNumerSeriesCode;
use App\Model\Route;
use App\Model\WaInternalRequisition;
use App\Models\SaleCenterSmallPackItems;
use App\Models\SaleCenterSmallPacks;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Model\WaInventoryLocationUom;
use App\Models\SaleCenterSmallPackDispatch;
use App\Models\SaleCenterSmallPackDispatchItems;
use App\Models\SaleCenterSmallPackDispatchStatus;
use Barryvdh\DomPDF\Facade as PDF;
use App\Interfaces\SmsService;
use App\Model\WaUnitOfMeasure;
use App\Models\RouteRepresentatives;
use App\Models\SmallPackDriverDispatch;
use App\Models\SmallPackDriverDispatchItems;
use App\SalesmanShift;
use App\SalesmanShiftStoreDispatch;
use Carbon\Carbon;

class SmallPacksController extends Controller
{
    public function __construct(protected SmsService $smsService)
    {

    }

    public function get_user_routes(Request $request)
    {
       $user = JWTAuth::toUser($request->token);
        //    DB::table('wa_internal_requisitions')->update(['center_small_pack_id'=>null]);
        //    DB::table('sale_center_small_pack_dispatches')->delete();
        //    DB::table('sale_center_small_pack_dispatch_statuses')->delete();
        //    DB::table('sale_center_small_pack_dispatch_items')->delete();
        //    DB::table('sale_center_small_pack_items')->delete();
        //    DB::table('sale_center_small_packs')->delete();

       try {
        // Get only the order taking days routes
        $today = Carbon::now()->dayOfWeek;

        $routes = Route::with('internalRequisitions')
        ->whereHas('currentRepresentative', function ($q) use($user) {
            $q->where('user_id', $user->id);
        })
        ->whereRaw("FIND_IN_SET(?, order_taking_days)", [$today])
      
        ->get()->map(function($route){
            $order = WaInternalRequisition::where('route_id', $route->id)->whereDate('created_at', Carbon::now()->toDateString())->first();
            if($order){
                $has_order = true;
            }else{
                $has_order = false;
            }
            return [
                "id" => $route->id,
                "route_name" => $route->route_name,
                "centers_count" => count($route->centers),
                'has_order' => $has_order,
            ];
        });
        
        return response()->json(['status' => true, 'message' => 'Routes fetched successfully', 'routes' => $routes]);
       } catch (\Exception $e) {        
        return response()->json(['status' => false, 'message' => $e->getMessage()]);
       }
    }

    public function get_route_centres(Request $request,$id)
    {
        $user = JWTAuth::toUser($request->token);
        try {
            $route = Route::find($id);
            
            $centers = $route->centers->map(function($center) use($user){
                if($center->name){
                    $centerInfo = $this->getCenterItems($center->id,$user);

                    return [
                        "id" => $center->id,
                        "name" => $center->name,
                        "item_count" => count($centerInfo['items']),
                    ];
                }
            })->filter();
            
            return response()->json(['status' => true, 'message' => 'Center fetched successfully', 'centers' => $centers]);
           } catch (\Exception $e) {        
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
           }
    }

    public function initiate_centre_small_packs(Request $request)
    {
        $user = JWTAuth::toUser($request->token);
        
        DB::beginTransaction();
        try {            
            $centers = DeliveryCentres::find($request->centre_id);
            $routeCenters  =  DeliveryCentres::where('route_id', $centers->route_id)->get();
            foreach($routeCenters as $center){
                $center->is_active = true;
                $center->save();
            }
        
            $shift = SalesmanShift::where('route_id',$centers->route_id)->where('status','open')->latest()->first();
            if (!$shift) {
                return response()->json(['status' => false, 'message' => 'You don\'t have open Shift'], 422);
            } 

            $customers = $centers->waRouteCustomers->pluck('id');

            $smallPacks = SaleCenterSmallPacks::create([
                'created_by' => $user->id,
                'center_id' => $request->centre_id,
                'restaurant_id' => $centers->route->restaurant_id ,
                'route_id' => $centers->route->id,
                'shift_id' => $shift->id,
            ]);
            
            $orders = WaInternalRequisition::wherein('wa_route_customer_id',$customers)
                        ->whereDate('created_at', Carbon::today())
                        ->whereNull('center_small_pack_id')
                        ->get();

            $arrOrders =[];

            foreach($orders as $order){
                foreach ($order->getRelatedItem as $item) {
                    // Check for small pack items
                    //use bin
                    $inventoryItemBin = WaInventoryLocationUom::latest()
                    ->where('location_id', $user->wa_location_and_store_id)
                    ->where('inventory_id', $item->wa_inventory_item_id)
                    ->first();
                    $bin = WaUnitOfMeasure::find($inventoryItemBin->uom_id);
                    
                    // if ($item->getInventoryItemDetail->packSize->pack_size=='SMALL PACK') { 
                    if ($bin->is_display == 1) { 

                        $arrOrders[]=[
                            'sale_center_small_pack_id' => $smallPacks->id,
                            'wa_inventory_item_id' => $item->wa_inventory_item_id,
                            'wa_internal_requisition_item_id' =>$item->id,
                            'wa_route_customer_id' => $order->wa_route_customer_id,
                            'requisition_no' => $order->requisition_no,
                            'bin_id' => $inventoryItemBin->uom_id,
                            'quantity' => $item->quantity,
                            'center_id' => $request->centre_id
                        ];
                    }
                }        
                $order->update(['center_small_pack_id'=>$smallPacks->id]);      
            }
            
            if(count($arrOrders) > 0){
                SaleCenterSmallPackItems::insert($arrOrders);

                if ($centers->route->currentRepresentative) {
                    $phone = $centers->route->currentRepresentative->user?->phone_number;
                    $routeName = $centers->route?->route_name;
                    $this->smsService->sendMessage('Loading Sheet For Center: '. $centers->name .'of Route: '. $routeName .'  has been generated.', $phone);
                }

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Successfully marked center as complete.']);
            } elseif(count($orders))
            {
                // This is to update the Internal Requisitions
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Successfully marked center as complete.']);
            } else {
                return response()->json(['status' => false, 'message' => 'You have no orders in this center'], 422);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();        
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_centre_loading_sheet(Request $request,$center)
    {
        $user = JWTAuth::toUser($request->token);
        try{
            $itemsCanTrue = $this->getCenterItems($center,$user);

            return response()->json(['status' => true, 'data'=>$itemsCanTrue]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getCenterItems($center, $user)
    {
        $centerInfo = DeliveryCentres::find($center);
        $customers = $centerInfo->waRouteCustomers->pluck('id');

        $smallPacks = SaleCenterSmallPacks::with('items')
                            ->where('center_id', $center)
                            ->where('dispatched', false)
                            ->whereDoesntHave('dispatchedPacks')
                            ->get();

        $packItems = [];
        $packId = null;
        $canDispatch=false;

        // Process small packs
        foreach ($smallPacks as $packs) {
            foreach ($packs->items as $pack) {
                $this->updatePackItems($packItems, $pack->inventoryItem->stock_id_code, $pack->wa_inventory_item_id, $pack->inventoryItem->title, $pack->quantity, $pack->uom->title);
            }
            $packId = $packs->id;
        }

        if (count($packItems)) {
            $canDispatch=true;
        }

        // Process orders and add items to packItems
        $orders = WaInternalRequisition::whereIn('wa_route_customer_id', $customers)
                    ->whereDate('created_at', Carbon::today())
                    ->whereNull('center_small_pack_id')
                    ->get();

        foreach ($orders as $order) {
            foreach ($order->getRelatedItem as $item) {
                if ($item->getInventoryItemDetail->packSize->pack_size=='SMALL PACK') {
                    $inventoryItemBin = WaInventoryLocationUom::latest()
                        ->where('location_id', $user->wa_location_and_store_id)
                        ->where('inventory_id', $item->wa_inventory_item_id)
                        ->first();

                    $this->updatePackItems(
                        $packItems,
                        $item->getInventoryItemDetail->stock_id_code,
                        $item->wa_inventory_item_id,
                        $item->getInventoryItemDetail->title,
                        $item->quantity,
                        $inventoryItemBin ? $inventoryItemBin->uom->title : null
                    );
                }
            }
        }

        return [
            'id' => $packId,
            'items' => $packItems,
            'can_dispatch' => $canDispatch
        ];
    }

    /**
     * Update the pack items array.
     */
    private function updatePackItems(&$packItems, $stockIdCode, $itemId, $title, $quantity, $bin)
    {
        $existingItemKey = array_search($stockIdCode, array_column($packItems, 'stock_id_code'));

        if ($existingItemKey !== false) {
            // If stock_id_code exists, add the totalQty to the existing item
            $packItems[$existingItemKey]['totalQty'] += $quantity;
        } else {
            // If stock_id_code does not exist, add the new item
            $packItems[] = [
                "id" => $itemId,
                "stock_id_code" => $stockIdCode,
                "title" => $title,
                "totalQty" => $quantity,
                "bin" => $bin,
            ];
        }
    }

    public function create_centre_dispatch_sheet(Request $request)
    {
        $user = JWTAuth::toUser($request->token);

        DB::beginTransaction();
        try {

            // $getCenter =  SaleCenterSmallPackItems::select("center_id","sale_center_small_pack_id")
            //             ->where('sale_center_small_pack_id',$request->sheet_id)
            //             ->first();
            $getCenter = SaleCenterSmallPacks::find($request->sheet_id);

            $centers = DeliveryCentres::find($getCenter->center_id);
            $customers = $centers->waRouteCustomers->pluck('id');
            
            $orders = WaInternalRequisition::wherein('wa_route_customer_id',$customers)
                        ->where('wa_shift_id',$getCenter->shift_id)
                        ->whereDate('created_at', Carbon::today())
                        ->whereNull('center_small_pack_id')
                        ->get();

            $arrOrders =[];

            foreach($orders as $order){
                foreach ($order->getRelatedItem as $item) {
                    // Check for small pack items
                    if ($item->getInventoryItemDetail->packSize->pack_size=='SMALL PACK') {
                    
                        $inventoryItemBin = WaInventoryLocationUom::latest()
                        ->where('location_id', $user->wa_location_and_store_id)
                        ->where('inventory_id', $item->wa_inventory_item_id)
                        ->first();

                        $arrOrders[]=[
                            'sale_center_small_pack_id' => $getCenter->id,
                            'wa_inventory_item_id' => $item->wa_inventory_item_id,
                            'wa_internal_requisition_item_id' =>$item->id,
                            'wa_route_customer_id' => $order->wa_route_customer_id,
                            'requisition_no' => $order->requisition_no,
                            'bin_id' => $inventoryItemBin->uom_id,
                            'quantity' => $item->quantity,
                            'center_id' => $getCenter->centre_id
                        ];
                    }
                }        
                $order->update(['center_small_pack_id'=>$getCenter->id]);      
            }
            
            if(count($arrOrders) > 0){
                SaleCenterSmallPackItems::insert($arrOrders);
            } 

            ///////////////////////////
            $packs = SaleCenterSmallPackItems::select("wa_inventory_item_id","bin_id","quantity","center_id")
                    ->where('sale_center_small_pack_id',$request->sheet_id)
                    ->get();

            $checkIfCreated = SaleCenterSmallPackDispatch::where('sale_center_small_pack_id', $request->sheet_id)->count();
            
            if($checkIfCreated){
                return response()->json(['status' => false, 'message' => 'Dispatch Sheet Already Created'], 422);
            }

            $series_module = WaNumerSeriesCode::where('module', 'SMALL_PACK_DISPATCH')->first();
            $lastNumberUsed = $series_module->last_number_used;
            $newNumber = (int)$lastNumberUsed + 1;
            $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
            $series_module->update(['last_number_used' => $newNumber]);

            $dispatch = SaleCenterSmallPackDispatch::create([
                'sale_center_small_pack_id' => $request->sheet_id,
                'created_by' => $user->id,
                'center_id' => $getCenter->center_id,
                'document_no' => $newCode,
                'shift_id' => $getCenter->shift_id
            ]);      

            $statusBin=[];
            
            foreach ($packs as $pack) {
                SaleCenterSmallPackDispatchItems::create([
                    'dispatch_id' => $dispatch->id,
                    'bin_id' => $pack->bin_id,
                    'wa_inventory_item_id' => $pack->wa_inventory_item_id,
                    'total_quantity' => $pack->quantity,
                    'center_id' => $pack->center_id
                ]);
                if (!in_array($pack->bin_id, $statusBin)) {
                    $statusBin[] = $pack->bin_id; 
                    SaleCenterSmallPackDispatchStatus::create([
                        'dispatch_id' => $dispatch->id,
                        'bin_id' => $pack->bin_id,
                        'center_id' => $pack->center_id
                    ]);
                }
            }            
            DB::commit();
            return response()->json(['status' => true, 'message'=>'Created center dispatch sheet successfully', 'dispatch_sheet_id'=>$dispatch->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_centre_dispatch_sheets(Request $request, $center)
    {
        $user = JWTAuth::toUser($request->token);
        
        try {
            $date = Carbon::createFromFormat('d/m/Y', $request->date_filter)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date provided.',
            ], 422);
        }


        try{
            
            $dispatchSheet = SaleCenterSmallPackDispatch::with('items', 'items.inventoryItem', 'status.uom')
                ->whereHas('saleCenter', function ($q) use($center) {
                    $q->where('center_id', $center);
                })
                ->whereDate('created_at', $date)
                ->get();


            $items = $dispatchSheet->map(function ($dispatch) {
                // Group items by UOM title
                $bins = $dispatch->items->groupBy(function ($b) {
                    return $b->uom->title;
                })->map(function ($groupedItems, $uomTitle) use ($dispatch) {
                    // // Map each grouped item to the desired structure
                    // $binArr = $groupedItems->map(function ($bin) {
                    //     return [
                    //         "id" => $bin->inventoryItem->id,
                    //         "stock_id_code" => $bin->inventoryItem->stock_id_code,
                    //         "title" => $bin->inventoryItem->title,
                    //         "totalQty" => $bin->total_quantity
                    //     ];
                    $groupedByItemId = $groupedItems->groupBy(function ($bin) {
                        return $bin->inventoryItem->id;
                    });
                    $binArr = $groupedByItemId->map(function ($itemsWithSameId) {
                        $firstItem = $itemsWithSameId->first();

                        return [
                            "id" => $firstItem->inventoryItem->id,
                            "stock_id_code" => $firstItem->inventoryItem->stock_id_code,
                            "title" => $firstItem->inventoryItem->title,
                            "totalQty" => $itemsWithSameId->sum('total_quantity')
                        ];
                    });
            
                    // Determine the received status for the UOM
                    $receivedStatus = $dispatch->status->where('uom.title', $uomTitle)->first();
                    return [
                        "id" => $receivedStatus->bin_id,
                        "title" => $uomTitle,
                        "items" => $binArr->toArray(),
                        "has_received_items" => $receivedStatus ? $receivedStatus->received : false
                    ];
                });
            
                // Return dispatch ID and bins
                return [
                    "id" => $dispatch->id,
                    "document_no" => $dispatch->document_no,
                    "bins" => $bins->values()->toArray() // Ensure bins are in an array
                ];
            })->values()->toArray();

        
            return response()->json(['status' => true, 'data'=>$items]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_single_centre_dispatch_sheet(Request $request, $dispatch) 
    {
        $user = JWTAuth::toUser($request->token);

        try {
            $dispatchSheet = SaleCenterSmallPackDispatch::with('items', 'items.inventoryItem', 'status.uom')
                //check if request->bin_id is not null and get  where items have this bin
                ->when($request->bin_id, function ($query) use($request){
                    $bin_id = $request->bin_id;
                    return $query->whereHas('items', function ($q) use ($bin_id) {
                        $q->where('bin_id', $bin_id);
                    });
                })
                ->find($dispatch);

            if ($dispatchSheet) {
                $dispatchInfo = [
                    'id' => $dispatchSheet->id,
                    'document_no' => $dispatchSheet->document_no,
                    'center' => $dispatchSheet->saleCenter->center->name,
                    'sale_center' =>$dispatchSheet->saleCenter->id,
                    'route' =>$dispatchSheet->saleCenter->center->route ? $dispatchSheet->saleCenter->center->route->route_name : null,
                    'print_count' => $dispatchSheet->print_count,
                ];
                $bins = $dispatchSheet->items->groupBy(function ($b) {
                    return $b->uom->title;
                })->map(function ($groupedItems, $uomTitle) use ($dispatchSheet) {
                    $groupedByItemId = $groupedItems->groupBy(function ($bin) {
                        return $bin->inventoryItem->id;
                    });
                    $binArr = $groupedByItemId->map(function ($itemsWithSameId) {
                        $firstItem = $itemsWithSameId->first();

                        return [
                            "id" => $firstItem->inventoryItem->id,
                            "stock_id_code" => $firstItem->inventoryItem->stock_id_code,
                            "title" => $firstItem->inventoryItem->title,
                            "totalQty" => $itemsWithSameId->sum('total_quantity')
                        ];
                    });
            
                    return [
                        "title" => $uomTitle,
                        "items" => $binArr->toArray(),
                    ];
                });
            
                // Return dispatch ID and bins
                $data = $bins->values()->toArray();
    
                $dispatchSheet->print_count++;
                $dispatchSheet->save();
                $pdf = PDF\Pdf::loadView('admin.small_packs.dispatch_print', compact('data','dispatchInfo'));
                return $pdf->stream();
            } else{
                return response()->json(['status' => false, 'message' => 'No Dispatch Found'], 422);
            }
            
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
        
    }

    public function receive_centre_dispatch_sheet(Request $request)
    {
        $user = JWTAuth::toUser($request->token);
        
        DB::beginTransaction();
        try{
            $dispatch = SaleCenterSmallPackDispatchStatus::where('dispatch_id',$request->dispatch_sheet_id)
            ->where('bin_id',$request->bin_id)
            ->first();
            
            if($dispatch->dispatched == 0){
                return response()->json(['status' => false, 'message' => 'Items Not yet Dispatched'], 422);
            } elseif($dispatch->received != 0){
                return response()->json(['status' => false, 'message' => 'Already Received Dispatched'], 422);
            }
            foreach($request->item_ids as $inventoryItemId){
                $saleCenterSmallPackDispatchItem = SaleCenterSmallPackDispatchItems::where('dispatch_id', $dispatch->dispatch_id)
                    ->where('bin_id', $request->bin_id)
                    ->where('wa_inventory_item_id', $inventoryItemId)->update(['is_received' => true]);
              
            }

            // $dispatch->update([
            //     'received' => true,
            // ]);

            DB::commit();
            $allReceived = SaleCenterSmallPackDispatchItems::where('dispatch_id', $dispatch->dispatch_id)->where('is_received', false)->first();

            if($allReceived){
                $dispatch->update([
                    'received' => false,
                ]);
            }else{
                $dispatch->update([
                    'received' => true,
                ]);

            }
           
            return response()->json(['status' => true, 'message'=>'Received dispatch sheet items successfully']);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function get_route_dispatch_sheets(Request $request, $routeId)
    {
        $user = JWTAuth::toUser($request->token);

        try {
            $date = Carbon::createFromFormat('d/m/Y', $request->date_filter)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date provided.',
            ], 422);
        }
        
        try {
            
            $route = Route::find($routeId);
            $documentNo=null;
            $received=false;
            $driver_dispatch=false;
            $shift_status = true;

            $centers = $route->centers->map(function($center) use($date,&$documentNo,&$received,&$driver_dispatch, &$shift_status){
                $isFullyDispatched = true;
                if($center->name){
                    $centerInfo = $this->getDispatchCenterItems($center->route_id,$center->id,$date);
                    $documentNo = $centerInfo['document_no'] ?? $documentNo;
                    $received = $centerInfo['received'] ?? $received;
                    if (!$driver_dispatch) {
                        $driver_dispatch = $centerInfo['driver_dispatch'] ?? $driver_dispatch;
                    }
                    if ($shift_status) {
                        $shift_status = $centerInfo['shift_status'];
                    }

                    $allItemsReceived = collect($centerInfo['items'])->every(function ($item) {
                        return $item['is_fully_dispatched'] == true;
                    });

                    if (!$allItemsReceived) {
                        $isFullyDispatched = false;
                    }
                    return [
                        "id" => $center->id,
                        "centre_name" => $center->name,
                        "dispatch_sheets" => $centerInfo['items'],
                        "is_fully_dispatched" => $isFullyDispatched,
                    ];
                }
            })->filter();

            $return =[
                "has_driver_dispatch" => $driver_dispatch,
                "document_no"=> $documentNo,
                "received" => $received,
                "shift_status" => $shift_status,
                "centre_sheets" => $centers,
            ];

            return response()->json(['status' => true, 'data' => $return]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function getDispatchCenterItems($route,$center, $date)
    {
        $documentNo=null;
        $received=false;
        $driver_dispatch=false;
        $shiftStatus =true;
        $dispachItems = [];
      

        $dispatched = SmallPackDriverDispatch::where('route_id',$route)
        ->whereDate('created_at', $date)
        ->whereHas('items.storeDispatch', function ($q) use($center) {
            $q->where('center_id', $center);
        })
        ->first();

        if ($dispatched) {
            $driver_dispatch = true;
            $documentNo = $dispatched->document_no;
            $received = $dispatched->received ? true: false;

            $shift = SalesmanShift::find($dispatched->shift_id);
            $shiftStatus = $shift->status=='open'? true: false;

            foreach ($dispatched->items as $value) {
                $binsArr = [];
                $isFullyDispatched = true;
                foreach ($value->storeDispatch->status as $bin) {
                    $items=[];
                    foreach ($bin->dispatch->items as $item) {
                    if($item->bin_id == $bin->bin_id){
                        $items[]=[
                            "id" => $item->inventoryItem->id,
                            "stock_id_code" => $item->inventoryItem->stock_id_code,
                            "title" => $item->inventoryItem->title,
                            "totalQty" => $item->total_quantity,
                            "is_received" => $item->is_received,
                        ];

                    }

                
                    }
                    $allItemsReceived = collect($items)->every(function ($item) {
                        return $item['is_received'] == true;
                    });

                    if (!$allItemsReceived) {
                        $isFullyDispatched = false;
                    }
                    $binsArr[]=[
                                "id" => $bin->bin_id,
                                "title" => $bin->uom->title,
                                "items" =>$items,
                                "has_received_items" => $bin->received? true: false,
                    ];
                }
               

                $dispachItems[] = [
                                    "id" => $value->id,
                                    "document_no" => $value->document_no,
                                    "is_fully_dispatched" => $isFullyDispatched,
                                    "bins" => $binsArr
                                ];
            }
        }
         
        $notDispatched = SaleCenterSmallPackDispatch::with('saleCenter')
            ->where('center_id',$center)
            ->doesntHave('driverDispatch')
            ->whereDate('created_at', $date)
            ->get();

        foreach ($notDispatched as $value) {
            $binsArr = [];
            $isFullyDispatched = true;


            $shift = SalesmanShift::find($value->shift_id);
            $shiftStatus = $shift->status=='open'? true : false;

            foreach ($value->status as $bin) {
                $items=[];
                foreach ($bin->dispatch->items as $item) {
                    if($item->bin_id == $bin->bin_id){
                        $items[]=[
                            "id" => $item->inventoryItem->id,
                            "stock_id_code" => $item->inventoryItem->stock_id_code,
                            "title" => $item->inventoryItem->title,
                            "totalQty" => $item->total_quantity,
                            "is_received" => $item->is_received,
    
                        ];
                        
                    }
                
                }
                $allItemsReceived = collect($items)->every(function ($item) {
                    return $item['is_received'] == true;
                });

                if (!$allItemsReceived) {
                    $isFullyDispatched = false;
                }
                $binsArr[]=[
                            "id" => $bin->bin_id,
                            "title" => $bin->uom->title,
                            "items" =>$items,
                            "has_received_items" => $bin->received? true: false,
                ];
            }

            $dispachItems[] = [
                                "id" => $value->id,
                                "document_no" => $value->document_no,
                                "is_fully_dispatched" => $isFullyDispatched,
                                "bins" => $binsArr
                            ];
        }
        
        return [
            "driver_dispatch" => $driver_dispatch,
            "received" => $received,
            "document_no" => $documentNo,
            "items" => $dispachItems,
            "shift_status" => $shiftStatus
        ];
    }

    public function create_delivery_dispatch_sheet(Request $request)
    {
        $user = JWTAuth::toUser($request->token);

        DB::beginTransaction();
        try{

            $centers = DeliveryCentres::where('route_id',$request->route_id)
            ->get()->pluck('id');

            $dispatches = SaleCenterSmallPackDispatch::with('saleCenter','driverDispatch.storeDispatch')
            ->whereIn('center_id',$centers)
            ->whereNull('driver_dispatch_id')
            ->get();

         

            if ($dispatches->count() > 0) {
                foreach($dispatches as $dispatch){
                    $centerDispatchItems = SaleCenterSmallPackDispatchItems::where('dispatch_id',$dispatch->id)->get();
                    foreach($centerDispatchItems as $centerItems){
                        $storeDispatch = SalesmanShiftStoreDispatch::where('shift_id', $dispatch->shift_id)->where('bin_location_id', $centerItems->bin_id)->first();
                        // $dispatchItem = SalesmanShiftStoreDispatchItem::where('dispatch_id', $dispatch->id)->where('wa_inventory_item_id', $centerDispatchItem->wa_inventory_item_id)->first();
                        // $storeDispatch->update(['dispatched' => true]);
                        $storeDispatch->dispatched = true;
                        $storeDispatch->save();
    
                    }
                }
                $series_module = WaNumerSeriesCode::where('module', 'SMALL_PACK_DRIVER_DISPATCH')->first();
                $lastNumberUsed = $series_module->last_number_used;
                $newNumber = (int)$lastNumberUsed + 1;
                $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
                $series_module->update(['last_number_used' => $newNumber]);

                $dispatchSingle = $dispatches->first();

                $dispatch = SmallPackDriverDispatch::create([
                    'created_by' => $user->id,
                    'route_id' => $request->route_id,
                    'document_no' => $newCode,
                    'shift_id' => $dispatchSingle->shift_id
                ]);   

                foreach ($dispatches as $value) {
                    SmallPackDriverDispatchItems::create([
                        'small_pack_driver_dispatch_id' => $dispatch->id,
                        'sale_center_small_pack_dispatch_id' => $value->id
                    ]);
                    $value->update(['driver_dispatch_id' => $dispatch->id]);
                }
                DB::commit();
                
                return response()->json(['status' => true, 'message'=>'Delivery dispatch sheet created successfully', 'dispatch_sheet_id'=>$dispatch->id]);
            } else{
                return response()->json(['status' => false, 'message' => 'No Loading sheet to Dispatch'], 422);
            }

            
        } catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_single_delivery_dispatch_sheet(Request $request,$dispatch)
    {
        $user = JWTAuth::toUser($request->token);

        try {
            $dispatchSheet = SmallPackDriverDispatch::with('items','items.storeDispatch')
                ->find($dispatch);

            if ($dispatchSheet) {
                $dispatchInfo = [
                    'id' => $dispatchSheet->id,
                    'document_no' => $dispatchSheet->document_no,
                    'route' =>$dispatchSheet->route ? $dispatchSheet->route->route_name : null,
                    'print_count' => $dispatchSheet->print_count,
                ];

                $items = [];

                foreach ($dispatchSheet->items as $value) {
                    foreach ($value->storeDispatch->status as $bin) {
                        foreach ($bin->dispatch->items as $item) {
                            $binTitle = $item->uom->title;
                            $itemId = $item->inventoryItem->id;
                            
                            // Check if bin_title already exists in the items array
                            if (!isset($items[$binTitle])) {
                                $items[$binTitle] = [];
                            }

                              // Check if the item already exists under the respective binTitle
                            $existingIndex = null;
                            foreach ($items[$binTitle] as $index => $existingItem) {
                                if ($existingItem['id'] === $itemId) {
                                    $existingIndex = $index;
                                    break;
                                }
                            }

                            // // Append the item details under the respective bin_title
                            // $items[$binTitle][] = [
                            //     "id" => $item->inventoryItem->id,
                            //     "stock_id_code" => $item->inventoryItem->stock_id_code,
                            //     "title" => $item->inventoryItem->title,
                            //     "totalQty" => $item->total_quantity
                            // ];

                        if ($existingIndex !== null) {
                            // Item exists, cumulate the totalQty
                            $items[$binTitle][$existingIndex]['totalQty'] += $item->total_quantity;
                        } else {
                            // Item doesn't exist, add it
                            $items[$binTitle][] = [
                                "id" => $item->inventoryItem->id,
                                "stock_id_code" => $item->inventoryItem->stock_id_code,
                                "title" => $item->inventoryItem->title,
                                "totalQty" => $item->total_quantity
                            ];
                        }
                        }
                    }
                }

                // Prepare the grouped structure
                $groupedItems = [];

                foreach ($items as $binTitle => $grouped) {
                    $groupedItems[] = [
                        "title" => $binTitle,
                        "items" => $grouped
                    ];
                }
            
                // Return dispatch ID and bins
                $data = $groupedItems;
    
                $dispatchSheet->print_count++;
                $dispatchSheet->save();
                $pdf = PDF\Pdf::loadView('admin.small_packs.dispatch_print_driver', compact('data','dispatchInfo'));
                return $pdf->stream();
            } else{
                return response()->json(['status' => false, 'message' => 'No Dispatch Found'], 422);
            }
            
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

    }

}
