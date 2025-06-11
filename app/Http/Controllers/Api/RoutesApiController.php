<?php

namespace App\Http\Controllers\Api;

use App\DeliveryManShift;
use App\DeliverySchedule;
use App\DeliveryScheduleCustomer;
use App\Model\DeliveryCentres;
use App\Model\Route;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaRouteCustomer;
use App\SalesmanShift;
use App\User;
use App\VehicleAssignment;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoutesApiController extends Controller
{
    public function getRouteList(Request $request): JsonResponse
    {
        $user = JWTAuth::toUser($request->token);
        $payload = [
            'status' => true,
            'message' => 'success',
            'routes' => [],
        ];

        try {
            //            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Token mismatch';
                return response()->json($payload, 422);
            }

            $routes = [];
            switch ($user->role_id) {
                case 4:
                    $userRouteIds = $user->routes->pluck('id');
                    $routes = Route::withCount(['waRouteCustomer as shops_count'])
                        ->whereIn('id', $userRouteIds)
                        ->get()
                        ->map(function (Route $route) use ($user) {
                            $route->order_taking_day = $this->getOrderTakingDay($route->order_taking_days);
                            $route->is_order_taking_day = false;
                            $orderTakingDays = explode(',', $route->order_taking_days);
                            if ($orderTakingDays) {
                                $today = Carbon::now()->dayOfWeek;
                                if (in_array($today, $orderTakingDays)) {
                                    $route->is_order_taking_day = true;
                                }
                            }

                            return [
                                'id' => $route->id,
                                'route_name' => $route->route_name,
                                'is_order_taking_day' => $route->is_order_taking_day,
                                'order_taking_day' => $route->order_taking_day,
                                'items_to_be_received' => false,
                                'should_validate_gate_pass' => false,
                                'gate_pass_code' => "",
                                'shops_count' => $route->shops_count
                            ];
                            // return $this->getBasicRouteInformation($route, $user);
                        });

                    break;
                case 6:
                    $deliverySchedule = DeliverySchedule::whereNotIn('status', ['finished'])->where('driver_id', $user->id)->first();
                    if (!$deliverySchedule) {
                        break;
                    }

                    $route = Route::with(['centers', 'centers.waRouteCustomers', 'sections', 'polylines'])->find($deliverySchedule->route_id);
                    $routes = collect([$route])->map(function (Route $route) use ($user, $deliverySchedule) {
                        $route->shops = DeliveryScheduleCustomer::where('delivery_schedule_id', $deliverySchedule->id)->count();
                        $route->shops_count = $route->shops;
                        $centerIds = DB::table('delivery_schedule_customers')
                            ->where('delivery_schedule_id', $deliverySchedule->id)
                            ->join('wa_route_customers', 'delivery_schedule_customers.customer_id', 'wa_route_customers.id')
                            ->pluck('wa_route_customers.delivery_centres_id')
                            ->toArray();
                        $route->centers_count = count(array_unique($centerIds));
                        return $this->getBasicRouteInformation($route, $user, overrideCenterCount: false);
                    });

                    break;
                default:
                    break;
            }

            //            $routes = Route::with(['centers', 'centers.waRouteCustomers', 'sections', 'polylines'])
            //                ->withCount(['centers'])
            //                ->withCount(['waRouteCustomer as shops'])
            //                ->whereIn("id", $userRouteIds)->get();

            //            $routes = collect($routes)->map(function (Route $route) use ($user) {
            //                //
            //                $routeData = $this->getBasicRouteInformation($route, $user, null, null);
            //                unset($routeData->polylines);
            //                return $routeData;
            //            });

            $payload['routes'] = $routes;
            return response()->json($payload);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['trace'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    public function getRouteDeliveryCenters(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'success',
            'route' => [],
        ];

        try {
            $validator = Validator::make($request->all(), [
                'route_id' => 'required',
            ]);

            if ($validator->fails()) {
                $payload['status'] = false;
                $payload['message'] = 'Route id is required';

                return response()->json($payload, 422);
            }

            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'The provided token is invalid';
                return response()->json($payload, 422);
            }

            if ($user->role_id == 4) {
                $route = Route::with('centers')->withCount(['waRouteCustomer as shops_count', 'centers as centers_count'])->find($request->route_id);
                $route->target_amount = manageAmountFormat($route->sales_target);
                $route->target_balance = manageAmountFormat($route->sales_target);

                $route->order_taking_day = $this->getOrderTakingDay($route->order_taking_days);
                $route->is_order_taking_day = false;
                $orderTakingDays = explode(',', $route->order_taking_days);
                if ($orderTakingDays) {
                    $today = Carbon::now()->dayOfWeek;
                    if (in_array($today, $orderTakingDays)) {
                        $route->is_order_taking_day = true;
                    }
                }

                $latestOpenShift = SalesmanShift::latest()->where('route_id', $route->id)->where('status', 'open')->first();
                $routeUnvisitedCustomers = collect();

                if ($latestOpenShift) {
                    $shiftTotal = DB::table('wa_internal_requisition_items as items')
                        ->join('wa_internal_requisitions as orders', function ($join) use ($latestOpenShift) {
                            $join->on('orders.id', '=', 'items.wa_internal_requisition_id')->where('wa_shift_id', $latestOpenShift->id);
                        })
                        ->sum('total_cost_with_vat') ?? 0;

                    $route->target_balance = manageAmountFormat($route->sales_target - $shiftTotal);
                    if (($route->sales_target - $shiftTotal) < 0) {
                        $route->target_balance = manageAmountFormat(0);
                    }

                    $routeUnvisitedCustomers = DB::table('salesman_shift_customers')
                        ->select('delivery_centres_id')
                        ->join('wa_route_customers', 'salesman_shift_customers.route_customer_id', 'wa_route_customers.id')
                        ->where('salesman_shift_id', $latestOpenShift->id)
                        ->where('visited', false)
                        ->get();
                }

                $route->centers = $route->centers->map(function ($center) use ($routeUnvisitedCustomers) {
                    $center->unvisited_shops = $routeUnvisitedCustomers->where('delivery_centres_id', $center->id)->count();
                    return $center;
                });

                $payload['route'] = $route;
                return response()->json($payload);
            }

            if ($user->role_id == 6) {
                $deliverySchedule = DeliverySchedule::whereNotIn('status', ['finished'])->where('driver_id', $user->id)->first();
                $centerIds = DB::table('delivery_schedule_customers')
                    ->where('delivery_schedule_id', $deliverySchedule->id)
                    ->join('wa_route_customers', 'delivery_schedule_customers.customer_id', 'wa_route_customers.id')
                    ->pluck('wa_route_customers.delivery_centres_id')
                    ->toArray();

                $centers = DeliveryCentres::with(['route', 'waRouteCustomers'])->whereIn('id', $centerIds);
            } else {
                $centers = DeliveryCentres::with(['route', 'waRouteCustomers'])->latest()->where('route_id', $request->route_id);
            }

            if ($request->search_query) {
                $centers = $centers->where('name', 'LIKE', "%$request->search_query%");
            }

            $centers = $centers->get();
            foreach ($centers as $center) {
                $center->unvisited_shops = 0;
                $shopList = [];
                foreach ($center->waRouteCustomers as $shop) {
                    $shopList[] = [
                        'id' => $shop->id,
                        'bussiness_name' => $shop->bussiness_name,
                        'lat' => $shop->lat,
                        'lng' => $shop->lng
                    ];

                    switch ($user->role_id) {
                        case 4:
                            $currentShift = SalesmanShift::with('shiftCustomers')->where('status', 'open')
                                ->where('salesman_id', $user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->shiftCustomers()->where('route_customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    if ($routeCustomer->visited != 1) {
                                        $center->unvisited_shops += 1;
                                    }
                                }
                            }
                            break;
                        case 6:
                            $currentShift = DeliverySchedule::with('customers')->latest()->started()->forDriver($user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->customers()->where('customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    $shop->visited_by_deliveryman = $routeCustomer->visited == 1;
                                    if ($routeCustomer->visited != 1) {
                                        $center->unvisited_shops += 1;
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }

                $center->wa_route_customers = $shopList;

                unset($center->waRouteCustomers);
                unset($center->route);
            }

            $routeInfo = $this->getBasicRouteInformation(Route::find($request->route_id), $user, ignoreCenterIntel: true);
            $routeInfo->centers = $centers;
            unset($routeInfo->sections);

            $payload['route'] = $routeInfo;
            return response()->json($payload);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    public function getCenterShops(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'delivery_center_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Delivery center id is required'], 422);
            }

            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['message' => 'The provided token is invalid'], 422);
            }

            $centre = DeliveryCentres::find($request->delivery_center_id);
            $shops = WaRouteCustomer::where('delivery_centres_id', $request->delivery_center_id);
            if ($user->role_id == 6) {
                $delivery = DeliverySchedule::with(['customers'])->latest()->forDriver($user->id)->where('route_id', $centre->route_id)->first();
                $customerIds = $delivery->customers()->pluck('customer_id')->toArray();
                $shops = $shops->whereIn('id', $customerIds);
            }

            $shops = $shops->where('status', 'approved');

            if ($request->search_query) {
                $shops = $shops->where('bussiness_name', 'LIKE', "%$request->search_query%");
            }

            $appUrl = env('APP_URL');
            $shops = $shops->orderBy('created_at', 'DESC')
                ->cursorPaginate(100)
                ->through(function (WaRouteCustomer $shop) use ($appUrl, $request, $user) {
                    if ($shop->image_url) {
                        $shop->photo = "$appUrl/uploads/shops/" . $shop->image_url;
                    }

                    $shop->can_edit = $shop->created_by == 0;

                    $route = Route::find($shop->route_id);
                    $route->order_taking_day = $this->getOrderTakingDay($route->order_taking_days);
                    $route->is_order_taking_day = false;
                    $orderTakingDays = explode(',', $route->order_taking_days);
                    if ($orderTakingDays) {
                        $today = Carbon::now()->dayOfWeek;
                        if (in_array($today, $orderTakingDays)) {
                            $route->is_order_taking_day = true;
                        }
                    }
    
                    $route->override_check_sales_proximity = false;
                    $shop->route = $route;

                    // $shop->route = $this->getBasicRouteInformation(route: Route::find($shop->route_id), user: $user, ignoreCenterIntel: true);
                    // unset($shop->route->centers);
                    // unset($shop->route->sections);

                    $shop->visited_by_salesman = false;
                    $shop->visited_by_deliveryman = false;
                    switch ($user->role_id) {
                        case 4:
                            $currentShift = SalesmanShift::with('shiftCustomers')->where('status', 'open')
                                ->where('salesman_id', $user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->shiftCustomers()->where('route_customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    $shop->visited_by_salesman = $routeCustomer->visited == 1;
                                }
                            }

                            break;

                        case 6:
                            $currentShift = DeliverySchedule::with('customers')->latest()->started()->forDriver($user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->customers()->where('customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    $undeliveredOrders = WaInternalRequisition::where('wa_shift_id', $currentShift->shift_id)
                                        ->where('wa_route_customer_id', $shop->id)->whereNotIn('status', ['DELIVERED', 'PAID'])->get();
                                    if ($undeliveredOrders && $undeliveredOrders->count() >= 1) {
                                        $shop->visited_by_deliveryman = false;
                                    } else {
                                        $shop->visited_by_deliveryman = $routeCustomer->visited == 1;
                                    }
                                }
                            }

                            break;
                        default:
                            break;
                    }

                    return $shop;
                });

            return $this->jsonify($shops, 200);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    public function getRouteById(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'success',
            'route' => []
        ];

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Your session is invalid';
                return response()->json($payload, 422);
            }

            if (!$request->route_id) {
                $payload['status'] = false;
                $payload['message'] = 'Route id is required';
                return response()->json($payload, 422);
            }

            $route = Route::find($request->route_id);
            if (!$route) {
                $payload['status'] = false;
                $payload['message'] = 'A route with the provided id was not found';
                return response()->json($payload, 404);
            }

            $routeData = $this->getBasicRouteInformation($route, $user);
            unset($routeData['centers']);
            unset($routeData['sections']);

            $payload['route'] = $routeData;
            return response()->json($payload);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = 'An server error was encountered.';
            $payload['trace'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    public function createRouteCenter(Request $request): JsonResponse
    {

        $payload = [
            'status' => true,
            'message' => 'success',
            'data' => null,
        ];

        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            $payload['status'] = false;
            $payload['message'] = 'You have Validation errors';
            $payload['errors'] = $validator->errors();

            return response()->json($payload, 422);
        }
        try {
            $center =  DeliveryCentres::updateOrCreate([
                'route_id' => $request->route_id,
                'name' => $request->name,
                'center_location_name' => $request->name,
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ], [
                'preferred_center_radius' => 100,
                'route_id' => $request->route_id,
                'name' => $request->name,
                'center_location_name' => $request->name,
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ]);
            $payload['status'] = true;
            $payload['message'] = 'Center Created Successfully';
            $payload['data'] = $center;
            return response()->json($payload);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['data'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    public function getRouteVerificationPercentage(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'route_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => 'Unsuccessful', 'errors' => $validation->errors()], 422);
        }
        $date = '2024-06-05';
        $routeId = $request->route_id;

        //        $stats = WaRouteCustomer::where('route_id', $routeId)
        //            ->whereDate('created_at', $date)
        //            ->selectRaw('
        //            SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified_count,
        //            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_users_after_date,
        //            COUNT(*) as total_count
        //        ',[$date2])
        //            ->first();

        $stats = WaRouteCustomer::where('route_id', $routeId)
            ->whereDate('created_at', $date)
            ->selectRaw('
                SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified_count,
                COUNT(*) as total_count
            ')
            ->first();

        $newUsersAfterDateCount = WaRouteCustomer::where('route_id', $routeId)
            ->whereDate('created_at', '>', $date)
            ->count();

        $percentageUnverified = ($stats->total_count > 0) ? ($stats->unverified_count / $stats->total_count) * 100 : 0;

        return response()->json([
            'total_customers' => (int) $stats->total_count,
            'new_customers' => (int)$newUsersAfterDateCount,
            'verified_count' => (int)($stats->total_count -  $stats->unverified_count),
            'unverified_count' => (int) $stats->unverified_count,
            'percentage_unverified' => number_format($percentageUnverified, 2),
            'percentage_verified' => 100 -  number_format($percentageUnverified, 2),
        ]);
    }
}
