<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\VehicleType;
use App\VehicleModel;
use App\VehicleSupplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VehicleModelController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'vehicle-models';
        $this->base_route = 'vehicle-models';
        $this->resource_folder = 'admin.vehicle_models';
        $this->base_title = 'Vehicle Models';
    }


    public function getVehicleModels(): JsonResponse
    {
        try {
            $models = VehicleModel::all();
            return $this->jsonify(['data' => $models], 200);
        } catch (\Throwable $e) {
            return $this->jsonify([], 500);
        }
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

        $vehicleModels = VehicleModel::all();
        return view("$this->resource_folder.index", compact('title', 'model', 'breadcum', 'base_route', 'vehicleModels'));
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
        $vehicleTypes = VehicleType::all();
        $vehicleSuppliers = VehicleSupplier::all();


        return view("$this->resource_folder.create", compact('title', 'model', 'breadcum', 'base_route', 'vehicleTypes', 'vehicleSuppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
           
    
            VehicleModel::create([
                'name' => $request->name,
                'vehicle_type_id' => $request->vehicle_type_id,
                'suppliers' => $request->supplier,
                'unladed_weight' => $request->unladed_weight,
                'ma_load_capacity' => $request->max_load_capacity,
                'fuel_tank_capacity' => $request->fuel_tank_capacity,
                'tyre_count'=>$request->tyre_count,
                'axle_count'=>$request->axle_count,
                'travel_expense'=>$request->travel_expense

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
        $vehicleModel = VehicleModel::find($id);
        $vehicleTypes = VehicleType::all();
        $modelType = VehicleType::find($vehicleModel->vehicle_type_id);
        $suppliers = VehicleSupplier::all();
        $modelSupplier = VehicleSupplier::find($vehicleModel->suppliers);


        return view("$this->resource_folder.edit", compact('title', 'model', 'breadcum', 'base_route', 'vehicleModel','vehicleTypes','modelType', 'suppliers', 'modelSupplier'));

    }
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $model = VehicleModel::find($id);
            $model->name = $request->name;
            $model->vehicle_type_id = $request->vehicle_type_id;
            $model->ma_load_capacity = $request->max_load_capacity;
            $model->fuel_tank_capacity = $request->fuel_tank_capacity;
            $model->tyre_count = $request->tyre_count;
            $model->axle_count = $request->axle_count;
            $model->travel_expense = $request->travel_expense;
            $model->suppliers = $request->supplier;
            $model->unladed_weight = $request->unladed_weight;  
            $model->save();

            return redirect()->route("$this->base_route.index")->with('success', 'Supplier updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }

    }
}
