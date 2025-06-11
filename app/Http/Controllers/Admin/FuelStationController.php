<?php

namespace App\Http\Controllers\Admin;

use App\FuelStation;
use App\Model\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FuelSupplier;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class FuelStationController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'fueling-stations';
        $this->base_route = 'fuel-stations';
        $this->resource_folder = 'admin.fuel_stations';
        $this->base_title = 'Fuel Stations';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $stations = FuelStation::with(['branch', 'fuelSupplier.supplierDetails'])->get()->map(function (FuelStation $station) {
            $station->display_diesel_price = format_amount_with_currency($station->fuel_price);
            return $station;
        });

        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'stations'));
    }

    public function create()
    {
        $title = 'Add Station';
        $breadcum = [$this->base_title => route("$this->base_route.index"), $title => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $googleMapsApiKey = config('app.google_maps_api_key');
        $restaurants = Restaurant::select(['name', 'id'])->get();
        $suppliers = FuelSupplier::with(['supplierDetails'])->get();
        return view("$this->resource_folder.create",
            compact(
                'title',
                'model',
                'breadcum',
                'base_route',
                'googleMapsApiKey',
                'restaurants',
                'suppliers'
            )
        );
    }

    public function store(Request $request)
    {
        FuelStation::create([
            'name' => $request->name,
            'location_name' => $request->location_name,
            'lat' => $request->lat ? $request->lat : 0,
            'lng' => $request->lng ? $request->lng : 0,
            'branch_id' => $request->branch_id,
            'fuel_price' => $request->fuel_price,
            'fuel_supplier_id' => $request->supplier,

        ]);

        return redirect()->route("$this->base_route.index")->with('success', 'Station created successfully');
    }

    public function edit($id)
    {
        $station = FuelStation::find($id);
        $title = "$station->name | Edit";
        $breadcum = [$this->base_title => route("$this->base_route.index"), $station->name => route("$this->base_route.show", $station->id), 'Edit' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $googleMapsApiKey = config('app.google_maps_api_key');
        $restaurants = Restaurant::select(['name', 'id'])->get();
        $suppliers = FuelSupplier::with(['supplierDetails'])->get();

        return view("$this->resource_folder.edit",
            compact(
                'title',
                'model',
                'breadcum',
                'base_route',
                'googleMapsApiKey',
                'restaurants',
                'station',
                'suppliers',
            )
        );
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $station = FuelStation::find($id);
        $station->update([
            'name' => $request->name,
            'location_name' => $request->location_name,
            'lat' => $request->lat ? $request->lat : 0,
            'lng' => $request->lng ? $request->lng : 0,
            'branch_id' => $request->branch_id,
            'fuel_price' => $request->fuel_price,
            'fuel_supplier_id' => $request->supplier,


        ]);

        return redirect()->route("$this->base_route.index")->with('success', 'Station updated successfully');
    }
    public function getFuelStations(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        try {
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            }
            $getUserData = JWTAuth::toUser($request->token);
            $stations = FuelStation::where('branch_id', $getUserData->restaurant_id)->get();    
            return response()->json(['status'=>true,'fuel_stations'=>$stations]);     
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getMessage()]);
        }
      
    }
}
