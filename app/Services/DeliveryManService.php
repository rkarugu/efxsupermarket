<?php

namespace App\Services;

use App\LoadingSheetDispatch;
use App\Model\Route;
use App\VehicleAssignment;

class DeliveryManService
{
    static public function getAssignedRoute($user)
    {
//        $vehicleAssignment = VehicleAssignment::where('user_id', $user->id)->first();
        if (!$user->vehicle) {
            return null;
        }

        $assignedLoadingSheet = LoadingSheetDispatch::latest()
            ->where('vehicle_id', $user->vehicle->id)
            ->where('delivery_status', '!=', 'finished')
            ->first();

        if (!$assignedLoadingSheet) {
            return null;
        }

        return Route::find($assignedLoadingSheet->route_id);
    }

    static public function getCurrentLoadingSheet($user)
    {
        $vehicleAssignment = VehicleAssignment::where('user_id', $user->id)->first();
        if (!$user->vehicle) {
            return null;
        }

        $assignedLoadingSheet = LoadingSheetDispatch::latest()
            ->where('vehicle_id', $user->vehicle->id)
            ->where('delivery_status', '!=', 'finished')
            ->first();

        return $assignedLoadingSheet;
    }
}