<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Models\SaleCenterSmallPacks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\SaleCenterSmallPackDispatch;

class BinLocationController extends Controller
{
    public function getUnReceivedBins(Request $request): JsonResponse
    {
        try {
            $bins = [];
            $user = JWTAuth::toUser($request->token);
            if ($user->role_id == 6) {
                $delivery = DeliverySchedule::forDriver($user->id)->whereIn('status', ['consolidating', 'consolidated'])->first();
                if ($delivery) {
                    $route = Route::with(['currentRepresentative', 'currentRepresentative.user'])->where('id',$delivery->route_id)->first();
                    
                    $bins = DB::table('wa_unit_of_measures')
                        ->select(
                            'wa_unit_of_measures.id',
                            'wa_unit_of_measures.title as bin_location_name',
                            'salesman_shift_store_dispatches.id as dispatch_id'
                        )
                        ->join('salesman_shift_store_dispatches', function ($join) use ($delivery) {
                            $join->on('salesman_shift_store_dispatches.bin_location_id', '=', 'wa_unit_of_measures.id')
                                ->where('shift_id', $delivery->shift_id)->where('dispatched', true)->where('received', false);
                        })
                        // ->where('wa_unit_of_measures.is_display', 0)
                        ->get()
                        ->map(function ($record) {
                            $record->item_count = DB::table('salesman_shift_store_dispatch_items')->where('dispatch_id', $record->dispatch_id)->count();
                            unset($record->dispatch_id);
                            return $record;
                        });

                        // $dispatchSheet = SaleCenterSmallPackDispatch::with('items', 'items.inventoryItem', 'driverDispatch.driverDispatch')
                        //     ->whereHas('saleCenter.internalRequisition', function ($q) use($delivery) {
                        //         $q->where('wa_shift_id', $delivery->shift_id);
                        //     })
                        //     ->whereHas('driverDispatch.driverDispatch', function ($q) {
                        //         $q->where('received', false);
                        //     })
                        //     ->whereHas('items', function ($q) {
                        //         $q->where('is_received_by_driver', false);
                        //     })
                        //     ->get();
                        //     $counter =0;
                        //     foreach ($dispatchSheet as $key => $value) {
                        //         $counter += $value->items->count();
                        //     }
                        //     if(!$dispatchSheet->isEmpty())
                        //     {
                        //         $bins[] =[
                        //             // "id" => $dispatchSheet[0]->driverDispatch->driverDispatch->id,
                        //             "id" => $dispatchSheet[0]->id,
                        //             "bin_location_name" => "GROUP REP". ' ('.($route->currentRepresentative?->user?->name ?? " - ").")",
                        //             "item_count" => $counter,
                        //             "small_pack" => true,
                        //         ];
                        //     }
                }
            }
            return $this->jsonify(['data' => $bins], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
