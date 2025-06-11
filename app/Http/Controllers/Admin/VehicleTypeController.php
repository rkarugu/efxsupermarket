<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function getVehicleTypes(): JsonResponse
    {
        try {
            $types = VehicleType::select('id', 'name')->get();
            return $this->jsonify(['data' =>$types], 200);
        } catch (\Throwable $e) {
            return $this->jsonify([], 500);
        }
    }
}