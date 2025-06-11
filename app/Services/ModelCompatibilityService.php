<?php

namespace App\Services;

use App\Model\Route;
use App\Model\User;
use App\Model\WaCustomer;
use App\Model\WaRouteCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Responsible for make models compatible with critical changes, like new field additions, etc.
 */
class ModelCompatibilityService
{

    public function update(): array
    {
        DB::beginTransaction();

        try {
            $route = Route::create([
                'route_name' => 'Many-Shop Route',
                'start_lat' => -1.0348210290863,
                'start_lng' => 37.077470863453,
                'is_physical_route' => 1,
            ]);

            $center = $route->centers()->create([
                'name' => 'Many Shop center',
                'lat' => -1.0348210290863,
                'lng' => 37.077470863453,
                'preferred_center_radius' => 100000,
            ]);

            foreach (range(1, 200) as $number) {
                $route->waRouteCustomer()->create([
                    'created_by' => 1,
                    'customer_id' => 11,
                    'name' => "Shop $number",
                    'bussiness_name' => "Shop $number",
                    'delivery_centres_id' => $center->id,
                    'lat' => -1.0348210290863,
                    'lng' => 37.077470863453,
                    'status' => 'approved'
                ]);
            }

            DB::commit();
            return ['success' => true];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function temp4()
    {
        foreach (Route::with(['centers', 'waRouteCustomer'])->get() as $route) {
            $newCenter = [
                'name' => "$route->route_name CENTER",
                'lat' => $route->start_lat,
                'lng' => $route->start_lng,
                'center_location_name' => "$route->route_name",
                'preferred_center_radius' => 100000
            ];

            $key = env('GOOGLE_MAPS_API_KEY');
            $searchAddress = "$route->route_name KENYA";
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?address=$searchAddress}&region=ke&key=$key");
            if ($response->ok()) {
                $result = json_decode($response->body(), true);
                if ($result['status'] == 'OK') {
                    $newCenter['lat'] = $result['results'][0]['geometry']['location']['lat'];
                    $newCenter['lng'] = $result['results'][0]['geometry']['location']['lng'];
                    $newCenter['preferred_center_radius'] = 5000;
                }
            }
            $createdCenter = $route->centers()->create($newCenter);
            $route->waRouteCustomer()->update(['delivery_centres_id' => $createdCenter->id]);
        }
    }

    public function temp2()
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $fileName = public_path('routes.xlsx');
        $spreadsheet = $reader->load($fileName);
        $data = $spreadsheet->getActiveSheet()->toArray();
        foreach ($data as $row) {
            // Route
            $route = Route::with(['centers', 'waRouteCustomer'])->where('route_name', $row[0])->first();
            if (!$route) {
                $route = Route::create([
                    'route_name' => $row[0],
                    'is_physical_route' => 1,
                    'start_lat' => -1.0348210290862596,
                    'start_lng' => 37.07747086345301,
                    'starting_location_name' => 'Kanini Haraka Main HQ',
                    'order_taking_days' => '2',
                    'delivery_days' => '3',
                    'salesman_proximity' => '100',
                    'route_manager_proximity' => '100',
                    'restaurant_id' => 1,
                ]);
            } else {
                $route->centers()->delete();
            }

            // Center
            $center = [
                'name' => "$route->route_name CENTER",
                'lat' => $route->start_lat,
                'lng' => $route->start_lng,
                'center_location_name' => "$route->route_name",
                'preferred_center_radius' => 100000
            ];

            $key = env('GOOGLE_MAPS_API_KEY');
            $searchAddress = "$route->route_name KENYA";
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?address=$searchAddress}&region=ke&key=$key");
            if ($response->ok()) {
                $result = json_decode($response->body(), true);
                if ($result['status'] == 'OK') {
                    $center['lat'] = $result['results'][0]['geometry']['location']['lat'];
                    $center['lng'] = $result['results'][0]['geometry']['location']['lng'];
                    $center['preferred_center_radius'] = 5000;
                }
            }

            $createdCenter = $route->centers()->create($center);
            $route->waRouteCustomer()->update(['delivery_centres_id' => $createdCenter->id]);

            // Manager
            $createManager = true;
            $managerName = $row[1];
            if ($existingManager = User::with('routes')->where('name', $managerName)->first()) {
                if ($existingManager->role_id == 5) {
                    $createManager = false;
                    $managerRoutes = $existingManager->routes->pluck('id')->toArray();
                    if (!in_array($route->id, $managerRoutes)) {
                        $existingManager->routes()->attach($route->id);
                    }
                } else {
                    $managerName = "$managerName ROUTE MANAGER";
                }
            }

            if ($createManager) {
                $manager = User::create([
                    'name' => $managerName,
                    'password' => Hash::make('password'),
                    'restaurant_id' => 1,
                    'wa_department_id' => 3,
                    'wa_location_and_store_id' => 6,
                    'role_id' => 5,
                    'status' => '1',
                ]);

                $manager->routes()->attach($route->id);
            }

            // Salesman
            $createSalesman = true;
            $salesmanName = $row[2];
            if ($existingSalesman = User::with('routes')->where('name', $salesmanName)->first()) {
                if ($existingSalesman->role_id == 4) {
                    $createSalesman = false;
                    $salesmanRoutes = $existingSalesman->routes->pluck('id')->toArray();
                    if (!in_array($route->id, $salesmanRoutes)) {
                        $existingSalesman->routes()->attach($route->id);
                    }
                } else {
                    $salesmanName = "$salesmanName SALESMAN";
                }
            }

            if ($createSalesman) {
                $salesman = User::create([
                    'name' => $salesmanName,
                    'phone_number' => "0$row[3]",
                    'password' => Hash::make('password'),
                    'restaurant_id' => 1,
                    'wa_department_id' => 3,
                    'wa_location_and_store_id' => 6,
                    'role_id' => 4,
                    'status' => '1',
                ]);

                $salesman->routes()->attach($route->id);
            }

            // Dispatcher
            $createDispatcher = true;
            $dispatcherName = $row[4];
            if ($existingDispatcher = User::with('routes')->where('name', $dispatcherName)->first()) {
                if ($existingDispatcher->role_id == 7) {
                    $createDispatcher = false;
                    $dispatcherRoutes = $existingDispatcher->routes->pluck('id')->toArray();
                    if (!in_array($route->id, $dispatcherRoutes)) {
                        $existingDispatcher->routes()->attach($route->id);
                    }
                } else {
                    $dispatcherName = "$dispatcherName DISPATCHER";
                }
            }

            if ($createDispatcher) {
                $dispatcher = User::create([
                    'name' => $dispatcherName,
                    'password' => Hash::make('password'),
                    'restaurant_id' => 1,
                    'wa_department_id' => 3,
                    'wa_location_and_store_id' => 6,
                    'role_id' => 7,
                    'status' => '1',
                ]);

                $dispatcher->routes()->attach($route->id);
            }

            // Driver
            $createDriver = true;
            $driverName = $row[5];
            if ($existingDriver = User::with('routes')->where('name', $driverName)->first()) {
                if ($existingDriver->role_id == 6) {
                    $createDriver = false;
                    $driverRoutes = $existingDriver->routes->pluck('id')->toArray();
                    if (!in_array($route->id, $driverRoutes)) {
                        $existingDriver->routes()->attach($route->id);
                    }
                } else {
                    $driverName = "$driverName DRIVER";
                }
            }

            if ($createDriver) {
                $driver = User::create([
                    'name' => $driverName,
                    'phone_number' => "0$row[7]",
                    'password' => Hash::make('password'),
                    'restaurant_id' => 1,
                    'wa_department_id' => 3,
                    'wa_location_and_store_id' => 6,
                    'role_id' => 6,
                    'status' => '1',
                ]);

                $driver->routes()->attach($route->id);
            }
        }
    }

    public function temp()
    {
        // Fill Distance Estimate for shops
        $shops = WaRouteCustomer::with('route')->where('distance_estimate', 0)->get()->filter(function ($shop) {
            return $shop->has_valid_location;
        });

        foreach ($shops as $shop) {
            $route = $shop->route;
            $startLat = $route->start_lat;
            $startLng = $route->start_lng;
            $endLat = $shop->lat;
            $endLng = $shop->lng;
            $shop->update([
                'distance_estimate' => MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng)
            ]);
        }

        // Get Route Polylines
        $routes = Route::with('waRouteCustomer')->get()->filter(function ($route) {
            return $route->has_valid_location;
        });

        foreach ($routes as $route) {
            $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get()->filter(function ($shop) {
                return $shop->has_valid_location;
            });

            $numberOfWaypointGroups = ceil($shops->count() / 25);

            $skip = 0;
            $waypointGroups = [];
            for ($counter = 1; $counter <= $numberOfWaypointGroups; $counter++) {
                $waypointGroups[] = $shops->skip($skip)->take(25);
                $skip += 25;
            }

            $startLat = $route->start_lat;
            $startLng = $route->start_lng;
            foreach ($waypointGroups as $group) {
                $lastShop = $group->last();
                $group->pop();
                $waypoints = [];
                foreach ($group as $shop) {
                    $waypoints[] = [
                        "location" => [
                            "latLng" => [
                                "latitude" => $shop->lat,
                                "longitude" => $shop->lng
                            ]
                        ],
                        "vehicleStopover" => true
                    ];
                }

                $response = MappingService::getRoute($startLat, $startLng, $lastShop->lat, $lastShop->lng, waypoints: $waypoints);
                if (isset($response['routes'][0]['polyline']['encodedPolyline'])) {
                    $polyline = $route->polylines()->create([
                        'polyline' => $response['routes'][0]['polyline']['encodedPolyline'],
                    ]);

                    if (isset($response['routes'][0]['optimizedIntermediateWaypointIndex'])) {
                        $polyline->update(['waypoint_order' => implode(",", $response['routes'][0]['optimizedIntermediateWaypointIndex'])]);
                    }

                }

                $startLat = $lastShop->lat;
                $startLng = $lastShop->lng;
            }
        }


        // Sections
        foreach (Route::with(['waRouteCustomer', 'sections'])->get() as $route) {
            $route->sections()->delete();
            $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get()
                ->filter(function ($shop) {
                    return $shop->has_valid_location;
                })->map(function (WaRouteCustomer $shop) use ($route) {
                    return [
                        'id' => $shop->id,
                        'lat' => (float)$shop->lat,
                        'lng' => (float)$shop->lng,
                        'name' => $shop->bussiness_name,
                    ];
                });

            $lastShop = null;
            for ($counter = 0; $counter < count($shops); $counter++) {
                $currentShop = $shops[$counter];
                $startLat = $counter == 0 ? $route->start_lat : $lastShop['lat'];
                $startLng = $counter == 0 ? $route->start_lng : $lastShop['lng'];
                $endLat = $currentShop['lat'];
                $endLng = $currentShop['lng'];

                $distanceEstimate = MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng);
                $timeEstimate = MappingService::getDurationBetweenPoints($startLat, $startLng, $endLat, $endLng);
                $route->sections()->create([
                    'start_shop_id' => $counter == 0 ? null : $lastShop['id'],
                    'start_lat' => $startLat,
                    'start_lng' => $startLng,
                    'end_shop_id' => $currentShop['id'],
                    'end_lat' => $endLat,
                    'end_lng' => $endLng,
                    'start_point_is_plan_start_point' => $counter == 0,
                    'fuel_estimate' => 0,
                    'road_type' => null,
                    'road_condition' => null,
                    'rainy_fuel_estimate' => 0,
                    'rainy_road_type' => null,
                    'rainy_road_condition' => null,
                    'distance_estimate' => $distanceEstimate,
                    'rainy_distance_estimate' => $distanceEstimate,
                    'time_estimate' => $timeEstimate,
                    'rainy_time_estimate' => $timeEstimate,
                ]);

                $lastShop = $currentShop;
            }
        }
    }

    public function temp3()
    {
        WaCustomer::query()->delete();

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $fileName = public_path('customers.xlsx');
        $spreadsheet = $reader->load($fileName);
        $data = $spreadsheet->getActiveSheet()->toArray();
        foreach ($data as $row) {
            // Route
            $route = Route::with(['centers', 'waRouteCustomer'])->find($row[5]);

            $customer = new WaCustomer();
            $customer->customer_code = getCodeWithNumberSeries('CUSTOMERS');
            $customer->customer_name = $route->route_name;
            $customer->credit_limit = 0;
            $customer->route_id = $route->id;
            $customer->save();
            updateUniqueNumberSeries('CUSTOMERS', $customer->customer_code);

            // Reset centers, reserve customers first
            $routeCustomers = $route->waRouteCustomer;
            $route->centers()->delete();
            $newCenter = [
                'name' => "$route->route_name CENTER",
                'lat' => $route->start_lat,
                'lng' => $route->start_lng,
                'center_location_name' => "$route->route_name",
                'preferred_center_radius' => 100000
            ];

            $key = env('GOOGLE_MAPS_API_KEY');
            $searchAddress = "$route->route_name KENYA";
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?address=$searchAddress}&region=ke&key=$key");
            if ($response->ok()) {
                $result = json_decode($response->body(), true);
                if ($result['status'] == 'OK') {
                    $newCenter['lat'] = $result['results'][0]['geometry']['location']['lat'];
                    $newCenter['lng'] = $result['results'][0]['geometry']['location']['lng'];
                    $newCenter['preferred_center_radius'] = 5000;
                }
            }
            $createdCenter = $route->centers()->create($newCenter);

            // resave customers
            foreach ($routeCustomers as $deletedRouteCustomer) {
                $shop = new WaRouteCustomer();
                $shop->route_id = $route->id;
                $shop->delivery_centres_id = $createdCenter->id;
                $shop->customer_id = $customer->id;
                $shop->created_by = $deletedRouteCustomer->created_by;
                $shop->name = $deletedRouteCustomer->name;
                $shop->bussiness_name = $deletedRouteCustomer->bussiness_name;
                $shop->phone = $deletedRouteCustomer->phone;
                $shop->town = $createdCenter->name;
                $shop->location_name = $createdCenter->name;
                $shop->lat = $deletedRouteCustomer->lat;
                $shop->lng = $deletedRouteCustomer->lng;
                $shop->account_number = $deletedRouteCustomer->account_number;
                $shop->status = $deletedRouteCustomer->status;
                $shop->image_url = $deletedRouteCustomer->image_url;
                $shop->gender = $deletedRouteCustomer->gender;
                $deletedRouteCustomer->save();
            }


            $newShop = new WaRouteCustomer();
            $newShop->route_id = $route->id;
            $newShop->delivery_centres_id = $createdCenter->id;
            $newShop->customer_id = $customer->id;
            $newShop->created_by = 0;
            $newShop->name = $row[1];
            $newShop->bussiness_name = $row[1];
            $newShop->phone = "0$row[3]";
            $newShop->town = $createdCenter->name;
            $newShop->location_name = $createdCenter->name;
            $newShop->lat = $createdCenter->lat;
            $newShop->lng = $createdCenter->lng;
            $newShop->account_number = $row[2];
            $newShop->status = 'unverified';

            $newShop->save();
        }
    }
}