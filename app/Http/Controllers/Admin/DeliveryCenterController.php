<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Model\DeliveryCentres;
use App\Model\Route;
use App\SalesmanShift;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaRouteCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeliveryCenterController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'manage-delivery-centers';
        $this->base_route = 'manage-delivery-centers';
        $this->resource_folder = 'admin.delivery_centers';
        $this->base_title = 'Manage Delivery Centers';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;

        $query = DeliveryCentres::with(['route', 'waRouteCustomers'])->orderBy('name');
        if ($request->route_id) {
            $query = $query->where('route_id', $request->route_id);
        }

        if ($request->name_filter) {
            $searchQuery = $request->name_filter;
            $query = $query->where('name', 'like', "%$searchQuery")
                ->orWhere('name', 'like', "%$searchQuery%")
                ->orWhere('name', 'like', "$searchQuery%");
        }

        $centers = $query->simplePaginate(15);
        $centers->getCollection()->transform(function (DeliveryCentres $center) {
            return [
                'id' => $center->id,
                'name' => $center->name,
                'route' => [
                    'id' => $center->route->id,
                    'route_name' => $center->route->route_name,
                ],
                'shop_count' => $center->waRouteCustomers()->count(),
                'date_created' => Carbon::parse($center->created_at)->toFormattedDateString()
            ];
        });

        $base_route = $this->base_route;
        $routes = Route::select(['id', 'route_name'])->get();
        return view("$this->resource_folder.index", compact('title', 'breadcum', 'centers', 'base_route', 'model', 'routes'));
    }

    public function create()
    {
        $title = 'Add Delivery Center';
        $breadcum = [$this->base_title => route("$this->base_route.index"), $title => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $routes = Route::pluck('route_name', 'id');
        $googleMapsApiKey = config('app.google_maps_api_key');
        return view("$this->resource_folder.create", compact('title', 'model', 'breadcum', 'base_route', 'routes', 'googleMapsApiKey'));
    }

    public function store(Request $request)
    {
        try {
            DeliveryCentres::create([
                'route_id' => $request->route_id,
                'name' => $request->name,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'center_location_name' => $request->center_location_name,
                'preferred_center_radius' => $request->preferred_center_radius ?? 1000,
            ]);

            return redirect()->route("$this->base_route.index")->with('success', 'Delivery center created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error was encountered. Please try again.');
        }
    }

    public function storeFromApi(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $payload = json_decode($request->payload, true);
            foreach ($payload['centers'] as $center) {
                DeliveryCentres::create([
                    'route_id' => $payload['route_id'],
                    'name' => $center['name'],
                    'lat' => $center['lat'],
                    'lng' => $center['lng'],
                    'center_location_name' => $center['center_location_name'],
                    'preferred_center_radius' => $center['preferred_center_radius'] ?? 1000,
                ]);
            }

            DB::commit();
            return $this->jsonify(['message' => 'success'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function updateFromApi(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $payload = json_decode($request->payload, true);
            foreach ($payload['centers'] as $center) {
                DeliveryCentres::create([
                    'route_id' => $payload['route_id'],
                    'name' => $center['name'],
                    'lat' => $center['lat'],
                    'lng' => $center['lng'],
                    'center_location_name' => $center['center_location_name'],
                    'preferred_center_radius' => $center['preferred_center_radius'] ?? 1000,
                ]);
            }

            DB::commit();
            return $this->jsonify(['message' => 'success'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function adminuploaddeliverycenter(Request $request)
    {
        try {
            if ($request->has('center_name')) {
                for ($i = 0; $i < count($request->center_name); $i++) {
                    $add = new DeliveryCentres();
                    $add->route_id = $request->route_id;
                    $add->name = $request->center_name[$i];
                    $add->lat = $request->center_latitude[$i];
                    $add->lng = $request->center_longitude[$i];
                    $add->center_location_name = $request->center_location_name[$i];
                    $add->preferred_center_radius = $request->preferred_center_radius[$i] ?? 1000;
                    $add->save();
                }
                return redirect()->route('manage-route-linked-centers-list', $request->route_id)->with('center_update', 'Delivery center created successfully');

            }

            return redirect()->route('manage-route-linked-centers-list', $request->route_id);
        } catch (\Throwable $e) {
            return redirect()->back()->with('center_update', 'An error was encountered. Please try again.');
        }
    }

    public function updateRouteCenter(Request $request)
    {
        // $validator = Validator::make($request->all(),[
        //     'selected_center_id'=>'required',
        //     'update_center_name'=>'required',
        //     'center_location_name'=>'required',
        //     'update_center_latitude'=>'required',
        //     'update_center_longitude'=>'required',
        //     'update_preferred_center_radius'=>'required'
        // ]);
        // if ($validator->fails()){
        //     return response()->json(['errors'=>$validator->errors()]);
        // }else{
        //     $center = DeliveryCentres::where('id', $request->selected_center_id)->first();
        //     if($center){
        //         $center->name = $request->update_center_name;
        //         $center->lat = $request->update_center_latitude;
        //         $center->lng = $request->update_center_longitude;
        //         $center->center_location_name = $request->center_location_name;
        //         $center->preferred_center_radius = $request->update_preferred_center_radius ?? 1000;
        //         $center->save();

        //         // return response()->json(['update_center_message'=>'Delivery center details updated successfully.']);
        //         // return redirect()->back()->with('center_update','Delivery center details updated successfully.');
        //         return redirect()->back();
        //     }else{
        //         // return redirect()->json(['update_center_message'=>'Delivery center details not found.']);
        //         return redirect()->route('manage-route-linked-centers-list', $request->selected_center_id)->with('center_update','Delivery center details updated successfully.');
        //     }
        // }
        $request->validate([
            'selected_center_id' => 'required',
            'update_center_name' => 'required',
            'center_location_name' => 'required',
            'update_center_latitude' => 'required',
            'update_center_longitude' => 'required',
            'update_preferred_center_radius' => 'required'
        ]);
        $center = DeliveryCentres::where('id', $request->selected_center_id)->first();
        if ($center) {

            $center->name = $request->update_center_name;
            $center->lat = $request->update_center_latitude;
            $center->lng = $request->update_center_longitude;
            $center->center_location_name = $request->center_location_name;
            $center->preferred_center_radius = $request->update_preferred_center_radius ?? 1000;
            $center->save();

            return to_route('manage-route-linked-centers-list', $center->route_id)->with('center_update', 'Delivery center details updated successfully.');
        } else {
            return redirect()->back()->with('center_update', 'Delivery center details not found.');
        }

    }

    public function deleteSelectedRouteDetails(Request $request)
    {
        $request->validate([
            'delete_selected_center_id' => 'required',
        ]);

        $center = DeliveryCentres::where('id', $request->delete_selected_center_id)->first();
        $activeRouteCustomers = WaRouteCustomer::where('delivery_centres_id', $center->id)->get();
        if($activeRouteCustomers->count() > 0){
            return redirect()->back()->with('warning', 'Cannot delete center with active route customers');
        }
        if ($center) {
            $center->delete();
            $customers = WaRouteCustomer::where('center_id', $request->delete_selected_center_id)->delete();
            return redirect()->back()->with('center_update', 'Delivery center deleted successfully.');
        } else {
            return redirect()->back()->with('center_update', 'Delivery center not found.');
        }
    }

    public function edit($id)
    {
        $center = DeliveryCentres::find($id);
        $title = "Update Delivery center";
        $breadcum = [$this->base_title => route("$this->base_route.index"), $title => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $googleMapsApiKey = config('app.google_maps_api_key');
        $routes = Route::pluck('route_name', 'id');
        return view("$this->resource_folder.edit", compact('title', 'model', 'breadcum', 'base_route', 'googleMapsApiKey', 'center', 'routes'));
    }

    public function update(Request $request, $id)
    {
        try {
            $center = DeliveryCentres::find($id);
            $center->update([
                'route_id' => $request->route_id,
                'name' => $request->name,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'center_location_name' => $request->center_location_name,
                'preferred_center_radius' => $request->preferred_center_radius ?? 1000,
            ]);

            return redirect()->route("$this->base_route.index")->with('success', 'Delivery center updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error was encountered. Please try again.');
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $route = DeliveryCentres::find($id);
            $route->delete();

            return redirect()->route("$this->base_route.index")->with('success', 'Center deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred was encountered. Please try again.']);
        }
    }

    public function getCenterById(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'delivery_center_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Delivery center id is required'], 422);
            }

            $center = DeliveryCentres::with(['route', 'waRouteCustomers'])->find($request->delivery_center_id);
            if (!$center) {
                return $this->jsonify(['message' => 'A delivery center matching the provided id was not found.'], 404);
            }

            $center->unvisited_shops = 0;
            $user = JWTAuth::toUser($request->token);
            foreach ($center->waRouteCustomers as $shop) {
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

            unset($center->waRouteCustomers);
            unset($center->route);

            return $this->jsonify(['data' => $center], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
