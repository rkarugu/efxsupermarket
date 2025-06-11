<?php

namespace App\Http\Controllers\Admin;

use App\Events\VehicleLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\VehicleTelematicsData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleTelematicsDataController extends Controller
{
    public function __construct()
    {
    }

    public function receive(Request $request): JsonResponse
    {
        try {
            $requestData = $request->json()->all();

            foreach ($requestData as $index => $data) {
                try {
                    $vehicleTelematicsData = new VehicleTelematicsData();
                    $vehicleTelematicsData->device_number = $data['device.name'];
                    $vehicleTelematicsData->latitude = $data['position.latitude'] ?? null;
                    $vehicleTelematicsData->longitude = $data['position.longitude'] ?? null;
                    $vehicleTelematicsData->speed = $data['position.speed'] ?? null;
                    $vehicleTelematicsData->direction = $data['position.direction'] ?? null;
                    $vehicleTelematicsData->fuel_level = $data['escort.lls.value.1'] ?? null;
                    $vehicleTelematicsData->mileage = $data['vehicle.mileage'] ?? null;
                    $vehicleTelematicsData->ignition_status =  isset($data['engine.ignition.status']) ? $data['engine.ignition.status'] : null;
                    $vehicleTelematicsData->timestamp = Carbon::createFromTimestamp($data['timestamp']);
                    $vehicleTelematicsData->raw_timestamp = $data['timestamp'];
                    // $vehicleTelematicsData->data = json_encode($requestData);
                    $vehicleTelematicsData->data_index = $index;
                    $vehicleTelematicsData->save();

                    $latitude = $data['position.latitude'];
                    $longitude = $data['position.longitude'];
                    event(new VehicleLocationUpdated([
                        'device_number' => $data['device.name'],
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'direction' => $data['position.direction'],
                        'mileage' => $data['vehicle.mileage'] ?? 0,
                        'speed' => $data['position.speed'],
                        'fuel_level' => $data['escort.lls.value.1'] ?? 0,
                        'ignition_status' => isset($data['engine.ignition.status']) ? ($data['engine.ignition.status'] ? 'ON' : 'OFF') : 'OFF',
                        'movement' => isset($data['position.speed']) ? ($data['position.speed'] > 8) : false,
                        'is_offline' => false,
                        'timestamp' => Carbon::createFromTimestamp($data['timestamp']),
        
                    ]));        

                } catch (\Throwable $e) {
                    Log::info("Failed to save for single vehicle, with error {$e->getMessage()}");
                }
            }

            // $latitude = $requestData[0]['position.latitude'];
            // $longitude = $requestData[0]['position.longitude'];
            // event(new VehicleLocationUpdated([
            //     'device_number' => $requestData[0]['device.name'],
            //     'latitude' => $latitude,
            //     'longitude' => $longitude,
            //     'direction' => $requestData[0]['position.direction'],
            //     'mileage' => $requestData[0]['vehicle.mileage'] ?? 0,
            //     'speed' => $requestData[0]['position.speed'],
            //     'fuel_level' => $requestData[0]['escort.lls.value.1'] ?? 0,
            //     'ignition_status' => isset($requestData[0]['engine.ignition.status']) ? ($requestData[0]['engine.ignition.status'] ? 'ON' : 'OFF') : 'OFF',
            //     'movement' => isset($requestData[0]['position.speed']) ? ($requestData[0]['position.speed'] > 8) : false,
            //     'timestamp' => Carbon::createFromTimestamp($requestData[0]['timestamp']),
            //     'is_offline' => false,

            // ]));

            return $this->jsonify(['message' => 'Vehicle Data Received Successfully'], 200);
        } catch (\Throwable $e) {
            // DB::rollback();
            Log::info("Failed to receive vehicle telematics data, with error {$e->getMessage()}");
            return $this->jsonify(['message' => $e->getMessage()], 500);

        }

    }
}
