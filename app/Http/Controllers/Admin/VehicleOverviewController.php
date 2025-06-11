<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Exports\CommonReportDataExport;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\UserLog;
use App\Models\VehicleImmobilization;
use App\Models\VehicleTelematicsData;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class VehicleOverviewController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct(protected SmsService $smsService)
    {
        $this->model = 'vehicles-overview';
        $this->base_route = 'vehicles';
        $this->resource_folder = 'admin.vehicles';
        $this->base_title = 'My Fleet';
    }
   
    public function overview()
    {
        $title = $this->base_title;
        $model = $this->model;
        $pmodule = "vehicles";
        $permission = $this->mypermissionsforAModule();
        $googleMapsApiKey = config('app.google_maps_api_key');
        
        if(isset($permission[$model . '___switch-off']) || $permission == 'superadmin'){
            $canSwitchVehicleOff = true;
        }else{
            $canSwitchVehicleOff = false;
        }
        if(isset($permission[$model . '___view-history']) || $permission == 'superadmin'){
            $canViewHistory = true;
        }else{
            $canViewHistory = false;
        }
        $subquery = DB::connection('telematics')->table('vehicle_telematics')
        ->select('device_number', DB::raw('MAX(id) as max_id'))
        ->groupBy('device_number');

        $vehicles = DB::connection('telematics')->table('vehicle_telematics')
        ->select('vehicle_telematics.device_number',
        )
        ->joinSub($subquery, 'sub', function ($join) {
            $join->on('vehicle_telematics.id', '=', 'sub.max_id');
        })
        ->get();
        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $base_route = $this->base_route;
            // return view("$this->resource_folder.overview", compact('title', 'model', 'base_route', 'googleMapsApiKey', 'vehicles', 'canSwitchVehicleOff', 'canViewHistory'));
            return view("$this->resource_folder.live_tracking_overview", compact('title', 'model', 'base_route', 'googleMapsApiKey', 'vehicles', 'canSwitchVehicleOff', 'canViewHistory'));

        } else {
            return redirect()->back()->withErrors(['error' => pageRestrictedMessage()]);
        }

    }
    public function getLocations(){
        try {
            //get offline devices
            $twoHrsAgo = Carbon::now()->subHours(2);
            // $recentDeviceNumbers = DB::connection('telematics')->table('vehicle_telematics')
            //     ->select('device_number')
            //     ->where('created_at', '>=', $twoHrsAgo)
            //     ->distinct();
            // $offlineDevices = DB::connection('telematics')->table('vehicle_telematics')
            // ->select('device_number')
            // ->whereNotIn('device_number', $recentDeviceNumbers)
            // ->distinct('device_number')
            // ->count();
            // $offlineDevices = $aggregate = DB::connection('telematics')->table('vehicle_telematics AS vt')
            //     ->leftJoin('vehicle_telematics AS vt2', function ($join) {
            //         $join->on('vt.device_number', '=', 'vt2.device_number')
            //             ->where('vt2.timestamp', '>=', '2024-09-30 09:00:43');
            //     })
            //     ->whereNull('vt2.device_number')
            //     ->distinct()
            //     ->count('vt.device_number');
            $offlineDevices = 0;
     

            $subquery = DB::connection('telematics')->table('vehicle_telematics')
            ->select('device_number', DB::raw('MAX(id) as max_id'))
            ->groupBy('device_number');

            $results = DB::connection('telematics')->table('vehicle_telematics')
            ->select('vehicle_telematics.device_number',
            // 'vehicle_telematics.data',
            'vehicle_telematics.created_at',
            'vehicle_telematics.timestamp',
            'vehicle_telematics.fuel_level',
            'vehicle_telematics.mileage',
            'vehicle_telematics.speed',
            'vehicle_telematics.latitude',
            'vehicle_telematics.longitude',
            'vehicle_telematics.ignition_status',
            'vehicle_telematics.direction',
            'vehicle_telematics.timestamp'
            )
            // ->where('vehicle_telematics.created_at', '>=', $twoHrsAgo) //check signals receivedd in the last 2 hrs
            ->joinSub($subquery, 'sub', function ($join) {
                $join->on('vehicle_telematics.id', '=', 'sub.max_id');
            })
            ->get();
            $results = $results->map(function ($record) {
                
                // $data = json_decode($record->data); 
                $location =  '';
                $carbonTimeStamp = Carbon::parse($record->created_at);

                return [
                    'longitude' => $record->longitude,
                    'latitude' => $record->latitude,
                    'device' => $record->device_number,
                    'movement' => $record->speed > 8 ? true : false,
                    'ignition_status' => isset($record->ignition_status) ? ($record->ignition_status? 'ON' : 'OFF') : 'OFF',
                    'speed' => $record->speed,
                    'direction' => $record->direction,
                    'is_offline' => $carbonTimeStamp->lessThan(Carbon::now()->subHours(2)) ? true : false,
                    'current_location' => $location,
                    'mileage' => ceil($record->mileage ?? 0),
                    'timestamp' => $record->timestamp,
                    
                ];
            });
            $movingVehiclesCount = $results->filter(function ($vehicle) {
                return ($vehicle['movement'] && $vehicle['speed'] <= 65 && $vehicle['speed'] > 8 && !$vehicle['is_offline']);
            })->count();
            $overspeedingVehiclesCount = $results->filter(function ($vehicle) {
                return ($vehicle['movement'] && $vehicle['speed'] > 65 && !$vehicle['is_offline']);
            })->count();
            $idlingVehiclesCount = $results->filter(function ($vehicle) {
                return ($vehicle['ignition_status'] == 'ON' && $vehicle['speed'] <= 8 && !$vehicle['is_offline']);
            })->count();
            $stationeryVehicleCount = $results->filter(function ($vehicle){
                return ($vehicle['ignition_status']  == 'OFF' && !$vehicle['is_offline']);

            })->count();
    
            return response()->json([
                'results'=>$results,
                'moving_vehicle_count' => $movingVehiclesCount,
                'stationery_vehicle_count' => $stationeryVehicleCount,
                'overspeeding_vehicle_count' => $overspeedingVehiclesCount,
                'idling_vehicle_count' => $idlingVehiclesCount,
                'offline_vehicle_count' => $offlineDevices,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message', $e->getMessage()], 500);
        }
    }

    public function liveVehicleMovement($deviceName)
    {
        $title = $this->base_title; 
        $model = $this->model;
        $pmodule = "vehicles";
        $permission = $this->mypermissionsforAModule();
        $googleMapsApiKey = config('app.google_maps_api_key');
        $vehicleDetails  = DB::table('vehicles')
        ->select(
            'users.name as driver',)
        ->leftJoin('users', 'users.id', '=', 'vehicles.driver_id')
        ->where('license_plate_number', '=' ,$deviceName)
        ->first();
        if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
            $base_route = $this->base_route;
            return view("$this->resource_folder.single_vehicle_movement", compact('title', 'model', 'base_route', 'googleMapsApiKey', 'deviceName', 'vehicleDetails'));
        } else {
            return redirect()->back()->withErrors(['error' => pageRestrictedMessage()]);
        }

    }
    public function getVehicleMovement(Request $request, $deviceName){
        try {
            
            if($request->date && $request->to_date){
                $start = Carbon::parse($request->date)->toDateTimeString();
                $end = Carbon::parse($request->to_date)->toDateTimeString();

            }else{
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();

            }
            
            // $vehiclePositions  = DB::connection('telematics')->table('vehicle_telematics')
            //         ->select('vehicle_telematics.data', 
            //         'vehicle_telematics.timestamp',
            //         'vehicle_telematics.fuel_level',
            //         'vehicle_telematics.mileage',
            //         'vehicle_telematics.speed',
            //         'vehicle_telematics.latitude',
            //         'vehicle_telematics.longitude',
            //         'vehicle_telematics.ignition_status',
            //         'vehicle_telematics.direction',
            //         'vehicle_telematics.device_number',
            //         'vehicles.vehicle_model_id')
            //         ->whereBetween('vehicle_telematics.timestamp', [$start, $end])
            //         ->leftJoin('vehicles', 'vehicles.license_plate_number', '=', 'vehicle_telematics.device_number')
            //         ->where('device_number', '=', $deviceName)
            //         ->orderBy('vehicle_telematics.id', 'desc')
            //         ->get();
            $telematicsData = DB::connection('telematics')->table('vehicle_telematics')
                ->select(
                    // 'vehicle_telematics.data',
                    'vehicle_telematics.timestamp',
                    'vehicle_telematics.fuel_level',
                    'vehicle_telematics.mileage',
                    'vehicle_telematics.speed',
                    'vehicle_telematics.latitude',
                    'vehicle_telematics.longitude',
                    'vehicle_telematics.ignition_status',
                    'vehicle_telematics.direction',
                    'vehicle_telematics.device_number'
                )
                ->whereBetween('vehicle_telematics.timestamp', [$start, $end])
                ->where('device_number', '=', $deviceName)
                ->orderBy('vehicle_telematics.id', 'desc')
                ->get();
            $vehicles = DB::table('vehicles')
                ->where('license_plate_number', $deviceName)
                ->pluck('vehicle_model_id', 'license_plate_number');
            $vehiclePositions = $telematicsData->map(function($telematics) use ($vehicles) {
                    $telematics->vehicle_model_id = $vehicles->get($telematics->device_number);
                    return $telematics;
                });

            $vehicleMovement = $vehiclePositions->map(function ($record) {
                        // $data = json_decode($record->data); 
                        $fuel_level = $record->fuel_level ?? 0;
                        return [
                            'longitude' => $record->longitude,
                            'latitude' => $record->latitude,
                            'device' => $record->device_number,
                            'movement' => $record->speed > 8 ? true : false,
                            'ignition_status' => isset($record->ignition_status) ? ($record->ignition_status ? 'ON' : 'OFF') : 'OFF',
                            'speed' => $record->speed,
                            'direction' => $record->direction,
                            'time' => Carbon::parse($record->timestamp)->toTimeString(),
                            'fuel_level' => $fuel_level,
                            'mileage' => ceil($record->mileage ?? 0),
                        ];
                    });
            return response()->json([
                'vehicleMovement' => $vehicleMovement,              
            ]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message', $e->getMessage()], 500);
        }
    }

    private function fuelConversionChart(): array
    {
        return [
            '308' => '10',
            '687' => '20',
            '1064' => '30',
            '1394' => '40',
            '1690' => '50',
            '2060' => '60',
            '2377' => '70',
            '2684' => '80',
            '2989' => '90',
            '3200' => '98'
        ];
    }

    private function convertFuel($value): float
    {
        $conversionChart = $this->fuelConversionChart();
        $conversionKeys = array_keys($conversionChart);
        if (in_array((string)$value, $conversionKeys)) {
            return (float)$conversionChart[(string)$value];
        }

        if ($value < 308) {
            return 0;
        }

        if ($value > 3200) {
            return 98;
        }

        $lowerKey = collect($conversionKeys)->filter(function ($key) use ($value) {
            return (int)$key < $value;
        })->last();

        $convertedFuel = ($value / $lowerKey) * $conversionChart[$lowerKey];
        return ceil($convertedFuel);
    }
    private function getAddress($lat, $lng)
    {
        $latitude = $lat;
        $longitude = $lng;
        $apiKey = config('app.google_maps_api_key');

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "$latitude,$longitude",
            'key' => $apiKey,
        ]);
        if ($response->ok()) {            
            return json_decode($response->body(), true)['results'][2]['formatted_address'];
        } else {
            return null;
        }
    
    }
    public function getVehicleInfoWindowDetails(Request $request, $deviceName){
        try {
            

            $vehicle  = DB::table('vehicles')
            ->select(
                'vehicles.license_plate_number',
                'vehicles.primary_responsibility',
                'vehicle_models.name as model_name',
                'users.name as driver',
                'vehicles.id  as id',
                'vehicles.switch_off_status as switch_off_status',
                'vehicles.vehicle_model_id')
            ->leftJoin('vehicle_models', 'vehicles.vehicle_model_id', '=', 'vehicle_models.id')
            ->leftJoin('users', 'users.id', '=', 'vehicles.driver_id')
            ->where('license_plate_number', '=' ,$deviceName)
            ->first();

        if($vehicle){
            $scheduleData = DeliverySchedule::latest()
                ->where('vehicle_id', $vehicle->id)
                ->whereNot('status', 'finished')
                ->first();
            $schedule = $scheduleData ? $scheduleData->deliveryNumber : 'Not Assigned';
            $route  = $scheduleData ? $scheduleData->route?->route_name : '';
        }else{
            $schedule = 'Not Assigned';
            $route = '';
        }
        $telematicsData = DB::connection('telematics')->table('vehicle_telematics')
        ->where('device_number', $deviceName)
        ->latest('timestamp')
        ->first();
        // $data = json_decode($telematicsData->data);
        $fuel_level = $telematicsData->fuel_level  ?? 0;
        $location = $this->getAddress($telematicsData->latitude, $telematicsData->longitude);

        return response()->json([
            'device_name' => $deviceName,
            'vehicle' => $vehicle->license_plate_number ?? '' , 
            'driver' => $vehicle->driver ?? 'Not Assigned',
           'model' => $vehicle->model_name ?? '',
           'schedule' => $schedule,
            'route' => $route,
            'primary_purpose' => $vehicle->primary_responsibility ?? '',
            'location' => $location ?? '',
            'mileage' => ceil($telematicsData->mileage ?? 0),
            'fuel_level' => $fuel_level,
            'speed' => $telematicsData->speed,
            'ignition_status' => isset($telematicsData->ignition_status) ? ($telematicsData->ignition_status ? 'ON' : 'OFF') : 'OFF',
            'switch_off_status' => $vehicle->switch_off_status ?? 'on',
        ]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message', $th->getMessage()], 500);
        }
        
    }
    public function toggleVehicleIgnition(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $vehicle = Vehicle::latest()->where('license_plate_number', $request->deviceName)->first();
            if($request->action == 'switchOff'){
                if($vehicle){
                    $vehicle->switch_off_status = 'off';
                    $vehicle->save();
                     //switch off sms
                    $time = 86400;
                    $speed = $request->speed ?? 8;
                    $switchOffMsg = 'setdigout 1 '.$time.' '.$speed;
                    $this->smsService->sendMessage($switchOffMsg, $vehicle->sim_card_number);
                    //notify dev
                    $this->smsService->sendMessage("$vehicle->license_plate_number switched off",'254729825703');

                    $immobilizationEvent = new VehicleImmobilization();
                    $immobilizationEvent->vehicle_id = $vehicle->id;
                    $immobilizationEvent->causer_id = $user->id;
                    $immobilizationEvent->reason = $request->reason;
                    $immobilizationEvent->time = $time;
                    $immobilizationEvent->speed = $request->speed ?? 8;
                    $immobilizationEvent->save();

                }else{
                    return $this->jsonify(['message' => 'Vehicle not found'], 404);
                }
               
            }else{
                if($vehicle){
                    $vehicle->switch_off_status = 'on';
                    $vehicle->save();
                    $switchOnMsg = 'setdigout 0';
                    $this->smsService->sendMessage($switchOnMsg, $vehicle->sim_card_number);
                    $now = Carbon::now()->toDateTimeString();
                    $immobilizationEvent = VehicleImmobilization::latest()
                        ->where('vehicle_id', $vehicle->id)
                        ->whereNull('switch_on_date')
                        ->first();
                    if($immobilizationEvent){
                    $immobilizationEvent->switch_on_date = $now;
                    $immobilizationEvent->switch_on_by = $user->id;
                    $immobilizationEvent->save();
                    }
                }else{
                    return $this->jsonify(['message' => 'Vehicle not found'], 404);
                }

            }
            UserLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'module' => 'vehicle_tracking',
                'activity' => "$request->activity vehicle $vehicle->license_plate_number",
                'entity_id' => $vehicle->id,
                'user_agent' => 'Bizwiz WEB',
            ]);
            DB::commit();
            return $this->jsonify(['status' => 'success'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
              //notify dev
              $this->smsService->sendMessage($e->getMessage(),'254729825703');
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
    private function canterFuelConversionChart(): array
    {
        return [
            '308' => '10',
            '687' => '20',
            '1064' => '30',
            '1394' => '40',
            '1690' => '50',
            '2060' => '60',
            '2377' => '70',
            '2684' => '80',
            '2989' => '90',
            '3200' => '98'
        ];
    }

    private function fIFuelConversionChart(): array
    {
        return [
            '1' => '0',
            '44' => '11',
            '240' => '	22',
            '416' => '33',
            '583' => '44',
            '739' => '55',
            '895' => '66',
            '1043' => '77',
            '1188' => '88',
            '1335' => '99',
            '1485' => '110',
            '1657' => '121',
            '1799' => '132',
            '1947' => '143',
            '2103' => '154',
            '2255' => '165',
            '2396' => '176',
            '2549' => '187',
            '2710' => '198',
            '2750' => '200'
        ];
    }

    private function convertCanterFuel($value): float
    {
        $conversionChart = $this->canterFuelConversionChart();
        $conversionKeys = array_keys($conversionChart);
        if (in_array((string)$value, $conversionKeys)) {
            return (float)$conversionChart[(string)$value];
        }

        if ($value < 308) {
            return 0;
        }

        if ($value > 2989) {
            return 90;
        }

        $lowerKey = collect($conversionKeys)->filter(function ($key) use ($value) {
            return (int)$key < $value;
        })->last();

        $convertedFuel = ($value / $lowerKey) * $conversionChart[$lowerKey];
        return ceil($convertedFuel);
    }

    private function convertFIFuel($value): float
    {
        $conversionChart = $this->fIFuelConversionChart();
        $conversionKeys = array_keys($conversionChart);
        if (in_array((string)$value, $conversionKeys)) {
            return (float)$conversionChart[(string)$value];
        }

        if ($value < 1) {
            return 0;
        }

        if ($value > 2710) {
            return 90;
        }

        $lowerKey = collect($conversionKeys)->filter(function ($key) use ($value) {
            return (int)$key < $value;
        })->last();

        $convertedFuel = ($value / $lowerKey) * $conversionChart[$lowerKey];
        return ceil($convertedFuel);
    }

    public function downloadVehicleTelematicsReport(Request $request, $deviceName, $startDate, $endDate)
    {
        $vehicle = Vehicle::with('driver')
            ->where('license_plate_number', $deviceName)
            ->first();
            if($startDate && $endDate){ 
                $start = Carbon::parse($startDate)->toDateTimeString();
                $end = Carbon::parse($endDate)->toDateTimeString();

            }else{
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();

            }
            $telematicsData = DB::connection('telematics')->table('vehicle_telematics')
                ->select(
                    // 'vehicle_telematics.data',
                    'vehicle_telematics.timestamp',
                    'vehicle_telematics.fuel_level',
                    'vehicle_telematics.mileage',
                    'vehicle_telematics.speed',
                    'vehicle_telematics.latitude',
                    'vehicle_telematics.longitude',
                    'vehicle_telematics.ignition_status',
                    'vehicle_telematics.direction',
                    'vehicle_telematics.device_number'
                )
                ->whereBetween('vehicle_telematics.timestamp', [$start, $end])
                ->where('device_number', '=', $deviceName)
                ->orderBy('vehicle_telematics.timestamp', 'asc')
                ->get();
            $vehicles = DB::table('vehicles')
                ->where('license_plate_number', $deviceName)
                ->pluck('vehicle_model_id', 'license_plate_number');
            $vehiclePositions = $telematicsData->map(function($telematics) use ($vehicles) {
                    $telematics->vehicle_model_id = $vehicles->get($telematics->device_number);
                    return $telematics;
                });
            $vehicleMovement = $vehiclePositions->map(function ($record) use($vehicle) {
                        // $data = json_decode($record->data); 
                        $fuel_level = $record->fuel_level ?? 0;
                        return [
                            'longitude' => $record->longitude,
                            'latitude' => $record->latitude,
                            'device' => $record->device_number,
                            'movement' => $record->speed > 8 ? true : false,
                            'ignition_status' => isset($record->ignition_status) ? ($record->ignition_status ? 'ON' : 'OFF') : 'OFF',
                            'speed' => $record->speed,
                            'direction' => $record->direction,
                            'time' => $record->timestamp,
                            'fuel_level' => $fuel_level,
                            'mileage' => ceil($record->mileage ?? 0),
                        ];
                    });
                   
            $view = view(
                'admin.vehicles.vehicle_telematics_report',
                [
                    'vehicle' => $vehicle,
                    'start' => $start,
                    'end' => $end,
                    'data' => $vehicleMovement,
                ]
            );
            // $filePath = 'reports/' . $deviceName.'_location_report' . '.xlsx';
            // Excel::store(new CommonReportDataExport($view), $filePath, 'public');
        
            // return response()->json([
            //     'file' => Storage::url($filePath)
            // ]);
           return  Excel::download(new CommonReportDataExport($view), $deviceName.'_location_report' . '.xlsx');
            
    }   
}
