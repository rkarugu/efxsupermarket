<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FuelEntryParentTypes;
use App\Enums\FuelEntryStatus;
use App\Enums\VehicleResponsibilityTypes;
use App\FuelLpo;
use App\FuelStation;
use App\NewFuelEntry;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class NewFuelEntryController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'fuelentry';
        $this->base_route = 'fuel-entries';
        $this->resource_folder = 'admin.new_fuel_entries';
        $this->base_title = 'Fuel History';
    }

    public function listVehicles(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Token mismatch';
                return response()->json($payload, 422);
            }

            $vehicles = DB::table('fuel_entries')
                ->join('vehicles', function ($join) use ($user) {
                    $join->on('vehicles.id', '=', 'fuel_entries.vehicle_id')->where('branch_id', $user->restaurant_id);
                })
                ->leftJoin('delivery_schedules', 'delivery_schedules.id', '=', 'fuel_entries.shift_id')
                ->leftJoin('routes', 'routes.id', '=', 'delivery_schedules.route_id')
                ->select(
                    'fuel_entries.last_fuel_entry_level',
                    'fuel_entries.lpo_number',
                    'fuel_entries.comments',
                    'vehicles.id',
                    'vehicles.fuel_tank_capacity',
                    'vehicles.license_plate_number as license_plate',
                    'vehicles.primary_responsibility',
                    'vehicles.branch_id as vehicle_branch',
                    'routes.route_name as route',
                    'delivery_schedules.status as shift_status',
                    'delivery_schedules.finish_time as shift_end',
                    DB::raw("(DATE(delivery_schedules.actual_delivery_date)) as shift_date")
                )
                ->whereDate('fuel_entries.created_at', '>', Carbon::now()->subDays(5)->startOfDay())
                ->whereIn('entry_status', ['pending', 'expired'])
                ->get()->map(function ($record) use ($user) {
                    if ($record->primary_responsibility == FuelEntryParentTypes::RouteDelivery) {
                        if ($record->vehicle_branch != $user->restaurant_id) {
                            return null;
                        }
                    }

                    $canFuel = $record->shift_status == 'finished';
                    $record->can_fuel = $canFuel;

                    if (!$record->route) {
                        $record->route = $record->comments;
                        $record->can_fuel = true;
                    }

                    $fuelConsumed = 0;
                    if ($record->can_fuel) {
                        $lastFuel = DB::connection('telematics')
                            ->table('vehicle_telematics')
                            ->select('timestamp', 'mileage', 'fuel_level')
                            ->whereNotNull('fuel_level')
                            ->where('device_number', $record->license_plate)
                            ->whereBetween('timestamp', [Carbon::now()->subMinutes(5)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                            ->orderBy('timestamp', 'DESC')->get();
                        $record->records = $lastFuel;
                        $fuelConsumed = $record->fuel_tank_capacity - ceil($lastFuel->avg('fuel_level'));
                        if ($fuelConsumed < 0) {
                            $fuelConsumed = 0;
                        }
                    }

                    $record->fuel_consumed = $fuelConsumed;
                    return $record;
                });

            $payload['data'] = $vehicles;
            return $this->jsonify($payload, 200);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();

            return $this->jsonify($payload, 500);
        }
    }

    public function getManualLpoVehicles(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Token mismatch';
                return response()->json($payload, 422);
            }

            $today = Carbon::now()->toDateString();
            $branchVehicles = Vehicle::select(
                'id',
                'license_plate_number as license_plate',
                DB::raw("(select count(*) from fuel_entries where vehicle_id = vehicles.id and date(created_at) = '$today') as count")
            )
                ->where('branch_id', $user->restaurant_id)
                // ->having('count', '<', 1)
                ->get();

            $cartonVehicles = Vehicle::select(
                'id',
                'license_plate_number as license_plate',
            )
                ->where('primary_responsibility', VehicleResponsibilityTypes::CartonTruck)
                ->get();

            $payload['data'] = $branchVehicles->merge($cartonVehicles);
            return $this->jsonify($payload, 200);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();

            return $this->jsonify($payload, 500);
        }
    }

    public function generateManualLpo(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Token mismatch';
                return response()->json($payload, 422);
            }

            if (!$request->vehicle_id) {
                return $this->jsonify(['message' => "Vehicle ID field is required"], 422);
            }

            $vehicle = Vehicle::find($request->vehicle_id);

            $records = DB::connection('telematics')
                ->table('vehicle_telematics')
                ->select('timestamp', 'fuel_level')
                ->whereNotNull('fuel_level')
                ->where('device_number', $vehicle->license_plate_number)
                ->whereBetween('timestamp', [Carbon::now()->subMinutes(5)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->orderBy('timestamp', 'DESC')
                ->get();

            $fuelConsumed = $vehicle->fuel_tank_capacity - ($records->avg('fuel_level') ?? 0);

            return $this->jsonify(['message' => 'Fuel checked successfully', 'fuel_consumed' => "{$fuelConsumed}L"], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getFueledVehicles(Request $request): JsonResponse
    {
        // $validator = Validator::make($request->all(), [
        //     'token' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     $error = $this->validationHandle($validator->messages());
        //     return response()->json(['status' => false, 'message' => $error], 422);
        // }
        try {
            $appUrl = env('APP_URL');

            $user = JWTAuth::toUser($request->token);
            $lpos = NewFuelEntry::select(
                'fuel_entries.id as id',
                'vehicles.license_plate_number as license_plate',
                'fuel_entries.lpo_number',
                'fuel_entries.comments',
                'fuel_entries.entry_status',
                'fuel_entries.fuel_station_id',
                'routes.route_name as route',
                'fuel_entries.shift_type',
                'fuel_entries.manual_distance_covered as  distance_covered',
                'fuel_entries.manual_consumption_rate as consumption_rate',
                'fuel_entries.actual_fuel_quantity',
                'fuel_entries.end_shift_odometer as current_mileage',
                'fuel_entries.created_at as fueling_time',
                'fuel_entries.receipt_number',
                'fuel_entries.dashboard_photo',
                'fuel_entries.receipt_photo',
                DB::raw("(DATE(delivery_schedules.actual_delivery_date)) as shift_date")
            )
                ->leftJoin('vehicles', 'vehicles.id', '=', 'fuel_entries.vehicle_id')
                ->leftJoin('delivery_schedules', 'delivery_schedules.id', '=', 'fuel_entries.shift_id')
                ->leftJoin('routes', 'routes.id', '=', 'delivery_schedules.route_id')
                ->where('fueled_by', $user->id)
                //                ->whereNot('fuel_entries.entry_status', 'expired')
                ->whereDate('fuel_entries.created_at', Carbon::parse($request->date)->toDateString());

            //            if ($request->search) {
            //                $lpos = $lpos->where('vehicles.license_plate_number', 'like', '%' . $request->search . '%');
            //            }

            $lpos = $lpos->get()
                ->map(function ($entry) use ($appUrl) {
                    if ($entry->shift_type == 'Route Delivery') {
                        $entry->description = $entry->route;
                    } else {
                        $entry->description = $entry->comments;
                    }
                    $entry->dashboard_photo = "$appUrl/uploads/dashboard_photos/" . $entry->dashboard_photo;
                    $entry->receipt_photo = "$appUrl/uploads/dashboard_photos/" . $entry->receipt_photo;
                    $entry->can_edit = true;
                    //                    if ($entry->entry_status == 'pending' || $entry->entry_status == 'fueled_incomplete') {
                    //                        $entry->can_edit = true;
                    //                    }
                    return $entry;
                });

            return $this->jsonify(['data' => $lpos], 200);
        } catch (Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function listForApi(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];


        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Token mismatch';
                return response()->json($payload, 422);
            }

            $entries = DB::table('fuel_entries')
                ->join('vehicles', 'vehicles.id', '=', 'fuel_entries.vehicle_id')
                ->join('delivery_schedules', 'delivery_schedules.id', '=', 'fuel_entries.shift_id')
                ->join('routes', 'routes.id', '=', 'delivery_schedules.route_id')
                ->select(
                    'new_fuel_entries.distance_covered',
                    'new_fuel_entries.distance_estimate',
                    'new_fuel_entries.fuel_consumed',
                    'vehicles.id',
                    'vehicles.license_plate_number as vehicle',
                    'routes.route_name'
                )
                ->get()
                ->map(function ($record) {
                    $record->distance_covered = "$record->distance_covered Km";
                    $record->distance_estimate = "$record->distance_estimate Km";
                    $record->fuel_consumed = "$record->fuel_consumed L";

                    return $record;
                });

            $payload['data'] = $entries;
            return $this->jsonify($payload, 200);
        } catch (Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();
            //            $payload['message'] = 'A server error was encountered';

            return $this->jsonify($payload, 500);
        }
    }

    public function storeFromApi(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];

        DB::beginTransaction();

        try {
            $lpoEntry = NewFuelEntry::latest()->where('lpo_number', $request->lpo_number)->first();
            if ($lpoEntry->actual_fuel_quantity) {
                return $this->jsonify(['message' => 'LPO entry has already been fueled'], 422);
            }

            $vehicle = Vehicle::find($request->vehicle_id);
            $station = FuelStation::find($request->fuelling_station);
            $user = JWTAuth::toUser($request->token);

            $lastMileage = DB::connection('telematics')
                ->table('vehicle_telematics')
                ->whereNotNull('mileage')
                ->where('device_number', $vehicle->license_plate_number)
                ->whereBetween('timestamp', [Carbon::now()->subMinutes(5)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->orderBy('timestamp', 'DESC')->avg('mileage');

            $lpoEntry->update([
                'entry_status' => FuelEntryStatus::Fueled,
                'fuel_station_id' => $station->id,
                'fuel_price' => $station->fuel_price,
                'fueled_by' => $user->id,
                'end_shift_mileage' => $lastMileage,
                'end_shift_odometer' => $request->current_mileage,
                'manual_distance_covered' => $request->distance_covered,
                'manual_consumption_rate' => $request->consumption_rate,
                'actual_fuel_quantity' => $request->fueled_quantity,
                'receipt_number' => $request->receipt_no,
                'fueling_time' => $request->fueling_time ?? Carbon::now(),
            ]);

            if ($request->file('dashboard_photo')) {
                try {
                    $file = $request->file('dashboard_photo');
                    $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/dashboard_photos/'), $fileName);

                    $lpoEntry->update(['dashboard_photo' => $fileName]);
                } catch (\Throwable $e) {
                    // pass
                }
            }

            if ($request->file('receipt_photo')) {
                try {
                    $file = $request->file('receipt_photo');
                    $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/dashboard_photos/'), $fileName);

                    $lpoEntry->update(['receipt_photo' => $fileName]);
                } catch (\Throwable $e) {
                    // pass
                }
            }

            DB::commit();
            $payload['data'] = $lpoEntry;
            return $this->jsonify($payload, 200);
        } catch (Throwable $e) {
            DB::rollBack();
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();

            return $this->jsonify($payload, 500);
        }
    }

    public function updateFromApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // 'token' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        $payload = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];

        DB::beginTransaction();

        try {
            $lpoEntry = NewFuelEntry::find($request->id);
            if (!$lpoEntry) {
                return response()->json(['status' => false, 'message' => 'LPO Entry not found'], 404);
            }

            $station = FuelStation::find($request->fuelling_station);
            $lpoEntry->update([
                'entry_status' => FuelEntryStatus::Fueled,
                'fuel_station_id' => $station->id,
                'fuel_price' => $station->fuel_price,
                'end_shift_odometer' => $request->current_mileage,
                'manual_distance_covered' => $request->distance_covered,
                'manual_consumption_rate' => $request->consumption_rate,
                'actual_fuel_quantity' => $request->fueled_quantity,
                'receipt_number' => $request->receipt_no,
                'comments' => $request->comments ?? '',
            ]);

            if ($request->file('dashboard_photo')) {
                try {
                    $file = $request->file('dashboard_photo');
                    $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/dashboard_photos/'), $fileName);

                    $lpoEntry->update(['dashboard_photo' => $fileName]);
                } catch (\Throwable $e) {
                }
            }

            if ($request->file('receipt_photo')) {
                try {
                    $file = $request->file('receipt_photo');
                    $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/dashboard_photos/'), $fileName);

                    $lpoEntry->update(['receipt_photo' => $fileName]);
                } catch (\Throwable $e) {
                    // pass
                }
            }

            DB::commit();
            $payload['data'] = $lpoEntry;
            return $this->jsonify($payload, 200);
        } catch (Throwable $e) {
            DB::rollBack();
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();

            return $this->jsonify($payload, 500);
        }
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $entries = DB::table('new_fuel_entries')
            ->orderBy('id', 'DESC')
            ->leftJoin('fuel_lpos', 'fuel_lpos.id', '=', 'new_fuel_entries.fuel_lpo_id')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'fuel_lpos.vehicle_id')
            ->leftJoin('routes', 'routes.id', '=', 'fuel_lpos.route_id')
            ->select('new_fuel_entries.*', 'fuel_lpos.lpo_number', 'fuel_lpos.vehicle_id', 'vehicles.license_plate_number as license_plate', 'routes.route_name')
            ->get()->map(function ($record) {
                $record->date = Carbon::parse($record->created_at)->toDayDateTimeString();
                $record->distance_covered = $record->current_mileage - $record->pre_mileage;
                $record->distance_variance = $record->distance_covered - $record->distance_estimate;
                $record->fuel_variance = $record->fuel_consumed - $record->fuel_estimate;
                return $record;
            });
        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'entries'));
    }
}
