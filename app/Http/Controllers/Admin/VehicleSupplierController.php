<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\VehicleSupplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleSupplierController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'vehicle-suppliers';
        $this->base_route = 'vehicle-suppliers';
        $this->resource_folder = 'admin.vehicle_suppliers';
        $this->base_title = 'Vehicle Suppliers';
    }

    public function index(): View | RedirectResponse
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        $title = $this->base_title;
        $model = $this->model;

        $suppliers = VehicleSupplier::all();
        return view("$this->resource_folder.index", compact('title', 'model', 'breadcum', 'base_route', 'suppliers'));
    }

    public function create(): View | RedirectResponse
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add' => ''];
        $base_route = $this->base_route;
        $title = $this->base_title;
        $model = $this->model;
        $googleMapsApiKey = config('app.google_maps_api_key');


        return view("$this->resource_folder.create", compact('title', 'model', 'breadcum', 'base_route', 'googleMapsApiKey'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
           
    
            VehicleSupplier::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'physical_address' => $request->physical_address,
                'lat' => $request->loading_latitude,
                'lng' => $request->loading_longitude

            ]);

            return redirect()->route("$this->base_route.index")->with('success', 'Supplier added successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }
    public function edit($id): View | RedirectResponse
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }


        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Edit' => ''];
        $base_route = $this->base_route;
        $title = $this->base_title;
        $model = $this->model;
        $googleMapsApiKey = config('app.google_maps_api_key');
        $vehicleSupplier = VehicleSupplier::find($id);


        return view("$this->resource_folder.edit", compact('title', 'model', 'breadcum', 'base_route', 'googleMapsApiKey', 'vehicleSupplier'));

    }
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $vehicleSupplier = VehicleSupplier::find($id);
            $vehicleSupplier->name = $request->name;
            $vehicleSupplier->email = $request->email;
            $vehicleSupplier->phone = $request->phone;
            $vehicleSupplier->physical_address = $request->physical_address;
            $vehicleSupplier->lat = $request->loading_latitude;
            $vehicleSupplier->lng = $request->loading_longitude;
            $vehicleSupplier->save();

            return redirect()->route("$this->base_route.index")->with('success', 'Supplier updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }

    }


}
