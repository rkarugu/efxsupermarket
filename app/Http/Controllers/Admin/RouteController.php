<?php

namespace App\Http\Controllers\Admin;

use Polyline;
use Carbon\Carbon;
use App\Model\User;
use App\Model\Route;
use function Psy\sh;
use App\RouteSection;
use GuzzleHttp\Client;
use App\Model\Restaurant;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Exports\RoutesExport;
use App\Jobs\GetRouteSections;
use App\Model\DeliveryCentres;
use App\Model\WaRouteCustomer;
use App\Jobs\GetRoutePolylines;
use App\Services\MappingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Exports\RouteTargetsSummary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Queue;
use App\Http\Requests\NewRouteRequest;
use Illuminate\Support\Facades\Validator;
use App\Jobs\GetRouteShopDistanceEstimates;
use App\Models\RouteRepresentatives;
use App\Models\RouteSupervisors;

class RouteController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'manage-routes';
        $this->base_route = 'manage-routes';
        $this->resource_folder = 'admin.routes';
        $this->base_title = 'Manage Routes';
        $this->pmodule = 'manage-routes';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;
        $googleMapsApiKey = config('app.google_maps_api_key');
        $permission = $this->mypermissionsforAModule();
        $user = getLoggeduserProfile();
        $pmodule = $this->pmodule;

        $query = Route::withCount(['centers', 'waRouteCustomer as shops_count'])
            ->withSum('sections as total_distance', 'distance_estimate')->with(['centers', 'waRouteCustomer', 'polylines']);

        // Branch level display, except for admins
        // if ($user->role_id != 1) {
        //     $query = $query->where('restaurant_id', $user->restaurant_id);
        // }
        if (isset($permission[$pmodule . '___view-all-routes']) || $user->role_id == 1) {
            $query = $query;
        } else {
            $query = $query->where('restaurant_id', $user->restaurant_id);
        }

        $routeList = $query->orderBy('route_name')->get()->map(function (Route $route) {
            $route->branch = Restaurant::select('name')->find($route->restaurant_id)?->name;
            $route->total_distance = round(($route->total_distance / 1000), 2);

            return $route;
        });

        return view("$this->resource_folder.index_new", compact('title', 'breadcum', 'base_route', 'model', 'googleMapsApiKey', 'routeList'));
    }

    public function listing(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = 'route-manager-listing';
        $base_route = $this->base_route;
        $googleMapsApiKey = config('app.google_maps_api_key');
        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if ($isAdmin || isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::all();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }
        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'googleMapsApiKey', 'branches', 'isAdmin'));
    }

    public function routeTonnageSummary(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;
        $routes = Route::with(['sections', 'branch'])->orderBy('route_name')->get();
        $routes = $routes->map(function (Route $route) {
            $timeEstimate = $route->sections->sum('time_estimate');

            $payload = [
                'route_name' => $route->route_name,
                'sales_target' => number_format($route->sales_target, 2),
                'tonnage_target' => $route->tonnage_target . "T",
                'ctns_target' => $route->ctn_target,
                'dzns_target' => $route->dzn_target,
                'fuel_est' => $route->manual_fuel_estimate,
                'travel_expense' => number_format($route->travel_expense, 2),
                'salesman' => $route->salesman() ? $route->salesman()->name : 'Not Assigned',

            ];

            if ($route->is_physical_route == 0) {
                $payload['sales_target'] = 'N/A';
                $payload['tonnage_target'] = 'N/A';
                $payload['salesman'] = 'N/A';
                $payload['travel_expense'] = 'N/A';
            }

            return $payload;
        });
        if ($request->Download && $request->Download == "Download") {
            $export = new RouteTargetsSummary($routes);
            return Excel::download($export, 'route_target_summary.xlsx');
        }


        return view("$this->resource_folder.route_targets_summary", compact('title', 'breadcum', 'base_route', 'model', 'routes'));
    }

    public function datatable(Request $request): JsonResponse
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        $routes = null;

        if ($isAdmin || isset($permission['employees' . '___view_all_branches_data'])) {
            $routes = Route::with(['sections', 'branch'])->orderBy('route_name');
        } else {
            $routes = Route::where('restaurant_id', $authuser->userRestaurent->id)->with(['sections', 'branch'])->orderBy('route_name');
        }
        if ($request->branch) {
            $routes = $routes->where('restaurant_id', $request->branch);
        }
        $routes = $routes->get();
        $totalRoutes = $routes->count();
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $routes = $routes->filter(function (Route $route) use ($search) {
                return str_contains(strtolower($route->route_name), strtolower($search));
            })->values();
        }

        // $routes = $routes->slice($start)->take($limit);
        $totalFilteredRoutes = $routes->count();

        $routes = $routes->map(function (Route $route, $index) {
            $timeEstimate = $route->sections->sum('time_estimate');

            $payload = [
                'row_number' => $index + 1,
                'id' => $route->id,
                'route_name' => $route->route_name,
                'branch' => $route->branch ? $route->branch->name : '-',
                'order_taking_days' => $this->mapDayValueToDay($route->order_taking_days),
                'salesman' => $route->salesman() ? $route->salesman()->name : 'Not Assigned',
                'route_manager' => $route->routeManager() ? $route->routeManager()->name : 'Not Assigned',
                'travel_expense' => format_amount_with_currency($route->travel_expense),
                'actions' => "<span class='span-action'><a title='Edit' href='/admin/manage-routes/$route->id/edit'><img src='" . asset('assets/admin/images/edit.png') . "'></a></span>",
            ];

            if ($route->is_physical_route == 0) {
                $payload['order_taking_days'] = 'N/A';
                $payload['delivery_days'] = 'N/A';
                $payload['distance'] = 'N/A';
                $payload['duration'] = 'N/A';
                $payload['salesman'] = 'N/A';
                $payload['route_manager'] = 'N/A';
                $payload['travel_expense'] = 'N/A';
            }

            return $payload;
        });

        $responsePayload = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRoutes,
            "recordsFiltered" => $totalFilteredRoutes,
            "data" => $routes
        );

        return response()->json($responsePayload);
    }

    public function create()
    {
        $title = 'Add Route';
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add Route' => ''];
        $model = 'route-manager-listing';
        $base_route = $this->base_route;

        $googleMapsApiKey = config('app.google_maps_api_key');
        $branches = Restaurant::all();

        return view(
            "$this->resource_folder.create",
            compact(
                'title',
                'model',
                'breadcum',
                'base_route',
                'googleMapsApiKey',
                'branches',
            )
        );
    }

    public function store(NewRouteRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $request->validate([
                'route_name' => 'required',
                'restaurant_id' => 'required',
                'is_physical_route' => 'required',
                'is_pos_route' => 'required',
                'starting_location_name' => 'required_if:is_physical_route,1',
                'loading_latitude' => 'required_if:is_physical_route,1',
                'loading_longitude' => 'required_if:is_physical_route,1',
            ]);

            $start_monthly_order_frequency = [];
            $end_monthly_order_frequency = [];

            if ($request->is_physical_route == 1) {
                foreach ($request->monthly_order_frequency as $frequency) {
                    $frequencyParts = explode(' to ', $frequency);
                    if (count($frequencyParts) == 2) {
                        $start_monthly_order_frequency[] = (int)$frequencyParts[0];
                        $end_monthly_order_frequency[] = (int)$frequencyParts[1];
                    } else {
                        $start_monthly_order_frequency[] = 0;
                        $end_monthly_order_frequency[] = (int)$frequency;
                    }
                }
            }

            $route = Route::create([
                'route_name' => $request->route_name,
                'restaurant_id' => $request->restaurant_id,
                'is_physical_route' => $request->is_physical_route,
                'is_pos_route' => $request->is_pos_route,
                'group' => $request->group,
                'order_frequency' => $request->order_frequency,
                'start_monthly_order_frequency' => implode(',', $start_monthly_order_frequency),
                'end_monthly_order_frequency' => implode(',', $end_monthly_order_frequency),
            ]);

            if ($route->is_physical_route == 1) {
                $route->update([
                    'starting_location_name' => $request->starting_location_name,
                    'start_lat' => $request->loading_latitude,
                    'start_lng' => $request->loading_longitude,
                    'tonnage_target' => $request->tonnage_target ?? 0,
                    'sales_target' => $request->sales_target ?? 0,
                    'order_taking_days' => $request->order_taking_days ? implode(',', $request->order_taking_days) : null,
                    'salesman_proximity' => $request->salesman_proximity ?? 0,
                    'route_manager_proximity' => $request->salesman_proximity ?? 0,
                    'manual_fuel_estimate' => $request->manual_fuel_estimate ?? 0,
                    'ctn_target' => $request->ctn_target ?? 0,
                    'dzn_target' => $request->dzn_target ?? 0,
                    'travel_expense' => $request->travel_expense ?? 0,
                    'offsite_shift_allowance' => $request->offsite_shift_allowance ?? 0,
                    // 'offsite_allowance' => $request->offsite_allowance ?? 200,
                    'maximum_allowed_shifts' => $request->maximum_shifts ?? 1,
                    'estimated_shift_time' => $request->estimated_shift_time,
                ]);
            }

            DB::commit();
            return $this->jsonify(['data' => $route], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'route_name' => 'required',
                'restaurant_id' => 'required',
                'is_physical_route' => 'required',
                'starting_location_name' => 'required_if:is_physical_route,1',
                'loading_latitude' => 'required_if:is_physical_route,1',
                'loading_longitude' => 'required_if:is_physical_route,1',
            ]);

            $start_monthly_order_frequency = [];
            $start_monthly_order_frequency_object = null;
            $end_monthly_order_frequency = [];
            $end_monthly_order_frequencyobject = null;

            if (is_array($request->monthly_order_frequency)) {
                foreach ($request->monthly_order_frequency as $frequency) {
                    $frequencyParts = explode(' to ', $frequency);
                    if (count($frequencyParts) == 2) {
                        $start_monthly_order_frequency[] = (int)$frequencyParts[0];
                        $end_monthly_order_frequency[] = (int)$frequencyParts[1];
                    } else {
                        $start_monthly_order_frequency[] = 0;
                        $end_monthly_order_frequency[] = (int)$frequency;
                    }
                }
            } else {
                $start_monthly_order_frequency_object = null;
                $end_monthly_order_frequencyobject = null;
            }

            $route = Route::find($request->id);
            $route->update([
                'route_name' => $request->route_name,
                'restaurant_id' => $request->restaurant_id,
                'is_physical_route' => $request->is_physical_route,
                'is_pos_route' => $request->is_pos_route,
                'group' => $request->group,
                'order_frequency' => $request->order_frequency,
                'start_monthly_order_frequency' => is_array($request->monthly_order_frequency) ? implode(',', $start_monthly_order_frequency) : $request->start_monthly_order_frequency,
                'end_monthly_order_frequency' => is_array($request->monthly_order_frequency) ? implode(',', $end_monthly_order_frequency) : $request->end_monthly_order_frequency,
            ]);

            if ($route->is_physical_route == 1) {
                $route->update([
                    'starting_location_name' => $request->starting_location_name,
                    'start_lat' => $request->loading_latitude,
                    'start_lng' => $request->loading_longitude,
                    'tonnage_target' => $request->tonnage_target ?? 0,
                    'sales_target' => $request->sales_target ?? 0,
                    'order_taking_days' => $request->order_taking_days ? implode(',', $request->order_taking_days) : null,
                    'salesman_proximity' => $request->salesman_proximity ?? 0,
                    'route_manager_proximity' => $request->salesman_proximity ?? 0,
                    'manual_fuel_estimate' => $request->manual_fuel_estimate ?? 0,
                    'manual_distance_estimate' => $request->manual_distance_estimate ?? 0,
                    'manual_rate_estimate' => $request->manual_rate_estimate ?? 0,
                    'estimate_tonnage' => $request->estimate_tonnage ?? 0,
                    'ctn_target' => $request->ctn_target ?? 0,
                    'dzn_target' => $request->dzn_target ?? 0,
                    'travel_expense' => $request->travel_expense ?? 0,
                    'offsite_shift_allowance' => $request->offsite_shift_allowance ?? 0,
                    // 'offsite_allowance' => $request->offsite_allowance ?? 200,
                    'maximum_allowed_shifts' => $request->maximum_shifts ?? 1,
                    'estimated_shift_time' => $request->estimated_shift_time,
                ]);

                RouteRepresentatives::where('route_id',$request->id)->delete();
                RouteRepresentatives::create([
                    'route_id' => $request->id,
                    'user_id' => $request->representative,
                    'created_by' => Auth::user()->id
                ]);
            }
            
            if ($request->supervisor) {
                RouteSupervisors::where('route_id',$request->id)->delete();
                foreach ($request->supervisor as $value) {
                    RouteSupervisors::create([
                        'route_id' => $request->id,
                        'user_id' => $value,
                        'created_by' => Auth::user()->id
                    ]);
                }
            }
            

            DB::commit();
            return $this->jsonify(['data' => $route], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getMapViewRouteStats(Request $request): JsonResponse
    {
        $logged_user_info = User::find($request->user_id);
        $list = $logged_user_info->rolePermissions;
        $permission = array();
        foreach ($list as $data) {
            $permission[$data->module_name . '___' . $data->module_action] = $data->module_action;
        }
        $pmodule = $this->pmodule;
        try {

            $response = [
                'total_routes' => 0,
                'total_customers' => 0,
                'total_sales_target' => 0,
                'total_tonnage_target' => 0
            ];
            $query = Route::select('sales_target', 'tonnage_target')->withCount(['waRouteCustomer as shops'])
                ->withSum('sections as total_distance', 'distance_estimate');

            // Branch level display, except for admins
            // if ($request->user_role_id != 1) {
            //     $query = $query->where('restaurant_id', $request->user_restaurant_id);
            // }
            if (isset($permission[$pmodule . '___view-all-routes']) || $logged_user_info->role_id == 1) {
                $query = $query;
            } else {
                $query = $query->where('restaurant_id', $request->user_restaurant_id);
            }

            $routes = $query->get();
            $response['total_routes'] = $routes->count();
            $response['total_customers'] = $routes->sum('shops');
            $response['total_sales_target'] = $routes->sum('sales_target');
            $response['total_tonnage_target'] = $routes->sum('tonnage_target');

            $response['total_sales_target'] = format_amount_with_currency($response['total_sales_target']);

            return $this->jsonify(['data' => $response], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function getRoutePolylines(Request $request): JsonResponse
    {
        try {
            $route = Route::with(['waRouteCustomer', 'sections', 'centers'])->find($request->route_id);
            if (!$this->routeLocationIsValid($route)) {
                return $this->jsonify(['invalid_location' => true], 422);
            }

            $polylines = [];
            $shops = $route->waRouteCustomer()->whereNotNull('lat')->whereNotNull('lng')->orderBy('distance_estimate')->get();
            $shopsCount = $shops->count();
            if ($shopsCount > 0) {
                $legs = ceil($shops->count() / 25);
                if ($legs == 1) {
                    $lastShop = $shops->pop();
                    $waypoints = $shops->map(function (WaRouteCustomer $shop) {
                        return [
                            "location" => [
                                "latLng" => [
                                    "latitude" => $shop->lat,
                                    "longitude" => $shop->lng
                                ]
                            ],
                            "vehicleStopover" => true
                        ];
                    });

                    $response = MappingService::getRoute($route->start_lat, $route->start_lng, $lastShop->lat, $lastShop->lng, waypoints: $waypoints);
                    if ($response) {
                        $polylines[] = $response;
                    }
                }
            }

            return $this->jsonify(['data' => $polylines], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function getRouteCenters(Request $request): JsonResponse
    {
        try {
            $centers = DeliveryCentres::where('route_id', $request->route_id)->get();
            return $this->jsonify(['data' => $centers], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function getRouteShops(Request $request): JsonResponse
    {
        try {
            $shops = WaRouteCustomer::select('route_id', 'lat', 'lng', 'bussiness_name')->where('route_id', $request->route_id)->get();
            return $this->jsonify(['data' => $shops], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    private function routeLocationIsValid(Route $route): bool
    {
        return ($route->start_lat) && ($route->start_lat != 0) && ($route->start_lng) && ($route->start_lng != 0);
    }

    public function manageRouteLinkedCentersList($routeId)
    {
        $title = 'Manage Route Delivery Centers';
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add Route' => ''];
        $model = $this->model;
        $base_route = $this->base_route;
        $route = Route::find($routeId);

        $googleMapsApiKey = config('app.google_maps_api_key');
        $centers = DeliveryCentres::select(['id', 'name', 'created_at', 'center_location_name', 'preferred_center_radius'])->with('waRouteCustomers')
            ->where('route_id', $routeId)->get();
        return view(
            "$this->resource_folder.manage-route-centers-list",
            compact(
                'title',
                'model',
                'breadcum',
                'base_route',
                'googleMapsApiKey',
                'route',
                'centers',
            )
        );
    }

    public function show(int $id)
    {
        $route = Route::with(['centers', 'centers.waRouteCustomers'])->find($id);

        $route_sections = RouteSection::where('route_id', $route->id)->get();

        $breadcum = [$this->base_title => route("$this->base_route.index"), "$route->route_name" => ''];
        $title = "$route->route_name Route Plan";
        $googleMapsApiKey = config('app.google_maps_api_key');

        $routeMasterData = [
            'route_id' => $route->id,
            'route_name' => $route->route_name,
            'starting_location_name' => $route->starting_location_name,
            'starting_lat' => (float)$route->start_lat,
            'starting_lng' => (float)$route->start_lng,
            'end_lat' => 0,
            'end_lng' => 0,
            'route_centers' => $route->centers->map(function (DeliveryCentres $centre) use ($route) {
                return [
                    'id' => $centre->id,
                    'name' => $centre->name,
                    'lat' => (float)$centre->lat,
                    'lng' => (float)$centre->lng,
                    'preferred_radius' => $centre->preferred_center_radius,
                    'shops' => $centre->waRouteCustomers->map(function (WaRouteCustomer $shop) use ($route) {
                        return [
                            'id' => $shop->id,
                            'lat' => (float)$shop->lat,
                            'lng' => (float)$shop->lng,
                            'name' => $shop->bussiness_name,
                        ];
                    })
                ];
            }),
            'sections' => $route_sections,

        ];

        $lastShop = RouteSection::where('route_id', $route->id)->first();
        if ($lastShop) {
            $routeMasterData['end_lat'] = $lastShop['end_lat'];
            $routeMasterData['end_lng'] = $lastShop['end_lng'];
        }
        // dd($routeMasterData);
        return view("$this->resource_folder.show", [
            'title' => $title,
            'breadcum' => $breadcum,
            'model' => $this->model,
            'route' => $route,
            'base_route' => $this->base_route,
            'route_plan_data' => $routeMasterData,
            'googleMapsApiKey' => $googleMapsApiKey
        ]);
    }

    public function oldshow(int $id)
    {
        $route = Route::with(['centers', 'centers.waRouteCustomers'])->find($id);
        $route->sections()->delete();
        $breadcum = [$this->base_title => route("$this->base_route.index"), "$route->route_name" => ''];
        $title = "$route->route_name Route Plan";
        $googleMapsApiKey = config('app.google_maps_api_key');

        $routeMasterData = [
            'route_id' => $route->id,
            'route_name' => $route->route_name,
            'starting_location_name' => $route->starting_location_name,
            'starting_lat' => (float)$route->start_lat,
            'starting_lng' => (float)$route->start_lng,
            'end_lat' => 0,
            'end_lng' => 0,
            'route_centers' => $route->centers->map(function (DeliveryCentres $centre) use ($route) {
                return [
                    'id' => $centre->id,
                    'name' => $centre->name,
                    'lat' => (float)$centre->lat,
                    'lng' => (float)$centre->lng,
                    'preferred_radius' => $centre->preferred_center_radius,
                    'shops' => $centre->waRouteCustomers->map(function (WaRouteCustomer $shop) use ($route) {
                        return [
                            'id' => $shop->id,
                            'lat' => (float)$shop->lat,
                            'lng' => (float)$shop->lng,
                            'name' => $shop->bussiness_name,
                        ];
                    })
                ];
            }),
            'sections' => [],
        ];

        // Arrange shops based on distance from starting point
        $allShops = [];
        foreach ($routeMasterData['route_centers'] as $route_center) {
            foreach ($route_center['shops'] as $shop) {
                $distanceFromStartingPoint = 0;
                $startingLocationLat = (float)$route->start_lat;
                $startingLocationLng = (float)$route->start_lng;
                $shopLat = $shop['lat'];
                $shopLng = $shop['lng'];
                //                dd("$googleMapsApiKey, $startingLocationLat, $startingLocationLng, $shopLat, $shopLng");

                $distanceFromStartingPointResult = $this->getDurationOrDistanceBetweenPoints($startingLocationLat, $startingLocationLng, $shopLat, $shopLng);
                if ($distanceFromStartingPointResult) {
                    if (isset($distanceFromStartingPointResult['distance'])) {
                        if (isset($distanceFromStartingPointResult['distance']['value'])) {
                            $distanceFromStartingPoint = $distanceFromStartingPointResult['distance']['value'];
                        }
                    }
                }

                $shop['distance'] = $distanceFromStartingPoint;
                $allShops[] = $shop;
            }
        }

        $sortedShops = collect($allShops)->sortBy('distance');
        $allShops = $sortedShops->values();

        // Route sections data
        $lastShop = null;
        for ($counter = 0; $counter < count($allShops); $counter++) {
            $currentShop = $allShops[$counter];
            $section = [
                'id' => null,
                'starting_point' => [
                    'name' => null,
                    'shop_id' => null,
                    'start_point_is_plan_start_point' => false,
                    'lat' => 0,
                    'lng' => 0,
                ],
                'end_point' => [
                    'name' => null,
                    'shop_id' => null,
                    'lat' => 0,
                    'lng' => 0,
                ],
                'fuel_estimate' => 0,
                'distance_estimate' => 0,
                'time_estimate' => 0,
                'road_type' => null,
                'road_condition' => null,
                'rainy_fuel_estimate' => 0,
                'rainy_distance_estimate' => 0,
                'rainy_time_estimate' => 0,
                'rainy_road_type' => null,
                'rainy_road_condition' => null,
            ];

            if ($counter == 0) {
                $section['starting_point']['name'] = $routeMasterData['starting_location_name'];
                $section['starting_point']['start_point_is_plan_start_point'] = true;
                $section['starting_point']['lat'] = $routeMasterData['starting_lat'];
                $section['starting_point']['lng'] = $routeMasterData['starting_lng'];
            } else {
                $section['starting_point']['name'] = $lastShop['name'];
                $section['starting_point']['shop_id'] = $lastShop['id'];
                $section['starting_point']['lat'] = $lastShop['lat'];
                $section['starting_point']['lng'] = $lastShop['lng'];
            }

            $section['end_point']['name'] = $currentShop['name'];
            $section['end_point']['shop_id'] = $currentShop['id'];
            $section['end_point']['lat'] = $currentShop['lat'];
            $section['end_point']['lng'] = $currentShop['lng'];

            $fillingSection = null;
            $savedSection = $route->sections()
                ->where('start_shop_id', '=', $section['starting_point']['shop_id'])
                ->where('end_shop_id', '=', $section['end_point']['shop_id'])
                ->first();

            if (!$savedSection) {
                $newSection = $route->sections()->create([
                    'start_shop_id' => $section['starting_point']['shop_id'],
                    'start_lat' => $section['starting_point']['lat'],
                    'start_lng' => $section['starting_point']['lng'],
                    'end_shop_id' => $section['end_point']['shop_id'],
                    'end_lat' => $section['end_point']['lat'],
                    'end_lng' => $section['end_point']['lng'],
                    'start_point_is_plan_start_point' => $section['starting_point']['start_point_is_plan_start_point'],
                    'fuel_estimate' => 0,
                    'road_type' => null,
                    'road_condition' => null,
                    'rainy_fuel_estimate' => 0,
                    'rainy_road_type' => null,
                    'rainy_road_condition' => null,
                ]);

                // Update time & distance estimates
                $estimates = $this->getDurationOrDistanceBetweenPoints(
                    originLat: $newSection->start_lat,
                    originLng: $newSection->start_lng,
                    destinationLat: $newSection->end_lat,
                    destinationLng: $newSection->end_lng
                );

                $distanceEstimate = $this->extractDistanceFromResponse($estimates);
                $durationEstimate = $this->extractTimeFromResponse($estimates);

                $newSection->update([
                    'distance_estimate' => $distanceEstimate,
                    'rainy_distance_estimate' => $distanceEstimate,
                    'time_estimate' => $durationEstimate,
                    'rainy_time_estimate' => $durationEstimate,
                ]);

                $fillingSection = $newSection;
            } else {
                if (($savedSection->distance_estimate == 0) || ($savedSection->time_estimate == 0)) {
                    $estimates = $this->getDurationOrDistanceBetweenPoints(
                        originLat: $savedSection->start_lat,
                        originLng: $savedSection->start_lng,
                        destinationLat: $savedSection->end_lat,
                        destinationLng: $savedSection->end_lng
                    );

                    $distanceEstimate = $this->extractDistanceFromResponse($estimates);
                    $durationEstimate = $this->extractTimeFromResponse($estimates);

                    $savedSection->update([
                        'distance_estimate' => $distanceEstimate,
                        'rainy_distance_estimate' => $distanceEstimate,
                        'time_estimate' => $durationEstimate,
                        'rainy_time_estimate' => $durationEstimate,
                    ]);
                }

                $fillingSection = $savedSection;
            }

            $section['time_estimate'] = $fillingSection->time_estimate;
            $section['rainy_time_estimate'] = $fillingSection->rainy_time_estimate;
            $section['distance_estimate'] = $fillingSection->distance_estimate;
            $section['rainy_distance_estimate'] = $fillingSection->rainy_distance_estimate;
            $section['fuel_estimate'] = $fillingSection->fuel_estimate;
            $section['rainy_fuel_estimate'] = $fillingSection->rainy_fuel_estimate;
            $section['road_condition'] = $fillingSection->rainy_fuel_estimate;
            $section['rainy_road_condition'] = $fillingSection->rainy_road_condition;
            $section['road_type'] = $fillingSection->road_type;
            $section['rainy_road_type'] = $fillingSection->rainy_road_type;

            $routeMasterData['sections'][] = $section;
            $lastShop = $currentShop;
            $routeMasterData['end_lat'] = $lastShop['lat'];
            $routeMasterData['end_lng'] = $lastShop['lng'];
        }

        return view("$this->resource_folder.show", [
            'title' => $title,
            'breadcum' => $breadcum,
            'model' => $this->model,
            'base_route' => $this->base_route,
            'route_plan_data' => $routeMasterData,
            'googleMapsApiKey' => $googleMapsApiKey
        ]);
    }

    private function extractDistanceFromResponse($response): float|int
    {
        if (isset($response['distance'])) {
            if (isset($response['distance']['value'])) {
                return round((($response['distance']['value']) / 1000), 1);
            }
        }

        return 0;
    }

    private function extractTimeFromResponse($response): float|int
    {
        if (isset($response['duration'])) {
            if (isset($response['duration']['value'])) {
                return ceil((($response['duration']['value']) / 60));
            }
        }

        return 0;
    }

    public function edit($id)
    {
        $route = Route::with(['centers','currentRepresentative','currentSupervisor','supervisors'])->find($id);
        $orderTakingDays = [];
        if ($route->order_taking_days) {
            $orderTakingDays = explode(',', $route->order_taking_days);
        }
        $route->order_taking_days = $orderTakingDays;

        $title = "Update $route->route_name Route";
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Update Route' => ''];
        $model = 'route-manager-listing';
        $base_route = $this->base_route;

        $googleMapsApiKey = config('app.google_maps_api_key');
        $branches = Restaurant::all();

        return view("$this->resource_folder.edit", compact('title', 'model', 'breadcum', 'base_route', 'googleMapsApiKey', 'route', 'branches'));
    }

    public function getEditRouteCentersList(Request $request, $routeId)
    {
        $limit = $request->input('length');
        $start = $request->input('start');

        $query = DeliveryCentres::select(['id', 'name', 'created_at', 'center_location_name', 'preferred_center_radius'])->with('waRouteCustomers')
            ->where('route_id', $routeId);


        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('center_location_name', 'like', "%$search%");
            });
        }
        $deliveryCentres = $query->get();

        $deliveryCentres = $deliveryCentres->map(function (DeliveryCentres $deliveryCentre) use ($request) {

            $deliveryCentre->points = $deliveryCentre->lat . ',' . $deliveryCentre->lng;
            $deliveryCentre->action_links = $this->generateActionLinks($deliveryCentre);

            return $deliveryCentre;
        });

        $responsePayload = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => count($deliveryCentres),
            "recordsFiltered" => count($deliveryCentres),
            "data" => $deliveryCentres
        );

        return response()->json($responsePayload);
    }

    protected function generateActionLinks(DeliveryCentres $deliveryCenter): string
    {
        $editLink = route('update-delivery-center-details', $deliveryCenter->id);
        $declineLink = route('delete-delivery-center-details', $deliveryCenter->id);

        return "<a href='$editLink' onclick='return confirmApproval()'>Edit</a> | <a href='$declineLink' class='text-danger' onclick='return confirmDelete()'>Delete</a>";
    }

    public function editRouteDeliveryCenter($centerId)
    {
        return redirect()->back();
    }

    public function getCenterUpdateDetails($centerId)
    {
        $center = DeliveryCentres::select(['id', 'name', 'lat', 'lng', 'center_location_name', 'preferred_center_radius'])->find($centerId);
        if (!$center) {

            return redirect()->back()->withErrors("Delivery Center details not founds");
        }
        return response()->json($center);
    }

    public function manageRouteEditLinkedCenters($centerId)
    {
        $center = DeliveryCentres::select(['id', 'name', 'lat', 'lng', 'center_location_name', 'route_id', 'preferred_center_radius'])->find($centerId);

        if ($center) {
            $title = 'Edit Delivery Center Details';
            $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add Route' => ''];
            $model = $this->model;
            $base_route = $this->base_route;

            $googleMapsApiKey = config('app.google_maps_api_key');
            return view(
                "$this->resource_folder.edit_route_delivery_center_details",
                compact(
                    'title',
                    'model',
                    'breadcum',
                    'base_route',
                    'googleMapsApiKey',
                    'center'
                )
            );
        } else {
            return redirect()->back()->withErrors("Delivery Center details not found check");
        }
    }

    public function deleteRouteDeliveryCenter($centerId)
    {
        return redirect()->back();
    }

    public function updateSections(Request $request, $id): RedirectResponse
    {

        try {
            $route = Route::with(['sections'])->find($id);
            foreach ($route->sections as $section) {
                $section->update([
                    'fuel_estimate' => $request->get("fuel_estimate-$section->id", 0),
                    'distance_estimate' => $request->get("distance_estimate-$section->id", 0),
                    'time_estimate' => $request->get("time_estimate-$section->id", 0),
                    'road_condition' => $request->get("road_condition-$section->id"),
                    'road_type' => $request->get("road_type-$section->id"),
                    'rainy_fuel_estimate' => $request->get("rainy_fuel_estimate-$section->id", 0),
                    'rainy_distance_estimate' => $request->get("rainy_distance_estimate-$section->id", 0),
                    'rainy_time_estimate' => $request->get("rainy_time_estimate-$section->id", 0),
                    'rainy_road_condition' => $request->get("rainy_road_condition-$section->id"),
                    'rainy_road_type' => $request->get("rainy_road_type-$section->id"),
                ]);
            }

            return redirect()->back()->with('success', 'Route plan updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error occurred was encountered. Please try again.');
        }
    }

    public function getAllRoutes(): JsonResponse
    {
        $routes = Route::with(['users'])->get()->map(function (Route $route) {
            $routeSalesman = $route->users->where('role_id', 4)->first();
            $routeManager = $route->users->where('role_id', 5)->first();
            $payload = [
                'id' => $route->id,
                'route_name' => $route->route_name,
                'is_physical_route' => $route->is_physical_route == 1,
                'has_salesman' => $routeSalesman ? true : false,
                'has_route_manager' => $routeManager ? true : false,
            ];

            return $payload;
        });
        return $this->jsonify(['data' => $routes], 200);
    }

    private function mapDayValueToDay(string $dayValues = null): string
    {
        $daysValuesArray = explode(',', $dayValues);
        $daysArray = [];
        foreach ($daysValuesArray as $dayValue) {
            switch ($dayValue) {
                case 0:
                    $daysArray[] = 'Sundays';
                    break;
                case 1:
                    $daysArray[] = 'Mondays';
                    break;
                case 2:
                    $daysArray[] = 'Tuesdays';
                    break;
                case 3:
                    $daysArray[] = 'Wednesdays';
                    break;
                case 4:
                    $daysArray[] = 'Thursdays';
                    break;
                case 5:
                    $daysArray[] = 'Fridays';
                    break;
                case 6:
                    $daysArray[] = 'Saturdays';
                    break;
                default:
                    break;
            }
        }

        return implode(', ', $daysArray);
    }

    public function export()
    {
        $data = Route::with('users')->where('is_physical_route', 1)->get()->map(function (Route $route) {
            $manager = $route->users->where('role_id', 5)->first();
            $salesman = $route->users->where('role_id', 4)->first();

            $payload = [
                'id' => $route->id,
                'route_name' => $route->route_name,
                // 'distance_estimate' => $route->manual_distance_estimate ?? 0,
                // 'fuel_estimate' => $route->manual_fuel_estimate ?? 0,
                // 'rate_estimate' => $route->manual_rate_estimate ?? 0,
                'tonnage_target' => $route->tonnage_target ?? 0,
                'ctn_target' => $route->ctn_target ?? 0,
                'dzn_target' => $route->dzn_target ?? 0,
                'sales_target' => $route->sales_target ?? 0,
                'travel_expense' => $route->travel_expense ?? 0,
                // 'route_manager' => $manager?->name,
                // 'route_manager_phone' => $manager?->phone_number,
                'salesman' => $salesman?->name,
                'salesman_phone' => $salesman?->phone_number,
                'order_taking_days' => $this->mapDayValueToDay($route->order_taking_days),
            ];

            return $payload;
        });

        $export = new RoutesExport($data);
        return Excel::download($export, 'ROUTE MASTER DATA.xlsx');
    }

    public function routesByBranch($branchId)
    {
        $routes = Route::with([
            'salesmanUser' => fn($query) => $query->select('users.id', 'name'),
            'waCustomer'
        ])
            ->where('restaurant_id', $branchId)
            ->select('routes.id', 'route_name')
            ->get();

        return response()->json($routes);
    }

    public function fetchRouteusers($branch)
    {
        try {
            $users = User::where('restaurant_id', $branch)
            ->select('id', 'name', 'role_id')
            ->whereIn('role_id', [5,187])
            ->orderBy('name')
            ->get();

        $representatives = $users->where('role_id', 187)->values()->toArray();
        $supervisors = $users->where('role_id', 5)->values()->toArray();

        return $this->jsonify(['representatives' => $representatives, 'supervisors' => $supervisors], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
        
    }
}
