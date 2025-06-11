<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\VehicleMake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleMakeController extends Controller
{
    public function getVehicleMakes(): JsonResponse
    {
        try {
            $makes = VehicleMake::all();
            return $this->jsonify(['data' => $makes], 200);
        } catch (\Throwable $e) {
            return $this->jsonify([], 500);
        }
    }
}
