<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierVehicleType;

class SupplierVehicleTypeController extends Controller
{
    public function index(){
        try {
            return response()->json([
                'result' => 1,
                'message' => 'Ok',
                'data' => SupplierVehicleType::get(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
                'data' => [],
            ]);
        }
    }
}
