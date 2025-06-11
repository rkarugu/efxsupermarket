<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaUnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\Route;
use App\Vehicle;

class GlobalMethodsController extends Controller
{
    public function getBranchUoms(Request $request)
    {
        $branch_id = $request->branch_id;
        $uoms = WaUnitOfMeasure::select(
            'wa_unit_of_measures.id as id',
            'wa_unit_of_measures.title as title',
        )->leftJoin('wa_location_store_uom', 'wa_unit_of_measures.id', '=', 'wa_location_store_uom.uom_id')
        ->where('wa_location_store_uom.location_id', $branch_id)
        ->get();
        return response()->json(['uoms' => $uoms]);
    }
    public function getBranchRoutes(Request $request){
        $user = Auth::user();
        $branches = DB::table('user_branches')
            ->where('user_id', $user->id)
            ->pluck('restaurant_id')
            ->toArray();
        $routes = Route::where('restaurant_id', $request->branch_id)->get();
        return response()->json(['routes' => $routes]);
    } 
    public function getBranchVehicles(Request $request){
       
        $vehicles = Vehicle::where('branch_id', $request->branch_id)->get();
        return response()->json(['vehicles' => $vehicles]);
    } 
}
