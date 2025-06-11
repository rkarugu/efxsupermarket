<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Vehicle;
use Carbon\Carbon;
use App\DeliverySchedule;
use App\NewFuelEntry;
use App\Model\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VehicleCentreContoller extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'vehicles';
        $this->base_route = 'vehicles';
        $this->resource_folder = 'admin.vehicles';
        $this->base_title = 'My Fleet';
    }

    public function show($id)
    {
        // if (!can('see-overview', $this->permissionModule)) {
        //     return returnAccessDeniedPage();
        // }

        $model = $this->model;
        $title = 'Vehicle Centre';
        $breadcum = ['Vehicle Listing' => route('vehicles.index'), $title => ''];

        $vehicle = Vehicle::with(['type', 'driver', 'model','model.supplier'])->find($id);
        $vehicle->acquisition_date = Carbon::parse($vehicle->acquisition_date)->toFormattedDateString();
        $currentSchedule = DeliverySchedule::with('shift')->latest()->active()->forVehicle($vehicle->id)->first();
        $vehicle->current_schedule = $currentSchedule;
        $vehicle->display_travel_expense = format_amount_with_currency($vehicle->travel_expense);
        $vehicle->typeName = VehicleType::find($vehicle->model->vehicle_type_id)->value('name');

        $googleMapsApiKey = config('app.google_maps_api_key');
        
        return view('admin.vehicles.vehicle_centre', compact('title', 'model', 'breadcum','vehicle','googleMapsApiKey'));
    }

    public function getFuelHistory(Request $request): JsonResponse
    {
        try {
            $fuelHistoryTotal = 0;
            $fuelHistory = NewFuelEntry::with('fueledBy')
            ->where('vehicle_id',$request->vehicle_id)
            ->orderBy('created_at','DESC')
            ->get()
            ->map(function($fuel) use(&$fuelHistoryTotal){
                $fuelHistoryTotal += $fuel->actual_fuel_quantity;
                return [
                    'lpo_number' => $fuel->lpo_number,
                    'fueled_by' => $fuel->fueledBy?->name ?? '-' ,
                    'shift_type' => $fuel->shift_type,
                    'last_fuel_entry_mileage' => number_format($fuel->last_fuel_entry_mileage, 3, '.', ',') ?? '-',
                    'created_at' => date('d-m-Y H:i', strtotime($fuel->created_at)),
                    'fuel_quantity' => number_format($fuel->actual_fuel_quantity, 2, '.', ','),
                ];
            });

            return $this->jsonify(['data' => $fuelHistory, 'totals'=>['fuelHistoryTotal'=>$fuelHistoryTotal]], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
        
        
    }
    
}
