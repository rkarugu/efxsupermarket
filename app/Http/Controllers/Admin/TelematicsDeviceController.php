<?php

namespace App\Http\Controllers\Admin;

use App\Model\Vehicle;
use App\Telematics;
use App\TelematicsDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TelematicsDeviceController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'telematics-devices';
        $this->base_route = 'telematics-devices';
        $this->resource_folder = 'admin.telematics_devices';
        $this->base_title = 'Telematics Devices';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'devices'));
    }

    public function getDevices(Request $request): JsonResponse
    {
        try {
            $telematicsService = new Telematics();
            $devices_response = $telematicsService->getDevices();
            $devices = $devices_response[0]["items"];

            $devices = collect($devices)->map(function ($device) {
                $fuelLevel = 0;
                $odometer = 0;
                $fuelSensor = collect($device['device_data']['sensors'])->where('type', 'fuel_tank_calibration')->first();
                if ($fuelSensor) {
                    $fuelLevel = $fuelSensor['value'];
                }

                $odometerSensor = collect($device['device_data']['sensors'])->where('type', 'odometer')->first();
                if ($odometerSensor) {
                    $odometer = $odometerSensor['value'];
                }

                return [
                    'device_id' => $device['id'],
                    'device_imei' => $device['device_data']['imei'],
                    'device_name' => $device['name'],
                    'odometer' => $odometer,
                    'fuel_level' => $fuelLevel,
                ];
            })->toArray();

            return $this->jsonify(['data' => $devices], 200);
        } catch (\Throwable $e) {
            return $this->jsonify([], 500);
        }
    }
}
